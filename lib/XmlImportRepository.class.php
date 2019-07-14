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
 * Import an XML document of Repository into Qubit.
 *
 * jjp SITA 07 Jan 2015 - Add import ID to update record if already exist
 *
 * @package    AccesstoMemory
 * @subpackage library
 * @author     MJ Suhonos <mj@suhonos.ca>
 * @author     Peter Van Garderen <peter@artefactual.com>
 * @author     Mike Cantelon <mike@artefactual.com>
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class XmlImportRepositry
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
        'repository' => 'repository');
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
        if ($importSchema == 'repository') {
            $importDOM->validate();
            // if libxml threw errors, populate them to show in the template
            foreach (libxml_get_errors() as $libxmlerror) {
                $this->errors[] = sfContext::getInstance()->i18n->__('libxml error %code% on line %line% in input file: %message%', array('%code%' => $libxmlerror->code, '%message%' => $libxmlerror->message, '%line%' => $libxmlerror->line));
            }
        } else {
            throw new sfError404Exception('Unable to parse XML file: Perhaps not a Repository import file');
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
            $this->termData = QubitXMLImport::loadTermsFromTaxonomies(array(
            QubitTaxonomy::ACCESSION_ACQUISITION_TYPE_ID => 'acquisitionTypes', 
            QubitTaxonomy::ACCESSION_RESOURCE_TYPE_ID => 'resourceTypes', 
            QubitTaxonomy::ACCESSION_PROCESSING_STATUS_ID => 'processingStatus', 
            QubitTaxonomy::ACCESSION_PROCESSING_PRIORITY_ID => 'processingPriority', 
            QubitTaxonomy::REPOSITORY_TYPE_ID => 'repositoryentityTypes', 
            QubitTaxonomy::DESCRIPTION_STATUS_ID => 'descriptionStatusTypes', 
            QubitTaxonomy::DESCRIPTION_DETAIL_LEVEL_ID => 'detailLevelTypes', 
            QubitTaxonomy::NOTE_TYPE_ID => 'noteTypes',
            QubitTaxonomy::THEMATIC_AREA_ID => 'thematicArea'));
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
        // jjp SITA 07 Jan 2015 - Search is already imported. If exist then update else insert
        QubitXMLImport::addLog("go through methods and populate properties>>>".$this->importID." <<<<", "", get_class($this), false);
        if ($this->importID != "") {
            $updateObject = QubitActor::getByImportId($this->importID);
            QubitXMLImport::addLog("Get actor >>>".$this->importID." <<<<", "", get_class($this), false);
            if (isset($updateObject)) {
                QubitXMLImport::addLog("repositoryId>>>".$this->importID." <<<<", "", get_class($this), false);
                $updateObject->actorImportId = $this->importID;
                $updateObject->authorizedFormOfName = $currentObject->authorizedFormOfName;
                if (isset($this->repositoryValues)) {
                    // actor
                    $updateObject->history = $this->repositoryValues['history'];
                    $updateObject->datesOfExistence = $this->repositoryValues['datesOfExistence'];
                    $updateObject->places = $this->repositoryValues['places'];
                    $updateObject->legalStatus = $this->repositoryValues['legalStatus'];
                    $updateObject->functions = $this->repositoryValues['functions'];
                    $updateObject->mandates = $this->repositoryValues['mandates'];
                    $updateObject->internalStructures = $this->repositoryValues['internalStructures'];
                    $updateObject->generalContext = $this->repositoryValues['generalContext'];
                    $updateObject->rules = $this->repositoryValues['rules'];
                    $updateObject->sources = $this->repositoryValues['sources'];
                    $updateObject->revisionHistory = $this->repositoryValues['revisionHistory'];
                    $updateObject->descIdentifier = $this->repositoryValues['descIdentifier'];
                    $updateObject->corporateBodyIdentifiers = $this->repositoryValues['corporateBodyIdentifiers'];
                    // repository
                    $updateObject->authorizedFormOfName = $this->repositoryValues['authorizedFormOfName'];
                    $updateObject->identifier = $this->identifier;
                    $updateObject->openingTimes = $this->repositoryValues['openingTimes'];
                    $updateObject->geoculturalContext = $this->repositoryValues['geoculturalContext'];
                    $updateObject->internalStructures = $this->repositoryValues['internalStructures'];
                    $updateObject->holdings = $this->repositoryValues['holdings'];
                    $updateObject->findingAids = $this->repositoryValues['findingAids'];
                    $updateObject->uploadLimit = $this->repositoryValues['uploadLimit'];
                    $updateObject->collectingPolicies = $this->repositoryValues['collectingPolicies'];
                    $updateObject->buildings = $this->repositoryValues['buildings'];
                    $updateObject->accessConditions = $this->repositoryValues['accessConditions'];
                    $updateObject->disabledAccess = $this->repositoryValues['disabledAccess'];
                    $updateObject->researchServices = $this->repositoryValues['researchServices'];
                    $updateObject->reproductionServices = $this->repositoryValues['reproductionServices'];
                    $updateObject->publicFacilities = $this->repositoryValues['publicFacilities'];
                    $updateObject->descInstitutionIdentifier = $this->repositoryValues['descInstitutionIdentifier'];
                    $updateObject->descRules = $this->repositoryValues['descRules'];
                    $updateObject->descSources = $this->repositoryValues['descSources'];
                    $updateObject->descRevisionHistory = $this->repositoryValues['descRevisionHistory'];
                    $updateObject->descStatusId = $this->repositoryValues['status'];
                    $updateObject->descDetailId = $this->repositoryValues['levelOfDetail'];
                }
                // save the object after it's fully-populated with update data
                $updateObject->save();
                // write the ID onto the current XML node for tracking
                $domNode->setAttribute('xml:id', $updateObject->id);
                // Add Maintenance Note
                if (isset($this->repositoryValues['maintenanceNotes']) && $this->repositoryValues['maintenanceNotes'] != "") {
                    QubitXmlImport::addMaintenanceNote($updateObject->id, 'Maintenance note', $this->repositoryValues['maintenanceNotes'], $this->termData['noteTypes']);
                }
                // Write Aliases

                QubitXmlImport::addAliases($updateObject->id, "parallel", $this->parallelName);
                unset($this->parallelName);
                QubitXmlImport::addAliases($updateObject->id, "other", $this->otherName);
                unset($this->otherName);
                // jjp SITA - Add contact info
                QubitXmlImport::addUpdateContact($updateObject->id, $this->addressValues);
                unset($this->addressValues);
                // jjp SITA - update Term Relation table Entity Type
                QubitXmlImport::updateTermRelation($updateObject->id, $this->repositoryValues['entityTypeId']);
                // jjp SITA - update Term Relation table Thematic Area
                QubitXmlImport::updateTermRelation($updateObject->id, $this->repositoryValues['thematicArea']);
                unset($this->repositoryValues);
                unset($this->entityType);
	            unset($this->thematicArea);
            } else {
                //update
                if (isset($this->repositoryValues)) {
                    // actor
                    $currentObject->history = $this->repositoryValues['history'];
                    $currentObject->datesOfExistence = $this->repositoryValues['datesOfExistence'];
                    $currentObject->places = $this->repositoryValues['places'];
                    $currentObject->legalStatus = $this->repositoryValues['legalStatus'];
                    $currentObject->functions = $this->repositoryValues['functions'];
                    $currentObject->mandates = $this->repositoryValues['mandates'];
                    $currentObject->internalStructures = $this->repositoryValues['internalStructures'];
                    $currentObject->generalContext = $this->repositoryValues['generalContext'];
                    $currentObject->rules = $this->repositoryValues['rules'];
                    $currentObject->sources = $this->repositoryValues['sources'];
                    $currentObject->revisionHistory = $this->repositoryValues['revisionHistory'];
                    $currentObject->descIdentifier = $this->repositoryValues['descIdentifier'];
                    $currentObject->corporateBodyIdentifiers = $this->repositoryValues['corporateBodyIdentifiers'];
                    // repository
                    $updacurrentObjectteObject->authorizedFormOfName = $this->repositoryValues['authorizedFormOfName'];
                    $currentObject->identifier = $this->identifier;
                    $currentObject->openingTimes = $this->repositoryValues['openingTimes'];
                    $currentObject->geoculturalContext = $this->repositoryValues['geoculturalContext'];
                    $currentObject->internalStructures = $this->repositoryValues['internalStructures'];
                    $currentObject->holdings = $this->repositoryValues['holdings'];
                    $currentObject->findingAids = $this->repositoryValues['findingAids'];
                    $currentObject->uploadLimit = $this->repositoryValues['uploadLimit'];
                    $currentObject->collectingPolicies = $this->repositoryValues['collectingPolicies'];
                    $currentObject->buildings = $this->repositoryValues['buildings'];
                    $currentObject->accessConditions = $this->repositoryValues['accessConditions'];
                    $currentObject->disabledAccess = $this->repositoryValues['disabledAccess'];
                    $currentObject->researchServices = $this->repositoryValues['researchServices'];
                    $currentObject->reproductionServices = $this->repositoryValues['reproductionServices'];
                    $currentObject->publicFacilities = $this->repositoryValues['publicFacilities'];
                    $currentObject->descInstitutionIdentifier = $this->repositoryValues['descInstitutionIdentifier'];
                    $currentObject->descRules = $this->repositoryValues['descRules'];
                    $currentObject->descSources = $this->repositoryValues['descSources'];
                    $currentObject->descRevisionHistory = $this->repositoryValues['descRevisionHistory'];
                    $currentObject->descStatusId = $this->repositoryValues['status'];
                    $currentObject->descDetailId = $this->repositoryValues['levelOfDetail'];
                }

                $currentObject->authorizedFormOfName = $this->repositoryValues['authorizedFormOfName'];
                $currentObject->identifier = $this->identifier;
                // jjp SITA 07 Jan 2015 - Add import ID
                $currentObject->actorImportId = $this->importID;
                $currentObject->descStatusId = $this->status;
                $currentObject->descDetailId = $this->levelOfDetail;
                // save the object after it's fully-populated with import ID
                $currentObject->save();
                // write the ID onto the current XML node for tracking
                $domNode->setAttribute('xml:id', $currentObject->id);
                // Add Maintenance Note
                if (isset($this->repositoryValues['maintenanceNotes']) && $this->repositoryValues['maintenanceNotes'] != "") {
                    QubitXmlImport::addMaintenanceNote($currentObject->id, 'Maintenance note', $this->repositoryValues['maintenanceNotes'], $this->termData['noteTypes']);
                }
                // Write Aliases
                QubitXmlImport::addAliases($currentObject->id, "parallel", $this->parallelName);
                unset($this->parallelName);
                QubitXmlImport::addAliases($currentObject->id, "other", $this->otherName);
                unset($this->otherName);
                // jjp SITA 06 Jan 2015 - Add contact info
                QubitXmlImport::addUpdateContact($currentObject->id, $this->addressValues);
                unset($this->addressValues);
                // jjp SITA - update Term Relation table Entity Type
                QubitXmlImport::updateTermRelation($currentObject->id, $this->repositoryValues['entityTypeId']);
                // jjp SITA - update Term Relation table Thematic Area
                QubitXmlImport::updateTermRelation($currentObject->id, $this->repositoryValues['thematicArea']);
                unset($this->repositoryValues);
                unset($this->entityType);
	            unset($this->thematicArea);
	            unset($currentObject->language);
            }
        } else {
            // save the object after it's fully-populated
            //update
            $currentObject->authorizedFormOfName = $this->repositoryValues['authorizedFormOfName'];
            $currentObject->identifier = $this->identifier;
            $currentObject->descStatusId = $this->status;
            $currentObject->descDetailId = $this->levelOfDetail;
            $currentObject->save();
            // write the ID onto the current XML node for tracking
            $domNode->setAttribute('xml:id', $currentObject->id);
           // Add Maintenance Note
            if (isset($this->repositoryValues['maintenanceNotes']) && $this->repositoryValues['maintenanceNotes'] != "") {
                QubitXmlImport::addMaintenanceNote($currentObject->id, 'Maintenance note', $this->repositoryValues['maintenanceNotes'], $this->termData['noteTypes']);
            }
            // Write Aliases
            QubitXmlImport::addAliases($currentObject->id, "parallel", $this->parallelName);
            unset($this->parallelName);
	        QubitXmlImport::addAliases($currentObject->id, "other", $this->otherName);
            unset($this->otherName);
            // jjp SITA 06 Jan 2015 - Add contact info
            QubitXmlImport::addUpdateContact($currentObject->id, $this->addressValues);
            unset($this->addressValues);
            // jjp SITA - update Term Relation table
            QubitXmlImport::updateTermRelation($currentObject->id, $this->repositoryValues['entityTypeId']);
            // jjp SITA - update Term Relation table Thematic Area
            QubitXmlImport::updateTermRelation($currentObject->id, $this->repositoryValues['thematicArea']);
            unset($this->repositoryValues);
            unset($this->entityType);
            unset($this->thematicArea);
            unset($currentObject->language);
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
                case 'repositoryName':
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                            $currentObject->authorizedFormOfName = $childNode->item(0)->nodeValue;
                        }
                    }
                    break;

                case 'identifier':
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                            $this->importID = $childNode->item(0)->nodeValue;
                            $this->identifier = $childNode->item(0)->nodeValue;
                            QubitXMLImport::addLog("identifier>>>".$this->importID." <<<<", "", get_class($this), false);
                        }
                    }
                    break;

                case 'repositoryId':
                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                            $this->importID = $childNode->item(0)->nodeValue;
                            $this->identifier = $childNode->item(0)->nodeValue;
                            QubitXMLImport::addLog("repositoryId>>>".$this->importID." <<<<", "", get_class($this), false);
                        }
                    }
                    break;

                case 'repositoryDetail':
                    unset($this->repositoryValues);
                    foreach ($nodeList2 as $item) {
                        //repository detail
                        if (($childNode = $importDOM->xpath->query('./entityTypeId', $item)) !== null) {
                            $entityType = array();
                            foreach ($childNode as $entityTypeId) {
                                $this->entityType[] = QubitXmlImport::translateNameToTermId("Repository Entity Type", 'repositoryentityTypes', $this->termData, $entityTypeId->nodeValue);
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('./history', $item)) !== null) {
                            $currentObject->history = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./datesOfExistence', $item)) !== null) {
                            $currentObject->datesOfExistence = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./places', $item)) !== null) {
	                            $currentObject->places = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./legalStatus', $item)) !== null) {
                            $currentObject->legalStatus = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./functions', $item)) !== null) {
                            $currentObject->functions = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./mandates', $item)) !== null) {
                            $currentObject->mandates = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./internalStructures', $item)) !== null) {
                            $currentObject->internalStructures = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./generalContext', $item)) !== null) {
                            $currentObject->generalContext = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./rules', $item)) !== null) {
                            $currentObject->rules = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./sources', $item)) !== null) {
                            $currentObject->sources = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./revisionHistory', $item)) !== null) {
                            $currentObject->revisionHistory = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./descIdentifier', $item)) !== null) {
                            $currentObject->descIdentifier = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./corporateBodyIdentifiers', $item)) !== null) {
                            $currentObject->corporateBodyIdentifiers = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./openingTimes', $item)) !== null) {
                            $currentObject->openingTimes = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./geoculturalContext', $item)) !== null) {
                            $currentObject->geoculturalContext = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./holdings', $item)) !== null) {
                            $currentObject->holdings = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./findingAids', $item)) !== null) {
                            $currentObject->findingAids = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./internalStructures', $item)) !== null) {
                            $currentObject->internalStructures = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./uploadLimit', $item)) !== null) {
                            $currentObject->uploadLimit = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./collectingPolicies', $item)) !== null) {
                            $currentObject->collectingPolicies = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./buildings', $item)) !== null) {
                            $currentObject->buildings = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./accessConditions', $item)) !== null) {
                            $currentObject->accessConditions = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./disabledAccess', $item)) !== null) {
                            $currentObject->disabledAccess = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./researchServices', $item)) !== null) {
                            $currentObject->researchServices = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./reproductionServices', $item)) !== null) {
                            $currentObject->reproductionServices = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./publicFacilities', $item)) !== null) {
                            $currentObject->publicFacilities = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./descInstitutionIdentifier', $item)) !== null) {
                            $currentObject->descInstitutionIdentifier = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./descRules', $item)) !== null) {
                            $currentObject->descRules = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./descSources', $item)) !== null) {
                            $currentObject->descSources = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./status', $item)) !== null) {
                            $this->status = QubitXmlImport::translateNameToTermId("Description Status", 'descriptionStatusTypes', $this->termData, $childNode->item(0)->nodeValue);
                        }
                        if (($childNode = $importDOM->xpath->query('./thematicArea', $item)) !== null) {
                        
                            foreach ($childNode as $nodeThematicArea) {
	                            $this->thematicArea[] = QubitXmlImport::translateNameToTermId("Thematic Area", 'thematicArea', $this->termData, $nodeThematicArea->nodeValue);
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('./levelOfDetail', $item)) !== null) {
                            if ($childNode != "") {
                                $this->levelOfDetail = QubitXmlImport::translateNameToTermId("Level of Detail", 'detailLevelTypes', $this->termData, $childNode->item(0)->nodeValue);
                            }
                        }
                        if (($childNode = $importDOM->xpath->query('./maintenanceNotes', $item)) !== null) {
                            $this->maintenanceNotes = $childNode->item(0)->nodeValue;
                        }
                        if (($childNode = $importDOM->xpath->query('./descRevisionHistory', $item)) !== null) {
                            $currentObject->descRevisionHistory = $childNode->item(0)->nodeValue;
                        }
                        unset($this->repositoryValues);
                        $this->repositoryValues = array('entityTypeId' => $this->entityType, 'history' => $currentObject->history, 'datesOfExistence' => $currentObject->datesOfExistence, 'places' => $currentObject->places, 'legalStatus' => $currentObject->legalStatus, 'functions' => $currentObject->functions, 'mandates' => $currentObject->mandates, 'internalStructures' => $currentObject->internalStructures, 'generalContext' => $currentObject->generalContext, 'rules' => $currentObject->rules, 'sources' => $currentObject->sources, 'revisionHistory' => $currentObject->revisionHistory, 'descIdentifier' => $currentObject->descIdentifier, 'corporateBodyIdentifiers' => $currentObject->corporateBodyIdentifiers, 'authorizedFormOfName' => $currentObject->authorizedFormOfName, 'identifier' => $currentObject->identifier, 'openingTimes' => $currentObject->openingTimes, 'geoculturalContext' => $currentObject->geoculturalContext, 'holdings' => $currentObject->holdings, 'findingAids' => $currentObject->findingAids, 'internalStructures' => $currentObject->internalStructures, 'internalStructures' => $currentObject->internalStructures, 'uploadLimit' => $currentObject->uploadLimit, 'collectingPolicies' => $currentObject->collectingPolicies, 'buildings' => $currentObject->buildings, 'accessConditions' => $currentObject->accessConditions, 'disabledAccess' => $currentObject->disabledAccess, 'researchServices' => $currentObject->researchServices, 'reproductionServices' => $currentObject->reproductionServices, 'publicFacilities' => $currentObject->publicFacilities, 'descInstitutionIdentifier' => $currentObject->descInstitutionIdentifier, 'descRules' => $currentObject->descRules, 
                        'descSources' => $currentObject->descSources, 
                        'descRevisionHistory' => $currentObject->descRevisionHistory, 
                        'status' => $this->status, 
                        'levelOfDetail' => $this->levelOfDetail, 
                        'maintenanceNotes' => $this->maintenanceNotes, 
                        'thematicArea' => $this->thematicArea);
                    }
                    break;

                case 'repositoryAliases':

                    foreach ($nodeList2 as $item) {
                        if (($childNode = $importDOM->xpath->query('./parallel', $item)) !== null) {
                            $parallelName = array();
							unset($parallelName);
                            foreach ($childNode as $ParallelName) {
                                $this->parallelName[] = $ParallelName->nodeValue;
                            }
						}
                        if (($childNode = $importDOM->xpath->query('./other', $item)) !== null) {
                            $otherName = array();
							unset($otherName);
                            foreach ($childNode as $OtherName) {
                                $this->otherName[] = $OtherName->nodeValue;
                            }
                        }
                    }
                    break;

                case 'repositoryAddress':
                    if (($childNode = $importDOM->xpath->query('.', $item)) !== null) {
                        foreach ($nodeList2 as $item) {
                            if (($childNode = $importDOM->xpath->query('./primarycontact', $item)) !== null) {
                                $primaryContact = $childNode->item(0)->nodeValue;
                            }
                            if (($childNode = $importDOM->xpath->query('./title', $item)) !== null) {
                                $titleText = $childNode->item(0)->nodeValue;
                                $title = QubitTermI18n::getIdByName($titleText, QubitTaxonomy::TITLE)->id;
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
                                $contacttype = $childNode->item(0)->nodeValue;
                            }
							unset($this->addressValues);
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
                            'city' => $city, 'region' => $region, 
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
                            'contacttype' => $contacttype);
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

