<?php

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
 */
class QubitXmlImport
{
    protected $errors = null, $rootObject = null, $parent = null, $events = array(), $eadUrl = null, $sourceName = null, $options = array();
    public $indexDuringImport = false;
    public function import($xmlFile, $options = array(), $xmlOrigFileName = null)
    {
        $origFileName       = $xmlFile;
        //first transform the xml
        // load the XML document into a DOMXML object
        $importDOMTransform = new DOMDocument;
        $importDOMTransform->load($xmlFile);
        QubitXMLImport::addLog("Import xmlFile: >>" . $xmlFile . "<<", "", get_class($this), false);
        // if we were unable to parse the XML file at all
        if (empty($importDOMTransform->documentElement)) {
            QubitXMLImport::addImportError($type, 'Unable to parse XML file: malformed or unresolvable entities' . $type, get_class($this), true, $origFileName);
            throw new sfError404Exception('Unable to parse XML file: malformed or unresolvable entities - QubitXMLImport');
        }
        QubitXMLImport::addLog("Import type: >>" . $type . "<<", "", get_class($this), false);
        if (!isset($type)) // batch import not set to type
            {
            $type = $importDOMTransform->documentElement->tagName;
        }
        if (ltrim(rtrim($type)) == "") // batch import not set to type
            {
            $type = $importDOMTransform->documentElement->tagName;
        }
        
        $eadimports = $importDOMTransform->getElementsByTagName('eadsubmission');
        if ($eadimports->length != 0) {
            $type = 'eadsubmission';
        }
        $eadimports = $importDOMTransform->getElementsByTagName('accessionarchival');
        if ($eadimports->length != 0) {
            $type = 'eadsubmission';
        }
        //print $type;
        if ($type != 'ead' && $type != 'informationObject') {
            if ($type == 'accession') {
                // pick the XSLT sheet
                $xslt_file = "DrupalAccessionImport.xsl";
            } else if ($type == 'informationObject') {
                // pick the XSLT sheet
                $xslt_file = "InformationObjectImportDrupal.xsl";
            } else if ($type == 'authorityRecord' || $type == 'authority' || $type == 'donorauthority' || $type == 'legalauthority') {
                // pick the XSLT sheet
                $xslt_file = "DrupalAuthorityRecord.xsl";
                QubitXMLImport::addLog("Import xslt_file: >" . $xslt_file . "<", "", get_class($this), false);
            } else if ($type == 'donor' || $type == 'donoruser') {
                // pick the XSLT sheet
                $xslt_file = "DrupalDonorImport.xsl";
            } else if ($type == 'repository') {
                // pick the XSLT sheet
                $xslt_file = "DrupalPIImport.xsl";
            } else if ($type == 'researcher') {
                // pick the XSLT sheet
                $xslt_file = "DrupalResearcherImport.xsl";
            } else if ($type == 'eadsubmission') {
                // pick the XSLT sheet
                $xslt_file = "DrupaltoEAD.xsl";
            } else if ($type == 'user') {
                // pick the XSLT sheet
                $xslt_file = "DrupalUserImport.xsl";
            } else {
                QubitXMLImport::addImportError($xslt_file, sfContext::getInstance()->i18n->__('Unable to load import XSL filter (Transform): "%xslt_file%" ' . $type, array(
                    '%xslt_file%' => $xslt_file
                )), get_class($this), true, $origFileName);
                
                throw new sfError404Exception(sfContext::getInstance()->i18n->__('Unable to load import XSL filter (Transform): "%xslt_file%" ' . $type, array(
                    '%xslt_file%' => $xslt_file
                )));
            }
            $this->tmpDir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
            $xslt_file    = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'xslt' . DIRECTORY_SEPARATOR . $xslt_file;
            if (!file_exists($xslt_file)) {
                
                QubitXMLImport::addImportError($xslt_file, sfContext::getInstance()->i18n->__('Unable to load import XSL filter (Transform): "%xslt_file%" ' . $type, array(
                    '%xslt_file%' => $xslt_file
                )), get_class($this), true, $origFileName);
                
                throw new sfError404Exception(sfContext::getInstance()->i18n->__('Unable to load import XSL filter (xslt missing): "%xslt_file%"', array(
                    '%xslt_file%' => $xslt_file
                )));
            }
            // save transformed xml to this filename
            $randomFilename = substr(str_shuffle(MD5(microtime())), 0, 10);
            $xmlNewFile     = $this->tmpDir . $randomFilename . ".xml";
            QubitXMLImport::addLog("Transform xmlNewFile: " . $xmlNewFile, "", get_class($this), false);
            // Load the XML source
            $xml                   = new DOMDocument;
            $xml->formatOutput     = true;
            $xml->validateOnParse  = true;
            $xml->resolveExternals = true;
            $xml->load($xmlFile);
            QubitXMLImport::addLog("Transform xmlFile: " . $xmlFile, "", get_class($this), false);
            $xsl = new DOMDocument;
            $xsl->load($xslt_file);
            QubitXMLImport::addLog("Transform xslt_file: " . $xslt_file, "", get_class($this), false);
            // Configure the transformer
            $proc = new XSLTProcessor;
            $proc->importStyleSheet($xsl); // attach the xsl rules
            //$newdom = $proc->transformToXML($xml);
            $newdom = $proc->transformToDoc($xml);
            try {
                $transformedXML = $newdom->saveXML();
                QubitXMLImport::addLog("transformedXML ", "", get_class($this), false);
            }
            catch (DOMException $e) {
                QubitXMLImport::addLog("DOMException: " . $e->getMessage(), "", get_class($this), true);
                echo $e->getMessage();
                print "DOMException to save XML: " . $e->getMessage() . "\n";
            }
            $newdom->save($xmlNewFile);
            $xmlFile = $xmlNewFile;
            QubitXMLImport::addLog("xmlNewFile: " . $xmlNewFile, "", get_class($this), false);
        }

        // Needs to be created before validateOptions() is called.
        $this->i18n = sfContext::getInstance()->i18n;
        
        // Save options so we can access from processMethods
        $this->options = $options;
        $this->validateOptions();
                
        // Find type of file to import
        // load the XML document into a DOMXML object
        $importDOM = $this->loadXML($xmlFile, $options);
        
        if (null === $xmlOrigFileName) {
            // WebUI passes a temp file name in $xmlFile. e.g. /tmp/phpLjBIBv
            // If $xmlOrigFileName is null, save $xmlFile in keymap record
            $this->sourceName = basename($xmlFile);
        } else {
            // use the original file name when creating keymap record
            $this->sourceName = basename($xmlOrigFileName);
        }
        

        // if we were unable to parse the XML file at all
        if (empty($importDOM->documentElement)) {
            $errorMsg = $this->i18n->__('Unable to parse XML file: malformed or unresolvable entities');
            
            throw new Exception($errorMsg);
        }
        
        // if libxml threw errors, populate them to show in the template
        if ($importDOM->libxmlerrors) {
            // warning condition, XML file has errors (perhaps not well-formed or invalid?)
            foreach ($importDOM->libxmlerrors as $libxmlerror) {
                $xmlerrors[] = $this->i18n->__('libxml error %code% on line %line% in input file: %message%', array(
                    '%code%' => $libxmlerror->code,
                    '%message%' => $libxmlerror->message,
                    '%line%' => $libxmlerror->line
                ));
            }
            
            $this->errors = array_merge((array) $this->errors, $xmlerrors);
        }
        
        $this->stripComments($importDOM);
        
        // Add local XML catalog for EAD DTD and DC and MODS XSD validations
        putenv('XML_CATALOG_FILES=' . sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'catalog.xml');
        
        if ('mods' == $importDOM->documentElement->tagName) {
            // XSD validation for MODS
            $schema = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'xsd' . DIRECTORY_SEPARATOR . 'mods.xsd';
            
            if (!$importDOM->schemaValidate($schema)) {
                $this->errors[] = 'XSD validation failed';
            }
            
            // Populate errors to show in the template
            foreach (libxml_get_errors() as $libxmlerror) {
                $this->errors[] = $this->i18n->__('libxml error %code% on line %line% in input file: %message%', array(
                    '%code%' => $libxmlerror->code,
                    '%message%' => $libxmlerror->message,
                    '%line%' => $libxmlerror->line
                ));
            }
            
            $parser = new sfModsConvertor();
            if ($parser->parse($xmlFile)) {
                $this->rootObject = $parser->getResource();
            } else {
                $errorData      = $parser->getErrorData();
                $this->errors[] = array(
                    $this->i18n->__('SAX xml parse error %code% on line %line% in input file: %message%', array(
                        '%code%' => $errorData['code'],
                        '%message%' => $errorData['string'],
                        '%line%' => $errorData['line']
                    ))
                );
            }
            
            return $this;
        }
        
        if ('eac-cpf' == $importDOM->documentElement->tagName) {
            $this->rootObject           = new QubitActor;
            $this->rootObject->parentId = QubitActor::ROOT_ID;
            
            $eac = new sfEacPlugin($this->rootObject);
            $eac->parse($importDOM);
            
            if (!$this->handlePreSaveLogic($this->rootObject)) {
                return $this;
            }
            
            $this->rootObject->save();
            
            if (isset($eac->itemsSubjectOf)) {
                foreach ($eac->itemsSubjectOf as $item) {
                    $relation         = new QubitRelation;
                    $relation->object = $this->rootObject;
                    $relation->typeId = QubitTerm::NAME_ACCESS_POINT_ID;
                    
                    $item->relationsRelatedBysubjectId[] = $relation;
                    $item->save();
                }
            }
            
            return $this;
        }
        
        // FIXME hardcoded until we decide how these will be developed
        $validSchemas = array(
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
            'record' => 'oai_dc_record',
            'dc' => 'dc',
            'oai_dc:dc' => 'dc',
            'dublinCore' => 'dc',
            'metadata' => 'dc',
            //'mets' => 'mets',
            'mods' => 'mods',
            'ead' => 'ead',
            'add' => 'alouette',
            'http://www.w3.org/2004/02/skos/core#' => 'skos',
            'donor' => 'donor',
            'authority' => 'authority',
            'donorauthority' => 'donorauthority',
            'researcher' => 'researcher',
            'user' => 'user',
            'accession' => 'accession',
            'repository' => 'repository'
        );
        
        // determine what kind of schema we're trying to import
        $schemaDescriptors = array(
            $importDOM->documentElement->tagName
        );
        if (!empty($importDOM->namespaces)) {
            krsort($importDOM->namespaces);
            $schemaDescriptors = array_merge($schemaDescriptors, $importDOM->namespaces);
        }
        if (!empty($importDOM->doctype)) {
            $schemaDescriptors = array_merge($schemaDescriptors, array(
                $importDOM->doctype->name,
                $importDOM->doctype->systemId,
                $importDOM->doctype->publicId
            ));
        }
        $importSchema = "";
        foreach ($schemaDescriptors as $descriptor) {
            if (array_key_exists($descriptor, $validSchemas)) {
                $importSchema = $validSchemas[$descriptor];
                
                // Store the used descriptor to differentiate between
                // oai_dc:dc and simple dc in XSD validation
                $usedDescriptor = $descriptor;
            }
        }
        
        $type = $importSchema;
        // Find the proper task
        switch ($type) {
            case 'accession':
                $importer = new XmlImportAccession;
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'authority':
                $importer = new XmlImportAuthority;
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'donorauthority':
                $importer = new XmlImportAuthority;
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'event':
                $importer = new XmlImportEvent;
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'repository':
                $importer = new XmlImportRepositry;
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'donor':
                $importer = new XmlImportDonor;
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'informationObject':
                $importer = new XmlImportArchivalDescription;
                if (isset($this->parent)) {
                    $importer->setParent($this->parent);
                }
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'researcher':
                $importer = new XmlImportResearcher;
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'user':
                $importer = new XmlImportUser;
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'ead':
                $importer = new XmlImportArchivalDescription;
                if (isset($this->parent)) {
                    $importer->setParent($this->parent);
                }
                $importer->import($xmlFile, array(
                    'strictXmlParsing' => false
                ), $type);
                break;
            
            case 'dc':
                
                // XSD validation for DC
                $schema = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'xsd' . DIRECTORY_SEPARATOR;
                if ($usedDescriptor == 'oai_dc:dc') {
                    $schema .= 'oai_dc.xsd';
                } else {
                    $schema .= 'simpledc20021212.xsd';
                }
                
                if (!$importDOM->schemaValidate($schema)) {
                    $this->errors[] = 'XSD validation failed';
                }
                
                // Populate errors to show in the template
                foreach (libxml_get_errors() as $libxmlerror) {
                    $this->errors[] = $this->i18n->__('libxml error %code% on line %line% in input file: %message%', array(
                        '%code%' => $libxmlerror->code,
                        '%message%' => $libxmlerror->message,
                        '%line%' => $libxmlerror->line
                    ));
                }
                
                break;
            
            case 'skos':
                
                $criteria = new Criteria;
                $criteria->add(QubitSetting::NAME, 'plugins');
                $setting = QubitSetting::getOne($criteria);
                if (null === $setting || !in_array('sfSkosPlugin', unserialize($setting->getValue(array(
                    'sourceCulture' => true
                ))))) {
                    throw new sfException($this->i18n->__('The SKOS plugin is not enabled'));
                }
                $this->rootObject = QubitTaxonomy::getById($options['taxonomy']);
                $importer         = new sfSkosPlugin($options['taxonomy'], $options);
                // 'file' scheme required during SKOS file validation.
                $importer->load(is_file($xmlFile) ? "file://$xmlFile" : $xmlFile);
                $importer->importGraph();
                
                return $this;
                
                break;
            default:
                QubitXMLImport::addLog($type, 'Could not find import type:' . $type, get_class($this), true);
                QubitXMLImport::addImportError($type, 'Could not find import type:' . $type, get_class($this), true, $origFileName);
                
                
                throw new sfException('Could not find import type:"' . $type . '"');
                //        $importer = new XmlImportArchivalDescription;
                //        $importer->import($xmlFile, $parcing, $type);
                break;
        }
        return $this;
        
        $importMap = sfConfig::get('sf_app_module_dir') . DIRECTORY_SEPARATOR . 'object' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $importSchema . '.yml';
        if (!file_exists($importMap)) {
            // error condition, unknown schema or no import filter
            $errorMsg = $this->i18n->__('Unknown schema or import format: "%format%"', array(
                '%format%' => $importSchema
            ));
            
            throw new Exception($errorMsg);
        }
        
        $this->schemaMap = sfYaml::load($importMap);
        
        // if XSLs are specified in the mapping, process them
        if (!empty($this->schemaMap['processXSLT'])) {
            // pre-filter through XSLs in order
            foreach ((array) $this->schemaMap['processXSLT'] as $importXSL) {
                $importXSL = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'xslt' . DIRECTORY_SEPARATOR . $importXSL;
                
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
                } else {
                    $this->errors[] = $this->i18n->__('Unable to load import XSL filter: "%importXSL%"', array(
                        '%importXSL%' => $importXSL
                    ));
                }
            }
            
            // re-initialize xpath on the new XML
            $importDOM->xpath = new DOMXPath($importDOM);
        }
        
