<?php
/**
 * Publix to external source component.
 *
 * @package    qubit
 * @subpackage Publish Module
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 * @version    SVN: $Id
 */
class publishbrowsePublishAction extends sfAction
{
    public function execute($request)
    {
        $eadLevels             = array(
            'class',
            'collection',
            'file',
            'fonds',
            'item',
            'otherlevel',
            'recordgrp',
            'series',
            'subfonds',
            'subgrp',
            'subseries'
        );
        $this->iso639convertor = new fbISO639_Map;
        if (!$this->getUser()->isAuthenticated()) {
            QubitAcl::forwardUnauthorized();
        }
        
        $relationParentId = "";
        $this->form       = new sfForm;
        if (isset($this->getRoute()->resource)) {
            $infoObject2 = $this->getRoute()->resource;
            $this->form->setDefault('parent', $this->context->routing->generate(null, array(
                $infoObject2
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
        
        // get id's to filter on
        $this->classification = QubitXmlImport::translateNameToTermId2('Classification', QubitTerm::CLASSIFICATION_ID, 'Public');
        $this->publish        = QubitXmlImport::translateNameToTermId2('Publish', QubitTerm::PUBLISH_ID, 'Yes');
        $this->sensitive      = QubitXmlImport::translateNameToTermId2('Sensitive', QubitTerm::SENSITIVITY_ID, 'No');
        $this->refusal        = QubitXmlImport::translateNameToTermId2('Refusal', QubitTerm::REFUSALS_ID, 'None');
        $criteria             = new Criteria;
        $criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
        $criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAccessObject::ID);
        $criteria->addJoin(QubitAccessObject::ID, QubitAccessObjectI18n::ID);
        $criteria->add(QubitAccessObjectI18n::PUBLISHED, 0, Criteria::EQUAL); //Not published yet
        $criteria->add(QubitAccessObjectI18n::CLASSIFICATION_ID, $this->classification, Criteria::EQUAL); //public - Only Public to publish
        $criteria->add(QubitAccessObjectI18n::PUBLISH_ID, $this->publish, Criteria::EQUAL); // Publish Yes/No - Only No to publish
        $criteria->add(QubitAccessObjectI18n::SENSITIVITY_ID, $this->sensitive, Criteria::EQUAL); // Sensitive Yes/No - Only No to publish
        $criteria->add(QubitAccessObjectI18n::REFUSAL_ID, $this->refusal, Criteria::EQUAL); // Refusal None - Only None to publish
 
		//SITA - to fix cache
		$sids      = array();
		// Show only Repositories linked to user - Administrator can see all JJP SITA One Instance
		if ((!$this->context->user->isAdministrator()) && (QubitSetting::getByName('open_system') == '0')) {
		    $repositories = new QubitUser;
		    foreach (QubitRepository::getAll() as $item) {
		        if ($item->__toString() != "") {
		            if (0 < count($userRepos = $repositories->getRepositoriesById($this->context->user->getAttribute('user_id')))) {
		                $key = array_search($item->id, $userRepos);
		                if (false !== $key) {
		                    $sids[] = $item->id;
		                }
		            }
		        }
		    }
		    $criteria->add(QubitInformationObject::REPOSITORY_ID, $sids, Criteria::IN); // Refusal None - Only None to publish
		}

        switch ($request->sort) {
            case 'nameDown':
                
                break;
            
            case 'conditionDown':
                $criteria->addDescendingOrderByColumn('condition');
                break;
            
            case 'conditionUp':
                $criteria->addAscendingOrderByColumn('condition');
                break;
            
            case 'nameUp':
            default:
                $request->sort = 'nameUp';
                
        }
        
        // Page results
        $this->pager = new QubitPager('QubitAccessObject');
        $this->pager->setCriteria($criteria);
        $this->pager->setMaxPerPage($request->limit);
        $this->pager->setPage($request->page);
				
        if ($request->isMethod('post')) {
            $stack = array();
            foreach ($_REQUEST as $key => $value) {
                if (substr($key, 0, 2) == "id") {
                    $aID = $value;
                } else {
                    $aYN = $value;
                    array_push($stack, $aID, $aYN);
                }
            }
            
            $ii = 0;
            foreach ($stack as $value) {
				QubitXMLImport::addLog("Go in and publish +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++", "", get_class($this), true);
                $ii += 1;
                if ($ii % 2) {
                    $tID = $value;
                } else {
                    $tYN = $value;
                    if ($tYN == "Yes") {
                        foreach (QubitRelation::getRelationsBySubjectId($tID) as $item2) {
                            $filterCriteria = new Criteria;
                            
                            // Exclude root
                            $filterCriteria->add(QubitInformationObject::ID, QubitInformationObject::ROOT_ID, Criteria::NOT_EQUAL);
                            $filterCriteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
                            $filterCriteria->add(QubitInformationObjectI18n::CULTURE, $this->context->user->getCulture());
                            $filterCriteria->add($filterCriteria->getNewCriterion(QubitInformationObject::ID, $item2->objectId, Criteria::LIKE));
                            $filterCriteria->addAscendingOrderByColumn(QubitInformationObject::LEVEL_OF_DESCRIPTION_ID);
                            $filterCriteria->addAscendingOrderByColumn(QubitInformationObject::IDENTIFIER);
                            $filterCriteria->addAscendingOrderByColumn(QubitInformationObjectI18n::TITLE);
                            $infoObject = QubitInformationObject::getByCriteria($filterCriteria);
                            foreach ($infoObject->getAccessObjects() as $item) {
                                $filenameRandom = "";
                                if (isset($item->publishId)) {
                                    
                                    // make sure only "Yes" will be published
                                    
									QubitXMLImport::addLog("Check Publish", "", get_class($this), false);
                                    if ($item->publishId == $this->publish) {
                                        $publishname = "publish_" . $item2->objectId . "_" . date("Y-m-dHis") . ".csv";
                                        $locations   = array();
                                        $fileCopied  = false;
										QubitXMLImport::addLog("Publish", $publishname, get_class($this), false);
										$digitalObject = $infoObject->getDigitalObjectS();
                                        if (count($digitalObject) > 0) {
											QubitXMLImport::addLog("digitalObject", "digital object found", get_class($this), false);
                                            if (isset($digitalObject->mimeType)) {
                                                $path_parts = pathinfo(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name);
                                                if (!isset($digitalObject->name)) {
                                                    throw new sfException(sfContext::getInstance()->i18n->__(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name . " No digital image/file available."));
                                                }
                                                // only copy the thumbnail or in event of pdf/mp3/4 original file
                                                $mimePieces = explode('/', $digitalObject->mimeType);
                                                QubitXMLImport::addLog("Image", $mimePieces[0], get_class($this), false);
                                                if (QubitDigitalObject::isImageFile($digitalObject->getName())) {
                                                    $extension      = $path_parts['extension'];
                                                    $randomFilename = substr(str_shuffle(MD5(microtime())), 0, 10);
                                                    $filename       = $path_parts['filename'] . "." . $extension;
                                                    $mqPath         = QubitSetting::getByName('mq_path');
                                                    if ($mqPath == null) {
                                                        throw new sfException(sfContext::getInstance()->i18n->__("No MQ path defined. Contact support/administrator"));
                                                    } else {
                                                        // only copy thumbnail
                                                        if ($fileCopied == false) {
                                                            $filenameRandom = $path_parts['filename'] . "_" . $randomFilename . "." . $extension;
                                                            QubitXMLImport::addLog("filenameRandom: " . $filenameRandom, "filenameRandom", get_class($this), false);
                                                            $fileCopied = true;
                                                            if (file_exists($mqPath)) {
                                                                if (!copy(sfConfig::get('sf_web_dir') . $digitalObject->path . $filename, $mqPath . "/" . $filenameRandom)) {
                                                                    QubitXMLImport::addLog("Failed to copy file to MQ folder. Contact support/administrator", "Failed to copy file to MQ folder. Contact support/administrator", get_class($this), true);
                                                                    throw new sfException(sfContext::getInstance()->i18n->__("Failed to copy file to MQ folder. Contact support/administrator"));
                                                                }
                                                            } else {
                                                                QubitXMLImport::addLog("file_exists=false", "file_exists=false", get_class($this), true);
                                                                throw new sfException(sfContext::getInstance()->i18n->__("<br>No digital image/attachment file available or is corrupted. \n<br> Record ID: {$item2->objectId} <br>Identifier: {$infoObject->getIdentifier(array('cultureFallback' => true))} <br>Item description: {$infoObject->getTitle(array('cultureFallback' => true))} <br>Filename: {$filename} <br>Extenstion: {$extension} "));
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                $mimePieces = explode('/', $digitalObject->mimeType);
                                                QubitXMLImport::addLog("Audio", $mimePieces[0], get_class($this), false);
                                                if ($mimePieces[0] == "audio" || $digitalObject->mimeType == "application/pdf") {
                                                    QubitXMLImport::addLog("Audio/pdf", "", get_class($this), false);
                                                    $randomFilename = substr(str_shuffle(MD5(microtime())), 0, 10);
                                                    $filename       = $path_parts['filename'];
                                                    $extension      = $path_parts['extension'];
                                                    $mqPath         = QubitSetting::getByName('mq_path');
                                                    if ($mqPath == null) {
                                                        throw new sfException(sfContext::getInstance()->i18n->__("<br>No MQ path defined."));
                                                    } else {
                                                        if ($fileCopied == false) {
                                                            $filenameRandom = $path_parts['filename'] . "_" . $randomFilename . "." . $extension;
                                                            QubitXMLImport::addLog("filenameRandom: " . $filenameRandom, "filenameRandom", get_class($this), false);
                                                            $fileCopied = true;
                                                            
                                                            if (file_exists(sfConfig::get('sf_web_dir') . $digitalObject->path . $filename . "." . $extension)) {
                                                                if (!copy(sfConfig::get('sf_web_dir') . $digitalObject->path . $filename . "." . $extension, $mqPath . "/" . $filenameRandom)) {
                                                                    throw new sfException(sfContext::getInstance()->i18n->__("<br>Failed to copy file to MQ folder. Make sure folder exist and is writable. \n<br> Record ID: {$item2->objectId} <br>Identifier: {$infoObject->getIdentifier(array('cultureFallback' => true))} <br>Item description: {$infoObject->getTitle(array('cultureFallback' => true))}"));
                                                                } else {
                                                                    QubitXMLImport::addLog("Copy file to MQ folder. Success", $mqPath . "/" . $filenameRandom, get_class($this), true);
                                                                }
                                                            } else {
                                                                QubitXMLImport::addLog("file_exists=false", "file_exists=false: " + $digitalObject->path . $filename . "." . $extension, get_class($this), true);
                                                                throw new sfException(sfContext::getInstance()->i18n->__("<br>No digital image/attachment file available or is corrupted. \n<br> Record ID: {$item2->objectId} <br>Identifier: {$infoObject->getIdentifier(array('cultureFallback' => true))} <br>Item description: {$infoObject->getTitle(array('cultureFallback' => true))} <br>Filename: {$filename} <br>Extenstion: {$extension}"));
                                                            }
                                                            
                                                        }
                                                    }
                                                }
                                                
                                            } else {
                                                QubitXMLImport::addLog("No digital image/attachment file available or Mime Type unknown. Contact support/administrator", "No digital image/file available Mime Type unknown. Contact support/administrator", get_class($this), true);
                                                throw new sfException(sfContext::getInstance()->i18n->__("<br>No digital image/attachment file available or is corrupted. \n<br> Record ID: {$item2->objectId} <br>Identifier: {$infoObject->getIdentifier(array('cultureFallback' => true))} <br>Item description: {$infoObject->getTitle(array('cultureFallback' => true))} <br>Filename: {$filename} <br>Extenstion: {$extension} "));
                                            }
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getTitle(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $unitTitle = $value;
                                        } else {
                                            $unitTitle = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->alternateTitle)) {
                                            $alternateTitle = $value;
                                        } else {
                                            $alternateTitle = "";
                                        }
                                        
                                        if ($infoObject->levelOfDescriptionId) {
                                            if (in_array(strtolower($levelOfDescription = $infoObject->getLevelOfDescription()->getName(array(
                                                'culture' => 'en'
                                            ))), $eadLevels)) {
                                                $levelOfDescription = $levelOfDescription;
                                            } else {
                                                $levelOfDescription = $levelOfDescription;
                                            }
                                        }
                                        
                                        if ($infoObject->descriptionDetailId) {
                                            $descendantLevelOfDetail = QubitTerm::getById($infoObject->descriptionDetailId);
                                        } else {
                                            $descendantLevelOfDetail = "";
                                        }
                                        
                                        $repository = null;
                                        if (0 < strlen($infoObject->getIdentifier())) {
                                            foreach ($infoObject->ancestors->andSelf()->orderBy('rgt') as $item) {
                                                if (isset($item->repository)) {
                                                    $repository = $item->repository;
                                                    break;
                                                }
                                            }
                                        }
                                        
                                        if (isset($repository)) {
                                            if ($countrycode = $repository->getCountryCode()) //to check
                                                {
                                                $countrycode = $countrycode;
                                            }
                                        }
                                        
                                        $identifier = $infoObject->getIdentifier();
                                        foreach ($infoObject->getDates() as $date) {
                                            if ($date->typeId != QubitTerm::CREATION_ID) {
                                                if ($this->type = (String) $date->getType()) {
                                                    $dateType = strtolower($this->type);
                                                }
                                            } else {
                                                $dateType = "Creation";
                                            }
                                            
                                            if ($startdate = $date->getStartDate()) {
                                                $startDate = Qubit::renderDate($startdate);
                                                if (0 < strlen($enddate = $date->getEndDate())) {
                                                    $endDate = Qubit::renderDate($enddate);
                                                }
                                                
                                                $sDate     = $startdate;
                                                $sDate     = str_replace("-00-00", "", $startdate);
                                                $eDate     = $endDate;
                                                $eDate     = str_replace("-00-00", "", $endDate);
                                                $dateRange = Qubit::renderDateStartEnd($date->getDate(array(
                                                    'cultureFallback' => true
                                                )), $date->startDate, $date->endDate);
                                            }
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getExtentAndMedium(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $extendAndMedium = $this->esc_specialchars($value);
                                        }
                                        
                                        if ($value = $infoObject->getRepository()) {
                                            $repository = (String) $value;
                                        }
                                        
                                        $language   = "";
                                        $scriptCode = "";
                                        if (0 < count($infoObject->language) || 0 < count($infoObject->script) || 0 < count($infoObject->getNotesByType(array(
                                            'noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID
                                        ))->offsetGet(0))) {
                                            foreach ($infoObject->language as $languageCode) {
                                                $language = strtolower($this->iso639convertor->getID2($languageCode)) . " : " . $languageCode;
                                            }
                                            
                                            foreach ($infoObject->script as $scriptCode) {
                                                $scriptCode = $scriptCode;
                                            }
                                            
                                            if (0 < count($notes = $infoObject->getNotesByType(array(
                                                'noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID
                                            )))) {
                                                $languageAndScriptNotes = $notes[0]->getContent(array(
                                                    'cultureFallback' => true
                                                ));
                                            }
                                        }
                                        
                                        if ($infoObject->sources) {
                                            $sources = $infoObject->sources;
                                        } else {
                                            $sources = "";
                                        }
                                        
                                        if (0 < count($notes = $infoObject->getNotesByType(array(
                                            'noteTypeId' => QubitTerm::GENERAL_NOTE_ID
                                        )))) {
                                            $noteGeneral = "";
                                            foreach ($notes as $note) {
                                                $noteTypeId = $note->getContent(array(
                                                    'cultureFallback' => true
                                                ));
                                                if ($noteGeneral == "") {
                                                    $noteGeneral = $this->esc_specialchars($note);
                                                } else {
                                                    $noteGeneral = $noteGeneral . " \n\n" . $this->esc_specialchars($note);
                                                }
                                            }
                                        }
                                        
                                        $bibliographyPublicationNotes = "";
                                        if (0 < count($publicationNotes = $infoObject->getNotesByType(array(
                                            'noteTypeId' => QubitTerm::PUBLICATION_NOTE_ID
                                        )))) {
                                            $bibliographyPublicationNotes = "";
                                            foreach ($publicationNotes as $note) {
                                                if ($bibliographyPublicationNotes == "") {
                                                    $bibliographyPublicationNotes = $this->esc_specialchars($note);
                                                } else {
                                                    $bibliographyPublicationNotes = $bibliographyPublicationNotes . " \n\n" . $this->esc_specialchars($note);
                                                }
                                            }
                                        }
                                        
                                        if (0 < count($archivistsNotes = $infoObject->getNotesByType(array(
                                            'noteTypeId' => QubitTerm::ARCHIVIST_NOTE_ID
                                        )))) {
                                            $aCount         = 0;
                                            $archivistsNote = "";
                                            foreach ($archivistsNotes as $note) {
                                                if (0 < strlen($note)) {
                                                    if ($aCount == 0) {
                                                        if ($archivistsNote == "") {
                                                            $archivistsNote = $this->esc_specialchars($note);
                                                        } else {
                                                            $archivistsNote = $archivistsNote . " \n\n" . $this->esc_specialchars($note);
                                                        }
                                                    } else {
                                                        $archivistsNote = $archivistsNote . " \n\n" . $this->esc_specialchars($note);
                                                    }
                                                    
                                                    $aCount += 1;
                                                }
                                            }
                                        }
                                        
                                        $registry = $infoObject->getRegistry();
                                        if ($registry == "Unknown") {
                                            $registry = "";
                                        }
                                        $registryIdentifier = $registry->corporateBodyIdentifiers;
                                        $creators           = $infoObject->getCreators();
                                        $events             = $infoObject->getActorEvents(array(
                                            'eventTypeId' => QubitTerm::CREATION_ID
                                        ));
                                        if (0 < count($creators)) {
                                            foreach ($events as $date) {
                                                $creator = QubitActor::getById($date->actorId);
                                                if ($value = $date->getDescription(array(
                                                    'cultureFallback' => true
                                                ))) {
                                                    $description = $value;
                                                }
                                                
                                                if ($value = $creator->getHistory(array(
                                                    'cultureFallback' => true
                                                ))) {
                                                    $history = $value;
                                                } else {
                                                    $history = "";
                                                }
                                                if ($type = $creator->getEntityTypeId()) {
                                                    if (QubitTerm::PERSON_ID == $type) {
                                                        $authorizedFormOfNamePerson = $creator->getAuthorizedFormOfName(array(
                                                            'cultureFallback' => true
                                                        ));
                                                    } else if (QubitTerm::FAMILY_ID == $type) {
                                                        $authorizedFormOfNameFamily = $creator->getAuthorizedFormOfName(array(
                                                            'cultureFallback' => true
                                                        ));
                                                    } else if (QubitTerm::CORPORATE_BODY_ID == $type) {
                                                        $authorizedFormOfNameCorporate = $creator->getAuthorizedFormOfName(array(
                                                            'cultureFallback' => true
                                                        ));
                                                    } else {
                                                        $authorizedFormOfName = $creator->getAuthorizedFormOfName(array(
                                                            'cultureFallback' => true
                                                        ));
                                                    }
                                                }
                                            }
                                            
                                            // add repository details here
                                            if ($creator->datesOfExistence) {
                                                $datesOfExistence = $creator->datesOfExistence;
                                            }
                                        }
                                        
                                        if ($infoObject->getPublicationStatus()) {
                                            $publicationStatus = $infoObject->getPublicationStatus();
                                        }
                                        
                                        $descriptionStatus = ($infoObject->descriptionStatusId) ? QubitTerm::getById($infoObject->descriptionStatusId) : '';
                                        if ($descriptionStatus) {
                                            $descriptionStatus = $descriptionStatus;
                                        } else {
                                            $descriptionStatus = "";
                                        }
                                        
                                        if ($infoObject->descriptionIdentifier) {
                                            $descriptionIidentifier = $infoObject->descriptionIdentifier;
                                        } else {
                                            $descriptionIidentifier = "";
                                        }
                                        
                                        if ($infoObject->institutionResponsibleIdentifier) {
                                            $institutionResponsibleIdentifier = $infoObject->institutionResponsibleIdentifier;
                                        } else {
                                            $institutionResponsibleIdentifier = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getScopeAndContent(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $scopeAndContent = $value;
                                        } else {
                                            $scopeAndContent = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getArrangement(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $arrangement = $value;
                                        } else {
                                            $arrangement = "";
                                        }
                                        
                                        $materialtypes       = $infoObject->getMaterialTypes();
                                        $subjects            = $infoObject->getSubjectAccessPoints();
                                        $names               = $infoObject->getNameAccessPoints();
                                        $places              = $infoObject->getPlaceAccessPoints();
                                        $subjectAccessPoints = "";
                                        $placesGeogName      = "";
                                        $object              = "";
                                        if ((0 < count($materialtypes)) || (0 < count($subjects)) || (0 < count($names)) || (0 < count($places)) || (0 < count($infoObject->getActors()))) {
                                            foreach ($names as $name) {
                                                $object = $name->getObject();
                                            }
                                            
                                            foreach ($materialtypes as $materialtype) {
                                                $materialtypeGenreform = $materialtype->getTerm();
                                            }
                                            
                                            foreach ($subjects as $subject) {
                                                if ($subject->getTerm()->code) {
                                                    $subjectAccessPoints = $subject->getTerm()->code;
                                                }
                                                
                                                $subjectAccessPoints = $subject->getTerm();
                                            }
                                            
                                            foreach ($places as $place) {
                                                $placesGeogName = $place->getTerm();
                                            }
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getPhysicalCharacteristics(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $physicalCharacteristics = $value;
                                        } else {
                                            $physicalCharacteristics = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getAppraisal(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $appraisal = $value;
                                        } else {
                                            $appraisal = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getAcquisition(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $acquisition = $value;
                                        } else {
                                            $acquisition = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getAccruals(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $accruals = $value;
                                        } else {
                                            $accruals = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getArchivalHistory(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $archivalHistory = $value;
                                        } else {
                                            $archivalHistory = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getRevisionHistory(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $revisionHistory = $value;
                                        } else {
                                            $revisionHistory = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getLocationOfOriginals(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $locationOfOriginals = $value;
                                        } else {
                                            $locationOfOriginals = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getLocationOfCopies(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $locationOfCopies = $value;
                                        } else {
                                            $locationOfCopies = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getRelatedUnitsOfDescription(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $relatedUnitsOfDescription = $value;
                                        } else {
                                            $relatedUnitsOfDescription = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getAccessConditions(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $accessConditions = $value;
                                        } else {
                                            $accessConditions = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getReproductionConditions(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $reproductionConditions = $value;
                                        } else {
                                            $reproductionConditions = "";
                                        }
                                        
                                        if (0 < strlen($value = $infoObject->getFindingAids(array(
                                            'cultureFallback' => true
                                        )))) {
                                            $findingAids = $value;
                                        } else {
                                            $findingAids = "";
                                        }
                                        
                                        $availabilityId = "";
                                        foreach ($infoObject->getPresevationObjects() as $item) {
                                            QubitXMLImport::addLog("item->availabilityId: " . isset($item->availabilityId), "Publish", get_class($this), false);
                                            $availabilityId = QubitTerm::getById($item->availabilityId);
                                            ;
                                        }
                                        
                                        QubitXMLImport::addLog("$availabilityId: " . $availabilityId, "Publish", get_class($this), false);
                                        $publishPath = QubitSetting::getByName('publish_path');
                                        if ($publishPath == null) {
                                            throw new sfException(sfContext::getInstance()->i18n->__("No upload path defined. Contact support/administrator"));
                                        } else {
                                            
                                            // save to CSV
                                            
                                            $csvfile = fopen($publishPath . $publishname, "a") or die("Unable to open file!");
                                            $csvStringHeader = "identifier|unitid|unittitle|dateType|unitdate|startDate|endDate|level|extent|source|filereference|volume|partno|corpname|repocorpcode|countrycode|repocorpname|custodhist|scopecontent|appraisal|accruals|arrangement|accessrestrict|userestrict|langcode|scriptcode|langmaterial|phystech|otherfindaid|originalsloc|altformavail|relatedmaterial|relateddescriptions|bibliography|note|publicationnote|archivistnote|subject|geogname|name|descriptionIdentifier|institutionIdentifier|rules|statusDescription|levelOfDetail|date|desclanguage|descscript|langcode|scriptcode|recordtype|size|type|classification|availabilityId|registryidentifier|registry|filePath|parentid|donorName|legalEntityName|corporateBodyName\n";
                                            fwrite($csvfile, $csvStringHeader);
                                            if (0 < strlen($repoValue = $infoObject->getRepository())) {
                                                $repositoryCode = $repoValue->identifier;
                                            } else {
                                                $repositoryCode = "";
                                            }
                                            
                                            if ($infoObject->parentId == "1") {
                                                $parentid = "";
                                            } else {
                                                $informationObjParent = QubitInformationObject::getById($infoObject->parentId);
                                                if (isset($informationObjParent)) {
                                                    if ($infoObject->importId != $informationObjParent->importId) {
                                                        $parentid = $informationObjParent->importId;
                                                    } else {
                                                        $parentid = "";
                                                    }
                                                } else {
                                                    $parentid = "";
                                                }
                                            }
                                            
                                            $entityTypeType = "";
                                            $creators       = $infoObject->getCreators();
                                            foreach ($creators as $creator) {
                                                $creatorNameString = $creator->getAuthorizedFormOfName(array(
                                                    'culture' => $culture
                                                ));
                                                $entityType        = $creator->getEntityTypeId();
                                                $entityTypeType    = QubitTerm::getById($entityType);
                                                $corpname          = $creatorNameString;
                                                break;
                                            }
                                            
                                            if ("Legal Deposit" == $entityTypeType) {
                                                $donorName         = $creatorNameString;
                                                $corpname          = $creatorNameString;
                                                $legalEntityName   = "";
                                                $corporateBodyName = "";
                                            } else if ("Donor" == $entityTypeType) {
                                                $donorName         = "";
                                                $corpname          = $creatorNameString;
                                                $legalEntityName   = $creatorNameString;
                                                $corporateBodyName = "";
                                            } else if ("Corporate body" == $entityTypeType) {
                                                $donorName         = "";
                                                $corpname          = $creatorNameString;
                                                $legalEntityName   = "";
                                                $corporateBodyName = $creatorNameString;
                                            } else {
                                                $donorName         = "";
                                                $legalEntityName   = "";
                                                $corporateBodyName = "";
                                            }
                                            
                                            // for CSV export
                                            
                                            $this->CSVValues = array(
                                                'identifier' => $identifier,
                                                'unitid' => $infoObject->importId,
                                                'unittitle' => $unitTitle,
                                                'alternateTitle' => $alternateTitle,
                                                'dateType' => $dateType,
                                                'unitdate' => $dateRange,
                                                'startDate' => $sDate,
                                                'endDate' => $eDate,
                                                'level' => $levelOfDescription,
                                                'extent' => $extendAndMedium,
                                                'source' => $sources,
                                                'referenceNumber' => "",
                                                'volumeNumber' => "",
                                                'partNumber' => $infoObject->partNo,
                                                'corpname' => $corpname,
                                                'repositorycode' => $repositoryCode,
                                                'repositoryCountryCode' => $countrycode,
                                                'repocorpname' => $repository,
                                                'custodhist' => $archivalHistory,
                                                'scopecontent' => $scopeAndContent,
                                                'appraisal' => $appraisal,
                                                'accruals' => $accruals,
                                                'arrangement' => $arrangement,
                                                'accessrestrict' => "",
                                                'userestrict' => "",
                                                'langcode' => "en",
                                                'scriptcode' => "en",
                                                'langmaterial' => $languageAndScriptNotes,
                                                'phystech' => $physicalCharacteristics,
                                                'otherfindaid' => $findingAids,
                                                'originalsloc' => $locationOfOriginals,
                                                'altformavail' => "",
                                                'relateddescriptions' => $relatedUnitsOfDescription,
                                                'bibliography' => "",
                                                'relatedmaterial' => "",
                                                'note' => $noteGeneral,
                                                'archivistnote' => $archivistsNote,
                                                'publicationnote' => $bibliographyPublicationNotes,
                                                'subject' => $subjectAccessPoints,
                                                'geogname' => $placesGeogName,
                                                'name' => $object,
                                                'descriptionIdentifier' => $descriptionIidentifier,
                                                'institutionIdentifier' => $institutionResponsibleIdentifier,
                                                'rules' => $infoObject->rules,
                                                'statusDescription' => $descriptionStatus,
                                                'levelOfDetail' => $descendantLevelOfDetail,
                                                'date' => "",
                                                'desclanguage' => "en",
                                                'descscript' => "en",
                                                'langcode' => "en",
                                                'scriptcode' => "en",
                                                'recordtype' => $infoObject->format,
                                                'size' => $infoObject->size,
                                                'type' => $infoObject->typId,
                                                'classification' => "Public",
                                                'availabilityId' => $availabilityId,
                                                'registryIdentifier' => $registryIdentifier,
                                                'registry' => $registry,
                                                'filePath' => $filenameRandom,
                                                'parentid' => $parentid,
                                                'donorName' => $donorName,
                                                'legalEntityName' => $legalEntityName,
                                                'corporateBodyName' => $corporateBodyName
                                            );
                                            $accessID        = QubitAccessObject::getById($item2->subjectId);
                                            QubitXMLImport::addBatchPublishCSV($accessID, $item2->objectId, $this->CSVValues, $csvfile);
                                            fclose($csvfile);
                                            chmod($publishPath . $publishname, 0775);
                                            
                                            // Set the user
                                            
                                            chown($publishPath, "mqm");
                                            chgrp($publishPath, "mqm");
                                            if (isset($accessID)) {
                                                $accessID->published = true;
                                                $accessID->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        foreach (QubitRelation::getRelationsBySubjectId($tID) as $item2) {
                            $accessObject = QubitInformationObject::getById($item2->objectId);
                            foreach ($accessObject->getAccessObjects() as $item) {
                                $item->published = true;
                                $item->save();
                            }
                        }
                    }
                }
            }
        }
    }
    
    function esc_specialchars($value)
    // Numbers and boolean values get turned into strings which can cause problems
    {
        // with type comparisons (e.g. === or is_int() etc).
        return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, sfConfig::get('sf_charset')) : $value;
    }
}
