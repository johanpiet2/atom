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
 * Import an XML document of Accession into Qubit.
 *
 * @package    AccesstoMemory
 * @subpackage library
 * @author     MJ Suhonos <mj@suhonos.ca>
 * @author     Peter Van Garderen <peter@artefactual.com>
 * @author     Mike Cantelon <mike@artefactual.com>
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class XmlImportAccession
{
    protected $errors = null, $rootObject = null, $parent = null;
    public function import($xmlFile, $options = array(), $type)
    {
        // load the XML document into a DOMXML object
        $importDOM = $this->loadXML($xmlFile, $options);
        // if we were unable to parse the XML file at all
        if (empty($importDOM->documentElement)) {
            throw new sfError404Exception('Unable to parse XML file: malformed or unresolvable entities');
        }
        // if libxml threw errors, populate them to show in the template
        if ($importDOM->libxmlerrors) {
            // warning condition, XML file has errors (perhaps not well-formed or invalid?)
            foreach ($importDOM->libxmlerrors as $libxmlerror) {
                $xmlerrors[] = sfContext::getInstance()->i18n->__('libxml error %code% on line %line% in input file: %message%', array('%code%' => $libxmlerror->code, '%message%' => $libxmlerror->message, '%line%' => $libxmlerror->line));
            }
            $this->errors = array_merge((array)$this->errors, $xmlerrors);
        }
        // FIXME hardcoded until we decide how these will be developed
        $validSchemas = array(
        // document type declarations
        'accession' => 'accession');
        // determine what kind of schema we're trying to import
        $schemaDescriptors = array($importDOM->documentElement->tagName);
        if (!empty($importDOM->namespaces)) {
            krsort($importDOM->namespaces);
            $schemaDescriptors = array_merge($schemaDescriptors, $importDOM->namespaces);
        }
        if (!empty($importDOM->doctype)) {
            $schemaDescriptors = array_merge($schemaDescriptors, array($importDOM->doctype->name, $importDOM->doctype->systemId, $importDOM->doctype->publicId));
        }
        $importSchema = "";
        foreach ($schemaDescriptors as $descriptor) {
            if (array_key_exists($descriptor, $validSchemas)) {
                $importSchema = $validSchemas[$descriptor];
            }
        }
        if ($importSchema == 'accession') {
            $importDOM->validate();
            // if libxml threw errors, populate them to show in the template
            foreach (libxml_get_errors() as $libxmlerror) {
                $this->errors[] = sfContext::getInstance()->i18n->__('libxml error %code% on line %line% in input file: %message%', array('%code%' => $libxmlerror->code, '%message%' => $libxmlerror->message, '%line%' => $libxmlerror->line));
            }
        } else {
            throw new sfError404Exception('Unable to parse XML file: Perhaps not a Accession Record import file');
        }
        $importMap = sfConfig::get('sf_app_module_dir') . DIRECTORY_SEPARATOR . 'object' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $importSchema . '.yml';
        if (!file_exists($importMap)) {
            // error condition, unknown schema or no import filter
            $errorMsg = sfContext::getInstance()->i18n->__('Unknown schema or import format: "%format%"', array('%format%' => $importSchema));
            throw new Exception($errorMsg);
        }
        $this->schemaMap = sfYaml::load($importMap);
        // if XSLs are specified in the mapping, process them
        if (!empty($this->schemaMap['processXSLT'])) {
            // pre-filter through XSLs in order
            foreach ((array)$this->schemaMap['processXSLT'] as $importXSL) {
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
                    $this->errors[] = sfContext::getInstance()->i18n->__('Unable to load import XSL filter: "%importXSL%"', array('%importXSL%' => $importXSL));
                }
            }
            // re-initialize xpath on the new XML
            $importDOM->xpath = new DOMXPath($importDOM);
        }
        unset($this->schemaMap['processXSLT']);
        // go through schema map and populate objects/properties
        foreach ($this->schemaMap as $name => $mapping) {
            // if object is not defined or a valid class, we can't process this mapping
            if (empty($mapping['Object']) || !class_exists('Qubit' . $mapping['Object'])) {
                $this->errors[] = sfContext::getInstance()->i18n->__('Non-existent class defined in import mapping: "%class%"', array('%class%' => 'Qubit' . $mapping['Object']));
                continue;
            }
            // Load taxonomies into variables to avoid use of magic numbers
            $this->termData = QubitXMLImport::loadTermsFromTaxonomies(array(QubitTaxonomy::ACCESSION_ACQUISITION_TYPE_ID => 'acquisitionTypes', QubitTaxonomy::ACCESSION_RESOURCE_TYPE_ID => 'resourceTypes', QubitTaxonomy::ACCESSION_PROCESSING_STATUS_ID => 'processingStatus', QubitTaxonomy::ACCESSION_PROCESSING_PRIORITY_ID => 'processingPriority', QubitTaxonomy::REPOSITORY_TYPE_ID => 'entityTypes'));
            // get a list of XML nodes to process
            $nodeList = $importDOM->xpath->query($mapping['XPath']);
            foreach ($nodeList as $domNode) {
                // create a new object
                $class = 'Qubit' . $mapping['Object'];
                $currentObject = new $class;
                // set the rootObject to use for initial display in successful import
                if (!$this->rootObject) {
                    $this->rootObject = $currentObject;
                }
                // use DOM to populate object
                $this->populateObject($domNode, $importDOM, $mapping, $currentObject, $importSchema);
            }
        }
        return $this;
    }
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
            if (!empty($parentId) && is_callable(array($currentObject, 'setParentId'))) {
                $currentObject->parentId = $parentId;
            }
        } else {
            // orphaned object, set root if possible
            if (isset($this->parent)) {
                $currentObject->parentId = $this->parent->id;
            } else if (is_callable(array($currentObject, 'setRoot'))) {
                $currentObject->setRoot();
            }
        }
        // go through methods and populate properties
        $this->processMethods($domNode, $importDOM, $mapping['Methods'], $currentObject, $importSchema);
        // make sure we have a publication status set before indexing
        if ($currentObject instanceof QubitInformationObject && count($currentObject->statuss) == 0) {
            $currentObject->setPublicationStatus(sfConfig::get('app_defaultPubStatus', QubitTerm::PUBLICATION_STATUS_DRAFT_ID));
        }
        // jjp SITA 07 Jan 2015 - Search if already imported. If exist then update else insert
        if ($this->identifier != "") {
            $updateObject = QubitAccession::getByAccessionNumber($this->identifier);
            if (isset($updateObject)) {
                if (isset($this->accessionValues)) {
                    $updateObject->identifier = $this->accessionValues['accessionNumber'];
                    $updateObject->date = $this->accessionValues['acquisitionDate'];
                    $updateObject->sourceOfAcquisition = $this->accessionValues['sourceOfAcquisition'];
                    $updateObject->locationInformation = $this->accessionValues['locationInformation'];
                    $updateObject->acquisitionTypeId = $this->accessionValues['acquisitionType'];
                    $updateObject->resourceTypeId = $this->accessionValues['resourceType'];
                    $updateObject->title = $this->accessionValues['title'];
                    $updateObject->archivalHistory = $this->accessionValues['archivalHistory'];
                    $updateObject->scopeAndContent = $this->accessionValues['scopeAndContent'];
                    $updateObject->appraisal = $this->accessionValues['appraisal'];
                    $updateObject->receivedExtentUnits = $this->accessionValues['receivedExtentUnits'];
                    $updateObject->processingStatusId = $this->accessionValues['processingStatus'];
                    $updateObject->processingPriorityId = $this->accessionValues['processingPriority'];
                    $updateObject->processingNotes = $this->accessionValues['processingNotes'];
                    $updateObject->culture = $this->accessionValues['culture'];
                    $updateObject->physicalCharacteristics = $this->accessionValues['physicalCharacteristics'];
                    $updateObject->culture = "en";
                    $creatorsId = $this->accessionValues['creatorsId'];
                    if (isset($this->accessionValues['creators']))
                    {
		                if ($this->accessionValues['creators'] == "")
		                {
		                	$this->accessionValues['creators'] = $creatorsId;
		                }
                    }
                    else
                    {
                    	$this->accessionValues['creators'] = $creatorsId;
                    }
                    $creators = $this->accessionValues['creators'];
                    $donorId = $this->accessionValues['donorId'];
				    if (isset($this->accessionValues['donorId'])) 
				    {
						if (isset($this->accessionValues['donor']))
						{
						    if ($this->accessionValues['donor'] == "")
						    {
						    	$this->accessionValues['donor'] = $donorId;
						    }
						}
						else
						{
							$this->accessionValues['donor'] = $donorId;
						}
				    }
                    $donor = $this->accessionValues['donor'];
                    $actorId = $this->accessionValues['actorId'];
                    $actorName = $this->accessionValues['actorName'];
                }
                // to fix - multiple creators
                $updateObject->save();
                // write the ID onto the current XML node for tracking
                $domNode->setAttribute('xml:id', $updateObject->id);
                // if creators exist add creator actor
                if (isset($creators)) {
                    // fetch/create actor
                    $actor = XmlImportAccession::createOrFetchActor($creators, $creatorsId, $this->accessionValues, $updateObject->id, "actor");
                }
                // if donor exist add donor actor
                if (isset($donor)) {
                    $actor = XmlImportAccession::createOrFetchActor($donor, $actor->id, $this->accessionValues, $updateObject->id, "donor");
                    // Add contact info
                    QubitXmlImport::addUpdateContact($actor->id, $this->addressValues);
                }
                // if Archival Description exist add/update relation
                if (isset($this->archivalValues['accessionarchivalname'])) {
                    if (($informationObject = QubitInformationObject::getByIdentifier($this->archivalValues['accessionarchivalidentifier'])) !== null) {
                        // if relation exist delete it first
                        if (($relation = QubitRelation::getBySubjectAndObjectAndType($informationObject->id, $updateObject->id, QubitTerm::ACCESSION_ID)) !== null) {
                            $relation->delete();
                        }
                        // create relation between accession and Archival Description
                        QubitXmlImport::createRelation($informationObject->id, $updateObject->id, QubitTerm::ACCESSION_ID);
                    } else {
                        if (($informationObject = QubitInformationObject::getByIdentifier($this->archivalValues['accessionarchivalname'])) !== null) {
                            // if relation exist delete it first
                            if (($relation = QubitRelation::getBySubjectAndObjectAndType($informationObject->id, $updateObject->id, QubitTerm::ACCESSION_ID)) !== null) {
                                $relation->delete();
                            }
                            // create relation between accession and Archival Description
                            QubitXmlImport::createRelation($informationObject->id, $updateObject->id, QubitTerm::ACCESSION_ID);
                        } else {
                            // create Archival Description record with only Identifier and Import ID
                            XMLImportAccession::createInformationObject($updateObject, $this->archivalValues['accessionarchivalidentifier'], $this->archivalValues['accessionarchivalname'], $updateObject->id, $this->accessionValues['physicalCharacteristics'], $this->accessionValues['scopeAndContent'], $this->accessionValues['archivalHistory'], $this->accessionValues['appraisal']);
                        }
                    }
                }
            } else {
                $currentObject->identifier = $this->accessionValues['accessionNumber'];
                $currentObject->date = $this->accessionValues['acquisitionDate'];
                $currentObject->sourceOfAcquisition = $this->accessionValues['sourceOfAcquisition'];
                $currentObject->locationInformation = $this->accessionValues['locationInformation'];
                $currentObject->acquisitionTypeId = $this->accessionValues['acquisitionType'];
                $currentObject->resourceTypeId = $this->accessionValues['resourceType'];
                $currentObject->title = $this->accessionValues['title'];
                $currentObject->archivalHistory = $this->accessionValues['archivalHistory'];
                $currentObject->scopeAndContent = $this->accessionValues['scopeAndContent'];
                $currentObject->appraisal = $this->accessionValues['appraisal'];
                $currentObject->receivedExtentUnits = $this->accessionValues['receivedExtentUnits'];
                $currentObject->processingStatusId = $this->accessionValues['processingStatus'];
                $currentObject->processingPriorityId = $this->accessionValues['processingPriority'];
                $currentObject->processingNotes = $this->accessionValues['processingNotes'];
                $currentObject->culture = $this->accessionValues['culture'];
                $currentObject->physicalCharacteristics = $this->accessionValues['physicalCharacteristics'];
                $currentObject->culture = "en";
                $creatorsId = $this->accessionValues['creatorsId'];
                if (isset($this->accessionValues['creators']))
                {
	                if ($this->accessionValues['creators'] == "")
	                {
	                	$this->accessionValues['creators'] = $creatorsId;
	                }
                }
                else
                {
                	$this->accessionValues['creators'] = $creatorsId;
                }
                $creators = $this->accessionValues['creators'];
                $donorId = $this->accessionValues['donorId'];
		        if (isset($this->accessionValues['donorId'])) 
		        {
				    if (isset($this->accessionValues['donor']))
				    {
				        if ($this->accessionValues['donor'] == "")
				        {
				        	$this->accessionValues['donor'] = $donorId;
				        }
				    }
				    else
				    {
				    	$this->accessionValues['donor'] = $donorId;
				    }
		        }
                $donor = $this->accessionValues['donor'];
                $actorId = $this->accessionValues['actorId'];
                $actorName = $this->accessionValues['actorName'];

                // save the object after it's fully-populated
                $currentObject->save();
                // write the ID onto the current XML node for tracking
                $domNode->setAttribute('xml:id', $currentObject->id);
                // if creators exist add creator actor
                if (isset($this->accessionValues['creatorsId'])) {
                    $actor = XmlImportAccession::createOrFetchActor($this->accessionValues['creators'], $this->accessionValues['creatorsId'], $this->accessionValues, $currentObject->id, "actor");
                }
                // if donor exist add donor actor
                if (isset($this->accessionValues['donor'])) {
                    $actor = XmlImportAccession::createOrFetchActor($this->accessionValues['donor'], $actor->id, $this->accessionValues, $currentObject->id, "donor");
                    // Add contact info
                    QubitXmlImport::addUpdateContact($actor->id, $this->addressValues);
                }
                // if Archival Description exist add/update relation
                if (isset($this->archivalValues['accessionarchivalname'])) {
                    if (($informationObject = QubitInformationObject::getByImportId($this->archivalValues['accessionarchivalidentifier'])) !== null) {
                        // if relation exist delete it first
                        if (($relation = QubitRelation::getBySubjectAndObjectAndType($informationObject->id, $currentObject->id, QubitTerm::ACCESSION_ID)) !== null) {
                            $relation->delete();
                        }
                        // create relation between accession and Archival Description
                        QubitXmlImport::createRelation($informationObject->id, $currentObject->id, QubitTerm::ACCESSION_ID);
                    } else {
                        if (($informationObject = QubitInformationObject::getByIdentifier($this->archivalValues['accessionarchivalname'])) !== null) {
                            // if relation exist delete it first
                            if (($relation = QubitRelation::getBySubjectAndObjectAndType($informationObject->id, $currentObject->id, QubitTerm::ACCESSION_ID)) !== null) {
                                $relation->delete();
                            }
                            // create relation between accession and Archival Description
                            QubitXmlImport::createRelation($informationObject->id, $currentObject->id, QubitTerm::ACCESSION_ID);
                        } else {
                            // create Archival Description record with only Identifier and Import ID
                            XMLImportAccession::createInformationObject($currentObject, $this->archivalValues['accessionarchivalidentifier'], $this->archivalValues['accessionarchivalname'], $currentObject->id, $this->accessionValues['physicalCharacteristics'], $this->accessionValues['scopeAndContent'], $this->accessionValues['archivalHistory'], $this->accessionValues['appraisal']);
                        }
                    }
                }
            }
        } else {
            $currentObject->identifier = $this->accessionValues['accessionNumber'];
            $currentObject->date = $this->accessionValues['acquisitionDate'];
            $currentObject->sourceOfAcquisition = $this->accessionValues['sourceOfAcquisition'];
            $currentObject->locationInformation = $this->accessionValues['locationInformation'];
            $currentObject->acquisitionTypeId = $this->accessionValues['acquisitionType'];
            $currentObject->resourceTypeId = $this->accessionValues['resourceType'];
            $currentObject->title = $this->accessionValues['title'];
            $currentObject->archivalHistory = $this->accessionValues['archivalHistory'];
            $currentObject->scopeAndContent = $this->accessionValues['scopeAndContent'];
            $currentObject->appraisal = $this->accessionValues['appraisal'];
            $currentObject->receivedExtentUnits = $this->accessionValues['receivedExtentUnits'];
            $currentObject->processingStatusId = $this->accessionValues['processingStatus'];
            $currentObject->processingPriorityId = $this->accessionValues['processingPriority'];
            $currentObject->processingNotes = $this->accessionValues['processingNotes'];
            $currentObject->culture = $this->accessionValues['culture'];
            $currentObject->physicalCharacteristics = $this->accessionValues['physicalCharacteristics'];
            $currentObject->culture = "en";
            $creatorsId = $this->accessionValues['creatorsId'];
            if (isset($this->accessionValues['creators']))
            {
                if ($this->accessionValues['creators'] == "")
                {
                	$this->accessionValues['creators'] = $creatorsId;
                }
            }
            else
            {
            	$this->accessionValues['creators'] = $creatorsId;
            }
            $creators = $this->accessionValues['creators'];
            $donorId = $this->accessionValues['donorId'];
            if (isset($this->accessionValues['donorId'])) 
            {
		        if (isset($this->accessionValues['donor']))
		        {
		            if ($this->accessionValues['donor'] == "")
		            {
		            	$this->accessionValues['donor'] = $donorId;
		            }
		        }
		        else
		        {
		        	$this->accessionValues['donor'] = $donorId;
		        }
            }
            $donor = $this->accessionValues['donor'];
            $actorId = $this->accessionValues['actorId'];
            $actorName = $this->accessionValues['actorName'];
            // save the object after it's fully-populated
            $currentObject->save();
            // write the ID onto the current XML node for tracking
            $domNode->setAttribute('xml:id', $currentObject->id);
            // if creators exist add creator actor
            if (isset($this->accessionValues['creator'])) {
                $actor = XmlImportAccession::createOrFetchActor($this->accessionValues['creators'], $this->accessionValues['creatorsId'], $this->accessionValues, $currentObject->id, "actor");
            }
            // if donor exist add donor actor
            if (isset($this->accessionValues['donor'])) {
                $actor = XmlImportAccession::createOrFetchActor($this->accessionValues['donor'], $actor->id, $this->accessionValues, $currentObject->id, "donor");
                // Add contact info
                QubitXmlImport::addUpdateContact($actor->id, $this->addressValues);
            }
            // if Archival Description exist add/update relation
            if (isset($this->archivalValues['accessionarchivalname'])) {
                if (($informationObject = QubitInformationObject::getByImportId($this->archivalValues['accessionarchivalidentifier'])) !== null) {
                    // if relation exist delete it first
                    if (($relation = QubitRelation::getBySubjectAndObjectAndType($informationObject->id, $currentObject->id, QubitTerm::ACCESSION_ID)) !== null) {
                        $relation->delete();
                    }
                    // create relation between accession and Archival Description
                    QubitXmlImport::createRelation($informationObject->id, $currentObject->id, QubitTerm::ACCESSION_ID);
                } else {
                    if (($informationObject = QubitInformationObject::getByIdentifier($this->archivalValues['accessionarchivalname'])) !== null) {
                        // if relation exist delete it first
                        if (($relation = QubitRelation::getBySubjectAndObjectAndType($informationObject->id, $currentObject->id, QubitTerm::ACCESSION_ID)) !== null) {
                            $relation->delete();
                        }
                        // create relation between accession and Archival Description
                        QubitXmlImport::createRelation($informationObject->id, $currentObject->id, QubitTerm::ACCESSION_ID);
                    } else {
                        // create Archival Description record with only Identifier and Import ID
                        XMLImportAccession::createInformationObject($currentObject, $this->archivalValues['accessionarchivalidentifier'], $this->archivalValues['accessionarchivalname'], $currentObject->id, $this->accessionValues['physicalCharacteristics'], $this->accessionValues['scopeAndContent'], $this->accessionValues['archivalHistory'], $this->accessionValues['appraisal']);
                    }
                }
            }
        }
    }
    /*
     * Cycle through methods and populate object based on relevant data
     *
     * @return  null
    */
    private function processMethods(&$domNode, &$importDOM, $methods, &$currentObject, $importSchema)
    {
        // go through methods and populate properties
        foreach ($methods as $name => $methodMap) {
            // if method is not defined, we can't process this mapping
            if (empty($methodMap['Method']) || !is_callable(array($currentObject, $methodMap['Method']))) {
                $this->errors[] = sfContext::getInstance()->i18n->__('Non-existent method defined in import mapping: "%method%"', array('%method%' => $methodMap['Method']));
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
                case 'accessionName':
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                            $currentObject->sourceOfAcquisition = $childNode->item(0)->nodeValue;
                            $this->sourceOfAcquisition = $childNode->item(0)->nodeValue;
                        }
                    }
                    break;

                case 'accessionNumber':
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                            $currentObject->identifier = $childNode->item(0)->nodeValue;
                            $this->identifier = $childNode->item(0)->nodeValue;
                        }
                    }
                    break;

                case 'accessionDetail':
                    unset($this->accessionValues);
                    if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('./acquisitiondate', $item)) !== null) {
                                $currentObject->date = $childNode->item(0)->nodeValue;
                                $this->date = $childNode->item(0)->nodeValue;
                                if (isset($currentObject->date) && is_object($currentObject->date)) {
                                    $parsedDate = QubitXmlImport::parseDateLoggingErrors($currentObject->date);
                                    if ($parsedDate) {
                                        $currentObject->date = $parsedDate;
                                        $this->date = $parsedDate;
                                    }
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./sourceofacquisition', $item)) !== null) {
                                $currentObject->sourceOfAcquisition = $childNode->item(0)->nodeValue;
                                $this->sourceOfAcquisition = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./locationinformation', $item)) !== null) {
                                $currentObject->locationInformation = $childNode->item(0)->nodeValue;
                                $this->locationInformation = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./acquisitiontype', $item)) !== null) {
                                $this->acquisitionType = QubitXmlImport::translateNameToTermId("Acquisition Type", 'acquisitionTypes', $this->termData, $childNode->item(0)->nodeValue);
                            }
                            if (($childNode = $importDOM->xpath->query('./resourcetype', $item)) !== null) {
                                $this->resourceType = QubitXmlImport::translateNameToTermId("Resource Type", 'resourceTypes', $this->termData, $childNode->item(0)->nodeValue);
                            }
                            if (($childNode = $importDOM->xpath->query('./title', $item)) !== null) {
                                $currentObject->title = $childNode->item(0)->nodeValue;
                                $this->title = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./history', $item)) !== null) {
                                $currentObject->archivalHistory = $childNode->item(0)->nodeValue;
                                $this->archivalHistory = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./scopeandcontent', $item)) !== null) {
                                $currentObject->scopeAndContent = $childNode->item(0)->nodeValue;
                                $this->scopeAndContent = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./appraisal', $item)) !== null) {
                                $currentObject->appraisal = $childNode->item(0)->nodeValue;
                                $this->appraisal = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./physicalcharacteristics', $item)) !== null) {
                                $currentObject->physicalCharacteristics = $childNode->item(0)->nodeValue;
                                $this->physicalCharacteristics = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./receivedextentunits', $item)) !== null) {
                                $currentObject->receivedExtentUnits = $childNode->item(0)->nodeValue;
                                $this->receivedExtentUnits = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./processingstatus', $item)) !== null) {
                                $this->processingStatus = QubitXmlImport::translateNameToTermId("Processing Status", 'processingStatus', $this->termData, $childNode->item(0)->nodeValue);
                            }
                            if (($childNode = $importDOM->xpath->query('./processingpriority', $item)) !== null) {
                                $this->processingPriority = QubitXmlImport::translateNameToTermId("Processing Priority", 'processingPriority', $this->termData, $childNode->item(0)->nodeValue);
                            }
                            if (($childNode = $importDOM->xpath->query('./processingnotes', $item)) !== null) {
                                $currentObject->processingNotes = $childNode->item(0)->nodeValue;
                                $this->processingNotes = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./culture', $item)) !== null) {
                                $currentObject->culture = $childNode->item(0)->nodeValue;
                                $this->culture = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./actorid', $item)) !== null) {
                                $this->actorId = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./actorname', $item)) !== null) {
                                $this->actorName = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./creatorsid', $item)) !== null) {
                                $this->creatorsId = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./creators', $item)) !== null) {
                                $this->creators = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./donorid', $item)) !== null) {
                                $this->donorId = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./donor', $item)) !== null) {
                                $this->donor = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./entityTypeId', $item)) !== null) {
                            	if (isset($childNode->item(0)->nodeValue))
                            	{
                                	$this->entityTypeId = QubitXmlImport::translateNameToTermId("Entity Type", 'entityTypes', $this->termData, $childNode->item(0)->nodeValue);                            	
                            	}
                            	else
                            	{
                                	$this->entityTypeId = "";                            	
                            	}

                            }
                            if (($childNode = $importDOM->xpath->query('./legalStatus', $item)) !== null) {
                                $this->legalStatus = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./datesOfExistence', $item)) !== null) {
                                $this->datesOfExistence = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./places', $item)) !== null) {
                                $this->places = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./functions', $item)) !== null) {
                                $this->functions = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./mandates', $item)) !== null) {
                                $this->mandates = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./internalStructures', $item)) !== null) {
                                $this->internalStructures = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./generalContext', $item)) !== null) {
                                $this->generalContext = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./rules', $item)) !== null) {
                                $this->rules = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./sources', $item)) !== null) {
                                $this->sources = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./revisionHistory', $item)) !== null) {
                                $this->revisionHistory = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./descriptionIdentifier', $item)) !== null) {
                                $this->descriptionIdentifier = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./corporateBodyIdentifiers', $item)) !== null) {
                                $this->corporateBodyIdentifiers = $childNode->item(0)->nodeValue;
                            }
                            $this->accessionValues = array('accessionNumber' => $this->identifier, 'acquisitionDate' => $this->date, 'sourceOfAcquisition' => $this->sourceOfAcquisition, 'locationInformation' => $this->locationInformation, 'acquisitionType' => $this->acquisitionType, 'resourceType' => $this->resourceType, 'title' => $this->title, 'archivalHistory' => $this->archivalHistory, 'scopeAndContent' => $this->scopeAndContent, 'appraisal' => $this->appraisal, 'physicalCharacteristics' => $this->physicalCharacteristics, 'receivedExtentUnits' => $this->receivedExtentUnits, 'processingStatus' => $this->processingStatus, 'processingPriority' => $this->processingPriority, 'processingNotes' => $this->processingNotes, 'culture' => $this->culture, 'creatorsId' => $this->creatorsId, 'creators' => $this->creators, 'donorId' => $this->donorId, 'donor' => $this->donor, 'actorId' => $this->actorId, 'actorName' => $this->actorName, 'entityTypeId' => $this->entityTypeId, 'datesOfExistence' => $this->datesOfExistence, 'places' => $this->places, 'legalStatus' => $this->legalStatus, 'functions' => $this->functions, 'mandates' => $this->mandates, 'internalStructures' => $this->internalStructures, 'generalContext' => $this->generalContext, 'rules' => $this->rules, 'sources' => $this->sources, 'revisionHistory' => $this->revisionHistory, 'descriptionIdentifier' => $this->descriptionIdentifier, 'corporateBodyIdentifiers' => $this->corporateBodyIdentifiers);
                        }
                    }
                    break;

                case 'accessionAddress':
                    if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('./primarycontact', $item)) !== null) {
                                $primaryContact = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./title', $item)) !== null) {
                                $title = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./contactperson', $item)) !== null) {
                                $contactPerson = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./position', $item)) !== null) {
                                $position = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./email', $item)) !== null) {
                                $email = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./fax', $item)) !== null) {
                                $fax = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./telephone', $item)) !== null) {
                                $telephone = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./cell', $item)) !== null) {
                                $cell = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./website', $item)) !== null) {
                                $website = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./streetaddress', $item)) !== null) {
                                $streetAddress = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./city', $item)) !== null) {
                                $city = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./region', $item)) !== null) {
                                $region = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./countrycode', $item)) !== null) {
                                $countryCode = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalcode', $item)) !== null) {
                                $postalCode = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postaladdress', $item)) !== null) {
                                $postalAddress = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalcity', $item)) !== null) {
                                $postalCity = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalregion', $item)) !== null) {
                                $postalRegion = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalcountrycode', $item)) !== null) {
                                $postalCountryCode = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalpostcode', $item)) !== null) {
                                $postalPostCode = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./latitude', $item)) !== null) {
                                $latitude = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./longitude', $item)) !== null) {
                                $longitude = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./note', $item)) !== null) {
                                $note = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./contacttype', $item)) !== null) {
                                $contactType = $childNode->item(0)->nodeValue;
                            }
                            $this->addressValues = array('primaryContact' => $primaryContact, 'title' => $title, 'contactPerson' => $contactPerson, 'position' => $position, 'email' => $email, 'fax' => $fax, 'telephone' => $telephone, 'cell' => $cell, 'website' => $website, 'streetAddress' => $streetAddress, 'city' => $city, 'region' => $region, 'countryCode' => $countryCode, 'postalCode' => $postalCode, 'postalAddress' => $postalAddress, 'postalCity' => $postalCity, 'postalRegion' => $postalRegion, 'postalCountryCode' => $postalCountryCode, 'postalPostCode' => $postalPostCode, 'latitude' => $latitude, 'longitude' => $longitude, 'note' => $note, 'contactType' => $contactType);
                        }
                    }
                    break;

                case 'accessionArchival':
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('./accessionarchivalidentifier', $item)) !== null) {
                            $this->accessionarchivalidentifier = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./accessionarchivalname', $item)) !== null) {
                            $this->accessionarchivalname = $childNode->item(0)->nodeValue;
                        }
                        $this->archivalValues = array('accessionarchivalidentifier' => $this->accessionarchivalidentifier, 'accessionarchivalname' => $this->accessionarchivalname);
                    }
                    break;
                    // hack: some multi-value elements (e.g. 'languages') need to get passed as one array instead of individual nodes values
                    
                case 'languages':
                case 'language':
                    $langCodeConvertor = new fbISO639_Map;
                    $isID3 = ($importSchhema == 'dc') ? true : false;
                    $value = array();
                    foreach ($nodeList2 as $item) {
                        if ($twoCharCode = $langCodeConvertor->getID1($item->nodeValue, $isID3)) {
                            $value[] = strtolower($twoCharCode);
                        } else {
                            $value[] = $item->nodeValue;
                        }
                    }
                    $currentObject->language = $value;
                    $this->language = $value;
                    break;

                default:
                    foreach ($nodeList2 as $key => $domNode2) {
                        // normalize the node text; NB: this will strip any child elements, eg. HTML tags
                        $nodeValue = self::normalizeNodeValue($domNode2);
                        // if you want the full XML from the node, use this
                        $nodeXML = $domNode2->ownerDocument->saveXML($domNode2);
                        // set the parameters for the method call
                        if (empty($methodMap['Parameters'])) {
                            $parameters = array($nodeValue);
                        } else {
                            $parameters = array();
                            foreach ((array)$methodMap['Parameters'] as $parameter) {
                                // if the parameter begins with %, evaluate it as an XPath expression relative to the current node
                                if ('%' == substr($parameter, 0, 1)) {
                                    // evaluate the XPath expression
                                    $xPath = substr($parameter, 1);
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
                    }
                }
                unset($nodeList2);
            }
        }
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
        // FIXME: trap possible load validation errors (just suppress for now)
        $err_level = error_reporting(0);
        $doc = new DOMDocument('1.0', 'UTF-8');
        // Default $strictXmlParsing to false
        $strictXmlParsing = (isset($options['strictXmlParsing'])) ? $options['strictXmlParsing'] : false;
        // Pre-fetch the raw XML string from file so we can remove any default
        // namespaces and reuse the string for later when finding/registering namespaces.
        $rawXML = file_get_contents($xmlFile);
        if ($strictXmlParsing) {
            // enforce all XML parsing rules and validation
            $doc->validateOnParse = true;
            $doc->resolveExternals = true;
        } else {
            // try to load whatever we've got, even if it's malformed or invalid
            $doc->recover = true;
            $doc->strictErrorChecking = false;
        }
        $doc->formatOutput = false;
        $doc->preserveWhitespace = false;
        $doc->substituteEntities = true;
        $doc->loadXML($this->removeDefaultNamespace($rawXML));
        $xsi = false;
        $doc->namespaces = array();
        $doc->xpath = new DOMXPath($doc);
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
     * Get the root object for the import
     *
     * @return mixed the root object (object type depends on import type)
     */
    public function setParent($parent)
    {
        return $this->parent = $parent;
    }
    /**
     * Replace </lb> tags for '\n'
     *
     * @return node value without linebreaks tags
     */
    public static function replaceLineBreaks($node)
    {
        $nodeValue = '';
        foreach ($node->childNodes as $child) {
            if ($child->nodeName == 'lb') {
                $nodeValue.= "\n";
            } else {
                $nodeValue.= preg_replace('/[\n\r\s]+/', ' ', $child->nodeValue);
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
                        $nodeValue.= self::replaceLineBreaks($pNode);
                    } else {
                        $nodeValue.= "\n\n" . self::replaceLineBreaks($pNode);
                    }
                }
            } else {
                $nodeValue.= self::replaceLineBreaks($node);
            }
        } else {
            $nodeValue.= $node->nodeValue;
        }
        return $nodeValue;
    }
    /**
     * Create a Qubit actor or, if one already exists, fetch it
     *
     * @param string $name     name of actor
     * @param string $id     unique import id of actor
     * @param string $options  optional data
     *
     * @return QubitActor  created or fetched and update actor
     * For Import if a Unique Actor ID is passed we first search for it.
     * If not found, use Actor name to search.
     * If found update, else add new
     */
    public static function createOrFetchActor($name, $id, $accessionValues = array(), $currentID, $createType)
    {
        // Get actor or create a new one. If the actor exists the data is not overwritten
        //    if (null === $actor = QubitActor::getByNameAndRepositoryId($name, $accessionValues['repositoryId'])) To Fix jjp
        $addNew = "false";
        if ($createType == "actor")
        {
		    if (null === $actor = QubitActor::getByImportId($id)) {
		        if (null === $actor = QubitActor::getByNameAndRepositoryId($name)) {
		            $actor = new QubitActor;
		            $actor->parentId = QubitActor::ROOT_ID;
		            $addNew = "true";
		        }
		    }
	        $actor->actorImportId = $id;
		    if (isset($accessionValues['creatorsId'])) {
				$actor->identifier = $accessionValues['creatorsId'];
		    }
        }
        else
        {
    		if (null === $actor = QubitActor::getByNameAndRepositoryId($name)) 
    		{
				if (null === $actor = QubitDonor::getById($actor->id)) {
			        $actor = new QubitDonor;
			        $actor->parentId = QubitDonor::ROOT_ID;
			        $addNew = "true";
				}
		    }
		    if (isset($accessionValues['donorId'])) {
				$actor->identifier = $accessionValues['donorId'];
		    }
        }
        $actor->authorizedFormOfName = $name;
        if (isset($accessionValues['history'])) {
            $actor->history = $accessionValues['history'];
        }
        if (isset($accessionValues['entityTypeId'])) {
            //$actor->entityTypeId = $accessionValues['entityTypeId'];
        }
        if (isset($accessionValues['legalStatus'])) {
            //$actor->legalStatus = $accessionValues['legalStatus'];
        }
        if (isset($accessionValues['datesOfExistence'])) {
            $actor->datesOfExistence = $accessionValues['datesOfExistence'];
        }
        if (isset($accessionValues['places'])) {
            $actor->places = $accessionValues['places'];
        }
        if (isset($accessionValues['functions'])) {
            $actor->functions = $accessionValues['functions'];
        }
        if (isset($accessionValues['mandates'])) {
            $actor->mandates = $accessionValues['mandates'];
        }
        if (isset($accessionValues['internalStructures'])) {
            $actor->internalStructures = $accessionValues['internalStructures'];
        }
        if (isset($accessionValues['generalContext'])) {
            $actor->generalContext = $accessionValues['generalContext'];
        }
        if (isset($accessionValues['rules'])) {
            $actor->rules = $accessionValues['rules'];
        }
        if (isset($accessionValues['sources'])) {
            $actor->sources = $accessionValues['sources'];
        }
        if (isset($accessionValues['revisionHistory'])) {
            $actor->revisionHistory = $accessionValues['revisionHistory'];
        }
        if (isset($accessionValues['descriptionIdentifier'])) {
            $actor->descriptionIdentifier = $accessionValues['descriptionIdentifier'];
        }
        if (isset($accessionValues['corporateBodyIdentifiers'])) {
            $actor->corporateBodyIdentifiers = $accessionValues['corporateBodyIdentifiers'];
        } 
        $actor->save();
        
        if ($addNew == "true") {
            // create relation between accession and creator
            QubitXmlImport::createRelation($currentID, $actor->id, QubitTerm::DONOR_ID);
        }
        return $actor;
    }
    
    
    /**
     * Create a Qubit Information Object or, if one already exists, fetch it
     *
     * @param string $name     name of Archival Description
     * @param string $id
     *
     * If found update, else add new
     */
    public static function createInformationObject($currentObject, $accessionarchivalidentifier, $title, $id, $physicalCharacteristics, $scopeAndContent, $archivalHistory, $appraisal)
    {
        // Create new information object
        $informationObject = new QubitInformationObject;
        $informationObject->setRoot();
        // Populate fields
        $informationObject->identifier = $accessionarchivalidentifier;
        $informationObject->title = $title;
        $informationObject->physicalCharacteristics = $physicalCharacteristics;
        $informationObject->scopeAndContent = $scopeAndContent;
        $informationObject->archivalHistory = $archivalHistory;
        $informationObject->appraisal = $appraisal;
        // Copy (not link) rights
        foreach (QubitRelation::getRelationsBySubjectId($id, array('typeId' => QubitTerm::RIGHT_ID)) as $item) {
            $sourceRight = $item->object;
            $right = new QubitRights;
            $right->act = $sourceRight->act;
            $right->startDate = $sourceRight->startDate;
            $right->endDate = $sourceRight->endDate;
            $right->basis = $sourceRight->basis;
            $right->restriction = $sourceRight->restriction;
            $right->copyrightStatus = $sourceRight->copyrightStatus;
            $right->copyrightStatusDate = $sourceRight->copyrightStatusDate;
            $right->copyrightJurisdiction = $sourceRight->copyrightJurisdiction;
            $right->statuteNote = $sourceRight->statuteNote;
            // Right holder
            if (isset($sourceRight->rightsHolder)) {
                $right->rightsHolder = $sourceRight->rightsHolder;
            }
            // I18n
            $right->rightsNote = $sourceRight->rightsNote;
            $right->copyrightNote = $sourceRight->copyrightNote;
            $right->licenseIdentifier = $sourceRight->licenseIdentifier;
            $right->licenseTerms = $sourceRight->licenseTerms;
            $right->licenseNote = $sourceRight->licenseNote;
            $right->statuteJurisdiction = $sourceRight->statuteJurisdiction;
            $right->statuteCitation = $sourceRight->statuteCitation;
            $right->statuteDeterminationDate = $sourceRight->statuteDeterminationDate;
            foreach ($sourceRight->rightsI18ns as $sourceRightI18n) {
                if ($this->context->user->getCulture() == $sourceRightI18n->culture) {
                    continue;
                }
                $rightI18n = new QubitRightsI18n;
                $rightI18n->rightNote = $sourceRightI18n->rightNote;
                $rightI18n->copyrightNote = $sourceRightI18n->copyrightNote;
                $rightI18n->licenseIdentifier = $sourceRightI18n->licenseIdentifier;
                $rightI18n->licenseTerms = $sourceRightI18n->licenseTerms;
                $rightI18n->licenseNote = $sourceRightI18n->licenseNote;
                $rightI18n->statuteJurisdiction = $sourceRightI18n->statuteJurisdiction;
                $rightI18n->statuteCitation = $sourceRightI18n->statuteCitation;
                $rightI18n->statuteNote = $sourceRightI18n->statuteNote;
                $rightI18n->culture = $sourceRightI18n->culture;
                $right->rightsI18ns[] = $rightI18n;
            }
            $right->save();
            $relation = new QubitRelation;
            $relation->object = $right;
            $relation->typeId = QubitTerm::RIGHT_ID;
            $informationObject->relationsRelatedBysubjectId[] = $relation;
        }
        // Populate creators (from QubitRelation to QubitEvent)
        foreach (QubitRelation::getRelationsByObjectId($id, array('typeId' => QubitTerm::CREATION_ID)) as $item) {
            $event = new QubitEvent;
            $event->actor = $item->subject;
            $event->typeId = QubitTerm::CREATION_ID;
            $informationObject->events[] = $event;
        }
        // Relationship between the information object and accession record
        $relation = new QubitRelation;
        $relation->object = $currentObject;
        $relation->typeId = QubitTerm::ACCESSION_ID;
        $informationObject->relationsRelatedBysubjectId[] = $relation;
        // Set publication status
        $informationObject->setPublicationStatus(sfConfig::get('app_defaultPubStatus', QubitTerm::PUBLICATION_STATUS_DRAFT_ID));
        $informationObject->save();
    }
}