        if ($importSchema == 'ead') {
            // get ead url from ead header for use in matching this object
            if (is_object($urlValues = $importDOM->xpath->query('//eadheader/eadid/@url'))) {
                foreach ($urlValues as $url) {
                    $this->eadUrl = trim(preg_replace('/[\n\r\s]+/', ' ', $url->nodeValue));
                    // Possibly more than one url but we can only take one. Take first
                    // valid one.
                    break;
                }
            }
            
            // switch source culture if language is set in an EAD document
            if (is_object($langusage = $importDOM->xpath->query('//eadheader/profiledesc/langusage/language/@langcode'))) {
                $sf_user           = sfContext::getInstance()->user;
                $currentCulture    = $sf_user->getCulture();
                $langCodeConvertor = new fbISO639_Map;
                foreach ($langusage as $language) {
                    $isocode = trim(preg_replace('/[\n\r\s]+/', ' ', $language->nodeValue));
                    // convert to Symfony culture code
                    if (!$twoCharCode = strtolower($langCodeConvertor->getID1($isocode, false))) {
                        $twoCharCode = $isocode;
                    }
                    // Check to make sure that the selected language is supported with a Symfony i18n data file.
                    // If not it will cause a fatal error in the Language List component on every response.
                    
                    ProjectConfiguration::getActive()->loadHelpers('I18N');
                    
                    try {
                        format_language($twoCharCode, $twoCharCode);
                    }
                    catch (Exception $e) {
                        $this->errors[] = $this->i18n->__('EAD "langmaterial" is set to') . ': "' . $isocode . '". ' . $this->i18n->__('This language is currently not supported.');
                        continue;
                    }
                    
                    if ($currentCulture !== $twoCharCode) {
                        $this->errors[] = $this->i18n->__('EAD "langmaterial" is set to') . ': "' . $isocode . '" (' . format_language($twoCharCode, 'en') . '). ' . $this->i18n->__('Your XML document has been saved in this language and your user interface has just been switched to this language.');
                    }
                    $sf_user->setCulture($twoCharCode);
                    // can only set to one language, so have to break once the first valid language is encountered
                    break;
                }
            }
        }
        
        unset($this->schemaMap['processXSLT']);
        
