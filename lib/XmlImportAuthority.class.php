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
 * Import an XML document of Authority Record into Qubit.
 *
 * @package    AccesstoMemory
 * @subpackage library
 * @author     MJ Suhonos <mj@suhonos.ca>
 * @author     Peter Van Garderen <peter@artefactual.com>
 * @author     Mike Cantelon <mike@artefactual.com>
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class XmlImportAuthority
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
        'authority' => 'authority', 'donorauthority' => 'donorauthority');
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
        if ($importSchema == 'authority' || $importSchema == 'donorauthority') {
            $importDOM->validate();
            // if libxml threw errors, populate them to show in the template
            foreach (libxml_get_errors() as $libxmlerror) {
                $this->errors[] = sfContext::getInstance()->i18n->__('libxml error %code% on line %line% in input file: %message%', array('%code%' => $libxmlerror->code, '%message%' => $libxmlerror->message, '%line%' => $libxmlerror->line));
            }
        } else {
            throw new sfError404Exception('Unable to parse XML file: Perhaps not a Authority Record import file');
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
            $this->termData = QubitFlatfileImport::loadTermsFromTaxonomies(array(QubitTaxonomy::NOTE_TYPE_ID => 'noteTypes', QubitTaxonomy::ACTOR_ENTITY_TYPE_ID => 'actorTypes', QubitTaxonomy::ACTOR_RELATION_TYPE_ID => 'actorRelationTypes', QubitTaxonomy::DESCRIPTION_STATUS_ID => 'descriptionStatusTypes', QubitTaxonomy::DESCRIPTION_DETAIL_LEVEL_ID => 'detailLevelTypes'));
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
        // Search is already imported. If exist then update else insert
        QubitXMLImport::addLog("go through methods and populate properties>>>".$this->importID."<<<<", "", get_class($this), true);
        if ($this->importID != "") {
	        QubitXMLImport::addLog("Not empty:".$this->importID."<<<<", "", get_class($this), true);
            $updateObject = QubitActor::getByImportId($this->importID);
            if (isset($updateObject)) {
		        QubitXMLImport::addLog("Found previous entry:".$this->importID."<<<<", "", get_class($this), true);
	        	$currentObject = null;
                // jjp SITA 07 Jan 2015 - Add import ID
                $updateObject->actorImportId = $this->importID;
                $updateObject->authorizedFormOfName = $this->authorizedFormOfName;
                if (isset($this->authorityValues)) {
                    $updateObject->entityTypeId = $this->authorityValues['entityTypeId'];
                    $updateObject->history = $this->authorityValues['history'];
                    $updateObject->datesOfExistence = $this->authorityValues['datesOfExistence'];
                    $updateObject->places = $this->authorityValues['places'];
                    $updateObject->legalStatus = $this->authorityValues['legalStatus']; 
                    $updateObject->functions = $this->authorityValues['functions'];
                    $updateObject->mandates = $this->authorityValues['mandates'];
                    $updateObject->internalStructures = $this->authorityValues['internalStructures'];
                    $updateObject->generalContext = $this->authorityValues['generalContext']; 
                    $updateObject->rules = $this->authorityValues['rules'];
					if ($this->authorityValues['sources'] != "")
					{
                    	$updateObject->sources = $this->authorityValues['sources'];
					}                    
					$updateObject->revisionHistory = $this->authorityValues['revisionHistory'];
                    $updateObject->descriptionIdentifier = $this->authorityValues['descriptionIdentifier'];
                    $updateObject->corporateBodyIdentifiers = $this->authorityValues['corporateBodyIdentifiers']; 
                    $updateObject->descriptionStatusId = $this->authorityValues['status']; //
					if ($this->authorityValues['levelOfDetail'] != "")
					{
                	    $updateObject->descriptionDetailId = $this->authorityValues['levelOfDetail'];
					}                    
                    $updateObject->institutionResponsibleIdentifier = $this->authorityValues['descInstitutionIdentifier'];
                    $updateObject->language = $this->authorityValues['language'];
                }
                $updateObject->save();
                // Add Maintenance Note
                if (isset($this->authorityValues['maintenanceNotes']) && $this->authorityValues['maintenanceNotes'] != "") {
                    QubitXmlImport::addMaintenanceNote($updateObject->id, 'Maintenance note', $this->authorityValues['maintenanceNotes'], $this->termData['noteTypes']);
                }
                // write the ID onto the current XML node for tracking
                $domNode->setAttribute('xml:id', $updateObject->id);
                // Write Aliases
             //   QubitXmlImport::addAliases($updateObject->id, "parallel", $this->parallel);
                unset($this->parallel);
                $this->parallel = array();
             //   QubitXmlImport::addAliases($updateObject->id, "standardized", $this->standardized);
                unset($this->standardized);
             //   QubitXmlImport::addAliases($updateObject->id, "other", $this->other);
	            unset($this->other);
                // Add contact info
             //   QubitXmlImport::addUpdateContact($updateObject->id, $this->addressValues);
                unset($this->addressValues);
                // Write Relations
 // JJP                QubitXmlImport::addRelations($updateObject->id, $this->relationValues['category'], $this->relationValues['targetAuthorizedFormOfName'], $this->relationValues['relationDescription'], $this->relationValues['date'], $this->relationValues['startDate'], $this->relationValues['endDate'], $this->termData['actorRelationTypes']);

                unset($this->relationValues);
            } else {
	 	        QubitXMLImport::addLog("No previous record found:".$this->importID."<<<<", "", get_class($this), true);
                $currentObject->actorImportId = $this->importID;
                $currentObject->authorizedFormOfName = $this->authorizedFormOfName;
                $currentObject->entityTypeId = $this->authorityValues['entityTypeId'];
                $currentObject->institutionResponsibleIdentifier = $this->authorityValues['descInstitutionIdentifier'];
                // save the object after it's fully-populated
                $currentObject->save();
                
                // Add Maintenance Note
                if (isset($this->authorityValues['maintenanceNotes']) && $this->authorityValues['maintenanceNotes'] != "") {
                    QubitXmlImport::addMaintenanceNote($currentObject->id, 'Maintenance note', $this->authorityValues['maintenanceNotes'], $this->termData['noteTypes']);
                }
                // write the ID onto the current XML node for tracking
                $domNode->setAttribute('xml:id', $currentObject->id);
                // Write Aliases
                QubitXmlImport::addAliases($currentObject->id, "parallel", $this->parallel);
                unset($this->parallel);
                $this->parallel = array();
                QubitXmlImport::addAliases($currentObject->id, "standardized", $this->standardized);
                unset($this->standardized);
                QubitXmlImport::addAliases($currentObject->id, "other", $this->other);
	            unset($this->other);
                // Add contact info
                QubitXmlImport::addUpdateContact($currentObject->id, $this->addressValues);
                unset($this->addressValues);
                // Write Relations
// JJP                QubitXmlImport::addRelations($currentObject->id, $this->relationValues['category'], $this->relationValues['targetAuthorizedFormOfName'], $this->relationValues['relationDescription'], $this->relationValues['date'], $this->relationValues['startDate'], $this->relationValues['endDate'], $this->termData['actorRelationTypes']);

                //QubitXmlImport::addRelations($updateObject->id, $this->relatedValues['relatedNature'], $this->relatedValues['relatedTitle'], "fffff", $this->relatedValues['relatedDate'], $this->relatedValues['relatedStartDate'], $this->relatedValues['relatedEndDate'], $this->termData['actorRelationTypes']);

 //               unset($this->relationValues);
            }
        	$this->importID = "";
        } else {
	        QubitXMLImport::addLog("Empty:".$this->importID." <<<<", "", get_class($this), true);
            $currentObject->entityTypeId = $this->entityTypeId;
            $currentObject->institutionResponsibleIdentifier = $this->authorityValues['descInstitutionIdentifier'];
            // save the object after it's fully-populated
            $currentObject->save();
            // Add Maintenance Note
            if (isset($this->authorityValues['maintenanceNotes']) && $this->authorityValues['maintenanceNotes'] != "") {
                QubitXmlImport::addMaintenanceNote($currentObject->id, 'Maintenance note', $this->authorityValues['maintenanceNotes'], $this->termData['noteTypes']);
            }
            // write the ID onto the current XML node for tracking
            $domNode->setAttribute('xml:id', $currentObject->id);
            // Write Aliases
            QubitXmlImport::addAliases($currentObject->id, "parallel", $this->parallel);
            unset($this->parallel);
            $this->parallel = array();
            QubitXmlImport::addAliases($currentObject->id, "standardized", $this->standardized);
            unset($this->standardized);
            QubitXmlImport::addAliases($currentObject->id, "other", $this->other);
            unset($this->other);
            // Add contact info
            QubitXmlImport::addUpdateContact($currentObject->id, $this->addressValues);
            unset($this->addressValues);
            // Write Relations
// JJP            QubitXmlImport::addRelations($currentObject->id, $this->relationValues['category'], $this->relationValues['targetAuthorizedFormOfName'], $this->relationValues['relationDescription'], $this->relationValues['date'], $this->relationValues['startDate'], $this->relationValues['endDate'], $this->termData['actorRelationTypes']);

            //QubitXmlImport::addRelations($updateObject->id, $this->relatedValues['relatedNature'], $this->relatedValues['relatedTitle'], "fffff", $this->relatedValues['relatedDate'], $this->relatedValues['relatedStartDate'], $this->relatedValues['relatedEndDate'], $this->termData['actorRelationTypes']);

 //           unset($this->relationValues);
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
            $nodeList2 = $importDOM->xpath->query($methodMap['XPath'], $domNode);
            if (is_object($nodeList2)) {
                switch ($name) {
                case 'authorityName':
					QubitXMLImport::addLog("authorityName>>>in<<<<", "", get_class($this), false);
                     foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('./authorizedFormOfName', $domNode)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
				                $currentObject->authorizedFormOfName = $childNode->item(0)->nodeValue;
				                $this->authorizedFormOfName = $childNode->item(0)->nodeValue;
        						QubitXMLImport::addLog("authorityName>>>".$this->authorizedFormOfName." <<<<", "", get_class($this), false);
                            }
                        }
                    }
                    break;

                case 'authorityId':
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('./identifier', $domNode)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->importID = trim($childNode->item(0)->nodeValue);
        						QubitXMLImport::addLog("authorityId>>>".$this->importID." <<<<", "", get_class($this), true);
                                $currentObject->actorImportId = trim($childNode->item(0)->nodeValue);
                            }
                        }
                    }
                    break;

                case 'authorityDetail':
                    unset($this->authorityValues);
                    if (($childNode = $importDOM->xpath->query('.')) !== null) {
                        $this->entityTypeId = "";
                        $this->history = "";
                        $this->places = "";
                        $this->legalStatus = "";
                        $this->functions = "";
                        $this->mandates = "";
                        $this->internalStructures = "";
                        $this->generalContext = "";
                        $this->rules = "";
                        $this->sources = "";
                        $this->revisionHistory = "";
                        $this->descriptionIdentifier = "";
                        $this->corporateBodyIdentifiers = "";
                        $this->descInstitutionIdentifier = "";
                        $this->levelOfDetail = "";
                        $this->maintenanceNotes = "";
                        $this->language = "en";
                        $this->targetAuthorizedFormOfName = "";
                        $this->category = "";
                        $this->relationDescription = "";
                        $this->date = "";
                        $this->startDate = "";
                        $this->endDate = "";
                        $this->relationCulture = "en";

                        $this->relatedTitle = "";
                        $this->relatedNature = "";
                        $this->relatedType = "";
                        $this->relatedDate = "";
                        $this->relatedStartDate = "";
                        $this->relatedEndDate = "";
                        
	                    $this->primaryContact = "";
                        $this->contactPerson = "";
                        $this->email = "";
                        $this->fax = "";
                        $this->telephone = "";
                        $this->cell = "";
                        $this->website = "";
                        $this->streetAddress = "";
                        $this->city = "";
                        $this->region = "";
                        $this->countryCode = "";
                        $this->postalCode = "";
                        $this->postalAddress = "";
                        $this->postalCity = "";
                        $this->postalRegion = "";
                        $this->postalCountryCode = "";
                        $this->postalPostCode = "";
                        $this->latitude = "";
                        $this->longitude = "";
                        $this->contacttype = "";

                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('./entityTypeId', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->entityTypeId = QubitXmlImport::translateNameToTermId("type of entity", 'actorTypes', $this->termData, $childNode->item(0)->nodeValue);
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./history', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->history = $childNode->item(0)->nodeValue;
                                    $currentObject->history = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./datesOfExistence', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->datesOfExistence = $childNode->item(0)->nodeValue;
                                    $currentObject->datesOfExistence = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./places', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->places = $childNode->item(0)->nodeValue;
                                    $currentObject->places = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./legalStatus', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->legalStatus = $childNode->item(0)->nodeValue;
                                    $currentObject->legalStatus = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./functions', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->functions = $childNode->item(0)->nodeValue;
                                    $currentObject->functions = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./mandates', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->mandates = $childNode->item(0)->nodeValue;
                                    $currentObject->mandates = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./internalStructures', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->internalStructures = $childNode->item(0)->nodeValue;
                                    $currentObject->internalStructures = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./generalContext', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->generalContext = $childNode->item(0)->nodeValue;
                                    $currentObject->generalContext = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./rules', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->rules = $childNode->item(0)->nodeValue;
                                    $currentObject->rules = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./sources', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->sources = $childNode->item(0)->nodeValue;
                                    $currentObject->sources = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./revisionHistory', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->revisionHistory = $childNode->item(0)->nodeValue;
                                    $currentObject->revisionHistory = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./descriptionIdentifier', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->descriptionIdentifier = $childNode->item(0)->nodeValue;
                                    $currentObject->descriptionIdentifier = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./corporateBodyIdentifiers', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->corporateBodyIdentifiers = $childNode->item(0)->nodeValue;
                                    $currentObject->corporateBodyIdentifiers = $childNode->item(0)->nodeValue;

		                            $this->importID = trim($childNode->item(0)->nodeValue);
		    						QubitXMLImport::addLog("authorityId>>>".$this->importID." <<<<", "", get_class($this), true);
		                            $currentObject->actorImportId = trim($childNode->item(0)->nodeValue);


                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./descInstitutionIdentifier', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->descInstitutionIdentifier = $childNode->item(0)->nodeValue;
                                    $currentObject->descInstitutionIdentifier = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./status', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->status = QubitXmlImport::translateNameToTermId("Description Status", 'descriptionStatusTypes', $this->termData, $childNode->item(0)->nodeValue);
						            $currentObject->descriptionStatusId = QubitXmlImport::translateNameToTermId("Description Status", 'descriptionStatusTypes', $this->termData, $childNode->item(0)->nodeValue);
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./levelOfDetail', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->levelOfDetail = QubitXmlImport::translateNameToTermId("Level of Detail", 'detailLevelTypes', $this->termData, $childNode->item(0)->nodeValue);
						            $currentObject->descriptionDetailId = QubitXmlImport::translateNameToTermId("Level of Detail", 'detailLevelTypes', $this->termData, $childNode->item(0)->nodeValue);
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./maintenanceNotes', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->maintenanceNotes = $childNode->item(0)->nodeValue;
                                }
                            }
                            if (($childNode = $importDOM->xpath->query('./language', $item)) !== null) {
                                if (isset($childNode->item(0)->nodeValue)) {
                                    $this->language = $childNode->item(0)->nodeValue;
                                    $currentObject->language = $childNode->item(0)->nodeValue;
                                }
                            }
                            $this->authorityValues = array('entityTypeId' => $this->entityTypeId, 'history' => $this->history, 'datesOfExistence' => $this->datesOfExistence, 'places' => $this->places, 'legalStatus' => $this->legalStatus, 'functions' => $this->functions, 'mandates' => $this->mandates, 'internalStructures' => $this->internalStructures, 'generalContext' => $this->generalContext, 'rules' => $this->rules, 'sources' => $this->sources, 'revisionHistory' => $this->revisionHistory, 'descriptionIdentifier' => $this->descriptionIdentifier, 'corporateBodyIdentifiers' => $this->corporateBodyIdentifiers, 'status' => $this->status, 'levelOfDetail' => $this->levelOfDetail, 'descInstitutionIdentifier' => $this->descInstitutionIdentifier, 'maintenanceNotes' => $this->maintenanceNotes, 'language' => $this->language);
                        }
                    }
                    break;

                case 'authorityAliases':
                	unset($this->parallel);
                	$this->parallel = array();
                	unset($this->standardized);
                	$this->standardized = array();
                	unset($this->other);
                	$this->other = array();
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('./parallel', $item)) !== null) {
                            $parallelName = array();
							unset($parallelName);
                            foreach ($childNode as $ParallelName) {
                                $this->parallel[] = $ParallelName->nodeValue;
                            }
						}
 
                        if (($childNode = $importDOM->xpath->query('./standardized', $item)) !== null) {
                            $standardizedName = array();
							unset($standardizedName);
                            foreach ($childNode as $standardizedName) {
                                $this->standardized[] = $standardizedName->nodeValue;
                            }
						}
 
                        if (($childNode = $importDOM->xpath->query('./other', $item)) !== null) {                        
                            $otherName = array();
							unset($otherName);
                            foreach ($childNode as $otherName) {
                                $this->other[] = $otherName->nodeValue;
                            }
						}
                    }
                    break;

                case 'authorityRelations':
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/targetAuthorizedFormOfName', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->targetAuthorizedFormOfName = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/category', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->category = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/relationtype', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->relationtype = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/description', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->relationDescription = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/date', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->date = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/startDate', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->startDate = $childNode->item(0)->nodeValue;
                                if (isset($this->startDate) && is_object($this->startDate)) {
                                    $parsedDate = QubitXmlImport::parseDateLoggingErrors($this->startDate);
                                    if ($parsedDate) {
                                        $this->startDate = $parsedDate;
                                    }
                                }
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/endDate', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->endDate = $childNode->item(0)->nodeValue;
                                if (isset($this->endDate) && is_object($this->endDate)) {
                                    $parsedDate = QubitXmlImport::parseDateLoggingErrors($this->endDate);
                                    if ($parsedDate) {
                                        $this->endDate = $parsedDate;
                                    }
                                }
                            }
                        }

                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/relatedTitle', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->relatedTitle = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/relatedNature', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->relatedNature = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/relatedType', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->relatedType = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/relatedDate', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->relatedDate = $childNode->item(0)->nodeValue;
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/relatedStartDate', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->relatedStartDate = $childNode->item(0)->nodeValue;
                                if (isset($this->relatedStartDate) && is_object($this->relatedStartDate)) {
                                    $parsedDate = QubitXmlImport::parseDateLoggingErrors($this->relatedStartDate);
                                    if ($parsedDate) {
                                        $this->relatedStartDate = $parsedDate;
                                    }
                                }
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/relatedEndDate', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
                                $this->relatedEndDate = $childNode->item(0)->nodeValue;
                                if (isset($this->relatedEndDate) && is_object($this->relatedEndDate)) {
                                    $parsedDate = QubitXmlImport::parseDateLoggingErrors($this->relatedEndDate);
                                    if ($parsedDate) {
                                        $this->relatedEndDate = $parsedDate;
                                    }
                                }
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('//authority/authorityinfo/authorityrelations/culture', $item)) !== null) {
                            if (isset($childNode->item(0)->nodeValue)) {
		                        $this->relationCulture = $childNode->item(0)->nodeValue;
                            }
                        }
 // JJP                       $this->relationValues = array('targetAuthorizedFormOfName' => $this->targetAuthorizedFormOfName, 'category' => $this->category, 'relationDescription' => $this->relationDescription, 'date' => $this->date, 'startDate' => $this->startDate, 'endDate' => $this->endDate, 'relationCulture' => $this->relationCulture, 'relationtype' => $this->relationtype);

