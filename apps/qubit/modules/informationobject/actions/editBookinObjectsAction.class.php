<?php
/**
 * Presevation Object edit component.
 *
 * @package    qubit
 * @subpackage Preservation Module
 * @author     Ramaano Ndou <ramaano.ndou@sita.co.za> 
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 * @version    SVN: $Id
 */
class InformationObjectEditBookinObjectsAction extends DefaultEditAction
{
	public static $NAMES = array(
		'time_period',
		'MonthSelector_time_period',
		'year_time_period',
		'hour_time_period',
		'minute_time_period',
		'second_time_period',
		'name',
		'remarks',
		'requestor',
		'dispatcher',
		'location',
		'shelf',
		'row',
		'bin',
		'object_id',
		'unique_identifier',
		'record_condition',
		'service_provider',
		'requestor_type',
		'availability',
		'locationBookedInFrom'
	);
	protected
	function earlyExecute()
	{
		$this->resource = new QubitBookInObject;
		if (isset($this->getRoute()->resource))
		{
			$this->resource = $this->getRoute()->resource;
		}

		foreach(QubitRelation::getRelationsBySubjectId($this->resource->id) as $item2)
		{
			$this->informationObjects = QubitInformationObject::getById($item2->objectId);
		}

		$this->informationObj = new QubitInformationObject;
		$this->informationObj = QubitInformationObject::getById($this->informationObjects->id);
		$title = $this->context->i18n->__('Add new Booked Item');
		if (isset($this->getRoute()->resource))
		{
			if (1 > strlen($title = $this->resource->__toString()))
			{
				$title = $this->context->i18n->__('Untitled');
			}

			$title = $this->context->i18n->__('Edit %1%', array(
				'%1%' => $title
			));
		}

		// Check user authorization

		if (!QubitAcl::check($this->resource, 'bookIn'))
		{
		//	QubitAcl::forwardUnauthorized();
		}

		$this->response->setTitle("$title - {$this->response->getTitle() }");
	}

