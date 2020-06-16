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
 * Bulk export data to CSV
 *
 * @package    symfony
 * @subpackage task
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class exportBulkExterTask extends exportBulkBaseTask
{ 
  protected $namespace        = 'export';
  protected $name             = 'bulkExter';
  protected $briefDescription = 'Bulk export multiple CSV files at once';

  /**
   * @see sfTask
   */
  protected function configure()
  {
    //$this->addCommonArgumentsAndOptions();
    //$this->addOptions(array(
    //  new sfCommandOption('format', null, sfCommandOption::PARAMETER_OPTIONAL, 'XML format ("ead" or "mods")', 'ead')
    //));
  }

  /**
   * @see sfTask
   */
  public function execute($arguments = array(), $options = array())
  {
/*
    $options['format'] = $this->normalizeExportFormat(
      $options['format'],
      array('ead', 'mods')
    );

    if (!isset($options['single-slug']))
    {
      $this->checkPathIsWritable($arguments['path']);
    }

    $configuration = ProjectConfiguration::getApplicationConfiguration('qubit', 'cli', false);
    $sf_context = sfContext::createInstance($configuration);

    // QubitSetting are not available for tasks? See lib/SiteSettingsFilter.class.php
    sfConfig::add(QubitSetting::getSettingsArray());

    $itemsExported = 0;

    $conn = $this->getDatabaseConnection();
    $rows = $conn->query($this->informationObjectQuerySql($options), PDO::FETCH_ASSOC);

    $this->includeXmlExportClassesAndHelpers();

    foreach ($rows as $row)
    {
      $resource = QubitInformationObject::getById($row['id']);

      // Don't export draft descriptions with public option
      if (isset($options['public']) && $options['public']
        && $resource->getPublicationStatus()->statusId == QubitTerm::PUBLICATION_STATUS_DRAFT_ID)
      {
        continue;
      }

      try
      {
        // Print warnings/notices here too, as they are often important.
        $errLevel = error_reporting(E_ALL);

        $rawXml = $this->captureResourceExportTemplateOutput($resource, $options['format'], $options);
        $xml = Qubit::tidyXml($rawXml);

        error_reporting($errLevel);
      }
      catch (Exception $e)
      {
        throw new sfException('Invalid XML generated for object '. $row['id'] .'.');
      }

      if (isset($options['single-slug']) && $options['format'] == 'ead')
      {
        if (is_dir($arguments['path']))
        {
          throw new sfException('When using the single-slug option with EAD, path should be a file.');
        }

        // If we're just exporting a single hierarchy of descriptions as EAD,
        // the given path is actually the full path and filename
        $filePath = $arguments['path'];
      }
      else
      {
        $filename = $this->generateSortableFilename($resource, 'xml', $options['format']);
        $filePath = sprintf('%s/%s', $arguments['path'], $filename);
      }

      if (false === file_put_contents($filePath, $xml))
      {
        throw new sfException("Cannot write to path: $filePath");
      }

      $this->indicateProgress($options['items-until-update']);

      if ($itemsExported++ % 1000 == 0)
      {
        Qubit::clearClassCaches();
      }
    }
    print "\nExport complete (". $itemsExported ." descriptions exported).\n";
*/
	$this->export();
  }
  
    public function export()
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
      //  $this->iso639convertor = new fbISO639_Map;
		
		$offsetE;
		$conn;
		$corporateBodyIdentifiers = "";
		$corpname  = "";
		$conversion  = "";
		$uid  = "";
		$gid  = "";
		$extendAndMedium = "";
        $relationParentId = "";
		$registryIdentifier = "";
		$itemsExported = 0;
		
		//SITA re-export to big
		$sql  = 'SELECT * FROM toExport;';

		if (!isset($conn))
		{
			$conn = $this->getDatabaseConnection();
		  //$conn = Propel::getConnection();
		}
 
		$toIndexBatch = $conn->prepare($sql);
		$toIndexBatch->execute(array(""));
		$row = $toIndexBatch->fetch();
		$offsetE = $row['offsetE']; 
		$limitE = $row['limitE'];

        $this->form       = new sfForm;
        
		$publishPath = QubitSetting::getByName('publish_path');
		$publishname = "publish_" . date("Y-m-dHi") . ".csv";

		if ($publishPath == null) {
			throw new sfException(sfContext::getInstance()->i18n->__("No upload path defined. Contact support/administrator"));
		} else {
			
			// save to CSV
			
			$csvfile = fopen($publishPath . $publishname, "a") or die("Unable to open file!");
			$csvStringHeader = "identifier|unitid|unittitle|dateType|unitdate|startDate|endDate|level|extent|source|filereference|volume|partno|corpname|repocorpcode|countrycode|repocorpname|custodhist|scopecontent|appraisal|accruals|arrangement|accessrestrict|userestrict|langcode|scriptcode|langmaterial|phystech|otherfindaid|originalsloc|altformavail|relatedmaterial|relateddescriptions|bibliography|note|publicationnote|archivistnote|subject|geogname|name|descriptionIdentifier|institutionIdentifier|rules|statusDescription|levelOfDetail|date|desclanguage|descscript|langcode|scriptcode|recordtype|size|type|classification|availabilityId|registryidentifier|registry|filePath|parentid|donorName|legalEntityName|corporateBodyName\n";
			fwrite($csvfile, $csvStringHeader);
			fclose($csvfile);
		}
		
		$filterCriteria1 = new Criteria;
		$filterCriteria1->add(QubitInformationObject::ID, QubitInformationObject::ROOT_ID, Criteria::NOT_EQUAL);
		$filterCriteria1->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
		$filterCriteria1->add(QubitInformationObject::ID, QubitInformationObject::ROOT_ID, Criteria::NOT_EQUAL);
		$filterCriteria1->setOffset($offsetE);
        $filterCriteria1->setLimit($limitE);
		$infoObject2 = QubitInformationObject::get($filterCriteria1);
		
		QubitXMLImport::addLog("mqPath: 11111111111111111111111111111111111111111111111111111111", "mqPath", get_class($this), false);
		
		foreach ($infoObject2 as $item2) {
			$itemsExported++;
			$filterCriteria = new Criteria;			
			$filterCriteria->add(QubitInformationObject::ID, QubitInformationObject::ROOT_ID, Criteria::NOT_EQUAL);
			$filterCriteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
			$filterCriteria->add(QubitInformationObject::ID, $item2->id, Criteria::EQUAL);  
			//$infoObject = QubitInformationObject::getByCriteria($filterCriteria);
			$infoObject = QubitInformationObject::getById($item2->id);
			if (isset($infoObject)) {				
			//	foreach ($infoObject->getAccessObjects() as $item) {
				QubitXMLImport::addLog("mqPath: ". $item2->id, "mqPath", get_class($this), false);
				QubitXMLImport::addLog("mqPath: ". $item2, "mqPath", get_class($this), false);
					$relationObject = QubitRelation::getRelationsBySubjectId($item2->id);				
					$filenameRandom = "";
					$locations   = array();
					$fileCopied  = false;
					if (null !== ($digitalObject = $infoObject->getDigitalObjectS())) {
						if (isset($digitalObject->mimeType)) {
							$path_parts = pathinfo(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name);
							if (!isset($digitalObject->name)) {
								throw new sfException(sfContext::getInstance()->i18n->__(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name . " No digital image/file available."));
							}
							// only copy the thumbnail or in event of pdf/mp3/4 original file
							if (QubitDigitalObject::isImageFile($digitalObject->getName())) {
								$extension      = $path_parts['extension'];
								$randomFilename = substr(str_shuffle(MD5(microtime())), 0, 10);
								$filename       = $path_parts['filename'] . "." . $extension;
								$mqPath         = QubitSetting::getByName('mq_path');
								QubitXMLImport::addLog("mqPath: " . $mqPath . "filename: ", $filename, "mqPath", get_class($this), false);
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
					$languageAndScriptNotes  = "";
	/*					if (isset($infoObject->language)) {
						if (0 < count($infoObject->language)) {
							foreach ($infoObject->language as $languageCode) {
								$language = strtolower($this->iso639convertor->getID2($languageCode)) . " : " . $languageCode;
							}						
						}
					}
					if (isset($infoObject->script)) {
						if (0 < count($infoObject->script)) {
							foreach ($infoObject->script as $scriptCode) {
								$scriptCode = $scriptCode;
							}
						}
					}
				if (isset($infoObject->getNotesByType(array('noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID))->offsetGet(0))) {
						if (0 < count($infoObject->getNotesByType(array('noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID))->offsetGet(0))) {
							if (0 < count($notes = $infoObject->getNotesByType(array('noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID)))) {
								$languageAndScriptNotes = $notes[0]->getContent(array(
									'cultureFallback' => true
								));
							}
						}
					}
					*/
					if ($infoObject->sources) {
						$sources = $infoObject->sources;
					} else {
						$sources = "";
					}
					
					$noteGeneral = "";
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
					
					$archivistsNote = "";
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
					
					$registry = $infoObject->getRegistryById($infoObject->registryId);
					if (isset($registry)) {
						$registryIdentifier = $registry->corporateBodyIdentifiers;
					}
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
							$culture = 'en';
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
							'unitid' => "a_".$infoObject->importId,
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
						$accessID        = QubitAccessObject::getById($relationObject->subjectId);
						$this::addBatchPublishCSV($this->CSVValues, $csvfile);
						fclose($csvfile);
						
						$offsetE++;
						$updateIndex = $conn->prepare("UPDATE toExport SET offsetE=".$offsetE.";");
						$updateIndex->execute(array(''));
						$updateIndex->closeCursor();
						$updateIndex = null;

						//chmod($publishPath . $publishname, 0775);
						//chown($publishPath, "mqm");
						//chgrp($publishPath, "mqm");
					}
				}
			//} else {
						//QubitXMLImport::addLog("mqPath: 333333333333333333333333333333333333333333333333333333333333", "mqPath", get_class($this), true);

			//}
		}
		print "\nExport complete (". $itemsExported ." descriptions exported).\n";
    }
    
    /**
     * Add Batch Publish .csv for Mainframe Import
     * "identifier|unitid|unittitle|remark|subject|source|fileReference|volume|partNumber|repository|repositoryName|repositoryCountry| 		 * dateStart|dateEnd|availabilityId|medium|corpId|repositoryType|creatorName|registryIdentifier|registry|filePath";
     * @return
     */
    public static function addBatchPublishCSV($CSVRecord, $csvfile)
    {
		$csvString = null;
		
		$cvsRepositoryCountryCode = str_replace('"', '""', ($CSVRecord['repositoryCountryCode']));
		$cvsRepositoryCode        = str_replace('"', '""', ($CSVRecord['repositorycode']));
		$cvsIdentifier            = str_replace('"', '""', ($CSVRecord['identifier']));
		$csvString                = $csvString . '"' . $cvsRepositoryCountryCode . " " . $cvsRepositoryCode . " " . $cvsIdentifier . '"|"';
		$cvsUnitid                = str_replace('"', '""', ($CSVRecord['unitid']));
		$csvString                = $csvString . $cvsUnitid . '"|"';
		$cvsTitle                 = str_replace('"', '""', ($CSVRecord['unittitle']));
		$cvsTitle                 = str_replace('+', chr(10) . chr(13) . "--", $cvsTitle);
		
		$unitTitleExtend = "";
		if (strlen($cvsTitle) > 200) {
			$unitTitleExtend = $cvsTitle;
			$cvsTitle = substr($cvsTitle, 0, 200);
		} else { 
			$unitTitleExtend = "";
		}
		
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

		if ($unitTitleExtend != "") {
			$cvsNote = "Title to long - truncated: " . $unitTitleExtend . " \n\n" . $cvsNote;
		}

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
		}
    }

    function esc_specialchars($value)
    // Numbers and boolean values get turned into strings which can cause problems
    {
        // with type comparisons (e.g. === or is_int() etc).
        return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, sfConfig::get('sf_charset')) : $value;
    }
 
}
