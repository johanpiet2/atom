<?php
ini_set("max_execution_time", 300);
set_time_limit(300);
error_reporting(E_ALL ^ E_STRICT);
/**
 * only for export of audit records missing in NARSSA website
 * remove when records is exported
 *
 *
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 */
class XmlImportArchivalDescription
{
    protected $errors = null, $rootObject = null, $parent = null;
    
    public function import($xmlFile, $options = array(), $type = null)
    {
        $this->langScriptMaterialCount = 0;
        $this->langMaterialCount = 0;
        $this->langMaterialScriptCount = 0;
        $this->langScriptCount = 0;
        $this->langDescCount = 0;
		$rowcount    = 0;
		// only for export of audit records missing in NARSSA website
		// remove when records is exported
		$csvfile     = null;
		$missingfile     = null;
		$publishPath = QubitSetting::getByName('publish_path');
		if ($publishPath == null) {
			QubitXMLImport::addLog($publishPath, "No upload path defined. Contact support/administrator", get_class($this), false);
			throw new sfException(sfContext::getInstance()->i18n->__("No upload path defined. Contact support/administrator"));
		} //$publishPath == null
		else {
			date_default_timezone_set('Africa/Johannesburg');
			$missingname = "atom-only.txt"; //file with missing import_id
			if (file_exists("/var/www/html/atom/downloads/" . $missingname)) {
				$missingfile = fopen("/var/www/html/atom/downloads/" . $missingname, "r") or die("Unable to open file!");
			}
		}
		$filename = '/var/www/html/atom/downloads/' . $missingname;
		$contents = file($filename);
		$publishname = "publish_" . date("20" . "ymdHi", time()) . "0.csv"; //file per minute

		//foreach($contents as $line) {
		while (($line = fgets($missingfile)) !== false) {
            try {
				$line = preg_replace( "/\r|\n/", "", $line );
				QubitXMLImport::addLog($line, " - line", get_class($this), false);

				$lineBreak = 100; 
				$publishname = "publish_" . date("20" . "ymdHi", time()) . $rowcount . ".csv"; //file per minute
				QubitXMLImport::addLog($publishPath . $publishname, " - $rowcount % $lineBreak 0000000000000000000000000", get_class($this), true);
				if ($rowcount === 0 || $rowcount % $lineBreak === 0) { 
				QubitXMLImport::addLog($rowcount, " - rowcount 111111111111111111111111111111111", get_class($this), true);
					if (file_exists($publishPath . $publishname)) {
				QubitXMLImport::addLog($rowcount, " - rowcount 2222222222222222222222222222222222", get_class($this), true);
						$csvfile = fopen($publishPath . $publishname, "a") or die("Unable to open file!");
					} else {
				QubitXMLImport::addLog($rowcount, " - rowcount 3333333333333333333333333333333333", get_class($this), true);
						$csvfile = fopen($publishPath . $publishname, "a") or die("Unable to open file!");
						$csvStringHeader = "identifier|unitid|unittitle|dateType|unitdate|startDate|endDate|level|extent|source|filereference|volume|partno|corpname|repocorpcode|countrycode|repocorpname|custodhist|scopecontent|appraisal|accruals|arrangement|accessrestrict|userestrict|langcode|scriptcode|langmaterial|phystech|otherfindaid|originalsloc|altformavail|relatedmaterial|relateddescriptions|bibliography|note|publicationnote|archivistnote|subject|geogname|name|descriptionIdentifier|institutionIdentifier|rules|statusDescription|levelOfDetail|date|desclanguage|descscript|langcode|scriptcode|recordtype|size|type|classification|availabilityId|registryidentifier|registry|filePath\n";
						fwrite($csvfile, $csvStringHeader);
					}
					//fclose($csvfile);
				}
				
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

			QubitXMLImport::addLog($line, " - populateObject", get_class($this), false);
				
//				$this->populateObject(preg_replace( "/\r|\n/", "", $line ),$rowcount, $publishname);
				QubitXMLImport::addLog($tID, " - tID ###############################1", get_class($this), true);

				$infoObject = QubitInformationObject::getByImportId($tID);
				QubitXMLImport::addLog($infoObject->id, " - infoObject->id ###############################2", get_class($this), true);

				if (isset($infoObject)) {
					$locations = array();
					$fileCopied = false;
					if (null !== ($digitalObject = $infoObject->getDigitalObject())) {
						if (isset($digitalObject->mimeType))
						{
							$path_parts = pathinfo(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name);
							if (!isset($digitalObject->name))
							{
								throw new sfException(sfContext::getInstance()->i18n->__(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name . " No digital image/file available. Contact support/administrator"));
							}

							// only copy the thumbnail or in event of pdf/mp3/4 original file
							QubitXMLImport::addLog("digitalObject->name", $digitalObject->name, get_class($this) , true);
							
							if (QubitDigitalObject::isImageFile($digitalObject->getName()))
							{
								$extension = $path_parts['extension'];
								$randomFilename = substr(str_shuffle(MD5(microtime())) , 0, 10);
								$filename = $path_parts['filename'] . "_142." . $extension;
								$mqPath = QubitSetting::getByName('mq_path');
								if ($mqPath == null)
								{
									throw new sfException(sfContext::getInstance()->i18n->__("No MQ path defined. Contact support/administrator"));
									QubitXMLImport::addLog("No MQ path defined. Contact support/administrator", "No MQ path defined. Contact support/administrator", get_class($this) , true);
								}
								else
								{
									// only copy thumbnail
									if ($fileCopied == false)
									{
										$filenameRandom = $path_parts['filename'] . "_" . $randomFilename . "_142." . $extension;
										QubitXMLImport::addLog("filenameRandom: " . $filenameRandom, "filenameRandom", get_class($this) , false);
										$fileCopied = true;
										if (file_exists($mqPath))
										{
											if (!copy(sfConfig::get('sf_web_dir') . $digitalObject->path . $filename, $mqPath . "/" . $filenameRandom))
											{
												QubitXMLImport::addLog("Failed to copy file to MQ folder. Contact support/administrator", "Failed to copy file to MQ folder. Contact support/administrator", get_class($this) , true);
												throw new sfException(sfContext::getInstance()->i18n->__("Failed to copy file to MQ folder. Contact support/administrator"));
											}
										}
										else
										{
											QubitXMLImport::addLog("file_exists=false", "file_exists=false", get_class($this) , true);
											throw new sfException(sfContext::getInstance()->i18n->__("file_exists=false. Contact support/administrator"));
										}
									}
								}
							}
							
							$mimePieces = explode('/', $digitalObject->mimeType);
							QubitXMLImport::addLog("Audio", $mimePieces[0], get_class($this) , true);
							if ($mimePieces[0] == "audio" || $digitalObject->mimeType == "application/pdf") {
								QubitXMLImport::addLog("Audio/pdf", "", get_class($this) , true);
								$randomFilename = substr(str_shuffle(MD5(microtime())) , 0, 10);
								$filename = $path_parts['filename']; // . "_142";
								$extension = $path_parts['extension'];
								$mqPath = QubitSetting::getByName('mq_path');
								if ($mqPath == null)
								{
									throw new sfException(sfContext::getInstance()->i18n->__("No MQ path defined. Contact support/administrator"));
									QubitXMLImport::addLog("No MQ path defined. Contact support/administrator", "No MQ path defined. Contact support/administrator", get_class($this) , true);
								}
								else
								{
									if ($fileCopied == false)
									{
										$filenameRandom = $path_parts['filename'] . "_" . $randomFilename . "." . $extension;
										QubitXMLImport::addLog("filenameRandom: " . $filenameRandom, "filenameRandom", get_class($this) , true);
										$fileCopied = true;
										if (file_exists($mqPath))
										{
										QubitXMLImport::addLog("copy filename: " . sfConfig::get('sf_web_dir') . $digitalObject->path . $filename. "." .$extension, "copy filename", get_class($this) , true);
											if (!copy(sfConfig::get('sf_web_dir') . $digitalObject->path . $filename. "." .$extension, $mqPath . "/" . $filenameRandom))
											{
												QubitXMLImport::addLog("Failed to copy file to MQ folder. Contact support/administrator", $mqPath . "/" . $filenameRandom, get_class($this) , true);
												throw new sfException(sfContext::getInstance()->i18n->__("Failed to copy file to MQ folder. Contact support/administrator"));
											}
										}
										else
										{
											QubitXMLImport::addLog("file_exists=false", "file_exists=false", get_class($this) , true);
											throw new sfException(sfContext::getInstance()->i18n->__("file_exists=false. Contact support/administrator"));
										}
									
									}
								}
							}

						}
						else
						{
							QubitXMLImport::addLog("No digital image/file available Mime Type unknown. Contact support/administrator", "No digital image/file available Mime Type unknown. Contact support/administrator", get_class($this) , true);
							throw new sfException(sfContext::getInstance()->i18n->__(" No digital image/file available Mime Type unknown. Contact support/administrator"));
						}
					}

					if (0 < strlen($value = $infoObject->getTitle(array(
						'cultureFallback' => true
					))))
					{
						$unitTitle = $value;
					}
					else
					{
						$unitTitle = "";
					}
			QubitXMLImport::addLog($unitTitle, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

					if (0 < strlen($value = $infoObject->alternateTitle))
					{
						$alternateTitle = $value;
					}
					else
					{
						$alternateTitle = "";
					}
			QubitXMLImport::addLog($alternateTitle, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

					if ($infoObject->levelOfDescriptionId)
					{
						if (in_array(strtolower($levelOfDescription = $infoObject->getLevelOfDescription()->getName(array(
							'culture' => 'en'
						))) , $eadLevels))
						{
							$levelOfDescription = $levelOfDescription;
						}
						else
						{
							$levelOfDescription = $levelOfDescription;
						}
					}
			QubitXMLImport::addLog($levelOfDescription, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

					if ($infoObject->descriptionDetailId)
					{
						$descendantLevelOfDetail = QubitTerm::getById($infoObject->descriptionDetailId);
					}
					else
					{
						$descendantLevelOfDetail = "";
					}
			QubitXMLImport::addLog($descendantLevelOfDetail, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

					$repository = null;
					if (0 < strlen($infoObject->getIdentifier()))
					{
						foreach($infoObject->ancestors->andSelf()->orderBy('rgt') as $item)
						{
							if (isset($item->repository))
							{
								$repository = $item->repository;
								break;
							}
						}
					}
			QubitXMLImport::addLog($repository, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

					if (isset($repository))
					{
						if ($countrycode = $repository->getCountryCode()) //to check
						{
							$countrycode = $countrycode;
						}
					}
			QubitXMLImport::addLog($countrycode, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

					$identifier = $infoObject->getIdentifier();
					foreach($infoObject->getDates() as $date)
					{
						if ($date->typeId != QubitTerm::CREATION_ID)
						{
							if ($this->type = (String)$date->getType())
							{
								$dateType = strtolower($this->type);
							}
						}
						else
						{
							$dateType = "Creation";
						}
			QubitXMLImport::addLog($dateType, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

						if ($startdate = $date->getStartDate())
						{
							$startDate = Qubit::renderDate($startdate);
							if (0 < strlen($enddate = $date->getEndDate()))
							{
								$endDate = Qubit::renderDate($enddate);
							}

							$sDate = $startdate;
							$eDate = $endDate;
							$dateRange = Qubit::renderDateStartEnd($date->getDate(array(
								'cultureFallback' => true
							)) , $date->startDate, $date->endDate);
						}
					}
			QubitXMLImport::addLog($startDate, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%startDate", get_class($this), false);
			QubitXMLImport::addLog($endDate, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%endDate", get_class($this), false);

					if (0 < strlen($value = $infoObject->getExtentAndMedium(array(
						'cultureFallback' => true
					))))
					{
						$extendAndMedium = $this->esc_specialchars($value);
					}
			QubitXMLImport::addLog($extendAndMedium, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%extendAndMedium", get_class($this), false);

			QubitXMLImport::addLog($repository, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%repository1", get_class($this), false);
					if ($value = $infoObject->getRepository())
					{
			QubitXMLImport::addLog($repository, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%repository2", get_class($this), false);
						$repository = (String)$value;
					}
			QubitXMLImport::addLog($repository, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%repository3", get_class($this), false);

					$language = "";
					$scriptCode = "";
			/*			if (0 < count($infoObject->language) || 0 < count($infoObject->script) || 0 < count($infoObject->getNotesByType(array(
						'noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID
					))->offsetGet(0)))
					{
						foreach($infoObject->language as $languageCode)
						{

							$language = strtolower($this->iso639convertor->getID2($languageCode)) . " : " . $languageCode;
						}

						foreach($infoObject->script as $scriptCode)
						{
							$scriptCode = $scriptCode;
						}

						if (0 < count($notes = $infoObject->getNotesByType(array(
							'noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID
						))))
						{
							$languageAndScriptNotes = $notes[0]->getContent(array(
								'cultureFallback' => true
							));
						}
					}
			*/
					QubitXMLImport::addLog($language, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%language", get_class($this), false);
					QubitXMLImport::addLog($scriptCode, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%scriptCode", get_class($this), false);
					QubitXMLImport::addLog($languageAndScriptNotes, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%languageAndScriptNotes", get_class($this), false);

					if ($infoObject->sources)
					{
						$sources = $infoObject->sources;
					}
					else
					{
						$sources = "";
					}
					QubitXMLImport::addLog($sources, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%sources", get_class($this), false);

					if (0 < count($notes = $infoObject->getNotesByType(array(
						'noteTypeId' => QubitTerm::GENERAL_NOTE_ID
					))))
					{
						foreach($notes as $note)
						{
							$noteTypeId = $note->getContent(array(
								'cultureFallback' => true
							));
							$noteGeneral = $this->esc_specialchars($note);
						}
					}
			QubitXMLImport::addLog($noteGeneral, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%noteGeneral", get_class($this), false);

					$bibliographyPublicationNotes = "";
					if (0 < count($publicationNotes = $infoObject->getNotesByType(array(
						'noteTypeId' => QubitTerm::PUBLICATION_NOTE_ID
					))))
					{
						foreach($publicationNotes as $note)
						{
							$bibliographyPublicationNotes = $note;
						}
					}
			QubitXMLImport::addLog($bibliographyPublicationNotes, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%bibliographyPublicationNotes", get_class($this), false);

					if (0 < count($archivistsNotes = $infoObject->getNotesByType(array(
						'noteTypeId' => QubitTerm::ARCHIVIST_NOTE_ID
					))))
					{
						$aCount = 0;
						foreach($archivistsNotes as $note)
						{
							if (0 < strlen($note))
							{
								if ($aCount == 0)
								{
									if ($bibliographyPublicationNotes == "")
									{
										$bibliographyPublicationNotes = $this->esc_specialchars($note);
									}
									else
									{
										$archivistsNote = $archivistsNote . " \n" . $this->esc_specialchars($note);
									}
								}
								else
								{
									$archivistsNote = $archivistsNote . " \n" . $this->esc_specialchars($note);
								}

								$aCount+= 1;
							}
						}
					}
			QubitXMLImport::addLog($archivistsNote, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%archivistsNote", get_class($this), false);

					$registry = $infoObject->getRegistry();
					if ($registry == "Unknown")
					{
						$registry = "";
					}
			QubitXMLImport::addLog($registry, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%registry", get_class($this), false);

					// $registryIdentifier = $infoObject->getRegistryId(array('cultureFallback' => true ));

					$registryIdentifier = $registry->corporateBodyIdentifiers;
					$creators = $infoObject->getCreators();
					$events = $infoObject->getActorEvents(array(
						'eventTypeId' => QubitTerm::CREATION_ID
					));
					if (0 < count($creators))
					{
						foreach($events as $date)
						{
							$creator = QubitActor::getById($date->actorId);

							// $bioghist = 'md5-' . ": " . $ead->getMetadataParameter('bioghist');
							// $eadDateFromEvent = $ead->renderEadDateFromEvent('creation', $date); //to check

							if ($value = $date->getDescription(array(
								'cultureFallback' => true
							)))
							{
								$description = $value;
							}

							if ($value = $creator->getHistory(array(
								'cultureFallback' => true
							)))
							{
								$history = $value;
							}
							else
							{
								$history = "";
							}

							// $ = "Origination: " . $ead->getMetadataParameter('origination');  //To Check

							if ($type = $creator->getEntityTypeId())
							{
								if (QubitTerm::PERSON_ID == $type)
								{
									$authorizedFormOfNamePerson = $creator->getAuthorizedFormOfName(array(
										'cultureFallback' => true
									));
								}
								else
								if (QubitTerm::FAMILY_ID == $type)
								{
									$authorizedFormOfNameFamily = $creator->getAuthorizedFormOfName(array(
										'cultureFallback' => true
									));
								}
								else
								if (QubitTerm::CORPORATE_BODY_ID == $type)
								{
									$authorizedFormOfNameCorporate = $creator->getAuthorizedFormOfName(array(
										'cultureFallback' => true
									));
								}
								else
								{
									$authorizedFormOfName = $creator->getAuthorizedFormOfName(array(
										'cultureFallback' => true
									));
								}
							}
						}

						// add repository details here

						if ($creator->datesOfExistence)
						{
							$datesOfExistence = $creator->datesOfExistence;
						}
					}
			QubitXMLImport::addLog($datesOfExistence, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%datesOfExistence", get_class($this), false);

					if ($infoObject->getPublicationStatus())
					{
						$publicationStatus = $infoObject->getPublicationStatus();
					}
			QubitXMLImport::addLog($publicationStatus, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%publicationStatus", get_class($this), false);

					$descriptionStatus = ($infoObject->descriptionStatusId) ? QubitTerm::getById($infoObject->descriptionStatusId) : '';
					if ($descriptionStatus)
					{
						$descriptionStatus = $descriptionStatus;
					}
					else
					{
						$descriptionStatus = "";
					}
			QubitXMLImport::addLog($descriptionStatus, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%descriptionStatus", get_class($this), false);

					if ($infoObject->descriptionIdentifier)
					{
						$descriptionIidentifier = $infoObject->descriptionIdentifier;
					}
					else
					{
						$descriptionIidentifier = "";
					}
			QubitXMLImport::addLog($descriptionIidentifier, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%descriptionIidentifier", get_class($this), false);

					if ($infoObject->institutionResponsibleIdentifier)
					{
						$institutionResponsibleIdentifier = $infoObject->institutionResponsibleIdentifier;
					}
					else
					{
						$institutionResponsibleIdentifier = "";
					}
			QubitXMLImport::addLog($institutionResponsibleIdentifier, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%institutionResponsibleIdentifier", get_class($this), false);

					if (0 < strlen($value = $infoObject->getScopeAndContent(array(
						'cultureFallback' => true
					))))
					{
						$scopeAndContent = $value;
					}
					else
					{
						$scopeAndContent = "";
					}
			QubitXMLImport::addLog($scopeAndContent, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%scopeAndContent", get_class($this), false);

					if (0 < strlen($value = $infoObject->getArrangement(array(
						'cultureFallback' => true
					))))
					{
						$arrangement = $value;
					}
					else
					{
						$arrangement = "";
					}
			QubitXMLImport::addLog($arrangement, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%arrangement", get_class($this), false);

					$materialtypes = $infoObject->getMaterialTypes();
					$subjects = $infoObject->getSubjectAccessPoints();
					$names = $infoObject->getNameAccessPoints();
					$places = $infoObject->getPlaceAccessPoints();
					$subjectAccessPoints = "";
					$placesGeogName = "";
					$object = "";
					if ((0 < count($materialtypes)) || (0 < count($subjects)) || (0 < count($names)) || (0 < count($places)) || (0 < count($infoObject->getActors())))
					{
						foreach($names as $name)
						{
							$object = $name->getObject();
						}

						foreach($materialtypes as $materialtype)
						{

							// if (0 < strlen($encoding = $ead->getMetadataParameter('genreform')))
							// {
							//     $genreform = $ead->getMetadataParameter('genreform') . ": " . $encoding;
							// }

							$materialtypeGenreform = $materialtype->getTerm();
						}

						foreach($subjects as $subject)
						{
							if ($subject->getTerm()->code)
							{
								$subjectAccessPoints = $subject->getTerm()->code;
							}

							$subjectAccessPoints = $subject->getTerm();
						}

						foreach($places as $place)
						{
							$placesGeogName = $place->getTerm();
						}
					}
			QubitXMLImport::addLog($subjectAccessPoints, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%subjectAccessPoints", get_class($this), false);

					if (0 < strlen($value = $infoObject->getPhysicalCharacteristics(array(
						'cultureFallback' => true
					))))
					{
						$physicalCharacteristics = $value;
					}
					else
					{
						$physicalCharacteristics = "";
					}
			QubitXMLImport::addLog($physicalCharacteristics, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%physicalCharacteristics", get_class($this), false);

					if (0 < strlen($value = $infoObject->getAppraisal(array(
						'cultureFallback' => true
					))))
					{
						$appraisal = $value;
					}
					else
					{
						$appraisal = "";
					}
			QubitXMLImport::addLog($appraisal, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%appraisal", get_class($this), false);

					if (0 < strlen($value = $infoObject->getAcquisition(array(
						'cultureFallback' => true
					))))
					{
						$acquisition = $value;
					}
					else
					{
						$acquisition = "";
					}
			QubitXMLImport::addLog($acquisition, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%acquisition", get_class($this), false);

					if (0 < strlen($value = $infoObject->getAccruals(array(
						'cultureFallback' => true
					))))
					{
						$accruals = $value;
					}
					else
					{
						$accruals = "";
					}
			QubitXMLImport::addLog($accruals, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%accruals", get_class($this), false);

					if (0 < strlen($value = $infoObject->getArchivalHistory(array(
						'cultureFallback' => true
					))))
					{
						$archivalHistory = $value;
					}
					else
					{
						$archivalHistory = "";
					}
			QubitXMLImport::addLog($archivalHistory, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%archivalHistory", get_class($this), false);

					if (0 < strlen($value = $infoObject->getRevisionHistory(array(
						'cultureFallback' => true
					))))
					{
						$revisionHistory = $value;
					}
					else
					{
						$revisionHistory = "";
					}
			QubitXMLImport::addLog($revisionHistory, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%revisionHistory", get_class($this), false);

					if (0 < strlen($value = $infoObject->getLocationOfOriginals(array(
						'cultureFallback' => true
					))))
					{
						$locationOfOriginals = $value;
					}
					else
					{
						$locationOfOriginals = "";
					}
			QubitXMLImport::addLog($locationOfOriginals, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%locationOfOriginals", get_class($this), false);

					if (0 < strlen($value = $infoObject->getLocationOfCopies(array(
						'cultureFallback' => true
					))))
					{
						$locationOfCopies = $value;
					}
					else
					{
						$locationOfCopies = "";
					}
			QubitXMLImport::addLog($locationOfCopies, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%locationOfCopies", get_class($this), false);

					if (0 < strlen($value = $infoObject->getRelatedUnitsOfDescription(array(
						'cultureFallback' => true
					))))
					{
						$relatedUnitsOfDescription = $value;
					}
					else
					{
						$relatedUnitsOfDescription = "";
					}
			QubitXMLImport::addLog($relatedUnitsOfDescription, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%relatedUnitsOfDescription", get_class($this), false);

					if (0 < strlen($value = $infoObject->getAccessConditions(array(
						'cultureFallback' => true
					))))
					{
						$accessConditions = $value;
					}
					else
					{
						$accessConditions = "";
					}
			QubitXMLImport::addLog($accessConditions, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%accessConditions", get_class($this), false);

					if (0 < strlen($value = $infoObject->getReproductionConditions(array(
						'cultureFallback' => true
					))))
					{
						$reproductionConditions = $value;
					}
					else
					{
						$reproductionConditions = "";
					}
			QubitXMLImport::addLog($reproductionConditions, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%reproductionConditions", get_class($this), false);

					if (0 < strlen($value = $infoObject->getFindingAids(array(
						'cultureFallback' => true
					))))
					{
						$findingAids = $value;
					}
					else
					{
						$findingAids = "";
					}
			QubitXMLImport::addLog($findingAids, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%findingAids", get_class($this), false);

					$availabilityId = "Yes";
			//					foreach($infoObject->getPresevationObjects() as $item)
			//					{
			//						QubitXMLImport::addLog("item->availabilityId: " . isset($item->availabilityId) , "Publish", get_class($this) , false);
			//						$availabilityId = QubitTerm::getById($item->availabilityId);;
			//					}

					QubitXMLImport::addLog("$availabilityId: " . $availabilityId, "Publish", get_class($this) , false);
					if (0 < strlen($repoValue = $infoObject->getRepository()))
					{
						$repositoryCode = $repoValue->identifier;
					}
					else
					{
						$repositoryCode = "";
					}
			QubitXMLImport::addLog($repositoryCode, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%repositoryCode", get_class($this), false);

					if ($infoObject->parentId == "1")
					{
						$parentid = "";
					}
					else
					{
						$informationObjParent = QubitInformationObject::getById($infoObject->parentId);
						if (isset($informationObjParent))
						{
							if ($infoObject->importId != $informationObjParent->importId)
							{
								$parentid = $informationObjParent->importId;
							}
							else
							{
								$parentid = "";
							}
						}
						else
						{
							$parentid = "";
						}
					}
			QubitXMLImport::addLog($parentid, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%parentid", get_class($this), false);

					$entityTypeType = "";
					$creators = $infoObject->getCreators();
					foreach($creators as $creator)
					{
						$creatorNameString = $creator->getAuthorizedFormOfName(array(
							'culture' => $culture
						));
						$entityType = $creator->getEntityTypeId();
						$entityTypeType = QubitTerm::getById($entityType);
						$corpname = $creatorNameString;
						break;
					}
			QubitXMLImport::addLog($entityType, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%entityType", get_class($this), false);
			QubitXMLImport::addLog($entityTypeType, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%entityTypeType", get_class($this), false);
			QubitXMLImport::addLog($corpname, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%corpname", get_class($this), false);

					if ("Legal Deposit" == $entityTypeType)
					{
						$donorName = $creatorNameString;
						$corpname = $creatorNameString;
						$legalEntityName = "";
						$corporateBodyName = "";
					}
					else if ("Donor" == $entityTypeType)
					{
						$donorName = "";
						$corpname = $creatorNameString;
						$legalEntityName = $creatorNameString;
						$corporateBodyName = "";
					}
					else if ("Corporate body" == $entityTypeType)
					{
						$donorName = "";
						$corpname = $creatorNameString;
						$legalEntityName = "";
						$corporateBodyName = $creatorNameString;
					}
					else
					{
						$donorName = "";
						$legalEntityName = "";
						$corporateBodyName = "";
					}
			QubitXMLImport::addLog($donorName, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%donorName", get_class($this), false);
			QubitXMLImport::addLog($legalEntityName, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%legalEntityName", get_class($this), false);
			QubitXMLImport::addLog($corporateBodyName, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%corporateBodyName", get_class($this), false);

					// for CSV export

					/*$this->CSVValues = array(
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
						'rules' => "",
						'statusDescription' => $descriptionStatus,
						'levelOfDetail' => $descendantLevelOfDetail,
						'date' => "",
						'desclanguage' => "en",
						'descscript' => "en",
						'langcode' => "en",
						'scriptcode' => "en",
						'recordtype' => "",
						'size' => "",
						'type' => "",
						'classification' => "Public",
						'availabilityId' => $availabilityId,
						'registryIdentifier' => $registryIdentifier,
						'registry' => $registry,
						'filePath' => $filenameRandom,
						'parentid' => $parentid,
						'donorName' => $donorName,
						'legalEntityName' => $legalEntityName,
						'corporateBodyName' => $corporateBodyName
					); */
		//			QubitXMLImport::addLog("&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&7" . $infoObject->id , "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this) , true);
		//			$this->addBatchPublishCSVMissing($this->CSVValues, $prowcount, $ppublishname);
					//fclose($csvfile);
					$csvString = null;

					$cvsRepositoryCountryCode = str_replace('"', '""', ($countrycode));
					$cvsRepositoryCode = str_replace('"', '""', ($repositoryCode));
					$cvsIdentifier = str_replace('"', '""', ($identifier));
					$csvString = $csvString . '"' . $cvsRepositoryCountryCode . " " . $cvsRepositoryCode . " " . $cvsIdentifier . '"|"';
					$cvsUnitid = str_replace('"', '""', ($infoObject->importId));
					$csvString = $csvString . $cvsUnitid . '"|"';
					$cvsTitle = str_replace('"', '""', ($unitTitle));
					$cvsTitle = str_replace('+', chr(10).chr(13)."--", $cvsTitle);
					$csvString = $csvString . $cvsTitle . '"|"';
					$cvsDateType = str_replace('"', '""', ($dateType));
					$csvString = $csvString . $cvsDateType . '"|"';
					$cvsUnitDate = str_replace('"', '""', ($dateRange));
					$csvString = $csvString . $cvsUnitDate . '"|"';
					$cvsStartDate = str_replace('"', '""', ($sDate));
					$csvString = $csvString . $cvsStartDate . '"|"';
					$cvsEndDate = str_replace('"', '""', ($eDate));
					$csvString = $csvString . $cvsEndDate . '"|"';
					$cvsLevel = str_replace('"', '""', ($levelOfDescription));
					$csvString = $csvString . $cvsLevel . '"|"';
					$cvsExtent = str_replace('"', '""', ($extendAndMedium));
					$csvString = $csvString . $cvsExtent . '"|"';
					$cvsSource = str_replace('"', '""', ($sources));
					$csvString = $csvString . $cvsSource . '"|"';
					$cvsReferenceNumber = str_replace('"', '""', "");
					$csvString = $csvString . $cvsReferenceNumber . '"|"';
					$cvsVolumeNumber = str_replace('"', '""', "");
					$csvString = $csvString . $cvsVolumeNumber . '"|"';
					$cvsPartNumber = str_replace('"', '""', ($infoObject->partNo));
					$csvString = $csvString . $cvsPartNumber . '"|"';
					$cvsCorpname = str_replace('"', '""', $corpname);
					$csvString = $csvString . $cvsCorpname . '"|"';
					$csvString = $csvString . $cvsRepositoryCode . '"|"';
					$csvString = $csvString . $cvsRepositoryCountryCode . '"|"';
					$cvsRepositoryName = str_replace('"', '""', ($repository));
					$csvString = $csvString . $cvsRepositoryName . '"|"';
					$cvsCustodhist = str_replace('"', '""', ($archivalHistory));
					$csvString = $csvString . $cvsCustodhist . '"|"';
					$cvsScopecontent = str_replace('"', '""', ($scopeAndContent));
					$csvString = $csvString . $cvsScopecontent . '"|"';
					$cvsAppraisal = str_replace('"', '""', ($appraisal));
					$csvString = $csvString . $cvsAppraisal . '"|"';
					$cvsAccruals = str_replace('"', '""', ($accruals));
					$csvString = $csvString . $cvsAccruals . '"|"';
					$cvsArrangement = str_replace('"', '""', ($arrangement));
					$csvString = $csvString . $cvsArrangement . '"|"';
					$cvsAccessrestrict = str_replace('"', '""', "");
					$csvString = $csvString . $cvsAccessrestrict . '"|"';
					$cvsUserestrict = str_replace('"', '""', "");
					$csvString = $csvString . $cvsUserestrict . '"|"';
					$cvsLangcode = str_replace('"', '""', "");
					$csvString = $csvString . $cvsLangcode . '"|"';
					$cvsScriptcode = str_replace('"', '""', "");
					$csvString = $csvString . $cvsScriptcode . '"|"';
					$cvsLangmaterial = str_replace('"', '""', ("en"));
					$csvString = $csvString . $cvsLangmaterial . '"|"';
					$cvsPhystech = str_replace('"', '""', ("en"));
					$csvString = $csvString . $cvsPhystech . '"|"';
					$cvsOtherfindaid = str_replace('"', '""', ($findingAids));
					$csvString = $csvString . $cvsOtherfindaid . '"|"';
					$cvsOriginalsloc = str_replace('"', '""', ($locationOfOriginals));
					$csvString = $csvString . $cvsOriginalsloc . '"|"';
					$cvsAltformavail = str_replace('"', '""', "");
					$csvString = $csvString . $cvsAltformavail . '"|"';
					$cvsRelatedmaterial = str_replace('"', '""', "");
					$csvString = $csvString . $cvsRelatedmaterial . '"|"';
					$cvsRelateddescriptions = str_replace('"', '""', ($relatedUnitsOfDescription));
					$csvString = $csvString . $cvsRelateddescriptions . '"|"';
					$cvsBibliography = str_replace('"', '""', "");
					$csvString = $csvString . $cvsBibliography . '"|"';
					$cvsNote = str_replace('"', '""', ($noteGeneral));
					$cvsNote = str_replace('+', chr(10).chr(13)."--", $cvsNote);
					$csvString = $csvString . $cvsNote . '"|"';
					$cvsPublicationnote = str_replace('"', '""', ($bibliographyPublicationNotes));
					$csvString = $csvString . $cvsPublicationnote . '"|"';
					$cvsArchivistnote = str_replace('"', '""', ($archivistsNote));
					$cvsArchivistnote = str_replace('+', chr(10).chr(13)."--", $cvsArchivistnote);
					$csvString = $csvString . $cvsArchivistnote . '"|"';
					$cvsSubject = str_replace('"', '""', ($subjectAccessPoints));
					$csvString = $csvString . $cvsSubject . '"|"';
					$cvsGeogname = str_replace('"', '""', ($placesGeogName));
					$csvString = $csvString . $cvsGeogname . '"|"';
					$cvsName = str_replace('"', '""', ($object));
					$csvString = $csvString . $cvsName . '"|"';
					$cvsDescriptionIdentifier = str_replace('"', '""', ($descriptionIidentifier));
					$csvString = $csvString . $cvsDescriptionIdentifier . '"|"';
					$cvsInstitutionIdentifier = str_replace('"', '""', ($institutionResponsibleIdentifier));
					$csvString = $csvString . $cvsInstitutionIdentifier . '"|"';
					$cvsRules = str_replace('"', '""', "");
					$csvString = $csvString . $cvsRules . '"|"';
					$cvsStatusDescription = str_replace('"', '""', ($descriptionStatus));
					$csvString = $csvString . $cvsStatusDescription . '"|"';
					$cvsLevelOfDetail = str_replace('"', '""', ($descendantLevelOfDetail));
					$csvString = $csvString . $cvsLevelOfDetail . '"|"';
					$cvsDate = str_replace('"', '""', "");
					$csvString = $csvString . $cvsDate . '"|"';
					$cvsDesclanguage = str_replace('"', '""', ("en"));
					$csvString = $csvString . $cvsDesclanguage . '"|"';
					$cvsDescscript = str_replace('"', '""', ("en"));
					$csvString = $csvString . $cvsDescscript . '"|"';
					$cvsLangcode = str_replace('"', '""', ("en"));
					$csvString = $csvString . $cvsLangcode . '"|"';
					$cvsScriptcode = str_replace('"', '""', ("en"));
					$csvString = $csvString . $cvsScriptcode . '"|"';
					$cvsRecordtype = str_replace('"', '""', "");
					$csvString = $csvString . $cvsRecordtype . '"|"';
					$cvsSize = str_replace('"', '""', "");
					$csvString = $csvString . $cvsSize . '"|"';
					$cvsType = str_replace('"', '""', "");
					$csvString = $csvString . $cvsType . '"|"';
					$cvsClassification = str_replace('"', '""', ("Public"));
					$csvString = $csvString . $cvsClassification . '"|"';
					$cvsAvailabilityId = str_replace('"', '""', ($availabilityId));
					$csvString = $csvString . $cvsAvailabilityId . '"|"';
					$cvsRegistryIdentifier = str_replace('"', '""', ($registryIdentifier));
					$csvString = $csvString . $cvsRegistryIdentifier . '"|"';
					$cvsRegistry = str_replace('"', '""', ($registry));
					$csvString = $csvString . $cvsRegistry . '"|"';
					$cvsFilePath = str_replace('"', '""', ($filenameRandom));
					if ($cvsFilePath != "")
					{
						$csvString = $csvString . "attachments/".$cvsFilePath . '"|"';
					}
					else
					{
						$csvString = $csvString . "".$cvsFilePath . '"|"';
					}

					$cvsParentId = str_replace('"', '""', ($parentid));
					$csvString = $csvString . $cvsParentId . '"|"';

					$cvsDonorName = str_replace('"', '""', ($donorName));
					$csvString = $csvString . $cvsDonorName . '"|"';
					
					$cvsLegalEntityName = str_replace('"', '""', ($legalEntityName));
					$csvString = $csvString . $cvsLegalEntityName . '"|"';
					
					$cvsCorporateBodyName = str_replace('"', '""', ($corporateBodyName));
					$csvString = $csvString . $cvsCorporateBodyName;

					$csvString = $csvString . '"' . "\n";
		QubitXMLImport::addLog($csvString, " - csvString 777777777777777777777777777777777777777777777771", get_class($this), true);
//					$publishPath = QubitSetting::getByName('publish_path');
//					QubitXMLImport::addLog("^^" . $publishPath , "^^", get_class($this) , true);

//					QubitXMLImport::addLog("^^irowcount=" . $rowcount , "^^", get_class($this) , true);
					fwrite($csvfile, $csvString);
					//fclose($csvfile2);
					chmod($publishPath . $publishname, 0775);

					// Set the user
					chown($publishPath, "mqm");
					chgrp($publishPath, "mqm");
				}
				//$time_post0 = microtime(true);
				//$exec_time0 = $time_post0 - $time_pre0;
				//print "Total time diff: ".$exec_time0."\n";
				$rowcount++;
				//print "Row: ".$rowcount."\n";                
            }
            catch (Exception $e) {
                // An exception has been thrown
                QubitXMLImport::addLog("error: " . $e->getMessage(), "", get_class($this), true);
            }
		}

        QubitXMLImport::addLog("Import rows: " . $rowcount, "", get_class($this), true);
        //fclose($csvfile);
        fclose($missingname);
        return $this;
    }
    
    private function populateObject($tID, $prowcount)
    {
		QubitXMLImport::addLog($tID, " - tID ###############################1", get_class($this), true);

		$infoObject = QubitInformationObject::getByImportId($tID);
		QubitXMLImport::addLog($infoObject->id, " - infoObject->id ###############################2", get_class($this), true);

		if (isset($infoObject)) {
			$locations = array();
			$fileCopied = false;
			if (null !== ($digitalObject = $infoObject->getDigitalObject())) {
				if (isset($digitalObject->mimeType))
				{
					$path_parts = pathinfo(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name);
					if (!isset($digitalObject->name))
					{
						throw new sfException(sfContext::getInstance()->i18n->__(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name . " No digital image/file available. Contact support/administrator"));
					}

					// only copy the thumbnail or in event of pdf/mp3/4 original file
					QubitXMLImport::addLog("digitalObject->name", $digitalObject->name, get_class($this) , true);
					
					if (QubitDigitalObject::isImageFile($digitalObject->getName()))
					{
						$extension = $path_parts['extension'];
						$randomFilename = substr(str_shuffle(MD5(microtime())) , 0, 10);
						$filename = $path_parts['filename'] . "_142." . $extension;
						$mqPath = QubitSetting::getByName('mq_path');
						if ($mqPath == null)
						{
							throw new sfException(sfContext::getInstance()->i18n->__("No MQ path defined. Contact support/administrator"));
							QubitXMLImport::addLog("No MQ path defined. Contact support/administrator", "No MQ path defined. Contact support/administrator", get_class($this) , true);
						}
						else
						{
							// only copy thumbnail
							if ($fileCopied == false)
							{
								$filenameRandom = $path_parts['filename'] . "_" . $randomFilename . "_142." . $extension;
								QubitXMLImport::addLog("filenameRandom: " . $filenameRandom, "filenameRandom", get_class($this) , false);
								$fileCopied = true;
								if (file_exists($mqPath))
								{
									if (!copy(sfConfig::get('sf_web_dir') . $digitalObject->path . $filename, $mqPath . "/" . $filenameRandom))
									{
										QubitXMLImport::addLog("Failed to copy file to MQ folder. Contact support/administrator", "Failed to copy file to MQ folder. Contact support/administrator", get_class($this) , true);
										throw new sfException(sfContext::getInstance()->i18n->__("Failed to copy file to MQ folder. Contact support/administrator"));
									}
								}
								else
								{
									QubitXMLImport::addLog("file_exists=false", "file_exists=false", get_class($this) , true);
									throw new sfException(sfContext::getInstance()->i18n->__("file_exists=false. Contact support/administrator"));
								}
							}
						}
					}
					
					$mimePieces = explode('/', $digitalObject->mimeType);
					QubitXMLImport::addLog("Audio", $mimePieces[0], get_class($this) , true);
					if ($mimePieces[0] == "audio" || $digitalObject->mimeType == "application/pdf") {
						QubitXMLImport::addLog("Audio/pdf", "", get_class($this) , true);
						$randomFilename = substr(str_shuffle(MD5(microtime())) , 0, 10);
						$filename = $path_parts['filename']; // . "_142";
						$extension = $path_parts['extension'];
						$mqPath = QubitSetting::getByName('mq_path');
						if ($mqPath == null)
						{
							throw new sfException(sfContext::getInstance()->i18n->__("No MQ path defined. Contact support/administrator"));
							QubitXMLImport::addLog("No MQ path defined. Contact support/administrator", "No MQ path defined. Contact support/administrator", get_class($this) , true);
						}
						else
						{
							if ($fileCopied == false)
							{
								$filenameRandom = $path_parts['filename'] . "_" . $randomFilename . "." . $extension;
								QubitXMLImport::addLog("filenameRandom: " . $filenameRandom, "filenameRandom", get_class($this) , true);
								$fileCopied = true;
								if (file_exists($mqPath))
								{
								QubitXMLImport::addLog("copy filename: " . sfConfig::get('sf_web_dir') . $digitalObject->path . $filename. "." .$extension, "copy filename", get_class($this) , true);
									if (!copy(sfConfig::get('sf_web_dir') . $digitalObject->path . $filename. "." .$extension, $mqPath . "/" . $filenameRandom))
									{
										QubitXMLImport::addLog("Failed to copy file to MQ folder. Contact support/administrator", $mqPath . "/" . $filenameRandom, get_class($this) , true);
										throw new sfException(sfContext::getInstance()->i18n->__("Failed to copy file to MQ folder. Contact support/administrator"));
									}
								}
								else
								{
									QubitXMLImport::addLog("file_exists=false", "file_exists=false", get_class($this) , true);
									throw new sfException(sfContext::getInstance()->i18n->__("file_exists=false. Contact support/administrator"));
								}
							
							}
						}
					}

				}
				else
				{
					QubitXMLImport::addLog("No digital image/file available Mime Type unknown. Contact support/administrator", "No digital image/file available Mime Type unknown. Contact support/administrator", get_class($this) , true);
					throw new sfException(sfContext::getInstance()->i18n->__(" No digital image/file available Mime Type unknown. Contact support/administrator"));
				}
			}

			if (0 < strlen($value = $infoObject->getTitle(array(
				'cultureFallback' => true
			))))
			{
				$unitTitle = $value;
			}
			else
			{
				$unitTitle = "";
			}
	QubitXMLImport::addLog($unitTitle, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

			if (0 < strlen($value = $infoObject->alternateTitle))
			{
				$alternateTitle = $value;
			}
			else
			{
				$alternateTitle = "";
			}
	QubitXMLImport::addLog($alternateTitle, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

			if ($infoObject->levelOfDescriptionId)
			{
				if (in_array(strtolower($levelOfDescription = $infoObject->getLevelOfDescription()->getName(array(
					'culture' => 'en'
				))) , $eadLevels))
				{
					$levelOfDescription = $levelOfDescription;
				}
				else
				{
					$levelOfDescription = $levelOfDescription;
				}
			}
	QubitXMLImport::addLog($levelOfDescription, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

			if ($infoObject->descriptionDetailId)
			{
				$descendantLevelOfDetail = QubitTerm::getById($infoObject->descriptionDetailId);
			}
			else
			{
				$descendantLevelOfDetail = "";
			}
	QubitXMLImport::addLog($descendantLevelOfDetail, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

			$repository = null;
			if (0 < strlen($infoObject->getIdentifier()))
			{
				foreach($infoObject->ancestors->andSelf()->orderBy('rgt') as $item)
				{
					if (isset($item->repository))
					{
						$repository = $item->repository;
						break;
					}
				}
			}
	QubitXMLImport::addLog($repository, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

			if (isset($repository))
			{
				if ($countrycode = $repository->getCountryCode()) //to check
				{
					$countrycode = $countrycode;
				}
			}
	QubitXMLImport::addLog($countrycode, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

			$identifier = $infoObject->getIdentifier();
			foreach($infoObject->getDates() as $date)
			{
				if ($date->typeId != QubitTerm::CREATION_ID)
				{
					if ($this->type = (String)$date->getType())
					{
						$dateType = strtolower($this->type);
					}
				}
				else
				{
					$dateType = "Creation";
				}
	QubitXMLImport::addLog($dateType, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this), false);

				if ($startdate = $date->getStartDate())
				{
					$startDate = Qubit::renderDate($startdate);
					if (0 < strlen($enddate = $date->getEndDate()))
					{
						$endDate = Qubit::renderDate($enddate);
					}

					$sDate = $startdate;
					$eDate = $endDate;
					$dateRange = Qubit::renderDateStartEnd($date->getDate(array(
						'cultureFallback' => true
					)) , $date->startDate, $date->endDate);
				}
			}
	QubitXMLImport::addLog($startDate, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%startDate", get_class($this), false);
	QubitXMLImport::addLog($endDate, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%endDate", get_class($this), false);

			if (0 < strlen($value = $infoObject->getExtentAndMedium(array(
				'cultureFallback' => true
			))))
			{
				$extendAndMedium = $this->esc_specialchars($value);
			}
	QubitXMLImport::addLog($extendAndMedium, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%extendAndMedium", get_class($this), false);

	QubitXMLImport::addLog($repository, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%repository1", get_class($this), false);
			if ($value = $infoObject->getRepository())
			{
	QubitXMLImport::addLog($repository, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%repository2", get_class($this), false);
				$repository = (String)$value;
			}
	QubitXMLImport::addLog($repository, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%repository3", get_class($this), false);

			$language = "";
			$scriptCode = "";
	/*			if (0 < count($infoObject->language) || 0 < count($infoObject->script) || 0 < count($infoObject->getNotesByType(array(
				'noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID
			))->offsetGet(0)))
			{
				foreach($infoObject->language as $languageCode)
				{

					$language = strtolower($this->iso639convertor->getID2($languageCode)) . " : " . $languageCode;
				}

				foreach($infoObject->script as $scriptCode)
				{
					$scriptCode = $scriptCode;
				}

				if (0 < count($notes = $infoObject->getNotesByType(array(
					'noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID
				))))
				{
					$languageAndScriptNotes = $notes[0]->getContent(array(
						'cultureFallback' => true
					));
				}
			}
	*/
			QubitXMLImport::addLog($language, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%language", get_class($this), false);
			QubitXMLImport::addLog($scriptCode, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%scriptCode", get_class($this), false);
			QubitXMLImport::addLog($languageAndScriptNotes, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%languageAndScriptNotes", get_class($this), false);

			if ($infoObject->sources)
			{
				$sources = $infoObject->sources;
			}
			else
			{
				$sources = "";
			}
			QubitXMLImport::addLog($sources, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%sources", get_class($this), false);

			if (0 < count($notes = $infoObject->getNotesByType(array(
				'noteTypeId' => QubitTerm::GENERAL_NOTE_ID
			))))
			{
				foreach($notes as $note)
				{
					$noteTypeId = $note->getContent(array(
						'cultureFallback' => true
					));
					$noteGeneral = $this->esc_specialchars($note);
				}
			}
	QubitXMLImport::addLog($noteGeneral, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%noteGeneral", get_class($this), false);

			$bibliographyPublicationNotes = "";
			if (0 < count($publicationNotes = $infoObject->getNotesByType(array(
				'noteTypeId' => QubitTerm::PUBLICATION_NOTE_ID
			))))
			{
				foreach($publicationNotes as $note)
				{
					$bibliographyPublicationNotes = $note;
				}
			}
	QubitXMLImport::addLog($bibliographyPublicationNotes, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%bibliographyPublicationNotes", get_class($this), false);

			if (0 < count($archivistsNotes = $infoObject->getNotesByType(array(
				'noteTypeId' => QubitTerm::ARCHIVIST_NOTE_ID
			))))
			{
				$aCount = 0;
				foreach($archivistsNotes as $note)
				{
					if (0 < strlen($note))
					{
						if ($aCount == 0)
						{
							if ($bibliographyPublicationNotes == "")
							{
								$bibliographyPublicationNotes = $this->esc_specialchars($note);
							}
							else
							{
								$archivistsNote = $archivistsNote . " \n" . $this->esc_specialchars($note);
							}
						}
						else
						{
							$archivistsNote = $archivistsNote . " \n" . $this->esc_specialchars($note);
						}

						$aCount+= 1;
					}
				}
			}
	QubitXMLImport::addLog($archivistsNote, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%archivistsNote", get_class($this), false);

			$registry = $infoObject->getRegistry();
			if ($registry == "Unknown")
			{
				$registry = "";
			}
	QubitXMLImport::addLog($registry, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%registry", get_class($this), false);

			// $registryIdentifier = $infoObject->getRegistryId(array('cultureFallback' => true ));

			$registryIdentifier = $registry->corporateBodyIdentifiers;
			$creators = $infoObject->getCreators();
			$events = $infoObject->getActorEvents(array(
				'eventTypeId' => QubitTerm::CREATION_ID
			));
			if (0 < count($creators))
			{
				foreach($events as $date)
				{
					$creator = QubitActor::getById($date->actorId);

					// $bioghist = 'md5-' . ": " . $ead->getMetadataParameter('bioghist');
					// $eadDateFromEvent = $ead->renderEadDateFromEvent('creation', $date); //to check

					if ($value = $date->getDescription(array(
						'cultureFallback' => true
					)))
					{
						$description = $value;
					}

					if ($value = $creator->getHistory(array(
						'cultureFallback' => true
					)))
					{
						$history = $value;
					}
					else
					{
						$history = "";
					}

					// $ = "Origination: " . $ead->getMetadataParameter('origination');  //To Check

					if ($type = $creator->getEntityTypeId())
					{
						if (QubitTerm::PERSON_ID == $type)
						{
							$authorizedFormOfNamePerson = $creator->getAuthorizedFormOfName(array(
								'cultureFallback' => true
							));
						}
						else
						if (QubitTerm::FAMILY_ID == $type)
						{
							$authorizedFormOfNameFamily = $creator->getAuthorizedFormOfName(array(
								'cultureFallback' => true
							));
						}
						else
						if (QubitTerm::CORPORATE_BODY_ID == $type)
						{
							$authorizedFormOfNameCorporate = $creator->getAuthorizedFormOfName(array(
								'cultureFallback' => true
							));
						}
						else
						{
							$authorizedFormOfName = $creator->getAuthorizedFormOfName(array(
								'cultureFallback' => true
							));
						}
					}
				}

				// add repository details here

				if ($creator->datesOfExistence)
				{
					$datesOfExistence = $creator->datesOfExistence;
				}
			}
	QubitXMLImport::addLog($datesOfExistence, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%datesOfExistence", get_class($this), false);

			if ($infoObject->getPublicationStatus())
			{
				$publicationStatus = $infoObject->getPublicationStatus();
			}
	QubitXMLImport::addLog($publicationStatus, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%publicationStatus", get_class($this), false);

			$descriptionStatus = ($infoObject->descriptionStatusId) ? QubitTerm::getById($infoObject->descriptionStatusId) : '';
			if ($descriptionStatus)
			{
				$descriptionStatus = $descriptionStatus;
			}
			else
			{
				$descriptionStatus = "";
			}
	QubitXMLImport::addLog($descriptionStatus, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%descriptionStatus", get_class($this), false);

			if ($infoObject->descriptionIdentifier)
			{
				$descriptionIidentifier = $infoObject->descriptionIdentifier;
			}
			else
			{
				$descriptionIidentifier = "";
			}
	QubitXMLImport::addLog($descriptionIidentifier, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%descriptionIidentifier", get_class($this), false);

			if ($infoObject->institutionResponsibleIdentifier)
			{
				$institutionResponsibleIdentifier = $infoObject->institutionResponsibleIdentifier;
			}
			else
			{
				$institutionResponsibleIdentifier = "";
			}
	QubitXMLImport::addLog($institutionResponsibleIdentifier, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%institutionResponsibleIdentifier", get_class($this), false);

			if (0 < strlen($value = $infoObject->getScopeAndContent(array(
				'cultureFallback' => true
			))))
			{
				$scopeAndContent = $value;
			}
			else
			{
				$scopeAndContent = "";
			}
	QubitXMLImport::addLog($scopeAndContent, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%scopeAndContent", get_class($this), false);

			if (0 < strlen($value = $infoObject->getArrangement(array(
				'cultureFallback' => true
			))))
			{
				$arrangement = $value;
			}
			else
			{
				$arrangement = "";
			}
	QubitXMLImport::addLog($arrangement, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%arrangement", get_class($this), false);

			$materialtypes = $infoObject->getMaterialTypes();
			$subjects = $infoObject->getSubjectAccessPoints();
			$names = $infoObject->getNameAccessPoints();
			$places = $infoObject->getPlaceAccessPoints();
			$subjectAccessPoints = "";
			$placesGeogName = "";
			$object = "";
			if ((0 < count($materialtypes)) || (0 < count($subjects)) || (0 < count($names)) || (0 < count($places)) || (0 < count($infoObject->getActors())))
			{
				foreach($names as $name)
				{
					$object = $name->getObject();
				}

				foreach($materialtypes as $materialtype)
				{

					// if (0 < strlen($encoding = $ead->getMetadataParameter('genreform')))
					// {
					//     $genreform = $ead->getMetadataParameter('genreform') . ": " . $encoding;
					// }

					$materialtypeGenreform = $materialtype->getTerm();
				}

				foreach($subjects as $subject)
				{
					if ($subject->getTerm()->code)
					{
						$subjectAccessPoints = $subject->getTerm()->code;
					}

					$subjectAccessPoints = $subject->getTerm();
				}

				foreach($places as $place)
				{
					$placesGeogName = $place->getTerm();
				}
			}
	QubitXMLImport::addLog($subjectAccessPoints, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%subjectAccessPoints", get_class($this), false);

			if (0 < strlen($value = $infoObject->getPhysicalCharacteristics(array(
				'cultureFallback' => true
			))))
			{
				$physicalCharacteristics = $value;
			}
			else
			{
				$physicalCharacteristics = "";
			}
	QubitXMLImport::addLog($physicalCharacteristics, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%physicalCharacteristics", get_class($this), false);

			if (0 < strlen($value = $infoObject->getAppraisal(array(
				'cultureFallback' => true
			))))
			{
				$appraisal = $value;
			}
			else
			{
				$appraisal = "";
			}
	QubitXMLImport::addLog($appraisal, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%appraisal", get_class($this), false);

			if (0 < strlen($value = $infoObject->getAcquisition(array(
				'cultureFallback' => true
			))))
			{
				$acquisition = $value;
			}
			else
			{
				$acquisition = "";
			}
	QubitXMLImport::addLog($acquisition, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%acquisition", get_class($this), false);

			if (0 < strlen($value = $infoObject->getAccruals(array(
				'cultureFallback' => true
			))))
			{
				$accruals = $value;
			}
			else
			{
				$accruals = "";
			}
	QubitXMLImport::addLog($accruals, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%accruals", get_class($this), false);

			if (0 < strlen($value = $infoObject->getArchivalHistory(array(
				'cultureFallback' => true
			))))
			{
				$archivalHistory = $value;
			}
			else
			{
				$archivalHistory = "";
			}
	QubitXMLImport::addLog($archivalHistory, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%archivalHistory", get_class($this), false);

			if (0 < strlen($value = $infoObject->getRevisionHistory(array(
				'cultureFallback' => true
			))))
			{
				$revisionHistory = $value;
			}
			else
			{
				$revisionHistory = "";
			}
	QubitXMLImport::addLog($revisionHistory, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%revisionHistory", get_class($this), false);

			if (0 < strlen($value = $infoObject->getLocationOfOriginals(array(
				'cultureFallback' => true
			))))
			{
				$locationOfOriginals = $value;
			}
			else
			{
				$locationOfOriginals = "";
			}
	QubitXMLImport::addLog($locationOfOriginals, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%locationOfOriginals", get_class($this), false);

			if (0 < strlen($value = $infoObject->getLocationOfCopies(array(
				'cultureFallback' => true
			))))
			{
				$locationOfCopies = $value;
			}
			else
			{
				$locationOfCopies = "";
			}
	QubitXMLImport::addLog($locationOfCopies, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%locationOfCopies", get_class($this), false);

			if (0 < strlen($value = $infoObject->getRelatedUnitsOfDescription(array(
				'cultureFallback' => true
			))))
			{
				$relatedUnitsOfDescription = $value;
			}
			else
			{
				$relatedUnitsOfDescription = "";
			}
	QubitXMLImport::addLog($relatedUnitsOfDescription, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%relatedUnitsOfDescription", get_class($this), false);

			if (0 < strlen($value = $infoObject->getAccessConditions(array(
				'cultureFallback' => true
			))))
			{
				$accessConditions = $value;
			}
			else
			{
				$accessConditions = "";
			}
	QubitXMLImport::addLog($accessConditions, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%accessConditions", get_class($this), false);

			if (0 < strlen($value = $infoObject->getReproductionConditions(array(
				'cultureFallback' => true
			))))
			{
				$reproductionConditions = $value;
			}
			else
			{
				$reproductionConditions = "";
			}
	QubitXMLImport::addLog($reproductionConditions, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%reproductionConditions", get_class($this), false);

			if (0 < strlen($value = $infoObject->getFindingAids(array(
				'cultureFallback' => true
			))))
			{
				$findingAids = $value;
			}
			else
			{
				$findingAids = "";
			}
	QubitXMLImport::addLog($findingAids, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%findingAids", get_class($this), false);

			$availabilityId = "Yes";
	//					foreach($infoObject->getPresevationObjects() as $item)
	//					{
	//						QubitXMLImport::addLog("item->availabilityId: " . isset($item->availabilityId) , "Publish", get_class($this) , false);
	//						$availabilityId = QubitTerm::getById($item->availabilityId);;
	//					}

			QubitXMLImport::addLog("$availabilityId: " . $availabilityId, "Publish", get_class($this) , false);
			if (0 < strlen($repoValue = $infoObject->getRepository()))
			{
				$repositoryCode = $repoValue->identifier;
			}
			else
			{
				$repositoryCode = "";
			}
	QubitXMLImport::addLog($repositoryCode, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%repositoryCode", get_class($this), false);

			if ($infoObject->parentId == "1")
			{
				$parentid = "";
			}
			else
			{
				$informationObjParent = QubitInformationObject::getById($infoObject->parentId);
				if (isset($informationObjParent))
				{
					if ($infoObject->importId != $informationObjParent->importId)
					{
						$parentid = $informationObjParent->importId;
					}
					else
					{
						$parentid = "";
					}
				}
				else
				{
					$parentid = "";
				}
			}
	QubitXMLImport::addLog($parentid, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%parentid", get_class($this), false);

			$entityTypeType = "";
			$creators = $infoObject->getCreators();
			foreach($creators as $creator)
			{
				$creatorNameString = $creator->getAuthorizedFormOfName(array(
					'culture' => $culture
				));
				$entityType = $creator->getEntityTypeId();
				$entityTypeType = QubitTerm::getById($entityType);
				$corpname = $creatorNameString;
				break;
			}
	QubitXMLImport::addLog($entityType, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%entityType", get_class($this), false);
	QubitXMLImport::addLog($entityTypeType, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%entityTypeType", get_class($this), false);
	QubitXMLImport::addLog($corpname, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%corpname", get_class($this), false);

			if ("Legal Deposit" == $entityTypeType)
			{
				$donorName = $creatorNameString;
				$corpname = $creatorNameString;
				$legalEntityName = "";
				$corporateBodyName = "";
			}
			else if ("Donor" == $entityTypeType)
			{
				$donorName = "";
				$corpname = $creatorNameString;
				$legalEntityName = $creatorNameString;
				$corporateBodyName = "";
			}
			else if ("Corporate body" == $entityTypeType)
			{
				$donorName = "";
				$corpname = $creatorNameString;
				$legalEntityName = "";
				$corporateBodyName = $creatorNameString;
			}
			else
			{
				$donorName = "";
				$legalEntityName = "";
				$corporateBodyName = "";
			}
	QubitXMLImport::addLog($donorName, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%donorName", get_class($this), false);
	QubitXMLImport::addLog($legalEntityName, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%legalEntityName", get_class($this), false);
	QubitXMLImport::addLog($corporateBodyName, "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%corporateBodyName", get_class($this), false);

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
				'rules' => "",
				'statusDescription' => $descriptionStatus,
				'levelOfDetail' => $descendantLevelOfDetail,
				'date' => "",
				'desclanguage' => "en",
				'descscript' => "en",
				'langcode' => "en",
				'scriptcode' => "en",
				'recordtype' => "",
				'size' => "",
				'type' => "",
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
//			QubitXMLImport::addLog("&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&7" . $infoObject->id , "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%", get_class($this) , true);
//			$this->addBatchPublishCSVMissing($this->CSVValues, $prowcount, $ppublishname);
			//fclose($csvfile);
			$csvString = null;

			$cvsRepositoryCountryCode = str_replace('"', '""', ($countrycode));
			$cvsRepositoryCode = str_replace('"', '""', ($repositoryCode));
			$cvsIdentifier = str_replace('"', '""', ($this->CSVValues['identifier']));
			$csvString = $csvString . '"' . $cvsRepositoryCountryCode . " " . $cvsRepositoryCode . " " . $cvsIdentifier . '"|"';
			$cvsUnitid = str_replace('"', '""', ($infoObject->importId));
			$csvString = $csvString . $cvsUnitid . '"|"';
			$cvsTitle = str_replace('"', '""', ($unitTitle));
			$cvsTitle = str_replace('+', chr(10).chr(13)."--", $cvsTitle);
			$csvString = $csvString . $cvsTitle . '"|"';
			$cvsDateType = str_replace('"', '""', ($dateType));
			$csvString = $csvString . $cvsDateType . '"|"';
			$cvsUnitDate = str_replace('"', '""', ($dateRange));
			$csvString = $csvString . $cvsUnitDate . '"|"';
			$cvsStartDate = str_replace('"', '""', ($sDate));
			$csvString = $csvString . $cvsStartDate . '"|"';
			$cvsEndDate = str_replace('"', '""', ($eDate));
			$csvString = $csvString . $cvsEndDate . '"|"';
			$cvsLevel = str_replace('"', '""', ($levelOfDescription));
			$csvString = $csvString . $cvsLevel . '"|"';
			$cvsExtent = str_replace('"', '""', ($extendAndMedium));
			$csvString = $csvString . $cvsExtent . '"|"';
			$cvsSource = str_replace('"', '""', ($sources));
			$csvString = $csvString . $cvsSource . '"|"';
			$cvsReferenceNumber = str_replace('"', '""', "");
			$csvString = $csvString . $cvsReferenceNumber . '"|"';
			$cvsVolumeNumber = str_replace('"', '""', "");
			$csvString = $csvString . $cvsVolumeNumber . '"|"';
			$cvsPartNumber = str_replace('"', '""', ($infoObject->partNo));
			$csvString = $csvString . $cvsPartNumber . '"|"';
			$cvsCorpname = str_replace('"', '""', $corpname);
			$csvString = $csvString . $cvsCorpname . '"|"';
			$csvString = $csvString . $cvsRepositoryCode . '"|"';
			$csvString = $csvString . $cvsRepositoryCountryCode . '"|"';
			$cvsRepositoryName = str_replace('"', '""', ($repository));
			$csvString = $csvString . $cvsRepositoryName . '"|"';
			$cvsCustodhist = str_replace('"', '""', ($archivalHistory));
			$csvString = $csvString . $cvsCustodhist . '"|"';
			$cvsScopecontent = str_replace('"', '""', ($scopeAndContent));
			$csvString = $csvString . $cvsScopecontent . '"|"';
			$cvsAppraisal = str_replace('"', '""', ($appraisal));
			$csvString = $csvString . $cvsAppraisal . '"|"';
			$cvsAccruals = str_replace('"', '""', ($accruals));
			$csvString = $csvString . $cvsAccruals . '"|"';
			$cvsArrangement = str_replace('"', '""', ($arrangement));
			$csvString = $csvString . $cvsArrangement . '"|"';
			$cvsAccessrestrict = str_replace('"', '""', "");
			$csvString = $csvString . $cvsAccessrestrict . '"|"';
			$cvsUserestrict = str_replace('"', '""', "");
			$csvString = $csvString . $cvsUserestrict . '"|"';
			$cvsLangcode = str_replace('"', '""', "");
			$csvString = $csvString . $cvsLangcode . '"|"';
			$cvsScriptcode = str_replace('"', '""', "");
			$csvString = $csvString . $cvsScriptcode . '"|"';
			$cvsLangmaterial = str_replace('"', '""', ("en"));
			$csvString = $csvString . $cvsLangmaterial . '"|"';
			$cvsPhystech = str_replace('"', '""', ("en"));
			$csvString = $csvString . $cvsPhystech . '"|"';
			$cvsOtherfindaid = str_replace('"', '""', ($findingAids));
			$csvString = $csvString . $cvsOtherfindaid . '"|"';
			$cvsOriginalsloc = str_replace('"', '""', ($locationOfOriginals));
			$csvString = $csvString . $cvsOriginalsloc . '"|"';
			$cvsAltformavail = str_replace('"', '""', "");
			$csvString = $csvString . $cvsAltformavail . '"|"';
			$cvsRelatedmaterial = str_replace('"', '""', "");
			$csvString = $csvString . $cvsRelatedmaterial . '"|"';
			$cvsRelateddescriptions = str_replace('"', '""', ($relatedUnitsOfDescription));
			$csvString = $csvString . $cvsRelateddescriptions . '"|"';
			$cvsBibliography = str_replace('"', '""', "");
			$csvString = $csvString . $cvsBibliography . '"|"';
			$cvsNote = str_replace('"', '""', ($noteGeneral));
			$cvsNote = str_replace('+', chr(10).chr(13)."--", $cvsNote);
			$csvString = $csvString . $cvsNote . '"|"';
			$cvsPublicationnote = str_replace('"', '""', ($bibliographyPublicationNotes));
			$csvString = $csvString . $cvsPublicationnote . '"|"';
			$cvsArchivistnote = str_replace('"', '""', ($archivistsNote));
			$cvsArchivistnote = str_replace('+', chr(10).chr(13)."--", $cvsArchivistnote);
			$csvString = $csvString . $cvsArchivistnote . '"|"';
			$cvsSubject = str_replace('"', '""', ($subjectAccessPoints));
			$csvString = $csvString . $cvsSubject . '"|"';
			$cvsGeogname = str_replace('"', '""', ($placesGeogName));
			$csvString = $csvString . $cvsGeogname . '"|"';
			$cvsName = str_replace('"', '""', ($object));
			$csvString = $csvString . $cvsName . '"|"';
			$cvsDescriptionIdentifier = str_replace('"', '""', ($descriptionIidentifier));
			$csvString = $csvString . $cvsDescriptionIdentifier . '"|"';
			$cvsInstitutionIdentifier = str_replace('"', '""', ($institutionResponsibleIdentifier));
			$csvString = $csvString . $cvsInstitutionIdentifier . '"|"';
			$cvsRules = str_replace('"', '""', "");
			$csvString = $csvString . $cvsRules . '"|"';
			$cvsStatusDescription = str_replace('"', '""', ($descriptionStatus));
			$csvString = $csvString . $cvsStatusDescription . '"|"';
			$cvsLevelOfDetail = str_replace('"', '""', ($descendantLevelOfDetail));
			$csvString = $csvString . $cvsLevelOfDetail . '"|"';
			$cvsDate = str_replace('"', '""', "");
			$csvString = $csvString . $cvsDate . '"|"';
			$cvsDesclanguage = str_replace('"', '""', ("en"));
			$csvString = $csvString . $cvsDesclanguage . '"|"';
			$cvsDescscript = str_replace('"', '""', ("en"));
			$csvString = $csvString . $cvsDescscript . '"|"';
			$cvsLangcode = str_replace('"', '""', ("en"));
			$csvString = $csvString . $cvsLangcode . '"|"';
			$cvsScriptcode = str_replace('"', '""', ("en"));
			$csvString = $csvString . $cvsScriptcode . '"|"';
			$cvsRecordtype = str_replace('"', '""', "");
			$csvString = $csvString . $cvsRecordtype . '"|"';
			$cvsSize = str_replace('"', '""', "");
			$csvString = $csvString . $cvsSize . '"|"';
			$cvsType = str_replace('"', '""', "");
			$csvString = $csvString . $cvsType . '"|"';
			$cvsClassification = str_replace('"', '""', ("Public"));
			$csvString = $csvString . $cvsClassification . '"|"';
			$cvsAvailabilityId = str_replace('"', '""', ($availabilityId));
			$csvString = $csvString . $cvsAvailabilityId . '"|"';
			$cvsRegistryIdentifier = str_replace('"', '""', ($registryIdentifier));
			$csvString = $csvString . $cvsRegistryIdentifier . '"|"';
			$cvsRegistry = str_replace('"', '""', ($registry));
			$csvString = $csvString . $cvsRegistry . '"|"';
			$cvsFilePath = str_replace('"', '""', ($filenameRandom));
			if ($cvsFilePath != "")
			{
				$csvString = $csvString . "attachments/".$cvsFilePath . '"|"';
			}
			else
			{
				$csvString = $csvString . "".$cvsFilePath . '"|"';
			}

			$cvsParentId = str_replace('"', '""', ($parentid));
			$csvString = $csvString . $cvsParentId . '"|"';

			$cvsDonorName = str_replace('"', '""', ($donorName));
			$csvString = $csvString . $cvsDonorName . '"|"';
			
			$cvsLegalEntityName = str_replace('"', '""', ($legalEntityName));
			$csvString = $csvString . $cvsLegalEntityName . '"|"';
			
			$cvsCorporateBodyName = str_replace('"', '""', ($corporateBodyName));
			$csvString = $csvString . $cvsCorporateBodyName;

			$csvString = $csvString . '"' . "\n";
			$publishPath = QubitSetting::getByName('publish_path');
			QubitXMLImport::addLog("^^" . $publishPath , "^^", get_class($this) , true);

			QubitXMLImport::addLog("^^irowcount=" . $irowcount , "^^", get_class($this) , true);
//			if (file_exists($publishPath . $ipublishname)) {
//				QubitXMLImport::addLog("exist^^" . $publishPath , "^^", get_class($this) , true);
//				$csvfile2 = fopen($publishPath . $ipublishname, "a") or die("Unable to open file!");
//				fwrite($csvfile2, $csvString);
//			} else {
//				QubitXMLImport::addLog("noit ^^" . $publishPath , "^^", get_class($this) , true);
//				$csvfile2 = fopen($publishPath . $ipublishname, "a") or die("Unable to open file!");
//				$csvStringHeader = "identifier|unitid|unittitle|dateType|unitdate|startDate|endDate|level|extent|source|filereference|volume|partno|corpname|repocorpcode|countrycode|repocorpname|custodhist|scopecontent|appraisal|accruals|arrangement|accessrestrict|userestrict|langcode|scriptcode|langmaterial|phystech|otherfindaid|originalsloc|altformavail|relatedmaterial|relateddescriptions|bibliography|note|publicationnote|archivistnote|subject|geogname|name|descriptionIdentifier|institutionIdentifier|rules|statusDescription|levelOfDetail|date|desclanguage|descscript|langcode|scriptcode|recordtype|size|type|classification|availabilityId|registryidentifier|registry|filePath\n";
//				fwrite($csvfile2, $csvStringHeader);
//				fwrite($csvfile2, $csvString);
//			}
//			fclose($csvfile2);
//			chmod($publishPath . $publishname, 0775);

			// Set the user

			chown($publishPath, "mqm");
			chgrp($publishPath, "mqm");
		}
	}
    /**
     * Add Batch Publish .csv for Mainframe Import
     * "identifier|unitid|unittitle|remark|subject|source|fileReference|volume|partNumber|repository|repositoryName|repositoryCountry| 		 * dateStart|dateEnd|availabilityId|medium|corpId|repositoryType|creatorName|registryIdentifier|registry|filePath";
     * @return
     */
    public static function addBatchPublishCSVMissing($CSVRecord, $irowcount, $ipublishname)
    {
        //		$csvStringHeader = "identifier|unitid|unittitle|remark|subject|source|fileReference|volume|partNumber|repository|repositoryName|repositoryCountry|dateStart|dateEnd|availabilityId|medium|corpId|repositoryType|creatorName|filePath\n";
		 $csvString = null;

		$cvsRepositoryCountryCode = str_replace('"', '""', ($CSVRecord['repositoryCountryCode']));
		$cvsRepositoryCode = str_replace('"', '""', ($CSVRecord['repositorycode']));
		$cvsIdentifier = str_replace('"', '""', ($CSVRecord['identifier']));
		$csvString = $csvString . '"' . $cvsRepositoryCountryCode . " " . $cvsRepositoryCode . " " . $cvsIdentifier . '"|"';
		$cvsUnitid = str_replace('"', '""', ($CSVRecord['unitid']));
		$csvString = $csvString . $cvsUnitid . '"|"';
		$cvsTitle = str_replace('"', '""', ($CSVRecord['unittitle']));
		$cvsTitle = str_replace('+', chr(10).chr(13)."--", $cvsTitle);
		$csvString = $csvString . $cvsTitle . '"|"';
		$cvsDateType = str_replace('"', '""', ($CSVRecord['dateType']));
		$csvString = $csvString . $cvsDateType . '"|"';
		$cvsUnitDate = str_replace('"', '""', ($CSVRecord['unitdate']));
		$csvString = $csvString . $cvsUnitDate . '"|"';
		$cvsStartDate = str_replace('"', '""', ($CSVRecord['startDate']));
		$csvString = $csvString . $cvsStartDate . '"|"';
		$cvsEndDate = str_replace('"', '""', ($CSVRecord['endDate']));
		$csvString = $csvString . $cvsEndDate . '"|"';
		$cvsLevel = str_replace('"', '""', ($CSVRecord['level']));
		$csvString = $csvString . $cvsLevel . '"|"';
		$cvsExtent = str_replace('"', '""', ($CSVRecord['extent']));
		$csvString = $csvString . $cvsExtent . '"|"';
		$cvsSource = str_replace('"', '""', ($CSVRecord['source']));
		$csvString = $csvString . $cvsSource . '"|"';
		$cvsReferenceNumber = str_replace('"', '""', ($CSVRecord['referenceNumber']));
		$csvString = $csvString . $cvsReferenceNumber . '"|"';
		$cvsVolumeNumber = str_replace('"', '""', ($CSVRecord['volumeNumber']));
		$csvString = $csvString . $cvsVolumeNumber . '"|"';
		$cvsPartNumber = str_replace('"', '""', ($CSVRecord['partNumber']));
		$csvString = $csvString . $cvsPartNumber . '"|"';
		$cvsCorpname = str_replace('"', '""', $CSVRecord['corpname']);
		$csvString = $csvString . $cvsCorpname . '"|"';
		$csvString = $csvString . $cvsRepositoryCode . '"|"';
		$csvString = $csvString . $cvsRepositoryCountryCode . '"|"';
		$cvsRepositoryName = str_replace('"', '""', ($CSVRecord['repocorpname']));
		$csvString = $csvString . $cvsRepositoryName . '"|"';
		$cvsCustodhist = str_replace('"', '""', ($CSVRecord['custodhist']));
		$csvString = $csvString . $cvsCustodhist . '"|"';
		$cvsScopecontent = str_replace('"', '""', ($CSVRecord['scopecontent']));
		$csvString = $csvString . $cvsScopecontent . '"|"';
		$cvsAppraisal = str_replace('"', '""', ($CSVRecord['appraisal']));
		$csvString = $csvString . $cvsAppraisal . '"|"';
		$cvsAccruals = str_replace('"', '""', ($CSVRecord['accruals']));
		$csvString = $csvString . $cvsAccruals . '"|"';
		$cvsArrangement = str_replace('"', '""', ($CSVRecord['arrangement']));
		$csvString = $csvString . $cvsArrangement . '"|"';
		$cvsAccessrestrict = str_replace('"', '""', ($CSVRecord['accessrestrict']));
		$csvString = $csvString . $cvsAccessrestrict . '"|"';
		$cvsUserestrict = str_replace('"', '""', ($CSVRecord['userestrict']));
		$csvString = $csvString . $cvsUserestrict . '"|"';
		$cvsLangcode = str_replace('"', '""', ($CSVRecord['langcode']));
		$csvString = $csvString . $cvsLangcode . '"|"';
		$cvsScriptcode = str_replace('"', '""', ($CSVRecord['scriptcode']));
		$csvString = $csvString . $cvsScriptcode . '"|"';
		$cvsLangmaterial = str_replace('"', '""', ($CSVRecord['langmaterial']));
		$csvString = $csvString . $cvsLangmaterial . '"|"';
		$cvsPhystech = str_replace('"', '""', ($CSVRecord['phystech']));
		$csvString = $csvString . $cvsPhystech . '"|"';
		$cvsOtherfindaid = str_replace('"', '""', ($CSVRecord['otherfindaid']));
		$csvString = $csvString . $cvsOtherfindaid . '"|"';
		$cvsOriginalsloc = str_replace('"', '""', ($CSVRecord['originalsloc']));
		$csvString = $csvString . $cvsOriginalsloc . '"|"';
		$cvsAltformavail = str_replace('"', '""', ($CSVRecord['altformavail']));
		$csvString = $csvString . $cvsAltformavail . '"|"';
		$cvsRelatedmaterial = str_replace('"', '""', ($CSVRecord['relatedmaterial']));
		$csvString = $csvString . $cvsRelatedmaterial . '"|"';
		$cvsRelateddescriptions = str_replace('"', '""', ($CSVRecord['relateddescriptions']));
		$csvString = $csvString . $cvsRelateddescriptions . '"|"';
		$cvsBibliography = str_replace('"', '""', ($CSVRecord['bibliography']));
		$csvString = $csvString . $cvsBibliography . '"|"';
		$cvsNote = str_replace('"', '""', ($CSVRecord['note']));
		$cvsNote = str_replace('+', chr(10).chr(13)."--", $cvsNote);
		$csvString = $csvString . $cvsNote . '"|"';
		$cvsPublicationnote = str_replace('"', '""', ($CSVRecord['publicationnote']));
		$csvString = $csvString . $cvsPublicationnote . '"|"';
		$cvsArchivistnote = str_replace('"', '""', ($CSVRecord['archivistnote']));
		$cvsArchivistnote = str_replace('+', chr(10).chr(13)."--", $cvsArchivistnote);
		$csvString = $csvString . $cvsArchivistnote . '"|"';
		$cvsSubject = str_replace('"', '""', ($CSVRecord['subject']));
		$csvString = $csvString . $cvsSubject . '"|"';
		$cvsGeogname = str_replace('"', '""', ($CSVRecord['geogname']));
		$csvString = $csvString . $cvsGeogname . '"|"';
		$cvsName = str_replace('"', '""', ($CSVRecord['name']));
		$csvString = $csvString . $cvsName . '"|"';
		$cvsDescriptionIdentifier = str_replace('"', '""', ($CSVRecord['descriptionIdentifier']));
		$csvString = $csvString . $cvsDescriptionIdentifier . '"|"';
		$cvsInstitutionIdentifier = str_replace('"', '""', ($CSVRecord['institutionIdentifier']));
		$csvString = $csvString . $cvsInstitutionIdentifier . '"|"';
		$cvsRules = str_replace('"', '""', ($CSVRecord['rules']));
		$csvString = $csvString . $cvsRules . '"|"';
		$cvsStatusDescription = str_replace('"', '""', ($CSVRecord['statusDescription']));
		$csvString = $csvString . $cvsStatusDescription . '"|"';
		$cvsLevelOfDetail = str_replace('"', '""', ($CSVRecord['levelOfDetail']));
		$csvString = $csvString . $cvsLevelOfDetail . '"|"';
		$cvsDate = str_replace('"', '""', ($CSVRecord['date']));
		$csvString = $csvString . $cvsDate . '"|"';
		$cvsDesclanguage = str_replace('"', '""', ($CSVRecord['desclanguage']));
		$csvString = $csvString . $cvsDesclanguage . '"|"';
		$cvsDescscript = str_replace('"', '""', ($CSVRecord['descscript']));
		$csvString = $csvString . $cvsDescscript . '"|"';
		$cvsLangcode = str_replace('"', '""', ($CSVRecord['langcode']));
		$csvString = $csvString . $cvsLangcode . '"|"';
		$cvsScriptcode = str_replace('"', '""', ($CSVRecord['scriptcode']));
		$csvString = $csvString . $cvsScriptcode . '"|"';
		$cvsRecordtype = str_replace('"', '""', ($CSVRecord['recordtype']));
		$csvString = $csvString . $cvsRecordtype . '"|"';
		$cvsSize = str_replace('"', '""', ($CSVRecord['size']));
		$csvString = $csvString . $cvsSize . '"|"';
		$cvsType = str_replace('"', '""', ($CSVRecord['type']));
		$csvString = $csvString . $cvsType . '"|"';
		$cvsClassification = str_replace('"', '""', ($CSVRecord['classification']));
		$csvString = $csvString . $cvsClassification . '"|"';
		$cvsAvailabilityId = str_replace('"', '""', ($CSVRecord['availabilityId']));
		$csvString = $csvString . $cvsAvailabilityId . '"|"';
		$cvsRegistryIdentifier = str_replace('"', '""', ($CSVRecord['registryIdentifier']));
		$csvString = $csvString . $cvsRegistryIdentifier . '"|"';
		$cvsRegistry = str_replace('"', '""', ($CSVRecord['registry']));
		$csvString = $csvString . $cvsRegistry . '"|"';
		$cvsFilePath = str_replace('"', '""', ($CSVRecord['filePath']));
		if ($cvsFilePath != "")
		{
			$csvString = $csvString . "attachments/".$cvsFilePath . '"|"';
		}
		else
		{
			$csvString = $csvString . "".$cvsFilePath . '"|"';
		}

		$cvsParentId = str_replace('"', '""', ($CSVRecord['parentid']));
		$csvString = $csvString . $cvsParentId . '"|"';

		$cvsDonorName = str_replace('"', '""', ($CSVRecord['donorName']));
		$csvString = $csvString . $cvsDonorName . '"|"';
		
		$cvsLegalEntityName = str_replace('"', '""', ($CSVRecord['legalEntityName']));
		$csvString = $csvString . $cvsLegalEntityName . '"|"';
		
		$cvsCorporateBodyName = str_replace('"', '""', ($CSVRecord['corporateBodyName']));
		$csvString = $csvString . $cvsCorporateBodyName;

		$csvString = $csvString . '"' . "\n";
		$publishPath = QubitSetting::getByName('publish_path');
		QubitXMLImport::addLog("^^" . $publishPath , "^^", get_class($this) , true);

		QubitXMLImport::addLog("^^irowcount=" . $rowcount , "^^", get_class($this) , true);
		if (file_exists($publishPath . $ipublishname)) {
			QubitXMLImport::addLog("exist^^" . $publishPath , "^^", get_class($this) , true);
			$csvfile2 = fopen($publishPath . $ipublishname, "a") or die("Unable to open file!");
			fwrite($csvfile2, $csvString);
		} else {
			QubitXMLImport::addLog("noit ^^" . $publishPath , "^^", get_class($this) , true);
			$csvfile2 = fopen($publishPath . $ipublishname, "a") or die("Unable to open file!");
			$csvStringHeader = "identifier|unitid|unittitle|dateType|unitdate|startDate|endDate|level|extent|source|filereference|volume|partno|corpname|repocorpcode|countrycode|repocorpname|custodhist|scopecontent|appraisal|accruals|arrangement|accessrestrict|userestrict|langcode|scriptcode|langmaterial|phystech|otherfindaid|originalsloc|altformavail|relatedmaterial|relateddescriptions|bibliography|note|publicationnote|archivistnote|subject|geogname|name|descriptionIdentifier|institutionIdentifier|rules|statusDescription|levelOfDetail|date|desclanguage|descscript|langcode|scriptcode|recordtype|size|type|classification|availabilityId|registryidentifier|registry|filePath\n";
			fwrite($csvfile2, $csvStringHeader);
			fwrite($csvfile2, $csvString);
		}
		fclose($csvfile2);
    }      

	function esc_specialchars($value)
	{ // Numbers and boolean values get turned into strings which can cause problems

		// with type comparisons (e.g. === or is_int() etc).

		return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, sfConfig::get('sf_charset')) : $value;
	}
	
    //string $c is the string of characters to use.
    //integer $l is how long you want the string to be.
    //boolean $u is whether or not a character can appear beside itself.
    function rand_chars($l, $u = FALSE)
    {
        $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        if (!$u) {
            for ($s = '', $i = 0, $z = strlen($c) - 1; $i < $l; $x = rand(0, $z), $s.= $c{$x}, $i++);
        } else {
            for ($i = 0, $z = strlen($c) - 1, $s = $c{rand(0, $z) }, $i = 1; $i != $l; $x = rand(0, $z), $s.= $c{$x}, $s = ($s{$i} == $s{$i - 1} ? substr($s, 0, -1) : $s), $i = strlen($s));
        }
        return $s;
    }
    
	
}