// JJP                        $this->relatedValues = array('relatedTitle' => $this->relatedTitle, 'relatedNature' => $this->relatedNature, 'relatedType' => $this->relatedType, 'relatedDate' => $this->relatedDate, 'relatedStartDate' => $this->relatedStartDate, 'relatedEndDate' => $this->relatedEndDate);
                    }

                    break;

                case 'authorityAddress':
                    if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('./primarycontact', $item)) !== null) {
                                $this->primaryContact = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./contactperson', $item)) !== null) {
                                $this->contactPerson = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./email', $item)) !== null) {
                                $this->email = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./fax', $item)) !== null) {
                                $this->fax = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./telephone', $item)) !== null) {
                                $this->telephone = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./cell', $item)) !== null) {
                                $this->cell = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./website', $item)) !== null) {
                                $this->website = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./streetaddress', $item)) !== null) {
                                $this->streetAddress = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./city', $item)) !== null) {
                                $this->city = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./region', $item)) !== null) {
                                $this->region = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./countrycode', $item)) !== null) {
                                $this->countryCode = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalcode', $item)) !== null) {
                                $this->postalCode = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postaladdress', $item)) !== null) {
                                $this->postalAddress = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalcity', $item)) !== null) {
                                $this->postalCity = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalregion', $item)) !== null) {
                                $this->postalRegion = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalcountrycode', $item)) !== null) {
                                $this->postalCountryCode = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./postalpostcode', $item)) !== null) {
                                $this->postalPostCode = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./latitude', $item)) !== null) {
                                $this->latitude = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./longitude', $item)) !== null) {
                                $this->longitude = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./note', $item)) !== null) {
                                $this->contacttype = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./contacttype', $item)) !== null) {
                                $this->note = $childNode->item(0)->nodeValue;
                            }
                            $this->addressValues = array('primaryContact' => $this->primaryContact, 'contactPerson' => $this->contactPerson, 'email' => $this->email, 'fax' => $this->fax, 'telephone' => $this->telephone, 'cell' => $this->cell, 'website' => $this->website, 'streetAddress' => $this->streetAddress, 'city' => $this->city, 'region' => $this->region, 'countryCode' => $this->countryCode, 'postalCode' => $this->postalCode, 'postalAddress' => $this->postalAddress, 'postalCity' => $this->postalCity, 'postalRegion' => $this->postalRegion, 'postalCountryCode' => $this->postalCountryCode, 'postalPostCode' => $this->postalPostCode, 'latitude' => $this->latitude, 'longitude' => $this->longitude, $this->note, $this->contacttype);
                        }
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
                        // invoke the object and method defined in the schema map
                        call_user_func_array(array(&$currentObject, $methodMap['Method']), $parameters);
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
}