	protected
	function addField($name)
	{
		switch ($name)
		{
		case 'remarks':
			$this->form->setDefault('remarks', ""); // bring a value of the  field in Bookin
			$this->form->setValidator('remarks', new sfValidatorString);
			$this->form->setWidget('remarks', new sfWidgetFormTextArea(array() , array(
				'rows' => 2
			)));
			break;

		case 'time_period':
			$this->form->setDefault('time_period', "");
			$this->form->setValidator('time_period', new sfValidatorString);
			$this->form->setWidget('time_period', new sfWidgetFormInput);
			
		case 'strong_room':
			foreach($this->informationObj->getPhysicalObjects() as $item)
			{
				$this->form->setDefault('strong_room', $item->__toString()); // bring a value of the  field in Bookin
			}

			$this->form->setValidator('strong_room', new sfValidatorString);
			$this->form->setWidget('strong_room', new sfWidgetFormInput);
			break;

		case 'shelf':
			$this->form->setDefault('shelf', $this->informationObj->shelf); // bring a value of the shelf field in Bookin
			$this->form->setValidator('shelf', new sfValidatorString);
			$this->form->setWidget('shelf', new sfWidgetFormInput);
			break;

		case 'row':
			$this->form->setDefault('row', $this->informationObj->row); // bring a value of the shelf field in Bookin
			$this->form->setValidator('row', new sfValidatorString);
			$this->form->setWidget('row', new sfWidgetFormInput);
			break;

		case 'bin':
			$this->form->setDefault('bin', $this->informationObj->bin); // bring a value of the shelf field in Bookin
			$this->form->setValidator('bin', new sfValidatorString);
			$this->form->setWidget('bin', new sfWidgetFormInput);
			break;

		case 'location':
			foreach($this->informationObj->getPhysicalObjects() as $item)
			{
				$this->form->setDefault('location', $item->location); // bring a value of the shelf field in Bookin
			}

			$this->form->setValidator('location', new sfValidatorString);
			$this->form->setWidget('location', new sfWidgetFormInput);
			break;

		case 'locationBookedInFrom':
			$this->form->setDefault('locationBookedInFrom', ""); // bring a value of the  field in Bookin
			$this->form->setValidator('locationBookedInFrom', new sfValidatorString);
			$this->form->setWidget('locationBookedInFrom', new sfWidgetFormTextArea(array() , array('rows' => 2)));
			break;

		case 'availability':
			foreach($this->informationObj->getPresevationObjects() as $item)
			{
				$this->form->setDefault('availability', $item->availability); // bring a value of the shelf field in Bookin
			}

			$this->form->setValidator('availability', new sfValidatorString);
			$this->form->setWidget('availability', new sfWidgetFormInput);
			break;

		case 'unique_identifier':
			foreach($this->informationObj->getPhysicalObjects() as $item)
			{
				$this->form->setDefault('unique_identifier', $item->uniqueIdentifier); // bring a value of the  field in Bookin
			}

			$this->form->setValidator('unique_identifier', new sfValidatorString);
			$this->form->setWidget('unique_identifier', new sfWidgetFormInput);
			break;

		case 'record_condition':
			$this->form->setDefault('record_condition', "");
			$this->form->setValidator('record_condition', new sfValidatorString);
			$this->form->setWidget('record_condition', new sfWidgetFormTextArea(array() , array(
				'rows' => 2
			)));
			break;

		case 'name':
			$this->form->setDefault($name, $this->resource[$name]);
			$this->form->setValidator($name, new sfValidatorString);
			$this->form->setWidget($name, new sfWidgetFormInput);
			break;

		case 'requestor':
			$this->form->setDefault('requestor', $this->context->user->getAttribute('user_name')); // bring a value of a 'dispatcher' field in Bookin
			$this->form->setValidator('requestor', new sfValidatorString);
			$this->form->setWidget($name, new sfWidgetFormInput);

			break;

		case 'dispatcher':
			$values = array();
			foreach(QubitUser::getAll() as $user)
			{
				$values[$user->id] = $user->__toString();
			}

			$this->form->setValidator('dispatcher', new sfValidatorString);
			$this->form->setWidget('dispatcher', new sfWidgetFormSelect(array(
				'choices' => $values
			)));
			break;

		case 'MonthSelector_time_period':
			$this->form->setValidator('MonthSelector_time_period', new sfValidatorString);
			$this->form->setWidget('MonthSelector_time_period', new sfWidgetFormInput);
			break;

		case 'year_time_period':
			$this->form->setValidator('year_time_period', new sfValidatorString);
			$this->form->setWidget('year_time_period', new sfWidgetFormInput);
			break;

		case 'hour_time_period':
			$this->form->setValidator('hour_time_period', new sfValidatorString);
			$this->form->setWidget('hour_time_period', new sfWidgetFormInput);
			break;

		case 'minute_time_period':
			$this->form->setValidator('minute_time_period', new sfValidatorString);
			$this->form->setWidget('minute_time_period', new sfWidgetFormInput);
			break;

		case 'second_time_period':
			$this->form->setValidator('second_time_period', new sfValidatorString);
			$this->form->setWidget('second_time_period', new sfWidgetFormInput);
			break;

		default:
			return parent::addField($name);
		}
	}