        // go through schema map and populate objects/properties
        foreach ($this->schemaMap as $name => $mapping) {
            // if object is not defined or a valid class, we can't process this mapping
            if (empty($mapping['Object']) || !class_exists('Qubit' . $mapping['Object'])) {
                $this->errors[] = $this->i18n->__('Non-existent class defined in import mapping: "%class%"', array(
                    '%class%' => 'Qubit' . $mapping['Object']
                ));
                continue;
            }
            
            // get a list of XML nodes to process
            $nodeList = $importDOM->xpath->query($mapping['XPath']);
            
            foreach ($nodeList as $domNode) {
                // create a new object
                $class         = 'Qubit' . $mapping['Object'];
                $currentObject = new $class;
                
                // set the rootObject to use for initial display in successful import
                if (!$this->rootObject) {
                    $this->rootObject = $currentObject;
                }
                
                // use DOM to populate object
                if (!$this->populateObject($domNode, $importDOM, $mapping, $currentObject, $importSchema)) {
                    break; // No match found for top level description on --update, end import
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Populate EAD information objects.
     *
     * @return bool  True if we want to continue populating objects, false if we want to end the import.
     */
    private function populateObject(&$domNode, &$importDOM, &$mapping, &$currentObject, $importSchema)
    {
        // if a parent path is specified, try to parent the node
        if (empty($mapping['Parent'])) {
            $parentNodes = new DOMNodeList;
        } else {
            $parentNodes = $importDOM->xpath->query('(' . $mapping['Parent'] . ')', $domNode);
        }
        
        if ($parentNodes->length > 0) {
            // parent ID comes from last node in the list because XPath forces forward document order
            $parentId = $parentNodes->item($parentNodes->length - 1)->getAttribute('xml:id');
            unset($parentNodes);
            
            if (!empty($parentId) && is_callable(array(
                $currentObject,
                'setParentId'
            ))) {
                $currentObject->parentId = $parentId;
            }
        } else {
            // orphaned object, set root if possible
            if (isset($this->parent)) {
                $currentObject->parentId = $this->parent->id;
            } else if (is_callable(array(
                    $currentObject,
                    'setRoot'
                ))) {
                $currentObject->setRoot();
            }
        }
        
        // go through methods and populate properties
        $this->processMethods($domNode, $importDOM, $mapping['Methods'], $currentObject, $importSchema);
        $doSave = true;
        
        // make sure we have a publication status set before indexing
        if ($currentObject instanceof QubitInformationObject && count($currentObject->statuss) == 0) {
            $currentObject->setPublicationStatus(sfConfig::get('app_defaultPubStatus', QubitTerm::PUBLICATION_STATUS_DRAFT_ID));
        }
        
        // if this is an information object in an XML EAD import, run the enhanced update check.
        if ($currentObject instanceof QubitInformationObject && $importSchema == 'ead') {
            $doSave = $this->handlePreSaveLogic($currentObject);
        }
        
        if ($doSave) {
            // save the object after it's fully-populated
            $currentObject->save();
            // if this is the root Info Object, save the EadUrl in the keymap table for matching.
            if ($currentObject instanceof QubitInformationObject && $importSchema == 'ead' && $this->rootObject === $currentObject) {
                $this->saveEadUrl($currentObject);
            }
            
            // write the ID onto the current XML node for tracking
            $domNode->setAttribute('xml:id', $currentObject->id);
        }
        
        return $doSave;
    }
    
    /*
     * Cycle through methods and populate object based on relevant data
     *
     * @return  null
     */
    private function processMethods(&$domNode, &$importDOM, $methods, &$currentObject, $importSchema)
    {
        // We want to keep track of nodes processed so we don't process one twice
        // if multiple selectors apply to it (for example the generic "odd" tag
        // handler should not trigger if a specific "odd" handler was previously
        // triggered for the same node)
        $processed = array();
        
        // go through methods and populate properties
        foreach ($methods as $name => $methodMap) {
            // if method is not defined, we can't process this mapping
            if (empty($methodMap['Method']) || !is_callable(array(
                $currentObject,
                $methodMap['Method']
            ))) {
                $this->errors[] = $this->i18n->__('Non-existent method defined in import mapping: "%method%"', array(
                    '%method%' => $methodMap['Method']
                ));
                continue;
            }
            
            // Get a list of XML nodes to process
            // This condition mitigates a problem where the XPath query wasn't working
            // as expected, see #4302 for more details
            if ($importSchema == 'dc' && $methodMap['XPath'] != '.') {
                $nodeList2 = $importDOM->getElementsByTagName($methodMap['XPath']);
            } else {
                $nodeList2 = $importDOM->xpath->query($methodMap['XPath'], $domNode);
            }
            
            if (is_object($nodeList2)) {
                switch ($name) {
                    // hack: some multi-value elements (e.g. 'languages') need to get passed as one array instead of individual nodes values
                    case 'languages':
                    case 'language':
                        $langCodeConvertor = new fbISO639_Map;
                        $isID3             = ($importSchhema == 'dc') ? true : false;
                        
                        $value = array();
                        foreach ($nodeList2 as $item) {
                            if ($twoCharCode = $langCodeConvertor->getID1($item->nodeValue, $isID3)) {
                                $value[] = strtolower($twoCharCode);
                            } else {
                                $value[] = $item->nodeValue;
                            }
                        }
                        $currentObject->language = $value;
                        
                        break;
                    
                    case 'flocat':
                    case 'digital_object':
                        $resources = array();
                        foreach ($nodeList2 as $item) {
                            $resources[] = $item->nodeValue;
                        }
                        
                        if (0 < count($resources)) {
                            $currentObject->importDigitalObjectFromUri($resources, $this->errors);
                        }
                        
                        break;
                    
                    case 'container':
                        // Get the collection root to check for existent phys. objects
                        if (!$this->collectionRoot) {
                            $this->collectionRoot = $this->rootObject->getCollectionRoot();
                        }
                        
                        foreach ($nodeList2 as $item) {
                            $name     = $item->nodeValue;
                            $parent   = $importDOM->xpath->query('@parent', $item)->item(0)->nodeValue;
                            $location = $importDOM->xpath->query('did/physloc[@id="' . $parent . '"]', $domNode)->item(0)->nodeValue;
                            
                            $options = array(
                                'type' => $importDOM->xpath->query('@type', $item)->item(0)->nodeValue,
                                'label' => $importDOM->xpath->query('@label', $item)->item(0)->nodeValue
                            );
                            
                            if ($this->collectionRoot) {
                                $options['collectionId'] = $this->collectionRoot->id;
                            }
                            
                            $currentObject->importPhysicalObject($location, $name, $options);
                        }
                        
                        break;
                    
                    case 'relatedunitsofdescription':
                        $i         = 0;
                        $nodeValue = '';
                        foreach ($nodeList2 as $item) {
                            if ($i++ == 0) {
                                $nodeValue .= self::normalizeNodeValue($item);
                            } else {
                                $nodeValue .= "\n\n" . self::normalizeNodeValue($item);
                            }
                        }
                        
                        $currentObject->setRelatedUnitsOfDescription($nodeValue);
                        
                        break;
                    
                    default:
                        foreach ($nodeList2 as $key => $domNode2) {
                            // Skip this node if method path isn't "self" and node's previously been processed
                            if ($methodMap['XPath'] != '.' && isset($processed[$domNode2->getNodePath()])) {
                                continue;
                            }
                            
                            // Take note that this node has been processed
                            $processed[$domNode2->getNodePath()] = true;
                            
                            // normalize the node text; NB: this will strip any child elements, eg. HTML tags
                            $nodeValue = self::normalizeNodeValue($domNode2);
                            
                            // if you want the full XML from the node, use this
                            $nodeXML = $domNode2->ownerDocument->saveXML($domNode2);
                            // set the parameters for the method call
                            if (empty($methodMap['Parameters'])) {
                                $parameters = array(
                                    $nodeValue
                                );
                            } else {
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
                                            }
                                            $parameters[] = $resultArray;
                                        } else {
                                            // pass the node value unaltered; this provides an alternative to $nodeValue above
                                            $parameters[] = $result->item(0)->nodeValue;
                                        }
                                    } else {
                                        // Confirm DOMXML node exists to avoid warnings at run-time
                                        if (false !== preg_match_all('/\$importDOM->xpath->query\(\'@\w+\', \$domNode2\)->item\(0\)->nodeValue/', $parameter, $matches)) {
                                            foreach ($matches[0] as $match) {
                                                $str = str_replace('->nodeValue', '', $match);
                                                
                                                if (null !== ($node = eval('return ' . $str . ';'))) {
                                                    // Substitute node value for search string
                                                    $parameter = str_replace($match, '\'' . $node->nodeValue . '\'', $parameter);
                                                } else {
                                                    // Replace empty nodes with null in parameter string
                                                    $parameter = str_replace($match, 'null', $parameter);
                                                }
                                            }
                                        }
                                        
                                        eval('$parameters[] = ' . $parameter . ';');
                                    }
                                }
                            }
                            
                            // Load taxonomies into variables to avoid use of magic numbers
                            $termData = QubitFlatfileImport::loadTermsFromTaxonomies(array(
                                QubitTaxonomy::NOTE_TYPE_ID => 'noteTypes',
                                QubitTaxonomy::RAD_NOTE_ID => 'radNoteTypes',
                                QubitTaxonomy::RAD_TITLE_NOTE_ID => 'titleNoteTypes',
                                QubitTaxonomy::DACS_NOTE_ID => 'dacsSpecializedNotesTypes'
                            ));
                            
                            $titleVariationNoteTypeId            = array_search('Variations in title', $termData['titleNoteTypes']['en']);
                            $titleAttributionsNoteTypeId         = array_search('Attributions and conjectures', $termData['titleNoteTypes']['en']);
                            $titleContinuationNoteTypeId         = array_search('Continuation of title', $termData['titleNoteTypes']['en']);
                            $titleStatRepNoteTypeId              = array_search('Statements of responsibility', $termData['titleNoteTypes']['en']);
                            $titleParallelNoteTypeId             = array_search('Parallel titles and other title information', $termData['titleNoteTypes']['en']);
                            $titleSourceNoteTypeId               = array_search('Source of title proper', $termData['titleNoteTypes']['en']);
                            $alphaNumericaDesignationsNoteTypeId = array_search('Alpha-numeric designations', $termData['radNoteTypes']['en']);
                            $physDescNoteTypeId                  = array_search('Physical description', $termData['radNoteTypes']['en']);
                            $editionNoteTypeId                   = array_search('Edition', $termData['radNoteTypes']['en']);
                            $conservationNoteTypeId              = array_search('Conservation', $termData['radNoteTypes']['en']);
                            
                            $pubSeriesNoteTypeId = array_search("Publisher's series", $termData['radNoteTypes']['en']);
                            $rightsNoteTypeId    = array_search("Rights", $termData['radNoteTypes']['en']);
                            $materialNoteTypeId  = array_search("Accompanying material", $termData['radNoteTypes']['en']);
                            $generalNoteTypeId   = array_search("General note", $termData['noteTypes']['en']);
                            
                            $dacsAlphaNumericaDesignationsNoteTypeId = array_search('Alphanumeric designations', $termData['dacsSpecializedNotesTypes']['en']);
                            $dacsCitationNoteTypeId                  = array_search("Citation", $termData['dacsSpecializedNotesTypes']['en']);
                            $dacsConservationNoteTypeId              = array_search("Conservation", $termData['dacsSpecializedNotesTypes']['en']);
                            $dacsProcessingInformationNoteTypeId     = array_search("Processing information", $termData['dacsSpecializedNotesTypes']['en']);
                            $dacsVariantTitleInformationNoteTypeId   = array_search("Variant title information", $termData['dacsSpecializedNotesTypes']['en']);
                            
                            // Invoke the object and method defined in the schema map
                            $result = call_user_func_array(array(
                                &$currentObject,
                                $methodMap['Method']
                            ), $parameters);
                            
                            // If an actor/event object was returned, track that
                            // in the events cache for later cleanup
                            if ($currentObject instanceof QubitInformationObject && !empty($result)) {
                                if ($methodMap['Method'] === 'importOriginationEadData') {
                                    foreach ($result as $actorNode) {
                                        $this->trackEvent($actorNode['actor'], $actorNode['node']);
                                    }
                                } else {
                                    $this->trackEvent($result, $domNode2);
                                }
                            }
                        }
                }
                
                unset($nodeList2);
            }
        }
        
        $this->associateEvents();
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
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        
        // FIXME: trap possible load validation errors (just suppress for now)
        $err_level = error_reporting(0);
        $doc       = new DOMDocument('1.0', 'UTF-8');
        
        // Default $strictXmlParsing to false
        $strictXmlParsing = (isset($options['strictXmlParsing'])) ? $options['strictXmlParsing'] : false;
        
        // Pre-fetch the raw XML string from file so we can remove any default
        // namespaces and reuse the string for later when finding/registering namespaces.
        $rawXML = file_get_contents($xmlFile);
        
        if ($strictXmlParsing) {
            // enforce all XML parsing rules and validation
            $doc->validateOnParse  = true;
            $doc->resolveExternals = true;
        } else {
            // try to load whatever we've got, even if it's malformed or invalid
            $doc->recover             = true;
            $doc->strictErrorChecking = false;
        }
        $doc->formatOutput       = false;
        $doc->preserveWhitespace = false;
        $doc->substituteEntities = true;
        
        $doc->loadXML($this->removeDefaultNamespace($rawXML));
        
        $xsi             = false;
        $doc->namespaces = array();
        $doc->xpath      = new DOMXPath($doc);
        
        // pass along any XML errors that have been generated
        $doc->libxmlerrors = libxml_get_errors();
        
        // if the document didn't parse correctly, stop right here
        if (empty($doc->documentElement)) {
            return $doc;
        }
        
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
            $pre = $xmlns[1];
            $uri = $xmlns[2];
            
            $doc->namespaces[$pre] = $uri;
            
