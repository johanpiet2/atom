<?php
/**
 * Presevation Object edit component.
 *
 * @package    qubit
 * @subpackage Preservation Module
 * @author     jjp
 * @version    SVN: $Id
 */

class reportsbrowseUnPublishAction extends sfAction {
    public 
    function execute($request) {
        
        if (!$this->getUser()->isAuthenticated()) {
            QubitAcl::forwardUnauthorized();
        }
        $this->form = new sfForm;
        
        if (isset($this->getRoute()->resource)) {
            $this->resource = $this->getRoute()->resource;
            $this->form->setDefault('parent', $this->context->routing->generate(null, array(
                $this->resource
            )));
            $this->form->setValidator('parent', new sfValidatorString);
            $this->form->setWidget('parent', new sfWidgetFormInputHidden);
        }
        // Check parameter
        
        if (isset($request->type)) {
            $this->type = $request->type;
        }
        
        if (!isset($request->limit)) {
            $request->limit = sfConfig::get('app_hits_per_page');
        }
        $criteria = new Criteria;
        $criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
        $criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAccessObject::ID);
        $criteria->addJoin(QubitAccessObject::ID, QubitAccessObjectI18n::ID);
        $criteria->add(QubitAccessObject::ID, $this->resource->id, Criteria::EQUAL);
        $accessId = $this->resource->id;
        
        // Page results
        $this->pager = new QubitPager('QubitAccessObject');
        $this->pager->setCriteria($criteria);
        $this->pager->setMaxPerPage($request->limit);
        $this->pager->setPage($request->page);
        
        if ($request->isMethod('post')) {
            
            foreach ($_REQUEST as $key => $value) {
                
                if (substr($key, 0, 2) == "id") {
                    $aID = $value;
                }
            }
            $filterCriteria = new Criteria;
            $filterCriteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
            $filterCriteria->addJoin(QubitRelation::SUBJECT_ID, QubitAccessObject::ID);
            $filterCriteria->addJoin(QubitAccessObject::ID, QubitAccessObjectI18n::ID);
            $filterCriteria->add(QubitAccessObject::ID, $aID, Criteria::EQUAL); //
            $accessObject = QubitInformationObject::getByCriteria($filterCriteria);
            
            if (isset($accessObject)) {
                $unpublishname = "unpublish_" . $accessObject->id . "_" . date("Y-m-dHis") . ".xml";
                $imp = new DOMImplementation;
                // Creates a DOMDocumentType instance
                $dtd = $imp->createDocumentType('unpublish', 'SYSTEM', '');
                // Creates a DOMDocument instance
                $doc = $imp->createDocument("", "", $dtd);
                // we want a nice output
                $doc->formatOutput = true;
                // Set other properties
                $doc->encoding = 'UTF-8';
                $doc->standalone = false;
                $root = $doc->createElement('unpublish');
                $root = $doc->appendChild($root);
                $uniqueid = $doc->createElement('uniqueid');
                $uniqueid = $root->appendChild($uniqueid);
                $text = $doc->createTextNode($accessObject->identifier);
                $text = $uniqueid->appendChild($text);
                $unittitle = $doc->createElement('unittitle');
                $unittitle = $root->appendChild($unittitle);
                $text = $doc->createTextNode($accessObject->title);
                $text = $unittitle->appendChild($text);
                $unitid = $doc->createElement('unitid');
                $unitid = $root->appendChild($unitid);
                $text = $doc->createTextNode($accessObject->importId);
                $text = $unitid->appendChild($text);
                
                foreach ($accessObject->getPresevationObjects() as $pitem) {
                    
                    if (isset($pitem->mediumId)) {
                        $medium = $doc->createElement('medium');
                        $medium = $root->appendChild($medium);
                        $text = $doc->createTextNode(QubitTerm::getById($pitem->mediumId));
                        $text = $medium->appendChild($text);
                    }
                }
                $format = $doc->createElement('format');
                $format = $root->appendChild($format);
                $text = $doc->createTextNode($accessObject->formatId);
                $text = $format->appendChild($text);
                $filereference = $doc->createElement('filereference');
                $filereference = $root->appendChild($filereference);
                $text = $doc->createTextNode('');
                $text = $filereference->appendChild($text);
                $unpublishPath = QubitSetting::getByName('unpublish_path');
                
                if ($unpublishPath == null) {
                    throw new Exception("No upload path defined. Contact support/administrator");
                }
                else {
                    $doc->save($unpublishPath . $unpublishname);
                    chmod($unpublishPath . $unpublishname, 0775);
                }
                $accessObj = QubitAccessObject::getById($aID);
                
                if (isset($accessObj)) {
                    $accessObj->published = 0;
                    $accessObj->publishId = QubitXmlImport::translateNameToTermId2('Publish', QubitTerm::PUBLISH_ID, 'No');
                    $accessObj->save();
                }
                else {
                    $errorMsg = "Problem finding record to Unpublish - Please call Support/Administrator";
                    throw new Exception($errorMsg);
                }
            }
            else {
                $errorMsg = "Problem finding record to Unpublish - Please call Support/Administrator";
                throw new Exception($errorMsg);
            }
        }
    }
}