	protected function processForm()
	{
		if (null !== $this->form->getValue('name'))
		{
			$bookinObject = new QubitBookinObject;
			$bookinObject->name = $this->form->getValue('name');
			if ($this->form->getValue('time_period') == null || $this->form->getValue('time_period') == "")
			{
				$time_period = "";
			}
			else
			{
				$time_period = $this->form->getValue('time_period');
			}

			$bookinObject->time_period = $time_period;
			if ($this->form->getValue('remarks') == null || $this->form->getValue('remarks') == "")
			{
				$remarks = "";
			}
			else
			{
				$remarks = $this->form->getValue('remarks');
			}

			$bookinObject->remarks = $remarks;
			if ($this->form->getValue('unique_identifier') == null || $this->form->getValue('unique_identifier') == "")
			{
				$unique_identifier = "";
			}
			else
			{
				$unique_identifier = $this->form->getValue('unique_identifier');
			}

			$bookinObject->unique_identifier = $unique_identifier; //Unique identifier
			if ($this->form->getValue('strong_room') == null || $this->form->getValue('strong_room') == "")
			{
				$strong_room = "";
			}
			else
			{
				$strong_room = $this->form->getValue('strong_room');
			}
			if ($this->form->getValue('location') == null || $this->form->getValue('location') == "")
			{
				$location = "";
			}
			else
			{
				$location = $this->form->getValue('location');
			}

			$strong_room = $strong_room . " " . $location;
			$bookinObject->strong_room = $strong_room; //new field
			
			if ($this->form->getValue('locationBookedInFrom') == null || $this->form->getValue('locationBookedInFrom') == "")
			{
				$locationBookedInFrom = "";
			}
			else
			{
				$locationBookedInFrom = $this->form->getValue('locationBookedInFrom');
			}
			$bookinObject->location = $locationBookedInFrom;

			if ($this->form->getValue('shelf') == null || $this->form->getValue('shelf') == "")
			{
				$shelf = "";
			}
			else
			{
				$shelf = $this->form->getValue('shelf');
			}

			$bookinObject->shelf = $shelf; //new field
			if ($this->form->getValue('row') == null || $this->form->getValue('row') == "")
			{
				$row = "";
			}
			else
			{
				$row = $this->form->getValue('row');
			}

			$bookinObject->row = $row; //new field
			if ($this->form->getValue('availability') == null || $this->form->getValue('availability') == "")
			{
				$availability = "";
			}
			else
			{
				$availability = $this->form->getValue('availability');
			}
			if ($availability != null)
			{
				$bookinObject->availability = $availability; //new field
			}
			else
			{
				$bookinObject->availability = "Yes";
			}
			if ($this->form->getValue('record_condition') == null || $this->form->getValue('record_condition') == "")
			{
				$record_condition = "";
			}
			else
			{
				$record_condition = $this->form->getValue('record_condition');
			}
			$bookinObject->receiverId = $this->form->getValue('requestor');
			$bookinObject->record_condition = $record_condition;
			$bookinObject->requestor_type = $this->resource['requestor_type'];
			$bookinObject->requestorId = $this->resource['requestorId'];
			$bookinObject->object_id = $this->informationObj->id;
			$bookinObject->save();
		}

		if (isset($this->request->delete_relations))
		{
			foreach($this->request->delete_relations as $item)
			{
				$params = $this->context->routing->parse(Qubit::pathInfo($item));
				$params['_sf_route']->resource->delete();
			}
		}
	}

	public

	function execute($request)
	{
		parent::execute($request);
		if ($request->isMethod('post'))
		{
			$this->form->bind($request->getPostParameters());
			if ($this->form->isValid())
			{
				$this->processForm();

				$this->publish($this->informationObjects);

				$this->resource->delete();
				
				if (null !== $next = $this->form->getValue('next'))
				{
					$this->redirect($next);
				}

				$this->redirect(array($this->informationObj,'module' => 'informationobject'));
			}
		}
	}

	public