            if ($pre == '') {
                $pre = 'noname';
            }
            $doc->xpath->registerNamespace($pre, $uri);
        }
        
        return $doc;
    }
    
    /**
     *
     *
     * @return DOMNodeList
     */
    public static function queryDomNode($node, $xpathQuery)
    {
        $doc = new DOMDocument();
        $doc->loadXML('<xml></xml>');
        $doc->documentElement->appendChild($doc->importNode($node, true));
        $xpath = new DOMXPath($doc);
        return $xpath->query($xpathQuery);
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
     * Set the parent resource for the import
     */
    public function setParent($parentId)
    {
        $this->parent = QubitObject::getById($parentId);
    }
    
    /** 
     * Load terms from one or more taxonomies and use the terms to populate one
     * or more array elements.
     *
     * @param array $taxonomies  array of taxonomy IDs and identifying names
     *
     * @return array  array of arrays containing taxonomy terms
     */
    public static function loadTermsFromTaxonomies($taxonomies)
    {
        $taxonomyTerms = array();
        foreach ($taxonomies as $taxonomyId => $varName) {
            $taxonomyTerms[$varName] = array();
            foreach (QubitXMLImport::getTaxonomyTerms($taxonomyId) as $termId => $term) {
                $taxonomyTerms[$varName][$termId] = $term->name;
            }
        }
        return $taxonomyTerms;
    }
    /**
     * Get the terms in a taxonomy, optionally specifying culture
     *
     * @param integer $taxonomyId  taxonomy ID
     * @param string $culture  culture code (defaulting to English)
     *
     * @return array  array of term IDs and their respective names
     */
    public static function getTaxonomyTerms($taxonomyId, $culture = 'en')
    {
        $terms     = array();
        $query     = "SELECT * FROM term t \r
      LEFT JOIN term_i18n ti ON t.id=ti.id AND ti.culture=? \r
      WHERE taxonomy_id=?";
        $statement = QubitXMLImport::sqlQuery($query, array(
            $culture,
            $taxonomyId
        ));
        while ($term = $statement->fetch(PDO::FETCH_OBJ)) {
            $terms[$term->id] = $term;
        }
        return $terms;
    }
    
    /**
     * Get the ID of a taxonomy, optionally specifying culture
     *
     * @param integer $taxonomyId  taxonomy ID
     * @param string $culture  culture code (defaulting to English)
     *
     * @return array  array of term IDs and their respective names
     */
    public static function getTaxonomyIDTerms($taxonomyId, $culture = 'en')
    {
        $terms     = array();
        $query     = "SELECT id FROM term_i18n t \r
      LEFT JOIN term_i18n ti ON t.id=ti.id AND ti.culture=? \r
      WHERE taxonomy_id=?";
        $statement = QubitXMLImport::sqlQuery($query, array(
            $culture,
            $taxonomyId
        ));
        while ($term = $statement->fetch(PDO::FETCH_OBJ)) {
            $terms[$term->id] = $term;
        }
        return $terms;
    }
    
    /**
     * Issue an SQL query
     *
     * @param string $query  SQL query
     * @param string $params  values to map to placeholders (optional)
     *
     * @return object  database statement object
     */
    public static function sqlQuery($query, $params = array())
    {
        $connection = Propel::getConnection();
        $statement  = $connection->prepare($query);
        for ($index = 0; $index < count($params); $index++) {
            $statement->bindValue($index + 1, $params[$index]);
        }
        $statement->execute();
        return $statement;
    }

    /**
     * Map a value to its corresponding term name then return the term ID
     * corresponding to the term name
     *
     * @param string $description  		description of subject (for error output)
     * @param string $value  				value that needs to be mapped to a term ID
     * @param array $valueToTermNameMap  	array mapping possible values to term names
     * @param array $terms  				array mapping term IDs to term names
     *
     * @return integer term ID
     */
    public static function translateNameToTermId($description, $value, $valueToTermNameMap, $terms)
    {
        if (isset($valueToTermNameMap[$value]) || count($valueToTermNameMap) == 0) {
            $termName = (count($valueToTermNameMap)) ? $valueToTermNameMap[$value] : $value;
            /*
            echo "========================================================<br>";
            echo "Terms In=>".$value." <- Search Value=".$terms."<->valueToTermNameMap=".count($valueToTermNameMap)."<br>";
            echo "Terms In=".count($termName)."<br>";
            foreach ($termName as &$value) {
            
            echo "Value=".$value."<br>";
            }
            echo "========================================================<br>";
            */
            if (in_array($terms, $termName)) {
                $termId = array_search($terms, $termName);
                return $termId;
            } else {
                QubitXMLImport::addLog($termName, 'Could not find ' . $termName . ' - ' . $value . ' in ' . $description . ' terms array.', get_class($this), true);
                $valuesList = "<br>========================================================<br>Possible values are<br>Search Value=" . $terms . "<br>Terms choices=" . count($termName) . "<br>";
                
                foreach ($termName as &$value) {
                    $valuesList = $valuesList . "Value=" . $value . "<br>";
                }
                $valuesList = $valuesList . "========================================================<br>";
                
                throw new sfException('Could not find ' . $termName . ' - ' . $terms . ' in ' . $description . ' terms array. ' . $valuesList);
            }
        } else {
            QubitXMLImport::addLog($value, 'Could not find a way to handle ' . $description . ' value "' . $value . '".', get_class($this), true);
            throw new sfException('Could not find a way to handle ' . $description . ' value "' . $value . '".');
        }
    }

    /**
     * Map a value to its corresponding term name then return the term ID
     * corresponding to the term name
     *
     * @param string $description  		description of subject (for error output)
     * @param string $value  				value that needs to be mapped to a term ID
     * @param array $valueToTermNameMap  	array mapping possible values to term names
     * @param array $terms  				array mapping term IDs to term names
     *
     * @return integer term ID
     */
    public static function translateNameToTermId2($description, $termCriteria, $value)
    {
        $termsList = QubitTerm::getAccessObjectValues($termCriteria);
        $terms     = array();
        foreach ($termsList as $item) {
            $terms[] = $item;
        }
        if (isset($terms) || count($terms) == 0) {
            $termName = (count($terms)) ? $terms : $value;
            /*
            echo "========================================================<br>";
            echo "Description=>".$description."<br>";
            echo "Terms In=>".$value."<->valueToTermNameMap=".count($terms)."<br>";
            echo "Terms Count=".count($termName)."<br>";
            foreach ($termName as &$values) {
            
            echo "Value=".$values." Value ID=".$values->id."<br>";
            }
            echo "========================================================<br>";
            */
            if (in_array($value, $termName)) {
                $termId = array_search($value, $termName);
                $termId = $terms[$termId]->id;
                return $termId;
            } else {
                QubitXMLImport::addLog($value, 'Could not find ' . $termName . ' - ' . $value . ' in ' . $description . ' terms array.', get_class($this), true);
                throw new sfException('Could not find ' . $termName . ' - ' . $value . ' in ' . $description . ' terms array.');
            }
        } else {
            QubitXMLImport::addLog($value, 'Could not find a way to handle ' . $description . ' value "' . $value . '".', get_class($this), true);
            throw new sfException('Could not find a way to handle ' . $description . ' value "' . $value . '".');
        }
    }
    
    /**
     * Attempt to parse date from non-machine-readable text,
     * returning false upon failure and logging failures.
     *
     * @param string $dateText  description of date
     *
     * @return string  date in YYYY-MM-DD format
     */
    public function parseDateLoggingErrors($dateText)
    {
        $date = $this->parseDate($dateText);
        if ($date) {
            return $date;
        } else {
            $this->logError('Could not parse date: ' . $dateText);
            return false;
        }
    }
    
    /**
     * Save Contact Information after Actor was added
     *
     * @return Contact
     */
    public static function addUpdateContact($actorId, $addressValues)
    {
        $contactInfo = QubitContactInformation::getByActorId($actorId);
        if (!isset($contactInfo)) {
            $contactInfo = new QubitContactInformation;
        }
        $contactInfo->primaryContact = $addressValues['primaryContact'];
        $strTitle                    = str_replace(' ', '', trim($addressValues['title']));
        $pos                         = strpos($strTitle, ".");
        
        if ($pos === false) {
            $strTitle = $strTitle . ".";
        }
        
        $titleValue                 = QubitTermi18n::getIdByNameOnly($strTitle);
        $contactInfo->title         = $titleValue;
        $contactInfo->contactPerson = $addressValues['contactPerson'];
        $contactInfo->position      = $addressValues['position'];
        $contactInfo->email         = $addressValues['email'];
        $contactInfo->fax           = $addressValues['fax'];
        $contactInfo->telephone     = $addressValues['telephone'];
        $contactInfo->cell          = $addressValues['cell'];
        $contactInfo->website       = $addressValues['website'];
        $contactInfo->streetAddress = $addressValues['streetAddress'];
        $contactInfo->city          = $addressValues['city'];
        $contactInfo->region        = $addressValues['region'];
        if ($addressValues['countryCode'] == null || $addressValues['countryCode'] == "") {
            $addressValues['countryCode'] = "ZA";
        }
        $contactInfo->countryCode   = $addressValues['countryCode'];
        $contactInfo->postalCode    = $addressValues['postalCode'];
        $contactInfo->postalAddress = $addressValues['postalAddress'];
        $contactInfo->postalCity    = $addressValues['postalCity'];
        $contactInfo->postalRegion  = $addressValues['postalRegion'];
        if ($addressValues['postalCountryCode'] == null || $addressValues['postalCountryCode'] == "") {
            $contactInfo->postalCountryCode = "ZA";
        } else {
            $contactInfo->postalCountryCode = $addressValues['postalCountryCode'];
        }
        $contactInfo->postalPostCode = $addressValues['postalPostCode'];
        $contactInfo->latitude       = $addressValues['latitude'];
        $contactInfo->longitude      = $addressValues['longitude'];
        $contactInfo->note           = $addressValues['note'];
        $contactInfo->contactType    = $addressValues['contacttype'];
        $contactInfo->actorId        = $actorId;
        $contactInfo->save();
        return $contactInfo;
    }
    
    /**
     * Create a relation between two Qubit objects
     *
     * @param integer $subjectId  subject ID
     * @param integer $objectId  object ID
     * @param integer $typeId  relation type
     *
     * @return QubitRelation  created relation
     */
    public static function createRelation($subjectId, $objectId, $typeId)
    {
        $relation            = new QubitRelation;
        $relation->subjectId = $subjectId;
        $relation->objectId  = $objectId;
        $relation->typeId    = $typeId;
        $relation->save();
        return $relation;
    }

    /**
     * Replace </lb> tags for '\n'
     *
     * @return node value without linebreaks tags
     */
    public static function replaceLineBreaks($node)
    {
        $nodeValue   = '';
        $fieldsArray = array(
            'extent',
            'physfacet',
            'dimensions'
        );
        
        foreach ($node->childNodes as $child) {
            if ($child->nodeName == 'lb') {
                $nodeValue .= "\n";
            } else if (in_array($child->tagName, $fieldsArray)) {
                foreach ($child->childNodes as $childNode) {
                    if ($childNode->nodeName == 'lb') {
                        $nodeValue .= "\n";
                    } else {
                        $nodeValue .= preg_replace('/[\n\r\s]+/', ' ', $childNode->nodeValue);
                    }
                }
            } else {
                $nodeValue .= preg_replace('/[\n\r\s]+/', ' ', $child->nodeValue);
            }
        }
        
        return $nodeValue;
    }
    
    /**
     * Make sure to remove any default namespaces from
     * EAD tags. See issue #7280 for details.
     */
    private function removeDefaultNamespace($xml)
    {
        return preg_replace('/(<ead.*?)xmlns="[^"]*"\s+(.*?>)/', '${1}${2}', $xml, 1);
    }
    
    /**
     * Remove all XML comments from the document.
     */
    private function stripComments($doc)
    {
        $xp    = new DOMXPath($doc);
        $nodes = $xp->query('//comment()');
        
        for ($i = 0; $i < $nodes->length; $i++) {
            $nodes->item($i)->parentNode->removeChild($nodes->item($i));
        }
    }
    
    /**
     * Normalize node, replaces <p> and <lb/>
     *
     * @return node value normalized
     */
    public static function normalizeNodeValue($node)
    {
        $nodeValue = '';
        
        if (!($node instanceof DOMAttr)) {
            $nodeList = $node->getElementsByTagName('p');
            
            if (0 < $nodeList->length) {
                $i = 0;
                foreach ($nodeList as $pNode) {
                    if ($i++ == 0) {
                        $nodeValue .= self::replaceLineBreaks($pNode);
                    } else {
                        $nodeValue .= "\n\n" . self::replaceLineBreaks($pNode);
                    }
                }
            } else {
                $nodeValue .= self::replaceLineBreaks($node);
            }
        } else {
            $nodeValue .= $node->nodeValue;
        }
        
        return trim($nodeValue);
    }
    
    /**
     * Track objects to be reassociated with an event on import.
     * This is used to associate actors and places with events for
     * RAD-style events.
     */
    private function trackEvent($object, $node)
    {
        $kind = $node->nodeName;
        if ($kind === 'geogname') {
            $key = 'place';
        } else if ($kind === 'unitdate') {
            $key = 'event';
        } else if (in_array($kind, array(
                'name',
                'persname',
                'corpname',
                'famname'
            ))) {
            $key = 'actor';
        } else {
            return;
        }
        
        $id = $node->getAttribute('id');
        if (!empty($id)) {
            // The ID value is suffixed with its category, e.g. 384_place
            // This is because `id` is required to be unique within the entire
            // document.
            //
            // First check if the ID is actually an AtoM tag, since the ID
            // may exist for another purpose.
            if (substr($id, 0, 4) == 'atom' && substr($id, -strlen($key)) == $key) {
                // chop off atom_ prefix and the _category suffix
                $id = substr($id, 5, -strlen($key) - 1);
                array_key_exists($id, $this->events) || $this->events[$id] = array();
                $this->events[$id][$key] = $object;
            }
        }
    }
    
    /**
     * Reattach all places and actors to their respective events,
     * using the $events map on this object.
     */
    private function associateEvents()
    {
        foreach ($this->events as $id => $values) {
            $event = $values['event'];
            
            if (empty($event)) {
                continue;
            }
            
            $place = array_key_exists('place', $values) ? $values['place'] : null;
            $actor = array_key_exists('actor', $values) ? $values['actor'] : null;
            
            if ($place) {
                $otr              = new QubitObjectTermRelation;
                $otr->termId      = $place->id;
                $otr->indexOnSave = false;
                
                $event->objectTermRelationsRelatedByobjectId[] = $otr;
            }
            
            if ($actor) {
                $event->actorId = $actor->id;
            }
        }
    }
    
    /**
     * Run presave logic (only available for information objects and actors)
     *
     * This method will determine if a new record should be created, skipped or replaced
     * based on the update, skip and limit options
     *
     * @param mixed  QubitInformationObject or QubitActor to save
     * @return bool  true to save the record, false to skip saving it
     */
    private function handlePreSaveLogic($resource)
    {
        // Populate variables based on resource class
        switch (get_class($resource)) {
            case 'QubitInformationObject':
                // Short circuit if 'delete-and-replace' is set with 'skip-unmatched' if this is
                // not the root object. After the top level record is loaded, there will be
                // nothing to match against as deleteFullHierarchy will have been called on
                // the first iteration. Load all recs in this situation as long as the top
                // level record matches. Currently the only update mode is 'delete-and-replace'
                // and the only match option that works with 'delete-and-replace' is
                // 'skip-unmatched'. This will need to be modified if additional matching
                // options are added.
                if ($this->options['update'] === 'delete-and-replace' && $this->options['skip-unmatched'] && $this->rootObject !== $resource) {
                    return true;
                }
                
                $title                   = $resource->title;
                $passesLimitFunctionName = 'passesLimitOptionForIo';
                $deleteFunctionName      = 'deleteFullHierarchy';
                
                $matchId = QubitInformationObject::getByTitleIdentifierAndRepo($resource->identifier, $resource->title, $resource->repository->authorizedFormOfName);
                
                if ($matchId) {
                    $matchResource = QubitInformationObject::getById($matchId);
                }
                
                // If resource not found, try matching against keymap table. eadUrl is
                // unique to EAD file, but not unique to each record in the file.
                // Matching on keymap will only make sense for the top level record.
                if (!isset($matchResource) && $this->eadUrl && $this->rootObject === $resource) {
                    $criteria = new Criteria;
                    $criteria->add(QubitKeymap::SOURCE_ID, $this->eadUrl);
                    $criteria->add(QubitKeymap::SOURCE_NAME, $this->sourceName);
                    $criteria->add(QubitKeymap::TARGET_NAME, 'information_object');
                    
                    if (null !== $keymap = QubitKeymap::getOne($criteria)) {
                        $matchResource = QubitInformationObject::getById($keymap->targetId);
                    }
                }
                
                break;
            
            case 'QubitActor':
                $title                   = $resource->authorizedFormOfName;
                $passesLimitFunctionName = 'passesLimitOptionForActor';
                $deleteFunctionName      = 'delete';
                
                $query = "SELECT object.id
          FROM object JOIN actor_i18n i18n
          ON object.id = i18n.id
          WHERE i18n.authorized_form_of_name = ?
          AND object.class_name = 'QubitActor';";
                
                $matchId = QubitPdo::fetchColumn($query, array(
                    $resource->authorizedFormOfName
                ));
                
                if ($matchId) {
                    $matchResource = QubitActor::getById($matchId);
                }
                
                break;
            
            default:
                // Create new record for not supported resources
                $this->errors[] = $this->i18n->__('Pre-save logic not supported for %class_name%', array(
                    '%class_name%' => get_class($resource)
                ));
                return true;
        }
        
        // No need to check match if we're not updating nor skipping matches
        if (!$this->options['update'] && !$this->options['skip-matched']) {
            $this->errors[] = $this->i18n->__('Creating a new record: %title%', array(
                '%title%' => $title
            ));
            return true;
        }
        
        // Match found, but not updating and skipping matches
        if (isset($matchResource) && !$this->options['update'] && $this->options['skip-matched']) {
            $this->errors[] = $this->i18n->__('Found duplicated record for %title%, skipping', array(
                '%title%' => $title
            ));
            return false;
        }
        
        // No match found and updating with skip unmatched
        if (!isset($matchResource) && $this->options['update'] && $this->options['skip-unmatched']) {
            $this->errors[] = $this->i18n->__('No match found for %title%, skipping', array(
                '%title%' => $title
            ));
            return false;
        }
        
        // Match found and updating, check limit option
        if (isset($matchResource) && $this->options['update']) {
            if (!call_user_func(array(
                $this,
                $passesLimitFunctionName
            ), $matchResource)) {
                $this->errors[] = $this->i18n->__('Match found for %title% outside the limit, skipping', array(
                    '%title%' => $title
                ));
                return false;
            } else {
                $this->errors[] = $this->i18n->__('Deleting and replacing record: %title%', array(
                    '%title%' => $title
                ));
                call_user_func(array(
                    $matchResource,
                    $deleteFunctionName
                ));
                return true;
            }
        }
        
        // Match not found when not updating and skipping matches
        $this->errors[] = $this->i18n->__('Creating a new record: %title%', array(
            '%title%' => $title
        ));
        return true;
    }
    
    /**
     * Check if an information object passes the limit option. Passes when:
     * - The limit option is not set
     * - The limit option is the slug of the resource's collection root
     * - The limit option is the slug of the resource's inherit repository
     *
     * @param QubitInformationObject $io  The information object to check
     * @return bool  The information object passes the limit option or not
     * @throws sfException  When the limit option is not accepted
     */
    private function passesLimitOptionForIo($io)
    {
        if (false === $limit = $this->getLimitIdAndClassName()) {
            return true;
        }
        
        switch ($limit->class_name) {
            case 'QubitRepository':
                $repo = $io->getRepository(array(
                    'inherit' => true
                ));
                return isset($repo) && $repo->id == $limit->id;
            
            case 'QubitInformationObject':
                $collectionRoot = $io->getCollectionRoot();
                return isset($collectionRoot) && $collectionRoot->id == $limit->id;
            
            default:
                throw new sfException($this->i18n->__('Slugs from %class_name% are not accepted as limit option for information objects', array(
                    '%class_name%' => $limit->class_name
                )));
        }
    }
    
    /**
     * Add Term Relation to Information Object
     *
     */
    public static function updateTermRelation($object_id, $term_id)
    {
        $relations = QubitObjectTermRelation::getByObjectId($object_id);
        if (isset($relations)) {
            foreach ($relations as $item) {
                for ($x = 0; $x < count($term_id); $x++) {
                    if ($item->termId == $term_id[$x]) {
                        // delete entry to later create it again
                        try {
                            // this function will pass back a value
                            $result = $item->delete();
                            
                            // if the result is an error, choose what to do with it
                            if ($result instanceof Exception) {
                                print "result = NULL<br>";
                                $result = NULL; // ignore the error
                            }
                        }
                        
                        // catch normal PHP Exceptions
                        catch (Exception $e) {
                            // handle normal exceptions
                            if (strpos($e, "This object has already been deleted") == 0) {
                                print strpos($e, "This object has already been deleted") . "<br>";
                                print "An error has accurred: " . $e . "<br>";
                            }
                        }
                    }
                }
            }
        }
        for ($x = 0; $x < count($term_id); $x++) {
            $relation           = new QubitObjectTermRelation;
            $relation->objectId = $object_id;
            $relation->termId   = $term_id[$x];
            $relation->save();
        }
    }
    
    /**
     * Add Maintenance Note
     *
     * @return
     */
    public static function addMaintenanceNote($authId, $typeId, $maintenanceNote, $noteTypes)
    {
        // Add Maintenance Note
        $typeId = array_search('Maintenance note', $noteTypes);
        // update if note exist otherwise create net note
        if (null == ($maintenanceNotes = QubitNote::getByIdAndType($authId, $typeId))) {
            $maintenanceNotes = new QubitNote;
        }
        $maintenanceNotes->typeId   = $typeId;
        $maintenanceNotes->objectId = $authId;
        $maintenanceNotes->content  = $maintenanceNote;
        $maintenanceNotes->save();
    }
    
    /**
     * Add Aliases
     *
     * @return
     */
    public static function addAliases($authId, $normalizedType, $data)
    {
        $typeIds        = array(
            'parallel' => QubitTerm::PARALLEL_FORM_OF_NAME_ID,
            'standardized' => QubitTerm::STANDARDIZED_FORM_OF_NAME_ID,
            'other' => QubitTerm::OTHER_FORM_OF_NAME_ID
        );
        $normalizedType = strtolower($normalizedType);
        if ($typeIds[$normalizedType]) {
            $typeId = $typeIds[$normalizedType];
        } else {
            QubitXMLImport::addLog($normalizedType, 'Invalid alias type"' . $normalizedType . '".', get_class($this), true);
            throw new sfException('Invalid alias type"' . $normalizedType . '".');
        }
        for ($x = 0; $x <= count($data); $x++) {
            // add other name if not already existing
            if (QubitOtherName::getByObjectIdAndTypeAndName($authId, $typeId, $data[$x]) == null) {
                if ($data[$x] != "") {
                    $otherName           = new QubitOtherName;
                    $otherName->objectId = $authId;
                    $otherName->name     = $data[$x];
                    $otherName->typeId   = $typeId;
                    $otherName->save();
                }
            }
        }
        unset($data);
    }

    /**
     * Add Relations
     *
     * @return
     */
    public static function addRelations($authId, $category, $targetAuthorizedFormOfName, $description, $date, $startDate, $endDate, $actorRelationTypes)
    {
        // determine type ID of relationship type
        $relationTypeId = array_search($category, $actorRelationTypes);
        if (!$relationTypeId) {
            QubitXMLImport::addLog($category, 'Unknown relationship type (Category and relationTypeId:' . $category . " - " . $relationTypeId, get_class($this), true);
            throw new sfException('Unknown relationship type :' . $category);
        } else {
            // determine type ID of relationship type
            // add relationship, with date/startdate/enddate/description
            if (($targetActor = BaseActori18n::getByAuthFormOfName($targetAuthorizedFormOfName)) == null) {
                $actorObject = new QubitActor;
                if (isset($actorObject)) {
                    $actorObject->actorImportId        = $targetAuthorizedFormOfName;
                    $actorObject->authorizedFormOfName = $targetAuthorizedFormOfName;
                    $actorObject->save();
                }
                
                $error = 'Target Actor (Relation to another Entity) "' . $targetAuthorizedFormOfName . '" does not exist - creating';
                QubitXMLImport::addLog($targetAuthorizedFormOfName, $error, "QubitActor", true);
                
                //throw new sfException($error); //Write to Error Log File!!!! to fix
                // if Relation does not exist creat new else update
                // if target relation does not exist????
                if (($relation = QubitRelation::getBySubjectAndObjectAndType($authId, $actorObject->id, $relationTypeId)) == null) {
                    $relation = new QubitRelation;
                }
                $relation->subjectId = $authId;
                $relation->objectId  = $actorObject->id;
                $relation->typeId    = $relationTypeId;
                if ($date != "") {
                    $relation->date = $date;
                }
                if ($startDate != "") {
                    $relation->startDate = $startDate;
                }
                if ($endDate != "") {
                    $relation->endDate = $endDate;
                }
                if ($description != "") {
                    $relation->description = $description;
                }
                $relation->save();
                
            } else {
                // if Relation does not exist creat new else update
                // if target relation does not exist????
                if (($relation = QubitRelation::getBySubjectAndObjectAndType($authId, $targetActor->id, $relationTypeId)) == null) {
                    $relation = new QubitRelation;
                }
                $relation->subjectId = $authId;
                $relation->objectId  = $targetActor->id;
                $relation->typeId    = $relationTypeId;
                if ($date != "") {
                    $relation->date = $date;
                }
                if ($startDate != "") {
                    $relation->startDate = $startDate;
                }
                if ($endDate != "") {
                    $relation->endDate = $endDate;
                }
                if ($description != "") {
                    $relation->description = $description;
                }
                $relation->save();
            }
        }
    }

    /**
     * Add Batch Publish .xml for Mainframe Import
     *
     * @return
     */
    public static function addBatchPublishXML($accessObject, $publishObjectID)
    {
        if (($publishObject = QubitInformationObject::getByID($publishObjectID)) !== null) {
            $publishname       = "Publish_" . $publishObject->id . "_" . date("Y-m-dHis") . ".xml";
            $imp               = new DOMImplementation;
            // Creates a DOMDocumentType instance
            $dtd               = $imp->createDocumentType('publish', 'SYSTEM', 'publish.dtd');
            // Creates a DOMDocument instance
            $doc               = $imp->createDocument("", "", $dtd);
            // we want a nice output
            $doc->formatOutput = true;
            // Set other properties
            $doc->encoding     = 'UTF-8';
            $doc->standalone   = false;
            $root              = $doc->createElement('publish');
            $root              = $doc->appendChild($root);
            $publishid         = $doc->createElement('publishid');
            $publishid         = $root->appendChild($publishid);
            $attr              = $doc->createAttribute('countrycode');
            $attr->appendChild($doc->createTextNode('ZA'));
            $publishid->appendChild($attr);
            $attr = $doc->createAttribute('encodinganalog');
            $attr->appendChild($doc->createTextNode('identifier'));
            $publishid->appendChild($attr);
            $attr = $doc->createAttribute('repositorycode');
            if (0 < strlen($value = $publishObject->getRepository())) {
                $attr->appendChild($doc->createTextNode($value->identifier));
            } else {
                $attr->appendChild($doc->createTextNode(''));
            }
            $publishid->appendChild($attr);
            $attr = $doc->createAttribute('mainagencycode');
            if (0 < strlen($repositoryValue = $publishObject->getRepository())) {
                $attr->appendChild($doc->createTextNode($repositoryValue));
            } else {
                $attr->appendChild($doc->createTextNode(''));
            }
            $publishid->appendChild($attr);
            $archdesc = $doc->createElement('archdesc');
            $archdesc = $root->appendChild($archdesc);
            $attr     = $doc->createAttribute('level');
            $attr->appendChild($doc->createTextNode('item'));
            $archdesc->appendChild($attr);
            $did       = $doc->createElement('did');
            $did       = $archdesc->appendChild($did);
            $uniqueid  = $doc->createElement('uniqueid');
            $uniqueid  = $did->appendChild($uniqueid);
            $text      = $doc->createTextNode($publishObject->identifier);
            $text      = $uniqueid->appendChild($text);
            $unittitle = $doc->createElement('unittitle');
            $unittitle = $did->appendChild($unittitle);
            $text      = $doc->createTextNode($publishObject->title);
            $text      = $unittitle->appendChild($text);
            $unitid    = $doc->createElement('unitid');
            $unitid    = $did->appendChild($unitid);
            $text      = $doc->createTextNode($publishObject->identifier);
            $text      = $unitid->appendChild($text);
            $startDate = "";
            $endDate   = "";
            foreach ($publishObject->events as $sourceEvent) {
                $startDate = $sourceEvent->startDate;
                $endDate   = $sourceEvent->endDate;
            }
            $unitdatestart = $doc->createElement('unitdatestart');
            $unitdatestart = $did->appendChild($unitdatestart);
            $text          = $doc->createTextNode($startDate);
            $text          = $unitdatestart->appendChild($text);
            $unitdateend   = $doc->createElement('unitdateend');
            $unitdateend   = $did->appendChild($unitdateend);
            $text          = $doc->createTextNode($endDate);
            $text          = $unitdateend->appendChild($text);
            $physdesc      = $doc->createElement('physdesc');
            $physdesc      = $did->appendChild($physdesc);
            $extent        = $doc->createElement('extent');
            $extent        = $physdesc->appendChild($extent);
            if (0 < strlen($value = $publishObject->getExtentAndMedium(array(
                'cultureFallback' => true
            )))) {
                $text = $doc->createTextNode($value);
            } else {
                $text = $doc->createTextNode('');
            }
            $text       = $extent->appendChild($text);
            $source     = $doc->createElement('source');
            $source     = $physdesc->appendChild($source);
            $text       = $doc->createTextNode($publishObject->sources);
            $text       = $source->appendChild($text);
            $partno     = $doc->createElement('partno');
            $partno     = $physdesc->appendChild($partno);
            $text       = $doc->createTextNode($publishObject->partNo);
            $text       = $partno->appendChild($text);
            $format     = $doc->createElement('archivetype');
            $format     = $physdesc->appendChild($format);
            $formatName = "";
            if (isset($publishObject->formatId)) {
                $text = QubitTermI18n::getNameById($publishObject->formatId, QubitTaxonomy::FORMATS);
                if (isset($text)) {
                    $formatName = $text->name;
                } else {
                    $formatName = "";
                }
            } else {
                $formatName = "";
            }
            $text          = $doc->createTextNode($formatName);
            $text          = $format->appendChild($text);
            $filereference = $doc->createElement('filereference');
            $filereference = $physdesc->appendChild($filereference);
            $text          = $doc->createTextNode($publishObject->locationOfOriginals);
            $text          = $filereference->appendChild($text);
            $repository    = $doc->createElement('repository');
            $repository    = $did->appendChild($repository);
            $corpid        = $doc->createElement('corpid');
            $corpid        = $repository->appendChild($corpid);
            if (0 < strlen($repositoryValue = $publishObject->getRepository())) {
                $text = $doc->createTextNode($repositoryValue->identifier);
            } else {
                $text = $doc->createTextNode('');
            }
            $text     = $corpid->appendChild($text);
            $corpname = $doc->createElement('corpname');
            $corpname = $repository->appendChild($corpname);
            $text     = $doc->createTextNode($repositoryValue);
            $text     = $corpname->appendChild($text);
            //$scopeandcontent = $doc->createElement('scopeandcontent');
            //$scopeandcontent = $repository->appendChild($scopeandcontent);
            //$text = $doc->createTextNode($publishObject->scopeAndContent);
            //$text = $scopeandcontent->appendChild($text);
            if (0 < count($notes = $publishObject->getNotesByType(array(
                'noteTypeId' => QubitTerm::ARCHIVIST_NOTE_ID
            )))) {
                foreach ($notes as $noteItem) {
                    if ($noteItem->getContent(array(
                        'cultureFallback' => true
                    )) != "") {
                        $note = $doc->createElement('note');
                        $note = $did->appendChild($note);
                        $text = $doc->createTextNode($noteItem->getContent(array(
                            'cultureFallback' => true
                        )));
                        $text = $note->appendChild($text);
                        $attr = $doc->createAttribute('type');
                        $attr->appendChild($doc->createTextNode('sourcesDescription'));
                        $note->appendChild($attr);
                    }
                }
            }
            if (0 < count($notes = $publishObject->getNotesByType(array(
                'noteTypeId' => QubitTerm::GENERAL_NOTE_ID
            )))) {
                foreach ($notes as $noteItem) {
                    if ($noteItem->getContent(array(
                        'cultureFallback' => true
                    )) != "") {
                        $note = $doc->createElement('note');
                        $note = $did->appendChild($note);
                        $text = $doc->createTextNode($noteItem->getContent(array(
                            'cultureFallback' => true
                        )));
                        $text = $note->appendChild($text);
                        $attr = $doc->createAttribute('type');
                        $attr->appendChild($doc->createTextNode('generalNote'));
                        $note->appendChild($attr);
                    }
                }
            }
            $preservationobject = $doc->createElement('preservationobject');
            $preservationobject = $did->appendChild($preservationobject);
            // mark as available
            $availability       = $doc->createElement('availability');
            $availability       = $preservationobject->appendChild($availability);
            $text               = $doc->createTextNode("Yes");
            $text               = $availability->appendChild($text);
            $publishPath        = QubitSetting::getByName('publish_path');
            if ($publishPath == null) {
                QubitXMLImport::addLog($publishPath, "No upload path defined. Contact support/administrator", get_class($this), true);
                throw new sfException(sfContext::getInstance()->i18n->__("No upload path defined. Contact support/administrator"));
            } else {
                $doc->save($publishPath . $publishname);
                $accessObj = QubitAccessObject::getById($accessObject);
                if (isset($accessObj)) {
                    $accessObj->published = true;
                    $accessObj->save();
                }
            }
        }
    }

    /**
     * Add Batch Publish .csv for Mainframe Import
     * "identifier|unitid|unittitle|remark|subject|source|fileReference|volume|partNumber|repository|repositoryName|repositoryCountry| 		 * dateStart|dateEnd|availabilityId|medium|corpId|repositoryType|creatorName|registryIdentifier|registry|filePath";
     * @return
     */
    public static function addBatchPublishCSV($accessObject, $publishObjectID, $CSVRecord, $csvfile)
    {
        //		$csvStringHeader = "identifier|unitid|unittitle|remark|subject|source|fileReference|volume|partNumber|repository|repositoryName|repositoryCountry|dateStart|dateEnd|availabilityId|medium|corpId|repositoryType|creatorName|filePath\n";
        if (($publishObject = QubitInformationObject::getByID($publishObjectID)) !== null) {
            $csvString = null;
            
            $cvsRepositoryCountryCode = str_replace('"', '""', ($CSVRecord['repositoryCountryCode']));
            $cvsRepositoryCode        = str_replace('"', '""', ($CSVRecord['repositorycode']));
            $cvsIdentifier            = str_replace('"', '""', ($CSVRecord['identifier']));
            $csvString                = $csvString . '"' . $cvsRepositoryCountryCode . " " . $cvsRepositoryCode . " " . $cvsIdentifier . '"|"';
            $cvsUnitid                = str_replace('"', '""', ($CSVRecord['unitid']));
            $csvString                = $csvString . $cvsUnitid . '"|"';
            $cvsTitle                 = str_replace('"', '""', ($CSVRecord['unittitle']));
            $cvsTitle                 = str_replace('+', chr(10) . chr(13) . "--", $cvsTitle);
            $csvString                = $csvString . $cvsTitle . '"|"';
            $cvsDateType              = str_replace('"', '""', ($CSVRecord['dateType']));
            $csvString                = $csvString . $cvsDateType . '"|"';
            $cvsUnitDate              = str_replace('"', '""', ($CSVRecord['unitdate']));
            $csvString                = $csvString . $cvsUnitDate . '"|"';
            $cvsStartDate             = str_replace('"', '""', ($CSVRecord['startDate']));
            $csvString                = $csvString . $cvsStartDate . '"|"';
            $cvsEndDate               = str_replace('"', '""', ($CSVRecord['endDate']));
            $csvString                = $csvString . $cvsEndDate . '"|"';
            $cvsLevel                 = str_replace('"', '""', ($CSVRecord['level']));
            $csvString                = $csvString . $cvsLevel . '"|"';
            $cvsExtent                = str_replace('"', '""', ($CSVRecord['extent']));
            $csvString                = $csvString . $cvsExtent . '"|"';
            $cvsSource                = str_replace('"', '""', ($CSVRecord['source']));
            $csvString                = $csvString . $cvsSource . '"|"';
            $cvsReferenceNumber       = str_replace('"', '""', ($CSVRecord['referenceNumber']));
            $csvString                = $csvString . $cvsReferenceNumber . '"|"';
            $cvsVolumeNumber          = str_replace('"', '""', ($CSVRecord['volumeNumber']));
            $csvString                = $csvString . $cvsVolumeNumber . '"|"';
            $cvsPartNumber            = str_replace('"', '""', ($CSVRecord['partNumber']));
            $csvString                = $csvString . $cvsPartNumber . '"|"';
            $cvsCorpname              = str_replace('"', '""', $CSVRecord['corpname']);
            $csvString                = $csvString . $cvsCorpname . '"|"';
            $csvString                = $csvString . $cvsRepositoryCode . '"|"';
            $csvString                = $csvString . $cvsRepositoryCountryCode . '"|"';
            $cvsRepositoryName        = str_replace('"', '""', ($CSVRecord['repocorpname']));
            $csvString                = $csvString . $cvsRepositoryName . '"|"';
            $cvsCustodhist            = str_replace('"', '""', ($CSVRecord['custodhist']));
            $csvString                = $csvString . $cvsCustodhist . '"|"';
            $cvsScopecontent          = str_replace('"', '""', ($CSVRecord['scopecontent']));
            $csvString                = $csvString . $cvsScopecontent . '"|"';
            $cvsAppraisal             = str_replace('"', '""', ($CSVRecord['appraisal']));
            $csvString                = $csvString . $cvsAppraisal . '"|"';
            $cvsAccruals              = str_replace('"', '""', ($CSVRecord['accruals']));
            $csvString                = $csvString . $cvsAccruals . '"|"';
            $cvsArrangement           = str_replace('"', '""', ($CSVRecord['arrangement']));
            $csvString                = $csvString . $cvsArrangement . '"|"';
            $cvsAccessrestrict        = str_replace('"', '""', ($CSVRecord['accessrestrict']));
            $csvString                = $csvString . $cvsAccessrestrict . '"|"';
            $cvsUserestrict           = str_replace('"', '""', ($CSVRecord['userestrict']));
            $csvString                = $csvString . $cvsUserestrict . '"|"';
            $cvsLangcode              = str_replace('"', '""', ($CSVRecord['langcode']));
            $csvString                = $csvString . $cvsLangcode . '"|"';
            $cvsScriptcode            = str_replace('"', '""', ($CSVRecord['scriptcode']));
            $csvString                = $csvString . $cvsScriptcode . '"|"';
            $cvsLangmaterial          = str_replace('"', '""', ($CSVRecord['langmaterial']));
            $csvString                = $csvString . $cvsLangmaterial . '"|"';
            $cvsPhystech              = str_replace('"', '""', ($CSVRecord['phystech']));
            $csvString                = $csvString . $cvsPhystech . '"|"';
            $cvsOtherfindaid          = str_replace('"', '""', ($CSVRecord['otherfindaid']));
            $csvString                = $csvString . $cvsOtherfindaid . '"|"';
            $cvsOriginalsloc          = str_replace('"', '""', ($CSVRecord['originalsloc']));
            $csvString                = $csvString . $cvsOriginalsloc . '"|"';
            $cvsAltformavail          = str_replace('"', '""', ($CSVRecord['altformavail']));
            $csvString                = $csvString . $cvsAltformavail . '"|"';
            $cvsRelatedmaterial       = str_replace('"', '""', ($CSVRecord['relatedmaterial']));
            $csvString                = $csvString . $cvsRelatedmaterial . '"|"';
            $cvsRelateddescriptions   = str_replace('"', '""', ($CSVRecord['relateddescriptions']));
            $csvString                = $csvString . $cvsRelateddescriptions . '"|"';
            $cvsBibliography          = str_replace('"', '""', ($CSVRecord['bibliography']));
            $csvString                = $csvString . $cvsBibliography . '"|"';
            $cvsNote                  = str_replace('"', '""', ($CSVRecord['note']));
            $cvsNote                  = str_replace('+', chr(10) . chr(13) . "--", $cvsNote);
            $csvString                = $csvString . $cvsNote . '"|"';
            $cvsPublicationnote       = str_replace('"', '""', ($CSVRecord['publicationnote']));
            $csvString                = $csvString . $cvsPublicationnote . '"|"';
            $cvsArchivistnote         = str_replace('"', '""', ($CSVRecord['archivistnote']));
            $cvsArchivistnote         = str_replace('+', chr(10) . chr(13) . "--", $cvsArchivistnote);
            $csvString                = $csvString . $cvsArchivistnote . '"|"';
            $cvsSubject               = str_replace('"', '""', ($CSVRecord['subject']));
            $csvString                = $csvString . $cvsSubject . '"|"';
            $cvsGeogname              = str_replace('"', '""', ($CSVRecord['geogname']));
            $csvString                = $csvString . $cvsGeogname . '"|"';
            $cvsName                  = str_replace('"', '""', ($CSVRecord['name']));
            $csvString                = $csvString . $cvsName . '"|"';
            $cvsDescriptionIdentifier = str_replace('"', '""', ($CSVRecord['descriptionIdentifier']));
            $csvString                = $csvString . $cvsDescriptionIdentifier . '"|"';
            $cvsInstitutionIdentifier = str_replace('"', '""', ($CSVRecord['institutionIdentifier']));
            $csvString                = $csvString . $cvsInstitutionIdentifier . '"|"';
            $cvsRules                 = str_replace('"', '""', ($CSVRecord['rules']));
            $csvString                = $csvString . $cvsRules . '"|"';
            $cvsStatusDescription     = str_replace('"', '""', ($CSVRecord['statusDescription']));
            $csvString                = $csvString . $cvsStatusDescription . '"|"';
            $cvsLevelOfDetail         = str_replace('"', '""', ($CSVRecord['levelOfDetail']));
            $csvString                = $csvString . $cvsLevelOfDetail . '"|"';
            $cvsDate                  = str_replace('"', '""', ($CSVRecord['date']));
            $csvString                = $csvString . $cvsDate . '"|"';
            $cvsDesclanguage          = str_replace('"', '""', ($CSVRecord['desclanguage']));
            $csvString                = $csvString . $cvsDesclanguage . '"|"';
            $cvsDescscript            = str_replace('"', '""', ($CSVRecord['descscript']));
            $csvString                = $csvString . $cvsDescscript . '"|"';
            $cvsLangcode              = str_replace('"', '""', ($CSVRecord['langcode']));
            $csvString                = $csvString . $cvsLangcode . '"|"';
            $cvsScriptcode            = str_replace('"', '""', ($CSVRecord['scriptcode']));
            $csvString                = $csvString . $cvsScriptcode . '"|"';
            $cvsRecordtype            = str_replace('"', '""', ($CSVRecord['recordtype']));
            $csvString                = $csvString . $cvsRecordtype . '"|"';
            $cvsSize                  = str_replace('"', '""', ($CSVRecord['size']));
            $csvString                = $csvString . $cvsSize . '"|"';
            $cvsType                  = str_replace('"', '""', ($CSVRecord['type']));
            $csvString                = $csvString . $cvsType . '"|"';
            $cvsClassification        = str_replace('"', '""', ($CSVRecord['classification']));
            $csvString                = $csvString . $cvsClassification . '"|"';
            $cvsAvailabilityId        = str_replace('"', '""', ($CSVRecord['availabilityId']));
            $csvString                = $csvString . $cvsAvailabilityId . '"|"';
            $cvsRegistryIdentifier    = str_replace('"', '""', ($CSVRecord['registryIdentifier']));
            $csvString                = $csvString . $cvsRegistryIdentifier . '"|"';
            $cvsRegistry              = str_replace('"', '""', ($CSVRecord['registry']));
            $csvString                = $csvString . $cvsRegistry . '"|"';
            $cvsFilePath              = str_replace('"', '""', ($CSVRecord['filePath']));
            if ($cvsFilePath != "") {
                $csvString = $csvString . "attachments/" . $cvsFilePath . '"|"';
            } else {
                $csvString = $csvString . "" . $cvsFilePath . '"|"';
            }
            
            $cvsParentId = str_replace('"', '""', ($CSVRecord['parentid']));
            $csvString   = $csvString . $cvsParentId . '"|"';
            
            $cvsDonorName = str_replace('"', '""', ($CSVRecord['donorName']));
            $csvString    = $csvString . $cvsDonorName . '"|"';
            
            $cvsLegalEntityName = str_replace('"', '""', ($CSVRecord['legalEntityName']));
            $csvString          = $csvString . $cvsLegalEntityName . '"|"';
            
            $cvsCorporateBodyName = str_replace('"', '""', ($CSVRecord['corporateBodyName']));
            $csvString            = $csvString . $cvsCorporateBodyName;
            
            $csvString   = $csvString . '"' . "\n";
            $publishPath = QubitSetting::getByName('publish_path');
            if ($publishPath == null) {
                QubitXMLImport::addLog($publishPath, "No upload path defined. Contact support/administrator", get_class($this), true);
                throw new sfException(sfContext::getInstance()->i18n->__("No upload path defined. Contact support/administrator"));
            } else {
                fwrite($csvfile, $csvString);
                //update AtoM to indicate item is published
                $accessObj = QubitAccessObject::getById($accessObject);
                if (isset($accessObj)) {
                    $accessObj->published = true;
                    $accessObj->save();
                }
            }
        }
    }

    /**
     * Add Logfile for import
     *
     * @return
     */
    public static function addLog($unitid, $comment, $curClass, $logEnabled)
    {
        if ($logEnabled == 1) {
            $log = QubitSetting::getByName('log');
            if ($log == "1") {
                $logFilename = QubitSetting::getByName('log_filename');
                if ($logFilename == null) {
                    throw new sfException(sfContext::getInstance()->i18n->__("No Log Filename defined. Contact support/administrator"));
                }
                if (rtrim($logFilename) == "") {
                    throw new sfException(sfContext::getInstance()->i18n->__("No Log Filename defined. Contact support/administrator"));
                }
                $logPath = QubitSetting::getByName('log_path');
                if ($logPath == null) {
                    throw new sfException(sfContext::getInstance()->i18n->__("No Log path defined. Contact support/administrator"));
                } else {
                    $now       = date("Ymd");
                    $nowDT     = date("Y-m-d H:i:s");
                    $lFilename = strtolower(pathinfo($logFilename, PATHINFO_FILENAME));
                    $lExt      = strtolower(pathinfo($logFilename, PATHINFO_EXTENSION));
                    $lFile     = $logPath . "/" . $lFilename . "_" . $now . "." . $lExt;
                    // log file imports
                    $logfile   = fopen($lFile, "a");
                    if ($logfile != null) {
                        if (!is_writable($lFile)) {
                            throw new sfException(sfContext::getInstance()->i18n->__("Unable to open log file!"));
                        }
                        if ($comment != "") {
                            fwrite($logfile, $nowDT . ": " . $unitid . " Comments: " . $comment . " Class: " . $curClass . "\n");
                        } else {
                            fwrite($logfile, $nowDT . ": " . $unitid . " Class:" . $curClass . "\n");
                        }
                        fclose($logfile);
                    }
                }
            }
        }
    }
    
    /**
     * Add Error file for import
     *
     * @return
     */
    public static function addImportError($unitid, $error, $curClass, $outputEnabled, $origFileName)
    {
        if ($outputEnabled == 1) {
            $output = QubitSetting::getByName('bulk_output');
            if ($output == "1") {
                $outputFilename = QubitSetting::getByName('output_filename');
                if ($outputFilename == null) {
                    QubitXMLImport::addLog($outputFilename, "No Output Filename defined. Contact support/administrator", get_class($this), true);
                    throw new sfException(sfContext::getInstance()->i18n->__("No Output Filename defined. Contact support/administrator"));
                }
                if (rtrim($outputFilename) == "") {
                    QubitXMLImport::addLog($outputFilename, "No Output Filename defined. Contact support/administrator", get_class($this), true);
                    throw new sfException(sfContext::getInstance()->i18n->__("No Output Filename defined. Contact support/administrator"));
                }
                $outputPath = QubitSetting::getByName('output_path');
                if ($outputPath == null) {
                    QubitXMLImport::addLog($outputPath, "No Output path defined. Contact support/administrator", get_class($this), true);
                    throw new sfException(sfContext::getInstance()->i18n->__("No Output path defined. Contact support/administrator"));
                } else {
                    $now        = date("Ymd");
                    $nowDT      = date("Ymdhis");
                    $lFilename  = strtolower(pathinfo($origFileName, PATHINFO_FILENAME));
                    $origExt    = strtolower(pathinfo($origFileName, PATHINFO_EXTENSION));
                    $lExt       = "err";
                    $lFile      = $outputPath . "/" . $lFilename . "_" . $now . "." . $lExt;
                    // output file imports
                    $outputfile = fopen($lFile, "a");
                    if ($outputfile != null) {
                        if (!is_writable($lFile)) {
                            QubitXMLImport::addLog($lFile, "Unable to open Output file!", get_class($this), true);
                            throw new sfException(sfContext::getInstance()->i18n->__("Unable to open Output file!"));
                        }
                        if ($error != "") {
                            fwrite($outputfile, $nowDT . ": " . $unitid . " Error: " . $error . " Class: " . $curClass . "\n");
                        } else {
                            fwrite($outputfile, $nowDT . ": " . $unitid . " Class:" . $curClass . "\n");
                        }
                        fclose($outputfile);
                    }
                    rename($origFileName, "$outputPath/$lFilename.$origExt.err");
                }
            }
        }
    }
    
    /**
     * Check if an actor passes the limit option. Passes when:
     * - The limit option is not set
     * - The limit option is the slug of the resource's maintaining repository
     *
     * @param QubitActor $actor  The actor object to check
     * @return bool  The actor passes the limit option or not
     * @throws sfException  When the limit option is not accepted
     */
    private function passesLimitOptionForActor($actor)
    {
        if (false === $limit = $this->getLimitIdAndClassName()) {
            return true;
        }
        
        switch ($limit->class_name) {
            case 'QubitRepository':
                $repo = $actor->getMaintainingRepository();
                return isset($repo) && $repo->id == $limit->id;
            
            default:
                throw new sfException($this->i18n->__('Slugs from %class_name% are not accepted as limit option for actors', array(
                    '%class_name%' => $limit->class_name
                )));
        }
    }
    
    /**
     * Obtain the limit type (class_name) and id based on the limit option slug
     *
     * @return mixed  bool false if no option set or no slug found or
     *                stdClass object with 'id' and 'class_name' properties
     */
    private function getLimitIdAndClassName()
    {
        if (empty($this->options['limit'])) {
            return false;
        }
        
        $query = "SELECT object.id, object.class_name
              FROM object JOIN slug ON slug.object_id = object.id
              WHERE slug.slug = ?";
        
        return QubitPdo::fetchOne($query, array(
            $this->options['limit']
        ));
    }
    
    /**
     * Save the EAD Url to the keymap table for matching against next time.
     */
    private function saveEadUrl(&$currentObject)
    {
        if ($this->eadUrl) {
            $keymap             = new QubitKeymap;
            $keymap->sourceId   = $this->eadUrl;
            $keymap->sourceName = $this->sourceName;
            $keymap->targetId   = $currentObject->id;
            $keymap->targetName = 'information_object';
            $keymap->save();
        }
    }
    
    /**
     * Ensure we were passed valid options, throw an exception otherwise.
     */
    private function validateOptions()
    {
        if ($this->options['update'] && $this->options['update'] !== 'delete-and-replace') {
            throw new sfException($this->i18n->__('EAD import currently only supports %mode% update mode.', array(
                '%mode%' => '"delete-and-replace"'
            )));
        }
    }
    
    public static function includeClassesAndHelpers()
    {
        $appRoot = sfConfig::get('sf_root_dir');
        
        $includes = array(
            '/plugins/sfSkosPlugin/lib/sfSkosPlugin.class.php',
            '/plugins/sfSkosPlugin/lib/sfSkosPluginException.class.php',
            '/plugins/sfSkosPlugin/lib/sfSkosUniqueRelations.class.php'
        );
        
        foreach ($includes as $include) {
            include_once $appRoot . $include;
        }
    }
}
