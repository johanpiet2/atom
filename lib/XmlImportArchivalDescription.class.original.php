<?php
ini_set("max_execution_time", 300);
set_time_limit(300);
error_reporting(E_ALL ^ E_STRICT);
/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Import an XML document into Qubit.
 *
 * @package    AccesstoMemory
 * @subpackage library
 * @author     MJ Suhonos <mj@suhonos.ca>
 * @author     Peter Van Garderen <peter@artefactual.com>
 * @author     Mike Cantelon <mike@artefactual.com>
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class XmlImportArchivalDescription
{
    protected $errors = null, $rootObject = null, $parent = null;
    
    public function import($xmlFile, $options = array(), $type = null)
    {
        QubitSearch::enable();
        $this->langScriptMaterialCount = 0;
        $this->langMaterialCount = 0;
        $this->langMaterialScriptCount = 0;

        $this->langScriptCount = 0;
        $this->langDescCount = 0;

        // load the XML document into a DOMXML object
        $importDOM = $this->loadXML($xmlFile, $options);
        QubitXMLImport::addLog("Import File: " . $xmlFile, "", get_class($this), false);
        // if we were unable to parse the XML file at all
        if (empty($importDOM->documentElement)) {
            QubitXMLImport::addLog($xmlFile, "Unable to parse XML file: malformed or unresolvable entities", get_class($this), true);
            $errorMsg = sfContext::getInstance()->i18n->__('Unable to parse XML file: malformed or unresolvable entities');
            throw new Exception($errorMsg);
        } //empty($importDOM->documentElement)
        // if libxml threw errors, populate them to show in the template
        if ($importDOM->libxmlerrors) {
            // warning condition, XML file has errors (perhaps not well-formed or invalid?)
            foreach ($importDOM->libxmlerrors as $libxmlerror) {
                $xmlerrors[] = sfContext::getInstance()->i18n->__('libxml error %code% on line %line% in input file: %message%', array(
                    '%code%' => $libxmlerror->code,
                    '%message%' => $libxmlerror->message,
                    '%line%' => $libxmlerror->line
                ));
            } //$importDOM->libxmlerrors as $libxmlerror
            $this->errors = array_merge((array) $this->errors, $xmlerrors);
        } //$importDOM->libxmlerrors
        if ('eac-cpf' == $importDOM->documentElement->tagName) {
            try {
		        QubitXMLImport::addLog("New Actor3: >>>".QubitActor::ROOT_ID."<<<", "", get_class($this), true);
                $this->rootObject           = new QubitActor;
                $this->rootObject->parentId = QubitActor::ROOT_ID;
                $eac                        = new sfEacPlugin($this->rootObject);
                $eac->parse($importDOM);
                $this->rootObject->save();
                QubitXMLImport::addLog("rootObject ", "", get_class($this), false);
            }
            catch (PDOException $e) {
                QubitXMLImport::addLog("DOMException rootObject: " . $e->getMessage(), "", get_class($this), true);
                echo $e->getMessage();
                print "DOMException to save XML rootObject: " . $e->getMessage() . "\n";
            }
            
            if (isset($eac->itemsSubjectOf)) {
                foreach ($eac->itemsSubjectOf as $item) {
                    try {
                        $relation                            = new QubitRelation;
                        $relation->object                    = $this->rootObject;
                        $relation->typeId                    = QubitTerm::NAME_ACCESS_POINT_ID;
                        $item->relationsRelatedBysubjectId[] = $relation;
                        $item->save();
                        QubitXMLImport::addLog("relation ", "", get_class($this), false);
                    }
                    catch (PDOException $e) {
                        QubitXMLImport::addLog("DOMException relation: " . $e->getMessage(), "", get_class($this), true);
                        echo $e->getMessage();
                        print "DOMException to save XML relation: " . $e->getMessage() . "\n";
                    }
                } //$eac->itemsSubjectOf as $item
            } //isset($eac->itemsSubjectOf)
            return $this;
        } //'eac-cpf' == $importDOM->documentElement->tagName
        // FIXME hardcoded until we decide how these will be developed
        $validSchemas      = array(
            // document type declarations
            '+//ISBN 1-931666-00-8//DTD ead.dtd Encoded Archival Description (EAD) Version 2002//EN' => 'ead',
            '-//Society of American Archivists//DTD ead.dtd (Encoded Archival Description (EAD) Version 1.0)//EN' => 'ead1',
            // namespaces
            'http://www.loc.gov/METS/' => 'mets',
            'http://www.loc.gov/mods/' => 'mods',
            'http://www.loc.gov/MARC21/slim' => 'marc',
            // root element names
            //'collection' => 'marc',
            //'record' => 'marc',
            //'mets' => 'mets',
            'record' => 'oai_dc_record',
            'dc' => 'dc',
            'oai_dc:dc' => 'dc',
            'dublinCore' => 'dc',
            'metadata' => 'dc',
            'mods' => 'mods',
            'ead' => 'ead',
            'add' => 'alouette',
            'http://www.w3.org/2004/02/skos/core#' => 'skos'
        );
        // determine what kind of schema we're trying to import
        $schemaDescriptors = array(
            $importDOM->documentElement->tagName
        );
        if (!empty($importDOM->namespaces)) {
            krsort($importDOM->namespaces);
            $schemaDescriptors = array_merge($schemaDescriptors, $importDOM->namespaces);
        } //!empty($importDOM->namespaces)
        if (!empty($importDOM->doctype)) {
            $schemaDescriptors = array_merge($schemaDescriptors, array(
                $importDOM->doctype->name,
                $importDOM->doctype->systemId,
                $importDOM->doctype->publicId
            ));
        } //!empty($importDOM->doctype)
        foreach ($schemaDescriptors as $descriptor) {
            if (array_key_exists($descriptor, $validSchemas)) {
                $importSchema = $validSchemas[$descriptor];
            } //array_key_exists($descriptor, $validSchemas)
        } //$schemaDescriptors as $descriptor
        switch ($importSchema) {
            case 'ead':
                // just validate EAD import for now until we can get StrictXMLParsing working for all schemas in the self::LoadXML function. Having problems right now loading schemas.
                //$importDOM->validate(); 
                // if libxml threw errors, populate them to show in the template
                //foreach (libxml_get_errors() as $libxmlerror)
                // {
                //    $this->errors[] = sfContext::getInstance()->i18n->__('libxml error %code% on line %line% in input file: %message%', array('%code%' => $libxmlerror->code, '%message%' => $libxmlerror->message, '%line%' => $libxmlerror->line));
                //  }
                break;
            
            case 'skos':
                $criteria = new Criteria;
                $criteria->add(QubitSetting::NAME, 'plugins');
                $setting = QubitSetting::getOne($criteria);
                if (null === $setting || !in_array('sfSkosPlugin', unserialize($setting->getValue(array(
                    'sourceCulture' => true
                ))))) {
                    throw new sfException(sfContext::getInstance()->i18n->__('The SKOS plugin is not enabled'));
                } //null === $setting || !in_array('sfSkosPlugin', unserialize($setting->getValue(array( 'sourceCulture' => true ))))
                $importTerms      = sfSkosPlugin::parse($importDOM, $options);
                $this->rootObject = QubitTaxonomy::getById(QubitTaxonomy::SUBJECT_ID);
                $this->count      = count($importTerms);
                return $this;
                break;
        } //$importSchema
        //start import
        $importMap = sfConfig::get('sf_app_module_dir') . DIRECTORY_SEPARATOR . 'object' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $importSchema . '.yml';
        QubitXMLImport::addLog("Import Map: " . $importMap, "", get_class($this), false);
        if (!file_exists($importMap)) {
            // error condition, unknown schema or no import filter
            $errorMsg = sfContext::getInstance()->i18n->__('Unknown schema or import format: "%format%"', array(
                '%format%' => $importSchema
            ));
            QubitXMLImport::addLog($importMap, $errorMsg, get_class($this), true);
            throw new Exception($errorMsg);
        } //!file_exists($importMap)
        $this->schemaMap = sfYaml::load($importMap);
        // if XSLs are specified in the mapping, process them
        if (!empty($this->schemaMap['processXSLT'])) {
            // pre-filter through XSLs in order
            foreach ((array) $this->schemaMap['processXSLT'] as $importXSL) {
                $importXSL = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'xslt' . DIRECTORY_SEPARATOR . $importXSL;
                QubitXMLImport::addLog("Import importXSL: " . $importXSL, "", get_class($this), false);
                if (file_exists($importXSL)) {
                    // instantiate an XSLT parser
                    $xslDOM = new DOMDocument;
                    $xslDOM->load($importXSL);
                    // Configure the transformer
                    $xsltProc = new XSLTProcessor;
                    $xsltProc->registerPHPFunctions();
                    $xsltProc->importStyleSheet($xslDOM);
                    $importDOM->loadXML($xsltProc->transformToXML($importDOM));
                    unset($xslDOM);
                    unset($xsltProc);
                } //file_exists($importXSL)
                else {
                    $this->errors[] = sfContext::getInstance()->i18n->__('Unable to load import XSL filter (Archival Description): "%importXSL%"', array(
                        '%importXSL%' => $importXSL
                    ));
                    QubitXMLImport::addLog("Unable to load import XSL filter (Archival Description): " . $importXSL, "", get_class($this), true);
                }
            } //(array) $this->schemaMap['processXSLT'] as $importXSL
            // re-initialize xpath on the new XML
            $importDOM->xpath = new DOMXPath($importDOM);
        } //!empty($this->schemaMap['processXSLT'])
        QubitXMLImport::addLog("Import Schema: " . $importSchema, "", get_class($this), false);
        // switch source culture if language is set in an EAD document
        if ($importSchema == 'ead') {
            if (is_object($langusage = $importDOM->xpath->query('//eadheader/profiledesc/langusage/language/@langcode'))) {
                $sf_user                 = sfContext::getInstance()->user;
                $currentCulture          = $sf_user->getCulture();
                $langCodeConvertor       = new fbISO639_Map;
                $this->languageShortCode = "";
                foreach ($langusage as $language) {
                    $isocode = trim(preg_replace('/[\n\r\s]+/', ' ', $language->nodeValue));
                    if ($isocode != "NoCode") {
                        // convert to Symfony culture code
                        if (!$twoCharCode = strtolower($langCodeConvertor->getID1($isocode, false))) {
                            $twoCharCode = $isocode;
                        } //!$twoCharCode = strtolower($langCodeConvertor->getID1($isocode, false))
                        // Check to make sure that the selected language is supported with a Symfony i18n data file.
                        // If not it will cause a fatal error in the Language List component on every response.
                        ProjectConfiguration::getActive()->loadHelpers('I18N');
                        try {
                            format_language($twoCharCode, $twoCharCode);
                        }
                        catch (Exception $e) {
                            $this->errors[] = sfContext::getInstance()->i18n->__('EAD "langmaterial" is set to') . ': "' . $isocode . '". ' . sfContext::getInstance()->i18n->__('This language is currently not supported.');
                            QubitXMLImport::addLog("This language is currently not supported: " . $e, "", get_class($this), true);
                            continue;
                        }
                        if ($currentCulture !== $twoCharCode) {
                            $this->errors[] = sfContext::getInstance()->i18n->__('EAD "langmaterial" is set to') . ': "' . $isocode . '" (' . format_language($twoCharCode, 'en') . '). ' . sfContext::getInstance()->i18n->__('Your XML document has been saved in this language and your user interface has just been switched to this language.');
                        } //$currentCulture !== $twoCharCode
                        $sf_user->setCulture($twoCharCode);
                    } //$langusage as $language
                    else {
                        $sf_user->setCulture('en');
                    }
                    // can only set to one language, so have to break once the first valid language is encountered
                    break;
                }
            } //is_object($langusage = $importDOM->xpath->query('//eadheader/profiledesc/langusage/language/@langcode'))
        } //$importSchema == 'ead'
        
        unset($this->schemaMap['processXSLT']);
        // go through schema map and populate objects/properties
        foreach ($this->schemaMap as $name => $mapping) {
            QubitXMLImport::addLog("Schema schemaMap name: " . $name, "", get_class($this), false);
            // if object is not defined or a valid class, we can't process this mapping
            if (empty($mapping['Object']) || !class_exists('Qubit' . $mapping['Object'])) {
                $this->errors[] = sfContext::getInstance()->i18n->__('Non-existent class defined in import mapping: "%class%"', array(
                    '%class%' => 'Qubit' . $mapping['Object']
                ));
                continue;
            } //empty($mapping['Object']) || !class_exists('Qubit' . $mapping['Object'])
            QubitXMLImport::addLog("Schema Map: " . $mapping['Object'], "", get_class($this), false);
            QubitXMLImport::addLog("Schema XPath: " . $mapping['XPath'], "", get_class($this), false);
            // get a list of XML nodes to process
            $nodeList = $importDOM->xpath->query($mapping['XPath']);
            QubitXMLImport::addLog("nodeList count: " . count($nodeList), "", get_class($this), false);
            $databaseManager    = new sfDatabaseManager(sfContext::getInstance()->getConfiguration());
            $databaseConnection = $databaseManager->getDatabase('propel');
            $username           = $databaseConnection->getParameter('username');
            $password           = $databaseConnection->getParameter('password');
            $dsnInfo            = ($databaseConnection->getParameter('dsn'));
            try {
                $conn = new PDO($dsnInfo, $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) {
                print 'Connection failed: ' . $e->getMessage() . "\n";
            }
            $rowcount    = 0;
            // only for mainframe import
            // remove when batch is imported
            $csvfile     = null;
            $publishPath = QubitSetting::getByName('publish_path');
            if ($publishPath == null) {
                QubitXMLImport::addLog($publishPath, "No upload path defined. Contact support/administrator", get_class($this), true);
                throw new sfException(sfContext::getInstance()->i18n->__("No upload path defined. Contact support/administrator"));
            } //$publishPath == null
            else {
                date_default_timezone_set('Africa/Johannesburg');
                $publishname = "publish_" . date("20" . "ymdH", time()) . ".csv"; //file per hour
            }
            
            try {
                // First of all, let's begin a transaction
                $conn->beginTransaction();
                QubitXMLImport::addLog("beginTransaction ", "", get_class($this), false);
                
                if (empty($nodeList)) {
                    QubitXMLImport::addLog("nodeList empty: ", "", get_class($this), false);
                } //empty($nodeList)
                else {
                    QubitXMLImport::addLog("nodeList not empty: ", "", get_class($this), false);
                }
                
                QubitXMLImport::addLog("nodeList count2: " . count($nodeList), "", get_class($this), false);
                foreach ($nodeList as $domNode) {
                    QubitXMLImport::addLog("foreach ", "", get_class($this), false);
                    // create a new object
                    $class         = 'Qubit' . $mapping['Object'];
                    $currentObject = new $class;
                    QubitXMLImport::addLog("currentObject: New", "", get_class($this), false);
                    // set the rootObject to use for initial display in successful import
                    if (!$this->rootObject) {
                        $this->rootObject = $currentObject;
                    } //!$this->rootObject
                    // use DOM to populate object
                    $time_pre0 = microtime(true);
                    
                    $this->repositoryCountryCode = null;
                    $this->repositoryName        = null;
                    $this->addressValues         = array();
                    $this->unitid                = null;
                    $this->repositoryCode        = null;
                    $this->partno                = null;
                    $this->size                  = null;
                    $this->type                  = null;
                    $this->available             = null;
                    $this->creatorName           = null;
                    $this->corpNameCA            = null;
                    $this->accessionnumber       = null;
                    $this->accessionname	     = null;
                    
                    $this->populateObject($domNode, $importDOM, $mapping, $currentObject, $importSchema);
                    $time_post0 = microtime(true);
                    $exec_time0 = $time_post0 - $time_pre0;
                    //print "Total time diff: ".$exec_time0."\n";
                    $rowcount++;
                    //print "Row: ".$rowcount."\n";
                } //$nodeList as $domNode
                
                // If we arrive here, it means that no exception was thrown
                // i.e. no query has failed, and we can commit the transaction
                $conn->commit();
                QubitXMLImport::addLog("commit ", "", get_class($this), false);
                //$conn->close();
                $conn = null;
            }
            catch (Exception $e) {
                // An exception has been thrown
                // We must rollback the transaction
                $conn->rollback();
                QubitXMLImport::addLog("rollback " . $e->getMessage(), "", get_class($this), true);
            }
        } //$this->schemaMap as $name => $mapping
        QubitXMLImport::addLog("Import rows: " . $rowcount, "", get_class($this), true);
        fclose($csvfile);
        return $this;
    }
    
    private function populateObject(&$domNode, &$importDOM, &$mapping, &$currentObject, $importSchema)
    {
        // if a parent path is specified, try to parent the node
        if (empty($mapping['Parent'])) {
            $parentNodes = new DOMNodeList;
        } //empty($mapping['Parent'])
        else {
            $parentNodes = $importDOM->xpath->query('(' . $mapping['Parent'] . ')', $domNode);
        }
        QubitXMLImport::addLog("function populateObject", "", get_class($this), false);
        if ($parentNodes->length > 0) {
            // parent ID comes from last node in the list because XPath forces forward document order
            $parentId = $parentNodes->item($parentNodes->length - 1)->getAttribute('xml:id');
            QubitXMLImport::addLog("parentId: " . $parentId, "", $parentId, true);
            unset($parentNodes);
            if (!empty($parentId) && is_callable(array(
                $currentObject,
                'setParentId'
            ))) {
                $currentObject->parentId = $parentId;
                QubitXMLImport::addLog("$parentId: " . $parentId, "", get_class($this), true);
            } //!empty($parentId) && is_callable(array( $currentObject, 'setParentId' ))
        } //$parentNodes->length > 0
        else {
            // orphaned object, set root if possible
            if (isset($this->parent)) {
                $currentObject->parentId = $this->parent->id;
                QubitXMLImport::addLog("isset(this->parent): " . isset($this->parent), "", get_class($this), true);
            } //isset($this->parent)
            else if (is_callable(array(
                    $currentObject,
                    'setRoot'
                ))) {
                QubitXMLImport::addLog("setRoot: ", "", get_class($this), false);
                $currentObject->setRoot();
                QubitXMLImport::addLog("is_callable(array(currentObject, 'setRoot')): " . is_callable(array(
                    $currentObject,
                    'setRoot'
                )), "", get_class($this), false);
            } //is_callable(array( $currentObject, 'setRoot' ))
            else {
                QubitXMLImport::addLog("not parent - not callable", "", get_class($this), false);
            }
        }
        // go through methods and populate properties
        $this->processMethods($domNode, $importDOM, $mapping['Methods'], $currentObject, $importSchema);
        // write contact details to repository - if repository does not exist, create otherwise do nothing
        $this->repositoryId = XmlImportArchivalDescription::setRepositoryWithCodes($this->repositoryName, $this->unitid, $this->repositoryCountryCode, $this->repositoryCode, $this->addressValues);
        $this->registryCode = $this->registers;
        $this->registryId = XmlImportArchivalDescription::setRegistryWithCodes($this->registers, $this->registryCode, $currentObject);
		
       if (isset($this->type)) {
            $entityTypeId = QubitTerm::getEntityTerm($this->type);
        } 

        $this->actor = $this->setActorByName($this->corpNameCA, array(
            'entity_type_id' => $entityTypeId,
            'event_type_id' => QubitTerm::CREATION_ID
        ));

        QubitXMLImport::addLog("Actor: " . $this->actor, " XmlImportArchivalDescription ", get_class($this), false);
        
        // make sure we have a publication status set before indexing
        if ($currentObject instanceof QubitInformationObject && count($currentObject->statuss) == 0) {
            $currentObject->setPublicationStatus(sfConfig::get('app_defaultPubStatus', QubitTerm::PUBLICATION_STATUS_DRAFT_ID));
            if (isset($this->repositoryId)) {
                $currentObject->repositoryId = $this->repositoryId;
            } 
        } 
        
        // Identifier consist of Repository, Source, Volume, File reference, Part number
        //		$this->source = $this->actor->corporateBodyIdentifiers;
        if ($this->source == "") {
            $this->source = "##";
        } //$this->source == ""
        if ($this->volumeNumber == "") {
            $this->volumeNumber = "##";
        } //$this->volumeNumber == ""
        if ($this->refno == "") {
            $this->refno = "##";
        } //$this->unitid == ""
        if ($this->partno == "") {
            $this->partno = "##";
        } 
        $this->unitid = $this->source . "_" . $this->volumeNumber . "_" . $this->refno . "_" . $this->partno;
        if ($this->partno == "##") {
            $this->partno = "";
        } 
        
        // if it exist from same Repository, create duplicate-renamed, entry else update
        if (($informationObject = QubitInformationObject::getByIdentifier($this->unitid)) !== null) {
            QubitXMLImport::addLog($currentObject->identifier, "Find record: " . $this->unitid, get_class($this), false);
            if ($informationObject->repositoryId == $this->repositoryId) {
                // if not skip duplicates
                $bulk_skip_duplicates = QubitSetting::getByName('bulk_skip_duplicates');
                if ($bulk_skip_duplicates != "1") {
                    QubitXMLImport::addLog($currentObject->identifier, "Repository Id: " . $this->repositoryId, get_class($this), false);
                    if (isset($this->repositoryId)) {
                        $currentObject->repositoryId = $this->repositoryId;
                    } 
                    if (isset($this->unitid)) {
                        //create random unique number to add to dupliacted record
                        $uuID                      = self::rand_chars(10, FALSE);
                        $currentObject->identifier = $this->unitid . "-dup-" . $uuID;
                        QubitXMLImport::addLog($currentObject->identifier, "Duplicate with same Repository Id - renamed", get_class($this), false);
                    } 
                    if (isset($this->partno)) {
                        if ($this->partno != "##") {
                            $currentObject->partNo = $this->partno;
                        } else {
                            $currentObject->partNo = "";
                        }
                    } 
                    if (isset($this->recordtype)) {
                        $typeTermId              = QubitTermI18n::getIdByName($this->recordtype, QubitTaxonomy::FORMATS);
                        $typeTermId              = $typeTermId->id;
                        $currentObject->formatId = $typeTermId;
                    } //isset($this->recordtype)
                    
                    if ($currentObject->importId == '' || $currentObject->importId == null) {
                        //create unique import_id 50 long
                        $uuIDImportId            = self::rand_chars(20, FALSE);
                        $unitImportId            = substr($this->unitid . "-" . $uuIDImportId, 0, 1024);
                        $unitImportId            = preg_replace('/[^A-Za-z0-9\. -]/', '', $unitImportId);
                        $currentObject->importId = $unitImportId;
                    } //$currentObject->importId == '' || $currentObject->importId == null
                    
                    // save the object after it's fully-populated
                    try {
                        $currentObject->save();
						$this->eventObjectId = $currentObject->id;

						QubitXMLImport::addLog("this->corpNameCA: " . $this->corpNameCA, "", get_class($this), true);
						if (strpos($this->corpNameCA, '_LD') !== false) {

							// Check relations related by subject and identifier
							$actor = QubitActor::getByAuthorizedFormOfName($this->corpNameCA);
							$actorId = $actor->id;
							QubitXMLImport::addLog("getByDescriptionIdentifier: " . $actor->id, "", get_class($this), true);
				                
							$criteria = new Criteria;
							$criteria->add(QubitEvent::TYPE_ID, QubitTerm::CREATION_ID);
							$criteria->add(QubitEvent::OBJECT_ID, $this->eventObjectId);
							$criteria->add(QubitEvent::ACTOR_ID, $actorId);
							$actorEvent = QubitEvent::getOne($criteria);
				 	  	    QubitXMLImport::addLog("actorEvent: " . $actorEvent->id, "", get_class($this), true);
				 	  	    QubitXMLImport::addLog("this->LDActor: " . $this->actor->id, "", get_class($this), true);
				 	  	    QubitXMLImport::addLog("this->old Actor: " . $actorEvent->actorId, "", get_class($this), true);


							$updateEvent = QubitEvent::getById($actorEvent->id);
							$updateEvent->actorId = $this->actor->id;
							try {
								$updateEvent->save();
							}
							catch (PDOException $e) {
								QubitXMLImport::addLog("DOMException 3: " . $e->getMessage(), "", get_class($this), false);
								echo $e->getMessage();
								print "DOMException to save XML 3: " . $e->getMessage() . "\n";
							}
						}				


                        //AtoM Import
                        
	                    $languages = explode("|", $this->AtoMLanguageOfDescription);
                        if (count($languages) != 0)
                        {
                        	QubitInformationObject::importLanguageData($this->AtoMLanguageOfDescription, 'language', $currentObject->id);
                        	QubitXMLImport::addLog("this->langPropScriptOfDescriptio: " . ($this->AtoMLanguageOfDescription), "", get_class($this), false);
                        }
						
					    $languages = explode("|", $langList);

						// add language(s) of description, if any
					    $languages = explode("|", $langList);
						if (count($languages) != 0)
						{
						   	QubitInformationObject::importLanguageData($langList, 'languageOfDescription', $currentObject->id);
							QubitXMLImport::addLog("AtoM languageOfDescription: " . ($langList), "", get_class($this), false);
						}
				
						// add script(s) of description, if any
						$languagesScripts = explode("|", $langScriptList);
						if (count($languagesScripts) != 0)
						{
						   	QubitInformationObject::importLanguageData($languagesScripts, 'scriptOfDescription', $currentObject->id);
							QubitXMLImport::addLog("AtoM languageOfDescription: " . ($languagesScripts), "", get_class($this), false);
						}

                      	// SITA Import
                      	  
	                    $languages = explode("|", $this->langPropListScriptDesc);
                        if (count($languages) != 0)
                        {
		                    QubitInformationObject::importLanguageData($this->langPropListScriptDesc, 'scriptOfDescription', $currentObject->id);
		                    QubitXMLImport::addLog("this->langPropScriptOfDescriptio: " . ($this->langPropListScriptDesc), "", get_class($this), false);
	                    }

	                    $languages = explode("|", $this->langPropListLanguageDesc);
                        if (count($languages) != 0)
                        {
		                    QubitInformationObject::importLanguageData($this->langPropListLanguageDesc, 'languageOfDescription', $currentObject->id);
		                    QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListLanguageDesc), "", get_class($this), false);
	                    }

	                    $languages = explode("|", $this->langPropListMaterial);
                        if (count($languages) != 0)
                        {
		                    QubitInformationObject::importLanguageData($this->langPropListMaterial, 'language', $currentObject->id);
		                    QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListMaterial), "", get_class($this), false);
	                    }

	                    $languages = explode("|", $this->langPropListScript);
                        if (count($languages) != 0)
                        {
		                    QubitInformationObject::importLanguageData($this->langPropListScript, 'script', $currentObject->id);
		                    QubitXMLImport::addLog("this->langPropListScript: " . ($this->langPropListScript), "", get_class($this), false);
	                    }

						$this->langScriptMaterialCount = 0;
						$this->langMaterialCount = 0;
						$this->langMaterialScriptCount = 0;

						$this->langScriptCount = 0;
						$this->langDescCount = 0;
                    }
                    catch (PDOException $e) {
                        QubitXMLImport::addLog("DOMException: " . $e->getMessage(), "", get_class($this), true);
                        echo $e->getMessage();
                        print "DOMException to save XML: " . $e->getMessage() . "\n";
                    }

		            //add accession record if in import file
		            // if Archival Description exist add/update relation  
	 				QubitXMLImport::addLog("this->accessionnumber check 3: " . $this->accessionnumber, "", get_class($this), true);
		            if (isset($this->accessionnumber)) {
		                //find in accession table 
		                if (($accession = QubitAccession::getByAccessionNumber($this->accessionnumber)) !== null) {
		                    // if relation exist delete it first
		                    if (($relation = QubitRelation::getBySubjectAndObjectAndType($accession->id, $currentObject->id, QubitTerm::ACCESSION_ID)) !== null) {
	 				QubitXMLImport::addLog("this->accessionnumber delete relation 3: ", "", get_class($this), false);

		                        $relation->delete();
		                    } 
		                } 
		                else {
		                    try {
	 							QubitXMLImport::addLog("this->accessionnumber not set 3: " . $this->accessionnumber, "", get_class($this), false);
		                        $accession          = new QubitAccession;
								$accession->identifier = $this->accessionnumber;
								$accession->culture = "en";
								$accession->title = $this->accessionname;

		                       // $accessionlentifier = $this->accessionnumber;
		                        $accession->save();
		                    }
		                    catch (PDOException $e) {
		                        QubitXMLImport::addLog("DOMException accession: " . $e->getMessage(), "", get_class($this), true);
		                        echo $e->getMessage();
		                        print "DOMException to save XML accession: " . $e->getMessage() . "\n";
		                    }
		                }
		                // create relation between accession and Archival Description
		                QubitXmlImport::createRelation($currentObject->id, $accession->id, QubitTerm::ACCESSION_ID);
		            } //isset($this->accessionnumber)

                
				} //$bulk_skip_duplicates != "1"
                else {
                    QubitXMLImport::addLog($currentObject->identifier, "Batch - Skipping duplicate", get_class($this), false);
                }
                // write the ID onto the current XML node for tracking
                $domNode->setAttribute('xml:id', $currentObject->id);
            } //$informationObject->repositoryId == $this->repositoryId
            else {
                QubitXMLImport::addLog($this->unitid, "Duplicate exist with different Repository Id", get_class($this), false);
                // duplicate exist with different Repository Id
                if (isset($this->repositoryId)) {
                    $currentObject->repositoryId = $this->repositoryId;
                } //isset($this->repositoryId)
                if (isset($this->unitid)) {
                    $currentObject->identifier = $this->unitid;
                } //isset($this->unitid)
                if (isset($this->partno)) {
                    if ($this->partno != "##") {
                        $currentObject->partNo = $this->partno;
                    } else {
                        $currentObject->partNo = "";
                    }
                } //isset($this->partno)
                if (isset($this->size)) {
                    $criteria = new Criteria;
                    $criteria->add(QubitTermI18n::NAME, $this->size);
                    $currentObject->size = QubitTerm::getOne($criteria);
                } //isset($this->size)
                if (isset($this->type)) {
                    $criteria = new Criteria;
                    $criteria->add(QubitTermI18n::NAME, $this->type);
                    $currentObject->typeId = QubitTerm::getOne($criteria);
                } //isset($this->type)
                
                if (isset($this->available)) {
                    $criteria = new Criteria;
                    $criteria->add(QubitTermI18n::NAME, $this->available);
                    $currentObject->available = QubitTerm::getOne($criteria);
                } //isset($this->available)
                
                if (isset($this->recordtype)) {
                    $typeTermId              = QubitTermI18n::getIdByName($this->recordtype, QubitTaxonomy::FORMATS);
                    $typeTermId              = $typeTermId->id;
                    $currentObject->formatId = $typeTermId;
                } //isset($this->recordtype)
                
                if ($currentObject->importId == '' || $currentObject->importId == null) {
                    //create unique import_id 50 long
                    $uuIDImportId            = self::rand_chars(20, FALSE);
                    $unitImportId            = substr($this->unitid . "-" . $uuIDImportId, 0, 1024);
                    $unitImportId            = preg_replace('/[^A-Za-z0-9\. -]/', '', $unitImportId);
                    $currentObject->importId = $unitImportId;
                } //$currentObject->importId == '' || $currentObject->importId == null
                // save the object after it's fully-populated
                try {
                    $currentObject->save();
					$this->eventObjectId = $currentObject->id;

					QubitXMLImport::addLog("this->corpNameCA: " . $this->corpNameCA, "", get_class($this), false);
					if (strpos($this->corpNameCA, '_LD') !== false) {

						// Check relations related by subject and identifier
						$actor = QubitActor::getByAuthorizedFormOfName($this->corpNameCA);
						$actorId = $actor->id;
						QubitXMLImport::addLog("getByDescriptionIdentifier: " . $actor->id, "", get_class($this), false);
		                    
						$criteria = new Criteria;
						$criteria->add(QubitEvent::TYPE_ID, QubitTerm::CREATION_ID);
						$criteria->add(QubitEvent::OBJECT_ID, $this->eventObjectId);
						$criteria->add(QubitEvent::ACTOR_ID, $actorId);
						$actorEvent = QubitEvent::getOne($criteria);
			 	  	    QubitXMLImport::addLog("actorEvent: " . $actorEvent->id, "", get_class($this), false);
			 	  	    QubitXMLImport::addLog("this->LDActor: " . $this->actor->id, "", get_class($this), false);
			 	  	    QubitXMLImport::addLog("this->old Actor: " . $actorEvent->actorId, "", get_class($this), false);


						$updateEvent = QubitEvent::getById($actorEvent->id);
						$updateEvent->actorId = $this->actor->id;
						try {
							$updateEvent->save();
						}
						catch (PDOException $e) {
						    QubitXMLImport::addLog("DOMException 3: " . $e->getMessage(), "", get_class($this), false);
						    echo $e->getMessage();
						    print "DOMException to save XML 3: " . $e->getMessage() . "\n";
						}
					}				


                    QubitInformationObject::importLanguageData($this->langPropListScriptDesc, 'scriptOfDescription', $currentObject->id);
                    QubitXMLImport::addLog("this->langPropScriptOfDescriptio: " . ($this->langPropListScriptDesc), "", get_class($this), false);

                    QubitInformationObject::importLanguageData($this->langPropListLanguageDesc, 'languageOfDescription', $currentObject->id);
                    QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListLanguageDesc), "", get_class($this), false);

                    QubitInformationObject::importLanguageData($this->langPropListMaterial, 'language', $currentObject->id);
                    QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListMaterial), "", get_class($this), false);

                    QubitInformationObject::importLanguageData($this->langPropListScript, 'script', $currentObject->id);
                    QubitXMLImport::addLog("this->langPropListScript: " . ($this->langPropListScript), "", get_class($this), false);

					$this->langScriptMaterialCount = 0;
					$this->langMaterialCount = 0;
					$this->langMaterialScriptCount = 0;

					$this->langScriptCount = 0;
					$this->langDescCount = 0;
                }
                catch (PDOException $e) {
                    QubitXMLImport::addLog("DOMException2: " . $e->getMessage(), "", get_class($this), true);
                    echo $e->getMessage();
                    print "DOMException to save XML2: " . $e->getMessage() . "\n";
                }
                
                //add accession record if in import file
                // if Archival Description exist add/update relation  
 				QubitXMLImport::addLog("this->accessionnumber check 1: " . $this->accessionnumber, "", get_class($this), true);
                if (isset($this->accessionnumber)) {
                    //find in accession table 
                    if (($accession = QubitAccession::getByAccessionNumber($this->accessionnumber)) !== null) {
                        // if relation exist delete it first
                        if (($relation = QubitRelation::getBySubjectAndObjectAndType($accession->id, $currentObject->id, QubitTerm::ACCESSION_ID)) !== null) {
 							QubitXMLImport::addLog("this->accessionnumber delete relation 1: ", "", get_class($this), false);

                            $relation->delete();
                        } 
                    } 
                    else {
                        try {
 							QubitXMLImport::addLog("this->accessionnumber not set 1: " . $this->accessionnumber, "", get_class($this), false);

	                        $accession          = new QubitAccession;
							$accession->identifier = $this->accessionnumber;
							$accession->culture = "en";
							$accession->title = $this->accessionname;

	                       // $accessionlentifier = $this->accessionnumber;
	                        $accession->save();

		                    QubitInformationObject::importLanguageData($this->langPropListScriptDesc, 'scriptOfDescription', $currentObject->id);
		                    QubitXMLImport::addLog("this->langPropScriptOfDescriptio: " . ($this->langPropListScriptDesc), "", get_class($this), false);

		                    QubitInformationObject::importLanguageData($this->langPropListLanguageDesc, 'languageOfDescription', $currentObject->id);
		                    QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListLanguageDesc), "", get_class($this), false);

		                    QubitInformationObject::importLanguageData($this->langPropListMaterial, 'language', $currentObject->id);
		                    QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListMaterial), "", get_class($this), false);

		                    QubitInformationObject::importLanguageData($this->langPropListScript, 'script', $currentObject->id);
		                    QubitXMLImport::addLog("this->langPropListScript: " . ($this->langPropListScript), "", get_class($this), false);

							$this->langScriptMaterialCount = 0;
							$this->langMaterialCount = 0;
							$this->langMaterialScriptCount = 0;

							$this->langScriptCount = 0;
							$this->langDescCount = 0;
                        }
                        catch (PDOException $e) {
                            QubitXMLImport::addLog("DOMException accession: " . $e->getMessage(), "", get_class($this), true);
                            echo $e->getMessage();
                            print "DOMException to save XML accession: " . $e->getMessage() . "\n";
                        }
                    }
                    // create relation between accession and Archival Description
                    QubitXmlImport::createRelation($currentObject->id, $accession->id, QubitTerm::ACCESSION_ID);
                } //isset($this->accessionnumber)
                
                // write the ID onto the current XML node for tracking
                $domNode->setAttribute('xml:id', $currentObject->id);
            }
        } //($informationObject = QubitInformationObject::getByIdentifier($this->unitid)) !== null
        else {
            QubitXMLImport::addLog($this->unitid, "New entry: ", get_class($this), true);
            //Does not exist
            if (isset($this->repositoryId)) {
                $currentObject->repositoryId = $this->repositoryId;
            } //isset($this->repositoryId)
            QubitXMLImport::addLog("", "isset(this->unitid): " . isset($this->unitid), get_class($this), false);
            if (isset($this->unitid)) {
                $currentObject->identifier = $this->unitid;
            } //isset($this->unitid)
            if (isset($this->partno)) {
                if ($this->partno != "##") {
                    $currentObject->partNo = $this->partno;
                } else {
                    $currentObject->partNo = "";
                }
            } //isset($this->partno)
            
            if (isset($this->size)) {
                $typeTermId          = QubitTermI18n::getIdByName($this->size, QubitTaxonomy::SIZE_ID);
                $currentObject->size = $typeTermId;
            } //isset($this->size)
            if (isset($this->type)) {
                $typeTermId           = QubitTermI18n::getIdByName($this->type, QubitTaxonomy::TYP_ID);
                $currentObject->typId = $typeTermId->id;
            } //isset($this->type)
            if (isset($this->available)) {
                $typeTermId                 = QubitTermI18n::getIdByName($this->available, QubitTaxonomy::EQUIPMENT_ID);
                $currentObject->equipmentId = $typeTermId->id;
            } //isset($this->available)
            
            if (isset($this->recordtype)) {
                $typeTermId              = QubitTermI18n::getIdByName($this->recordtype, QubitTaxonomy::FORMATS);
                $typeTermId              = $typeTermId->id;
                $currentObject->formatId = $typeTermId;
            } //isset($this->recordtype)
            
            if ($currentObject->importId == '' || $currentObject->importId == null) {
                //create unique import_id 50 long
                $uuIDImportId            = self::rand_chars(20, FALSE);
                $unitImportId            = substr($this->unitid . "-" . $uuIDImportId, 0, 1024);
                $unitImportId            = preg_replace('/[^A-Za-z0-9\. -]/', '', $unitImportId);
                $currentObject->importId = $unitImportId;
            } //$currentObject->importId == '' || $currentObject->importId == null
            
            QubitXMLImport::addLog($this->unitid, " - New entry - " . $currentObject->identifier, get_class($this), false);
            // save the object after it's fully-populated
            try {
                $currentObject->save();
				$this->eventObjectId = $currentObject->id;

				QubitXMLImport::addLog("this->corpNameCA: " . $this->corpNameCA, "", get_class($this), false);
				if (strpos($this->corpNameCA, '_LD') !== false) {

					// Check relations related by subject and identifier
					$actor = QubitActor::getByAuthorizedFormOfName($this->corpNameCA);
					$actorId = $actor->id;
					QubitXMLImport::addLog("getByDescriptionIdentifier: " . $actor->id, "", get_class($this), false);
                        
					$criteria = new Criteria;
				    $criteria->add(QubitEvent::TYPE_ID, QubitTerm::CREATION_ID);
				    $criteria->add(QubitEvent::OBJECT_ID, $this->eventObjectId);
				    $criteria->add(QubitEvent::ACTOR_ID, $actorId);
					$actorEvent = QubitEvent::getOne($criteria);
		 	  	    QubitXMLImport::addLog("actorEvent: " . $actorEvent->id, "", get_class($this), false);
		 	  	    QubitXMLImport::addLog("this->LDActor: " . $this->actor->id, "", get_class($this), false);
		 	  	    QubitXMLImport::addLog("this->old Actor: " . $actorEvent->actorId, "", get_class($this), false);


					$updateEvent = QubitEvent::getById($actorEvent->id);
					$updateEvent->actorId = $this->actor->id;
				    try {
						$updateEvent->save();
					}
				    catch (PDOException $e) {
				        QubitXMLImport::addLog("DOMException 3: " . $e->getMessage(), "", get_class($this), false);
				        echo $e->getMessage();
				        print "DOMException to save XML 3: " . $e->getMessage() . "\n";
				    }
				}				

                QubitInformationObject::importLanguageData($this->langPropListScriptDesc, 'scriptOfDescription', $currentObject->id);
                QubitXMLImport::addLog("this->langPropScriptOfDescriptio: " . ($this->langPropListScriptDesc), "", get_class($this), false);

                QubitInformationObject::importLanguageData($this->langPropListLanguageDesc, 'languageOfDescription', $currentObject->id);
                QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListLanguageDesc), "", get_class($this), false);

                QubitInformationObject::importLanguageData($this->langPropListMaterial, 'language', $currentObject->id);
                QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListMaterial), "", get_class($this), false);

                QubitInformationObject::importLanguageData($this->langPropListScript, 'script', $currentObject->id);
                QubitXMLImport::addLog("this->langPropListScript: " . ($this->langPropListScript), "", get_class($this), false);

				$this->langScriptMaterialCount = 0;
				$this->langMaterialCount = 0;
				$this->langMaterialScriptCount = 0;

				$this->langScriptCount = 0;
				$this->langDescCount = 0;
            }
            catch (PDOException $e) {
                QubitXMLImport::addLog("DOMException 3: " . $e->getMessage(), "", get_class($this), true);
                echo $e->getMessage();
                print "DOMException to save XML 3: " . $e->getMessage() . "\n";
            }
            
            //add accession record if in import file
            // if Archival Description exist add/update relation 
			QubitXMLImport::addLog("this->accessionnumber check 2: " . $this->accessionnumber, "", get_class($this), true);
            if (isset($this->accessionnumber)) {
                //find in accession table 
                if (($accession = QubitAccession::getByAccessionNumber($this->accessionnumber)) !== null) {
                    // if relation exist delete it first
                    if (($relation = QubitRelation::getBySubjectAndObjectAndType($accession->id, $currentObject->id, QubitTerm::ACCESSION_ID)) !== null) {
 						QubitXMLImport::addLog("this->accessionnumber delete relation 2: ", "", get_class($this), true);

                        $relation->delete();
                    } //($relation = QubitRelation::getBySubjectAndObjectAndType($accession->id, $currentObject->id, QubitTerm::ACCESSION_ID)) !== null
                } //($accession = QubitAccession::getByAccessionNumber($this->accessionnumber)) !== null
                else {
                    try {
						QubitXMLImport::addLog("this->accessionnumber not set 2: " . $this->accessionnumber, "", get_class($this), true);

                        $accession          = new QubitAccession;
						$accession->identifier = $this->accessionnumber;
						$accession->culture = "en";
						$accession->title = $this->accessionname;

                        $accession->save();

                        QubitInformationObject::importLanguageData($this->langPropScriptOfDescription, 'scriptOfDescription', $currentObject->id);
                        QubitInformationObject::importLanguageData($this->langPropListScriptDesc, 'scriptOfDescription', $currentObject->id);
                        QubitXMLImport::addLog("this->langPropScriptOfDescriptio: " . ($this->langPropListScriptDesc), "", get_class($this), false);

                        QubitInformationObject::importLanguageData($this->langPropListLanguageDesc, 'languageOfDescription', $currentObject->id);
                        QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListLanguageDesc), "", get_class($this), false);

                        QubitInformationObject::importLanguageData($this->langPropListMaterial, 'language', $currentObject->id);
                        QubitXMLImport::addLog("this->langPropListLanguageDesc: " . ($this->langPropListMaterial), "", get_class($this), false);

                        QubitInformationObject::importLanguageData($this->langPropListScript, 'script', $currentObject->id);
                        QubitXMLImport::addLog("this->langPropListScript: " . ($this->langPropListScript), "", get_class($this), false);

						$this->langScriptMaterialCount = 0;
						$this->langMaterialCount = 0;
						$this->langMaterialScriptCount = 0;

						$this->langScriptCount = 0;
						$this->langDescCount = 0;
                    }
                    catch (PDOException $e) {
                        QubitXMLImport::addLog("DOMException accession 2: " . $e->getMessage(), "", get_class($this), true);
                        echo $e->getMessage();
                        print "DOMException to save XML accession 2: " . $e->getMessage() . "\n";
                    }
                }
                // create relation between accession and Archival Description
                QubitXmlImport::createRelation($currentObject->id, $accession->id, QubitTerm::ACCESSION_ID);
            } //isset($this->accessionnumber)
            
            // write the ID onto the current XML node for tracking
            $domNode->setAttribute('xml:id', $currentObject->id);
        }
        $options   = array();
        $nodeIndex = new arElasticSearchInformationObjectPdo($currentObject->id, $options);
        $data      = $nodeIndex->serialize();
        
        QubitSearch::getInstance()->addDocument($data, 'QubitInformationObject');
        QubitSearch::getInstance()->optimize();
    }
    
    /*
     * Cycle through methods and populate object based on relevant data
     *
     * @return  null
     */
    private function processMethods(&$domNode, &$importDOM, $methods, &$currentObject, $importSchema)
    {
        QubitXMLImport::addLog("function processMethods", "", get_class($this), false);
        $this->registers = "";
        // go through methods and populate properties
        foreach ($methods as $name => $methodMap) {
            // if method is not defined, we can't process this mapping
            if (empty($methodMap['Method']) || !is_callable(array(
                $currentObject,
                $methodMap['Method']
            ))) {
                $this->errors[] = sfContext::getInstance()->i18n->__('Non-existent method defined in import mapping: "%method%"', array(
                    '%method%' => $methodMap['Method']
                ));
                continue;
            } //empty($methodMap['Method']) || !is_callable(array( $currentObject, $methodMap['Method'] ))
            // Get a list of XML nodes to process
            // This condition mitigates a problem where the XPath query wasn't working
            // as expected, see #4302 for more details
            if ($importSchema == 'dc' && $methodMap['XPath'] != '.') {
                $nodeList2 = $importDOM->getElementsByTagName($methodMap['XPath']);
            } //$importSchema == 'dc' && $methodMap['XPath'] != '.'
            else {
                $nodeList2 = $importDOM->xpath->query($methodMap['XPath'], $domNode);
            }
            if (is_object($nodeList2)) {
                QubitXMLImport::addLog("methods: " . $name, "", get_class($this), false);
                switch ($name) {
                    case 'controlaccess_name':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/controlaccess/name', $item)) !== null) {
                               // $this->creatorName = QubitXmlImport::normalizeNodeValue($item);
                            }
                        } 
                        break;
                    
                    case 'controlaccess_corpname':
                        $this->corpNameCA = "";
                        QubitXMLImport::addLog("methods controlaccess_corpname: >><<", "", get_class($this), false);
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/controlaccess/corpname', $item)) !== null) {
                                $this->corpNameCA = QubitXmlImport::normalizeNodeValue($item);
                                QubitXMLImport::addLog("methods controlaccess_corpname2: >>" . $this->corpNameCA . "<<", "", get_class($this), false);
                            } 
                        } 
                        break;
                    
                    case 'repository':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/repository/repocorpname', $item)) !== null) {
                                $this->repositoryName = QubitXmlImport::normalizeNodeValue($item);
                                QubitXMLImport::addLog("methods repository: " . $this->repositoryName, "", get_class($this), false);
                            } 
                        } 
                        break;
                    
                    case 'partNo':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/partno', $item)) !== null) {
                                $this->partno = QubitXmlImport::normalizeNodeValue($item);
                            } //($childNode = $importDOM->xpath->query('//did/partno', $item)) !== null
                        } //$nodeList2 as $item
                        break;
                    
                    case 'refNo':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/refno', $item)) !== null) {
                                $this->refno = QubitXmlImport::normalizeNodeValue($item);
                            } //($childNode = $importDOM->xpath->query('//did/partno', $item)) !== null
                        } //$nodeList2 as $item
                        break;
                    
                    case 'volNo':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/volno', $item)) !== null) {
                                $this->volumeNumber = QubitXmlImport::normalizeNodeValue($item);
                                QubitXMLImport::addLog("methods volNo: " . $this->volumeNumber, "", get_class($this), false);
                            } //($childNode = $importDOM->xpath->query('//did/volno', $item)) !== null
                        } //$nodeList2 as $item
                        break;
                    
                    case 'registers':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/registers', $item)) !== null) {
                                $this->registers = QubitXmlImport::normalizeNodeValue($item);
                                QubitXMLImport::addLog("methods registers: " . $this->registers, "", get_class($this), false);
                            } //($childNode = $importDOM->xpath->query('//did/registers', $item)) !== null
                        } //$nodeList2 as $item
                        break;
                    
                    case 'recordType':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/recordtype', $item)) !== null) {
                                $this->recordtype = QubitXmlImport::normalizeNodeValue($item);
                            } //($childNode = $importDOM->xpath->query('//did/recordtype', $item)) !== null
                        } //$nodeList2 as $item
                        break;
		             case 'accessionNumber':
		               if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
		                    foreach ($nodeList2 as $item) {
		                        if (($childNode = $importDOM->xpath->query('//did/accessionnumber', $item)) !== null) {
		                            $this->accessionnumber = QubitXmlImport::normalizeNodeValue($item);
									QubitXMLImport::addLog("this->accessionnumber: " . $this->accessionnumber , "", get_class($this), false);
		                        }
		                    }
		                }
		                break;
		             case 'accessionName':
		               if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
		                    foreach ($nodeList2 as $item) {
		                        if (($childNode = $importDOM->xpath->query('//did/accessionname', $item)) !== null) {
		                            $this->accessionname = QubitXmlImport::normalizeNodeValue($item);
									QubitXMLImport::addLog("this->accessionname: " . $this->accessionname , "", get_class($this), true);
		                        }
		                    }
		                }
		                break;
                    case 'mediaSize':
                        if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                            foreach ($nodeList2 as $item) {
                                if (($childNode = $importDOM->xpath->query('//did/mediasize', $item)) !== null) {
                                    $this->size = QubitXmlImport::normalizeNodeValue($item);
                                } //($childNode = $importDOM->xpath->query('//did/mediasize', $item)) !== null
                            } //$nodeList2 as $item
                        } //($childNode = $importDOM->xpath->query('.', $item)) !== null
                        
                        break;
                    
                    case 'type':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/type', $item)) !== null) {
                                $this->type = QubitXmlImport::normalizeNodeValue($item);
                            } //($childNode = $importDOM->xpath->query('//did/type', $item)) !== null
                        } //$nodeList2 as $item
                        break;
                    
                    case 'availabilityId':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('//did/availabilityId', $item)) !== null) {
                                $this->available = QubitXmlImport::normalizeNodeValue($item);
                            } //($childNode = $importDOM->xpath->query('//did/availabilityId', $item)) !== null
                        } //$nodeList2 as $item
                        break;
                    
                    case 'identifier':
                        if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                            foreach ($nodeList2 as $item) {
                                if (($childNode = $importDOM->xpath->query('//did/unitid', $item)) !== null) {
                                    $this->unitid = QubitXmlImport::normalizeNodeValue($item);
                                    
                                    QubitXMLImport::addLog($this->unitid, "unitid/identifier", get_class($this), false);
                                } //($childNode = $importDOM->xpath->query('//did/unitid', $item)) !== null
                                if (($childNode = $importDOM->xpath->query('//did/unitid/@countrycode', $item)) !== null) {
                                    if (isset($childNode->item(0)->nodeValue)) {
                                        $this->repositoryCountryCode = $childNode->item(0)->nodeValue;
                                    } //isset($childNode->item(0)->nodeValue)
                                    
                                } //($childNode = $importDOM->xpath->query('//did/unitid/@countrycode', $item)) !== null
                                if (($childNode = $importDOM->xpath->query('//did/unitid/@repositorycode', $item)) !== null) {
                                    $this->repositoryCode = $importDOM->xpath->query('@repositorycode', $item)->item(0)->nodeValue;
                                    QubitXMLImport::addLog($this->repositoryCode, "Repository Code", get_class($this), false);
                                } //($childNode = $importDOM->xpath->query('//did/unitid/@repositorycode', $item)) !== null
                            } //$nodeList2 as $item
                        } //($childNode = $importDOM->xpath->query('.', $item)) !== null
                        break;
                    
                    case 'repositoryAddress':
                        
                        foreach ($nodeList2 as $item) {
                            $primaryContact      = "";
                            $title               = "";
                            $contactPerson       = "";
                            $position            = "";
                            $email               = "";
                            $fax                 = "";
                            $telephone           = "";
                            $cell                = "";
                            $website             = "";
                            $streetAddress       = "";
                            $city                = "";
                            $region              = "";
                            $countryCode         = "";
                            $postalCode          = "";
                            $postalAddress       = "";
                            $postalCity          = "";
                            $postalRegion        = "";
                            $postalCountryCode   = "";
                            $postalPostCode      = "";
                            $latitude            = "";
                            $longitude           = "";
                            $note                = "";
                            $contactType         = "";
                            $this->addressValues = null;
                            
                            if (($childNode = $importDOM->xpath->query('./primarycontact', $item)) !== null) {
                                $primaryContact = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./primarycontact', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./title', $item)) !== null) {
                                $title = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./title', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./contactperson', $item)) !== null) {
                                $contactPerson = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./contactperson', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./position', $item)) !== null) {
                                $position = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./position', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./email', $item)) !== null) {
                                $email = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./email', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./fax', $item)) !== null) {
                                $fax = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./fax', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./telephone', $item)) !== null) {
                                $telephone = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./telephone', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./cell', $item)) !== null) {
                                $cell = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./cell', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./website', $item)) !== null) {
                                $website = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./website', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./streetaddress', $item)) !== null) {
                                $streetAddress = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./streetaddress', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./city', $item)) !== null) {
                                $city = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./city', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./region', $item)) !== null) {
                                $region = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./region', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./countrycode', $item)) !== null) {
                                $countryCode = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./countrycode', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./postalcode', $item)) !== null) {
                                $postalCode = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./postalcode', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./postaladdress', $item)) !== null) {
                                $postalAddress = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./postaladdress', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./postalcity', $item)) !== null) {
                                $postalCity = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./postalcity', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./postalregion', $item)) !== null) {
                                $postalRegion = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./postalregion', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./postalcountrycode', $item)) !== null) {
                                $postalCountryCode = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./postalcountrycode', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./postalpostcode', $item)) !== null) {
                                $postalPostCode = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./postalpostcode', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./latitude', $item)) !== null) {
                                $latitude = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./latitude', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./longitude', $item)) !== null) {
                                $longitude = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./longitude', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./note', $item)) !== null) {
                                $note = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./note', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('./contacttype', $item)) !== null) {
                                $contactType = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('./contacttype', $item)) !== null
                            $this->addressValues = array(
                                'primaryContact' => $primaryContact,
                                'title' => $title,
                                'contactPerson' => $contactPerson,
                                'position' => $position,
                                'email' => $email,
                                'fax' => $fax,
                                'telephone' => $telephone,
                                'cell' => $cell,
                                'website' => $website,
                                'streetAddress' => $streetAddress,
                                'city' => $city,
                                'region' => $region,
                                'countryCode' => $countryCode,
                                'postalCode' => $postalCode,
                                'postalAddress' => $postalAddress,
                                'postalCity' => $postalCity,
                                'postalRegion' => $postalRegion,
                                'postalCountryCode' => $postalCountryCode,
                                'postalPostCode' => $postalPostCode,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'note' => $note,
                                'contactType' => $contactType
                            );
                        } //$nodeList2 as $item
                        break;
                    // hack: some multi-value elements (e.g. 'languages') need to get passed as one array instead of individual nodes values
                    
                    case 'languages':
	                    QubitXMLImport::addLog("languages - languageOfDescription>>>>START/end<<<<", "", get_class($this), false);
                    
                    case 'langusagedesclanguage': // SITA Import languageOfDescription
						if ($this->langDescCount == 0)
						{
			                QubitXMLImport::addLog("langusagedesclanguage - languageOfDescription>>>>START<<<<", "", get_class($this), false);
			            	if (($childNode = $importDOM->xpath->query('//did/langusage/language', $item)) !== null) {
				                $this->langPropListLanguageDesc = "";
					            foreach ($childNode as $item) 
					            {
							            $this->langPropListLanguageDesc = QubitInformationObject::importDescLanguse($item, $this->langPropListLanguageDesc);
				                } 
					            $this->langDescCount = 1;
			                }
				            QubitXMLImport::addLog("langusagedesclanguage - languageOfDescription. >>>>" . $this->langPropListLanguageDesc . " END<<<<", "", get_class($this), false);
		                }
                    case 'langscriptmaterial': // SITA import scriptOfMaterial
						if ($this->langMaterialScriptCount == 0)
						{
			                QubitXMLImport::addLog("langscriptmaterial - scriptOfMaterial>>>>START<<<<", "", get_class($this), false);
			            	if (($childNode = $importDOM->xpath->query('//did/langscriptmaterial/language', $item)) !== null) {
				                $this->langPropListScript = "";
					            foreach ($childNode as $item) 
					            {
							            $this->langPropListScript = QubitInformationObject::importScritpLanguse($item, $this->langPropListScript);
				                } 
				            	$this->langMaterialScriptCount = 1;
			                }
				            QubitXMLImport::addLog("langscriptmaterial - scriptOfMaterial. >>>>" . $this->langPropListScript . " END<<<<", "", get_class($this), false);
		            	}
                    case 'langmaterialdesc': // SITA Import languageOfMaterial - language
						if ($this->langMaterialCount == 0)
						{
				            QubitXMLImport::addLog("langmaterial - languageOfMaterial. >>>>START<<<<", "", get_class($this), false);
			            	if (($childNode = $importDOM->xpath->query('//did/langmaterial/language', $item)) !== null) {
				                $this->langPropListMaterial = "";
					            foreach ($childNode as $item) 
					            {
							            $this->langPropListMaterial = QubitInformationObject::importDescLanguse($item, $this->langPropListMaterial);
				                } 
					            $this->langMaterialCount = 1;
			                }
				            QubitXMLImport::addLog("langmaterial - languageOfMaterial. >>>>" . $this->langPropListMaterial . " END<<<<", "", get_class($this), false);
			            }
                    case 'langscriptcode': //  SITA Import language - langscriptcode 
						if ($this->langScriptMaterialCount == 0)
						{
				            QubitXMLImport::addLog("langscriptcode - language. >>>>START<<<<", "", get_class($this), false);
			            	if (($childNode = $importDOM->xpath->query('//did/langscriptcode/language', $item)) !== null) {
					            $this->langPropListScriptDesc = "";
					            foreach ($childNode as $item) 
					            {
						            $this->langPropListScriptDesc = QubitInformationObject::importScritpLanguse($item, $this->langPropListScriptDesc);
				                } 
					            $this->langScriptMaterialCount = 1;
			                }
				            QubitXMLImport::addLog("langscriptcode - language. >>>>" . $this->langPropListScriptDesc . " END<<<<", "", get_class($this), false);
			            }
					case 'langmaterial':
					
                    case 'langusage':
	                    QubitXMLImport::addLog("langusage - languageOfDescription>>>>START<<<<", "", get_class($this), false);
				        $langCodeConvertor = new fbISO639_Map;
				        $isID3 = ($importSchhema == 'dc') ? true : false;
				        $value = array();
				        foreach ($nodeList2 as $item) {
	                    QubitXMLImport::addLog("langusage - languageOfDescription>item->nodeValue>>>".$item->nodeValue." END<<<<", "", get_class($this), false);
							if ($item->nodeValue != "NoCode") // SITA JJP
							{
	                    		QubitXMLImport::addLog("langusage - languageOfDescription>>>>".$item->nodeValue." not NoCode<<<<", "", get_class($this), false);
						        if ($twoCharCode = $langCodeConvertor->getID1($item->nodeValue, $isID3)) 
					        	{
						            $value[] = strtolower($twoCharCode);
						        } else {
						            $value[] = $item->nodeValue;
						        }
						    }
						    else
						    {
								$langCodeConvertor = new fbISO639_Map;
								$c_en              = sfCultureInfo::getInstance();
								$language_en       = $c_en->getLanguages();
								foreach ($language_en as $key => $valueLanguage) {
								    if ($item->nodeValue == $valueLanguage) {
								        if ($langCode = $langCodeConvertor->getID1($key, false)) {
                                			$langList = $langList . "|". $langCode;
								           // $value[] = strtolower($langCode);
								        } else {
                                			$langList = $langList . "|". $key;
								            //$value[] = $key;
								        }
								    }
								}
						    }
				        }
				        $this->AtoMLanguageOfDescription = $langList;
				       // $currentObject->language = $value;
	                    QubitXMLImport::addLog("langusage - languageOfDescription>>>>END<<<<", "", get_class($this), false);
				        break;

                    case 'processinfo':
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('p/date', $item)) !== null) {
                                $currentObject->revisionHistory = $childNode->item(0)->nodeValue;
                            } //($childNode = $importDOM->xpath->query('p/date', $item)) !== null
                            if (($childNode = $importDOM->xpath->query('p', $item)) !== null) {
                                $note = '';
                                foreach ($childNode as $pNode) {
                                    // A <p> node inside <processinfo> with no other children,
                                    // this is part of an archivist's note.
                                    if ($pNode->childNodes->length === 1 && $pNode->firstChild->nodeType === XML_TEXT_NODE) {
                                        // If this isn't our first <p> in the note, add newlines
                                        // to simulate paragraph.
                                        if (strlen($note) > 0) {
                                            $note .= "\n\n";
                                        } //strlen($note) > 0
                                        $note .= $pNode->nodeValue;
                                    } //$pNode->childNodes->length === 1 && $pNode->firstChild->nodeType === XML_TEXT_NODE
                                } //$childNode as $pNode
                                if (strlen($note) > 0) {
                                    $currentObject->importEadNote(array(
                                        'note' => $note,
                                        'noteTypeId' => QubitTerm::ARCHIVIST_NOTE_ID
                                    ));
                                } //strlen($note) > 0
                            } //($childNode = $importDOM->xpath->query('p', $item)) !== null
                            // TODO: Add more child node processing, for <note> <head> etc.
                            
                        } //$nodeList2 as $item
                        break;
                    
                    case 'flocat':
                    case 'digital_object':
                        $resources = array();
                        foreach ($nodeList2 as $item) {
                            $resources[] = $item->nodeValue;
                        } //$nodeList2 as $item
                        if (0 < count($resources)) {
                            $currentObject->importDigitalObjectFromUri($resources, $this->errors);
                        } //0 < count($resources)
                        break;
                    
                    case 'container':
                        foreach ($nodeList2 as $item) {
                            $container = $item->nodeValue;
                            $type      = $importDOM->xpath->query('@type', $item)->item(0)->nodeValue;
                            $label     = $importDOM->xpath->query('@label', $item)->item(0)->nodeValue;
                            $parent    = $importDOM->xpath->query('@parent', $item)->item(0)->nodeValue;
                            $location  = $importDOM->xpath->query('did/physloc[@id="' . $parent . '"]', $domNode)->item(0)->nodeValue;
                            $currentObject->importPhysicalObject($location, $container, $type, $label);
                        } //$nodeList2 as $item
                        break;
                    
                    case 'relatedunitsofdescription':
                        $i         = 0;
                        $nodeValue = '';
                        foreach ($nodeList2 as $item) {
                            if ($i++ == 0) {
                                $nodeValue .= QubitXmlImport::normalizeNodeValue($item);
                            } //$i++ == 0
                            else {
                                $nodeValue .= "\n\n" . QubitXmlImport::normalizeNodeValue($item);
                            }
                        } //$nodeList2 as $item
                        $currentObject->setRelatedUnitsOfDescription($nodeValue);
                        break;
                    
                    default:
                        foreach ($nodeList2 as $key => $domNode2) {
                            // normalize the node text; NB: this will strip any child elements, eg. HTML tags
                            $nodeValue = QubitXmlImport::normalizeNodeValue($domNode2);
                            // if you want the full XML from the node, use this
                            $nodeXML   = $domNode2->ownerDocument->saveXML($domNode2);
                            // set the parameters for the method call
                            if (empty($methodMap['Parameters'])) {
                                $parameters = array(
                                    $nodeValue
                                );
                            } //empty($methodMap['Parameters'])
                            else {
                                $parameters = array();
                                foreach ((array) $methodMap['Parameters'] as $parameter) {
                                    // if the parameter begins with %, evaluate it as an XPath expression relative to the current node
                                    if ('%' == substr($parameter, 0, 1)) {
                                        // evaluate the XPath expression
                                        $xPath  = substr($parameter, 1);
                                        $result = $importDOM->xpath->query($xPath, $domNode2);
                                        if ($result->length > 1) {
                                            // convert nodelist into an array
                                            foreach ($result as $element) {
                                                $resultArray[] = $element->nodeValue;
                                            } //$result as $element
                                            $parameters[] = $resultArray;
                                        } //$result->length > 1
                                        else {
                                            // pass the node value unaltered; this provides an alternative to $nodeValue above
                                            $parameters[] = $result->item(0)->nodeValue;
                                        }
                                    } //'%' == substr($parameter, 0, 1)
                                    else {
                                        // Confirm DOMXML node exists to avoid warnings at run-time
                                        if (false !== preg_match_all('/\$importDOM->xpath->query\(\'@\w+\', \$domNode2\)->item\(0\)->nodeValue/', $parameter, $matches)) {
                                            foreach ($matches[0] as $match) {
                                                $str = str_replace('->nodeValue', '', $match);
                                                if (null !== ($node = eval('return ' . $str . ';'))) {
                                                    // Substitute node value for search string
                                                    $parameter = str_replace($match, '\'' . $node->nodeValue . '\'', $parameter);
                                                } //null !== ($node = eval('return ' . $str . ';'))
                                                else {
                                                    // Replace empty nodes with null in parameter string
                                                    $parameter = str_replace($match, 'null', $parameter);
                                                }
                                            } //$matches[0] as $match
                                        } //false !== preg_match_all('/\$importDOM->xpath->query\(\'@\w+\', \$domNode2\)->item\(0\)->nodeValue/', $parameter, $matches)
                                        eval('$parameters[] = ' . $parameter . ';');
                                    }
                                } //(array) $methodMap['Parameters'] as $parameter
                            }
                            // Load taxonomies into variables to avoid use of magic numbers
                            /*$termData = QubitFlatfileImport::loadTermsFromTaxonomies(array(
                            QubitTaxonomy::NOTE_TYPE_ID      => 'noteTypes',
                            QubitTaxonomy::RAD_NOTE_ID       => 'radNoteTypes',
                            QubitTaxonomy::RAD_TITLE_NOTE_ID => 'titleNoteTypes',
                            QubitTaxonomy::FORMATS			 => 'recordTypes'
                            ));
                            
                            $titleVariationNoteTypeId            = array_search('Variations in title', $termData['titleNoteTypes']);
                            $titleAttributionsNoteTypeId         = array_search('Attributions and conjectures', $termData['titleNoteTypes']);
                            $titleContinuationNoteTypeId         = array_search('Continuation of title', $termData['titleNoteTypes']);
                            $titleStatRepNoteTypeId              = array_search('Statements of responsibility', $termData['titleNoteTypes']);
                            $titleParallelNoteTypeId             = array_search('Parallel titles and other title information', $termData['titleNoteTypes']);
                            $titleSourceNoteTypeId               = array_search('Source of title proper', $termData['titleNoteTypes']);
                            $alphaNumericaDesignationsNoteTypeId = array_search('Alpha-numeric designations', $termData['radNoteTypes']);
                            $physDescNoteTypeId                  = array_search('Physical description', $termData['radNoteTypes']);
                            $editionNoteTypeId                   = array_search('Edition', $termData['radNoteTypes']);
                            $conservationNoteTypeId              = array_search('Conservation', $termData['radNoteTypes']);
                            
                            $pubSeriesNoteTypeId                 = array_search("Publisher's series", $termData['radNoteTypes']);
                            $rightsNoteTypeId                    = array_search("Rights", $termData['radNoteTypes']);
                            $materialNoteTypeId                  = array_search("Accompanying material", $termData['radNoteTypes']);
                            $generalNoteTypeId                   = array_search("General note", $termData['radNoteTypes']); */
                            // invoke the object and method defined in the schema map
                            call_user_func_array(array(
                                &$currentObject,
                                $methodMap['Method']
                            ), $parameters);
                        } //$nodeList2 as $key => $domNode2
                } //$name
                unset($nodeList2);
            } //is_object($nodeList2)
        } //$methods as $name => $methodMap
    }
    
    /**
     * modified helper methods from (http://www.php.net/manual/en/ref.dom.php):
     *
     * - create a DOMDocument from a file
     * - parse the namespaces in it
     * - create a XPath object with all the namespaces registered
     *  - load the schema locations
     *  - validate the file on the main schema (the one without prefix)
     *
     * @param string $xmlFile XML document file
     * @param array $options optional parameters
     * @return DOMDocument an object representation of the XML document
     */
    protected function loadXML($xmlFile, $options = array())
    {
        QubitXMLImport::addLog("loadXML: " . $xmlFile, "", get_class($this), false);
        libxml_use_internal_errors(true);
        // FIXME: trap possible load validation errors (just suppress for now)
        $err_level        = error_reporting(0);
        $doc              = new DOMDocument('1.0', 'UTF-8');
        // Default $strictXmlParsing to false
        $strictXmlParsing = (isset($options['strictXmlParsing'])) ? $options['strictXmlParsing'] : false;
        // Pre-fetch the raw XML string from file so we can remove any default
        // namespaces and reuse the string for later when finding/registering namespaces.
        $rawXML           = file_get_contents($xmlFile);
        QubitXMLImport::addLog("rawXML: " . $rawXML, "", get_class($this), false);
        if ($strictXmlParsing) {
            // enforce all XML parsing rules and validation
            $doc->validateOnParse  = true;
            $doc->resolveExternals = true;
        } //$strictXmlParsing
        else {
            // try to load whatever we've got, even if it's malformed or invalid
            $doc->recover             = true;
            $doc->strictErrorChecking = false;
        }
        $doc->formatOutput       = false;
        $doc->preserveWhitespace = false;
        $doc->substituteEntities = true;
        $doc->loadXML($this->removeDefaultNamespace($rawXML));
        QubitXMLImport::addLog("doc loadXML: " . $rawXML, "", get_class($this), false);
        $xsi               = false;
        $doc->namespaces   = array();
        $doc->xpath        = new DOMXPath($doc);
        // pass along any XML errors that have been generated
        $doc->libxmlerrors = libxml_get_errors();
        // if the document didn't parse correctly, stop right here
        if (empty($doc->documentElement)) {
            QubitXMLImport::addLog("empty(doc->documentElement", "", get_class($this), true);
            return $doc;
        } //empty($doc->documentElement)
        error_reporting($err_level);
        // look through the entire document for namespaces
        // FIXME: #2787
        // https://projects.artefactual.com/issues/2787
        //
        // THIS SHOULD ONLY INSPECT THE ROOT NODE NAMESPACES
        // Consider: http://www.php.net/manual/en/book.dom.php#73793
        $re = '/xmlns:([^=]+)="([^"]+)"/';
        preg_match_all($re, $rawXML, $mat, PREG_SET_ORDER);
        foreach ($mat as $xmlns) {
            $pre                   = $xmlns[1];
            $uri                   = $xmlns[2];
            $doc->namespaces[$pre] = $uri;
            if ($pre == '') {
                $pre = 'noname';
            } //$pre == ''
            $doc->xpath->registerNamespace($pre, $uri);
        } //$mat as $xmlns
        
        return $doc;
    }
    
    /**
     * Return true if import had errors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->errors != null;
    }
    
    /**
     * Return array of error messages
     *
     * @return unknown
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Get the root object for the import
     *
     * @return mixed the root object (object type depends on import type)
     */
    public function getRootObject()
    {
        return $this->rootObject;
    }
    
    /**
     * Get the root object for the import
     *
     * @return mixed the root object (object type depends on import type)
     */
    public function setParent($parent)
    {
        return $this->parent = $parent;
    }
    
    /**
     * Make sure to remove any default namespaces from
     * EAD tags. See issue #7280 for details.
     */
    private function removeDefaultNamespace($xml)
    {
        return preg_replace('/(<ead.*?)xmlns="[^"]*"\s+(.*?>)/', '${1}${2}', $xml, 1);
    }
    
    // jjp SITA
    public function setRepositoryWithCodes($repositoryName, $unitid, $countrycode, $repositorycode, $addressValues)
    {
        QubitXMLImport::addLog("setRepositoryWithCodes repositoryName: " . $repositoryName, "", get_class($this), true);
        // ignore if repository URL instead of name is being passed
        if ($repositoryName !== '') {
            if (strtolower(substr($repositoryName, 0, 4)) !== 'http') {
                // see if Repository record already exists, if so link to it
                QubitXMLImport::addLog("setRepositoryWithCodes repositorycode: " . $repositorycode, "", get_class($this), false);
                $this->repository = QubitRepository::getByIdentifier($repositorycode);
                if (!isset($this->repository)) {
                    // if the repository does not already exist, create a new Repository and link to it
                    $this->repository = new QubitRepository;
                    $this->repository->setAuthorizedFormOfName($repositoryName);
                    $this->repository->identifier = $repositorycode;
                    QubitXMLImport::addLog($this->repository->identifier, "Repository save start", get_class($this), false);
                    try {
                        $this->repository->save();
                        QubitXMLImport::addLog("this->repository->save ", "", get_class($this), false);
                    }
                    catch (PDOException $e) {
                        QubitXMLImport::addLog("DOMException this->repository->save: " . $e->getMessage(), "", get_class($this), true);
                        echo $e->getMessage();
                        print "DOMException to save this->repository->save: " . $e->getMessage() . "\n";
                    }
                    
                    // jjp SITA 06 Jan 2015 - Add contact info
                    QubitXMLImport::addLog($this->repository->id, "repository save end/addUpdateContact - start", get_class($this), false);
                    QubitXmlImport::addUpdateContact($this->repository->id, $addressValues);
                    QubitXMLImport::addLog($this->repository->id, "addUpdateContact - end", get_class($this), true);
                } //!isset($this->repository)
                return $this->repository->id;
            } //strtolower(substr($repositoryName, 0, 4)) !== 'http'
        } //$repositoryName !== ''
        return;
    }
    
    // jjp SITA
    public function setRegistryWithCodes($registryName, $registrycode, $currentObjectRegistry)
    {
        QubitXMLImport::addLog("setRegistryWithCodes registryName: " . $registryName, "", get_class($this), false);
        // ignore if repository URL instead of name is being passed
        if ($registryName !== '') {
            // see if Repository record already exists, if so link to it
            
            foreach (QubitRegistry::getAll() as $item) {
                if ($item->__toString() == $registryName) {
                    try {
                        $currentObjectRegistry->registryId = $item->id;
                        $currentObjectRegistry->save();
                        QubitXMLImport::addLog("setRegistryWithCodes currentObject save: " . $item->id, "", get_class($this), false);
                    }
                    catch (PDOException $e) {
                        QubitXMLImport::addLog("DOMException setRegistryWithCodes currentObject save: : " . $e->getMessage(), "", get_class($this), true);
                        echo $e->getMessage();
                        print "DOMException setRegistryWithCodes currentObject save: : " . $e->getMessage() . "\n";
                    }
                } //$item->__toString() == $registryName
            } //QubitRegistry::getAll() as $item
            return $currentObjectRegistry->registryId;
        } //$registryName !== ''
        return;
    }
    
    //string $c is the string of characters to use.
    //integer $l is how long you want the string to be.
    //boolean $u is whether or not a character can appear beside itself.
    function rand_chars($l, $u = FALSE)
    {
        $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        if (!$u) {
            for ($s = '', $i = 0, $z = strlen($c) - 1; $i < $l; $x = rand(0, $z), $s .= $c{$x}, $i++);
        } //!$u
        else {
            for ($i = 0, $z = strlen($c) - 1, $s = $c{rand(0, $z)}, $i = 1; $i != $l; $x = rand(0, $z), $s .= $c{$x}, $s = ($s{$i} == $s{$i - 1} ? substr($s, 0, -1) : $s), $i = strlen($s));
        }
        return $s;
    }
    
    /**
     * This method will add an existing actor related to this information object to events or
     * name access points. Or, if no actor exists as such, create a new one to add to events
     * or name access point.
     *
     * First it will try to find an existing actor associated with this information object
     * that matches the specified name. If there are no actors with that name associated with
     * this information object, we will create a new actor with said name.
     *
     * To find existing actors associated with the information object, we check:
     * 1. Actors associated with this information object by an existing event
     * 2. Actors associated with this information object by relation (either subject or object)
     * 3. Actors that have the same name
     *
     * @param $name  The name of the actor
     * @param $options  An array of options filling in the new event or name access point info.
     *
     * @return QubitActor  The new or existing actor just added to the event/name access point list.
     */
    public function setActorByName($name, $options)
    {
        QubitXMLImport::addLog("setActorByName: " . $name, "", get_class($this), true);
        // Only create and link actor if the event or relation type is indicated
        if (!isset($options['event_type_id']) && !isset($options['relation_type_id'])) {
            return;
        } //!isset($options['event_type_id']) && !isset($options['relation_type_id'])
        
        // Check if the actor is already related in the description events
        // Store if it's related to avoid add it as a name access point
        $actor                 = $this->getActorByNameAndEvent($name);
        $existingEventRelation = !is_null($actor);

		// check if actor ends with _LD (legal deposit) and strip it and search it...
		if (strpos($name, '_LD') !== false) {
			$this->actorAuthorizedFormOfName = $name; 
			$this->actorAuthorizedFormOfName = substr($this->actorAuthorizedFormOfName,0,strlen($this->actorAuthorizedFormOfName)-3);
			$this->actorIdentifier = $name; 
		    // Check relations related by subject and identifier
    	    $actor = QubitActor::getByCorporateBodyIdentifier($name);
    	    QubitXMLImport::addLog("getByDescriptionIdentifier: " . $actor, "", get_class($this), true);

		    // If there isn't a match create a new actor
		    if (!$actor) {
 		  	    QubitXMLImport::addLog("New Actor: " . $actor, "", get_class($this), true);
		        QubitXMLImport::addLog("New Actor1: >>>".$this->actorAuthorizedFormOfName."<<<", "", get_class($this), true);
		        $actor           = new QubitActor;
		        $actor->parentId = QubitActor::ROOT_ID;
		        $actor->setAuthorizedFormOfName($this->actorAuthorizedFormOfName);
		        $actor->corporateBodyIdentifiers = $this->actorIdentifier;
		        
		        if (isset($options['entity_type_id'])) {
		            $actor->setEntityTypeId($options['entity_type_id']);
		        } //isset($options['entity_type_id'])
		        if (isset($options['source'])) {
		            $actor->setSources($options['source']);
		        } //isset($options['source'])
		        if (isset($options['rules'])) {
		            $actor->setRules($options['rules']);
		        } //isset($options['rules'])
		        if (isset($options['history'])) {
		            $actor->setHistory($options['history']);
		        } //isset($options['history'])
		        if (isset($options['dates_of_existence'])) {
		            $actor->datesOfExistence = $options['dates_of_existence'];
		        } //isset($options['dates_of_existence'])
		        
		        try {
		            $actor->save();
					$this->LDActor = $actor->id;
		            QubitXMLImport::addLog("actor->save: " . $item->id, "", get_class($this), false);
		        }
		        catch (PDOException $e) {
		            QubitXMLImport::addLog("DOMException actor->save: : " . $e->getMessage(), "", get_class($this), true);
		            echo $e->getMessage();
		            print "DOMException actor->save: : " . $e->getMessage() . "\n";
		        }
			} 
		}

        // Check relations related by subject
        if (!$actor) {
            $actor = $this->getActorByNameAndRelation($name, 'subject');
            QubitXMLImport::addLog("getActorByNameAndRelation subject: " . $actor, "", get_class($this), true);
        } 
        
        // Check relations related by object
        if (!$actor) {
            $actor = $this->getActorByNameAndRelation($name, 'object');
            QubitXMLImport::addLog("getActorByNameAndRelation object: " . $actor, "", get_class($this), true);
        } 
        
        // Lastly, check just if there are any other actors with this exact name
        if (!$actor) {
            $actor = QubitActor::getByAuthorizedFormOfName($name);
            QubitXMLImport::addLog("getByAuthorizedFormOfName: " . $actor, "", get_class($this), true);
        } 
        
        // If there isn't a match create a new actor
        if (!$actor) {
            QubitXMLImport::addLog("match create a new actor: " . $actor, "", get_class($this), true);
            QubitXMLImport::addLog("New Actor2: ", "", get_class($this), true);
            $actor           = new QubitActor;
            $actor->parentId = QubitActor::ROOT_ID;
            $actor->setAuthorizedFormOfName($name);
	        $actor->corporateBodyIdentifiers = $name;
           
            if (isset($options['entity_type_id'])) {
                $actor->setEntityTypeId($options['entity_type_id']);
            } 
            if (isset($options['source'])) {
                $actor->setSources($options['source']);
            } 
            if (isset($options['rules'])) {
                $actor->setRules($options['rules']);
            } 
            if (isset($options['history'])) {
                $actor->setHistory($options['history']);
            } 
            if (isset($options['dates_of_existence'])) {
                $actor->datesOfExistence = $options['dates_of_existence'];
            } 
            
            try {
                $actor->save();
                QubitXMLImport::addLog("actor->save: " . $item->id, "", get_class($this), true);
            }
            catch (PDOException $e) {
                QubitXMLImport::addLog("DOMException actor->save: : " . $e->getMessage(), "", get_class($this), true);
                echo $e->getMessage();
                print "DOMException actor->save: : " . $e->getMessage() . "\n";
            }
        } 
        // Create event or relation to link the information object and actor
        if (isset($options['event_type_id'])) {
            QubitXMLImport::addLog("isset event_type_id: ".$options['event_type_id'] . '<<>>' . $actor, "", get_class($this), true);
            // Create an event object to link the information object and actor
            $event = new QubitEvent;
            $event->setActor($actor);
            $event->setTypeId($options['event_type_id']);
            
            if (isset($options['dates'])) {
                $event->setDate($options['dates']);
            } 
            if (isset($options['date_start'])) {
                $event->setStartDate($options['date_start']);
            } 
            if (isset($options['date_end'])) {
                $event->setEndDate($options['date_end']);
            } 
            if (isset($options['event_note'])) {
                $event->setDescription($options['event_note']);
            } 
            
            $this->eventsRelatedByobjectId[] = $event;
        }
        // In EAD import, the term relation is not always created at this point;
        // it might be created afterwards.
        else if (isset($options['relation_type_id']) && isset($options['createRelation']) && false !== $options['createRelation']) {
            QubitXMLImport::addLog("relation: ", get_class($this), true);
            // Only add actor as name access point if they are not already linked to
            // an event (i.e. they are not already a "creator", "accumulator", etc.)
            if (!$existingEventRelation) {
	            QubitXMLImport::addLog("new QubitRelation: ".$options['event_type_id'] . '<<>>' . $actor, "", get_class($this), true);
                $relation         = new QubitRelation;
                $relation->object = $actor;
                $relation->typeId = QubitTerm::NAME_ACCESS_POINT_ID;
                
                $this->relationsRelatedBysubjectId[] = $relation;
            }
        }
        
        return $actor;
    }
    
    /**
     * Returns an actor if one exists with the specified name and
     * is related to this information object (either as a subject
     * or an object)
     *
     * @param $name  The actor name
     * @param $relatedBy  The relation type, either 'object' or 'subject'
     * @return QubitActor matching the specified parameters, null otherwise
     */
    private function getActorByNameAndRelation($name, $relatedBy = 'object')
    {
        // We could also maybe use $this->$varmagic here but
        // I figure just a simple if/else was more readable.
        if ($relatedBy === 'object') {
            $relations = $this->relationsRelatedByobjectId;
        } //$relatedBy === 'object'
        else {
            $relations = $this->relationsRelatedBysubjectId;
        }
        
        foreach ($relations as $relation) {
            if ($relation->$relatedBy instanceof QubitActor) {
                foreach ($relation->$relatedBy->actorI18ns as $actorI18n) {
                    if (isset($actorI18n->authorizedFormOfName) && $name == $actorI18n->authorizedFormOfName) {
                        QubitXMLImport::addLog("getActorByNameAndRelation relation->relatedBy: " . $relation->$relatedBy, "", get_class($this), false);
                        return $relation->$relatedBy;
                    } //isset($actorI18n->authorizedFormOfName) && $name == $actorI18n->authorizedFormOfName
                } //$relation->$relatedBy->actorI18ns as $actorI18n
            } //$relation->$relatedBy instanceof QubitActor
        } //$relations as $relation
        
        return null;
    }
    
    /**
     * Returns an actor if one exists with the specified name and
     * who is also part of an event related to this information object.
     *
     * @param $name  The actor name
     * @return QubitActor matching the specified parameters, null otherwise
     */
    private function getActorByNameAndEvent($name)
    {
        foreach ($this->eventsRelatedByobjectId as $event) {
            if (isset($event->actor)) {
                foreach ($event->actor->actorI18ns as $actorI18n) {
                    if (isset($actorI18n->authorizedFormOfName) && $name == $actorI18n->authorizedFormOfName) {
                        QubitXMLImport::addLog("getActorByNameAndRelationevent->actor: " . $event->actor, "", get_class($this), false);
                        return $event->actor;
                    } //isset($actorI18n->authorizedFormOfName) && $name == $actorI18n->authorizedFormOfName
                } //$event->actor->actorI18ns as $actorI18n
            } //isset($event->actor)
        } //$this->eventsRelatedByobjectId as $event
        
        return null;
    }
    
    function convertDate($i)
    {
        return "!!!!!!!!!!!!!!!!!!!!!!!!!!";
    }
}