	function publish($informationObj)
	{
		$publish = 'Yes'; //QubitXmlImport::translateNameToTermId2('Publish', QubitTerm::PUBLISH_ID, 'Yes');
		$filterCriteria = new Criteria;
		$filterCriteria->add(QubitInformationObject::ID, QubitInformationObject::ROOT_ID, Criteria::NOT_EQUAL);
		$filterCriteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
		$filterCriteria->add(QubitInformationObjectI18n::CULTURE, $this->context->user->getCulture());
		$filterCriteria->add($filterCriteria->getNewCriterion(QubitInformationObject::ID, $informationObj->id, Criteria::LIKE));
		$filterCriteria->addAscendingOrderByColumn(QubitInformationObject::LEVEL_OF_DESCRIPTION_ID);
		$filterCriteria->addAscendingOrderByColumn(QubitInformationObject::IDENTIFIER);
		$filterCriteria->addAscendingOrderByColumn(QubitInformationObjectI18n::TITLE);
		$infoObject = QubitInformationObject::getByCriteria($filterCriteria);
		foreach($infoObject->getAccessObjects() as $item)
		{
			if ($item->publishId == $publish)
			{
				QubitXMLImport::addLog("item->publishId: " . isset($item->publishId) , "Publish Bookin", get_class($this) , false);
				if (isset($item->publishId))
				{
					$publishname = "publish_" . $informationObj->id . "_" . date("Y-m-dHis") . ".csv";
					$locations = array();
					$fileCopied = false;
					if (null !== ($digitalObject = $infoObject->getDigitalObject()))
					{
						if (isset($digitalObject->mimeType))
						{
							$path_parts = pathinfo(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name);
							if (!isset($digitalObject->name))
							{
								throw new sfException(sfContext::getInstance()->i18n->__(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name . " No digital image/file available. Contact support/administrator"));
							}

							// only copy the thumbnail

							if (QubitDigitalObject::isImageFile($digitalObject->getName()))
							{
								$extension = "jpg";
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
						QubitXMLImport::addLog("Title: " . $value, "Publish Bookin", get_class($this) , false);
						$unitTitle = $value;
					}
					else
					{
						QubitXMLImport::addLog("Title: Empty" . $infoObject->id, "Publish Bookin", get_class($this) , false);
						$unitTitle = "";
					}

					if (0 < strlen($value = $infoObject->alternateTitle))
					{
						$alternateTitle = $value;
					}
					else
					{
						$alternateTitle = "";
					}

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

					if ($infoObject->descriptionDetailId)
					{
						$descendantLevelOfDetail = QubitTerm::getById($infoObject->descriptionDetailId);
					}
					else
					{
						$descendantLevelOfDetail = "";
					}

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

					if (isset($repository))
					{
						if ($countrycode = $repository->getCountryCode()) //to check
						{
							$countrycode = $countrycode;
						}
					}

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

					if (0 < strlen($value = $infoObject->getExtentAndMedium(array(
						'cultureFallback' => true
					))))
					{
						$extendAndMedium = BasePeer::esc_specialchars($value);
					}

					if ($value = $infoObject->getRepository())
					{
						$repository = (String)$value;
					}

					$language = "";
					$scriptCode = "";
					if (0 < count($infoObject->language) || 0 < count($infoObject->script) || 0 < count($infoObject->getNotesByType(array(
						'noteTypeId' => QubitTerm::LANGUAGE_NOTE_ID
					))->offsetGet(0)))
					{
						foreach($infoObject->language as $languageCode)
						{

							// $ = strtolower($iso639convertor->getID2($languageCode)) . " : " . format_language($languageCode); To Check

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

					if ($infoObject->sources)
					{
						$sources = $infoObject->sources;
					}
					else
					{
						$sources = "";
					}

					if (0 < count($notes = $infoObject->getNotesByType(array(
						'noteTypeId' => QubitTerm::GENERAL_NOTE_ID
					))))
					{
						foreach($notes as $note)
						{
							$noteTypeId = $note->getContent(array(
								'cultureFallback' => true
							));
							$noteGeneral = BasePeer::esc_specialchars($note);
						}
					}

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
										$bibliographyPublicationNotes = BasePeer::esc_specialchars($note);
									}
									else
									{
										$archivistsNote = $archivistsNote . " \n" . BasePeer::esc_specialchars($note);
									}
								}
								else
								{
									$archivistsNote = $archivistsNote . " \n" . BasePeer::esc_specialchars($note);
								}

								$aCount+= 1;
							}
						}
					}

					$registry = $infoObject->getRegistry();
					if ($registry == "Unknown")
					{
						$registry = "";
					}

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

					if ($infoObject->getPublicationStatus())
					{
						$publicationStatus = $infoObject->getPublicationStatus();
					}

					$descriptionStatus = ($infoObject->descriptionStatusId) ? QubitTerm::getById($infoObject->descriptionStatusId) : '';
					if ($descriptionStatus)
					{
						$descriptionStatus = $descriptionStatus;
					}
					else
					{
						$descriptionStatus = "";
					}

					if ($infoObject->descriptionIdentifier)
					{
						$descriptionIidentifier = $infoObject->descriptionIdentifier;
					}
					else
					{
						$descriptionIidentifier = "";
					}

					if ($infoObject->institutionResponsibleIdentifier)
					{
						$institutionResponsibleIdentifier = $infoObject->institutionResponsibleIdentifier;
					}
					else
					{
						$institutionResponsibleIdentifier = "";
					}

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

					$availabilityId = "";
					$publishPath = QubitSetting::getByName('publish_path');
					if ($publishPath == null)
					{
						throw new sfException(sfContext::getInstance()->i18n->__("No upload path defined. Contact support/administrator"));
					}
					else
					{
						// save to CSV
						try
						{
							$csvfile = fopen($publishPath . $publishname, "a");
						}
						catch (sfException $e)
						{
							// Log download exception
							$this->logMessage($e->getMessage, 'err');
							throw new sfException("Unable to open file! ".$publishPath . $publishname." ".$e->getMessage);							
						}
						$csvfile = fopen($publishPath . $publishname, "a") or die("Unable to open file!");

						$csvStringHeader = "identifier|unitid|unittitle|dateType|unitdate|startDate|endDate|level|extent|source|filereference|volume|partno|corpname|repocorpcode|countrycode|repocorpname|custodhist|scopecontent|appraisal|accruals|arrangement|accessrestrict|userestrict|langcode|scriptcode|langmaterial|phystech|otherfindaid|originalsloc|altformavail|relatedmaterial|relateddescriptions|bibliography|note|publicationnote|archivistnote|subject|geogname|name|descriptionIdentifier|institutionIdentifier|rules|statusDescription|levelOfDetail|date|desclanguage|descscript|langcode|scriptcode|recordtype|size|type|classification|availabilityId|registryidentifier|registry|filePath|parentid|donorName|legalEntityName|corporateBodyName\n";
						fwrite($csvfile, $csvStringHeader);
						if (0 < strlen($repoValue = $infoObject->getRepository()))
						{
							$repositoryCode = $repoValue->identifier;
						}
						else
						{
							$repositoryCode = "";
						}

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

						if ("Legal Deposit" == $entityTypeType)
						{
							$donorName = $creatorNameString;
							$corpname = $creatorNameString;
							$legalEntityName = "";
							$corporateBodyName = "";
						}
						else
						if ("Donor" == $entityTypeType)
						{
							$donorName = "";
							$corpname = $creatorNameString;
							$legalEntityName = $creatorNameString;
							$corporateBodyName = "";
						}
						else
						if ("Corporate body" == $entityTypeType)
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

						// for CSV export

						//get preservation
						if ($this->form->getValue('availability') == null)
						{
				        	$itemAvailible = QubitXmlImport::translateNameToTermId2('Availability', QubitTerm::AVAILABILITYS_ID, "Yes");
						}
						else
						{
				        	$itemAvailible = QubitXmlImport::translateNameToTermId2('Availability', QubitTerm::AVAILABILITYS_ID, $this->form->getValue('availability'));
				        }
			        	$itemAvailible = QubitTerm::getById($itemAvailible);
						QubitXMLImport::addLog("Item Availible: " . $itemAvailible, "Publish Bookin", get_class($this) , false);

						QubitXMLImport::addLog("this->CSVValues: " . $identifier, "Publish Bookin", get_class($this) , false);
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
							'availabilityId' => $itemAvailible, //preservation field...
							'registryIdentifier' => $registryIdentifier,
							'registry' => $registry,
							'filePath' => $filenameRandom,
							'parentid' => $parentid,
							'donorName' => $donorName,
							'legalEntityName' => $legalEntityName,
							'corporateBodyName' => $corporateBodyName
						);
						QubitXMLImport::addLog("addBatchPublishCSV: " . $informationObj->id, "Publish Bookin", get_class($this) , false);
						QubitXMLImport::addBatchPublishCSV($accessID, $informationObj->id, $this->CSVValues, $csvfile);
						fclose($csvfile);

						// Set the user and group

						chmod($publishPath . $publishname, 0775);
						chown($publishPath, "mqm");
						chgrp($publishPath, "mqm");
					}
				}
			}
		}
	}
}
