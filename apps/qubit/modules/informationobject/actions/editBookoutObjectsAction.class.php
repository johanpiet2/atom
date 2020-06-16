<?php
/*
 * This file is part of Qubit Toolkit.
 *
 * Qubit Toolkit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Qubit Toolkit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Qubit Toolkit.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Physical Object edit component.
 *
 * @package    qubit
 * @subpackage digitalObject
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 * @version    SVN: $Id
 */
class InformationObjectEditBookoutObjectsAction extends DefaultEditAction {
	public static $NAMES = array(
		'time_period', 
		'end_date', 
		'remarks', 
		'identifier', 
		'name', 
		'requestor', 
		'unique_identifier', 
		'strong_room', 
		'shelf', 
		'row', 
		'bin', 
		'medium', 
		'object_id', 
		'dispatcher', 
		'location', 
		'availability', 
		'service_provider', 
		'serviceProviderObject', 
		'serviceProviderFlag', 
		'researcherFlag', 
		'authorityRecordFlag', 
		'researcher', 
		'researcherObject', 
		'researcherFlag', 
		'authority_record', 
		'authorityRecordObject', 
		'authorityRecordFlag', 
		'record_condition', 
		'locationBookedOutTo',
		'cbReceipt'
	);

	protected function earlyExecute() {
		$this->form->getValidatorSchema()->setOption('allow_extra_fields', true);
		$this->resource = $this->getRoute()->resource;
		
		// Check that this isn't the root
		
		if (!isset($this->resource->parent)) {
			$this->forward404();
		}
		
		// Check user authorization
		
		if (!QubitAcl::check($this->resource, 'bookOut')) {
			QubitAcl::forwardUnauthorized();
		}
	}
	
	protected function addField($name) {
		switch ($name) {
			case 'identifier':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				$this->form->setDefault('identifier', $informationObj->identifier); // bring a value of the shelf field in Bookout
				$this->form->setValidator('identifier', new sfValidatorString);
				$this->form->setWidget('identifier', new sfWidgetFormInput);
				break;
			
			case 'unique_identifier':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				foreach ($informationObj->getPhysicalObjects() as $item) {
					$this->form->setDefault('unique_identifier', $item->uniqueIdentifier); // bring a value of the  field in Bookout
				}
				
				$this->form->setValidator('unique_identifier', new sfValidatorString);
				$this->form->setWidget('unique_identifier', new sfWidgetFormInput);
				break;
			
			case 'remarks':
				$this->form->setDefault('remarks', ""); // bring a value of the  field in Bookout
				$this->form->setValidator('remarks', new sfValidatorString);
				$this->form->setWidget('remarks', new sfWidgetFormTextArea(array(), array(
					'rows' => 2
				)));
				break;
			
			case 'locationBookedOutTo':
				$this->form->setDefault('locationBookedOutTo', ""); // bring a value of the  field in Bookout
				$this->form->setValidator('locationBookedOutTo', new sfValidatorString);
				$this->form->setWidget('locationBookedOutTo', new sfWidgetFormTextArea(array(), array(
					'rows' => 2
				)));
				break;
			
			case 'time_period':
				$this->form->setDefault('time_period', ""); // bring a value of the  field in Bookout
				$this->form->setValidator('time_period', new sfValidatorString);
				$this->form->setWidget('time_period', new sfWidgetFormInput);
				break;
			
			case 'end_date':
				$this->form->setDefault('end_date', ""); // bring a value of the  field in Bookout
				$this->form->setValidator('end_date', new sfValidatorString);
				$this->form->setWidget('end_date', new sfWidgetFormInput);
				break;
			
			case 'strong_room':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				foreach ($informationObj->getPhysicalObjects() as $item) {
					$this->form->setDefault('strong_room', $item->__toString()); // bring a value of the  field in Bookout
				}
				
				$this->form->setValidator('strong_room', new sfValidatorString);
				$this->form->setWidget('strong_room', new sfWidgetFormInput);
				break;
			
			case 'shelf':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				$this->form->setDefault('shelf', $informationObj->shelf); // bring a value of the shelf field in Bookout
				$this->form->setValidator('shelf', new sfValidatorString);
				$this->form->setWidget('shelf', new sfWidgetFormInput);
				break;
			
			case 'row':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				$this->form->setDefault('row', $informationObj->row); // bring a value of the shelf field in Bookout
				$this->form->setValidator('row', new sfValidatorString);
				$this->form->setWidget('row', new sfWidgetFormInput);
				break;
			
			case 'bin':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				$this->form->setDefault('bin', $informationObj->bin); // bring a value of the shelf field in Bookout
				$this->form->setValidator('bin', new sfValidatorString);
				$this->form->setWidget('bin', new sfWidgetFormInput);
				break;
			
			case 'location':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				foreach ($informationObj->getPhysicalObjects() as $item) {
					$this->form->setDefault('location', $item->location); // bring a value of the shelf field in Bookout
				}
				
				$this->form->setValidator('location', new sfValidatorString);
				$this->form->setWidget('location', new sfWidgetFormInput);
				break;
			
			case 'availability':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				foreach ($informationObj->getPresevationObjects() as $item) {
					$this->form->setDefault('availability', $item->availability); // bring a value of the
				}
				
				$this->form->setValidator('availability', new sfValidatorString);
				$this->form->setWidget('availability', new sfWidgetFormInput);
				break;
			
			case 'medium':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				foreach ($informationObj->getPresevationObjects() as $item) {
					$this->form->setDefault('medium', $item->medium); // bring a value of the
				}
				
				$this->form->setValidator('medium', new sfValidatorString);
				$this->form->setWidget('medium', new sfWidgetFormInput);
				break;
			
			case 'record_condition':
				$informationObj = QubitInformationObject::getById($this->resource->id);
				foreach ($informationObj->getAccessObjects() as $item) {
					$this->form->setDefault('record_condition', $item->restriction_condition); // bring a value of the
				}
				
				$this->form->setValidator('record_condition', new sfValidatorString);
				$this->form->setWidget('record_condition', new sfWidgetFormTextArea(array(), array(
					'rows' => 2
				)));
				break;
			
			case 'name':
				$this->form->setDefault($name, $this->resource); // bring a value of a name field in Bookout
				$this->form->setValidator($name, new sfValidatorString);
				$this->form->setWidget($name, new sfWidgetFormInput);
				break;
			
			case 'serviceProviderFlag':
				$this->form->setDefault('serviceProviderFlag', ""); // bring a value of the  field in Bookout
				$this->form->setValidator('serviceProviderFlag', new sfValidatorString);
				$this->form->setWidget('serviceProviderFlag', new sfWidgetFormInput);
				break;
			
			case 'researcherFlag':
				$this->form->setDefault('researcherFlag', ""); // bring a value of the  field in Bookout
				$this->form->setValidator('researcherFlag', new sfValidatorString);
				$this->form->setWidget('researcherFlag', new sfWidgetFormInput);
				break;
			
			case 'authorityRecordFlag':
				$this->form->setDefault('authorityRecordFlag', ""); // bring a value of the  field in Bookout
				$this->form->setValidator('authorityRecordFlag', new sfValidatorString);
				$this->form->setWidget('authorityRecordFlag', new sfWidgetFormInput);
				break;
			
			case 'service_provider':
				$choices = array();
				// Show only Repositories linked to user - Administrator can see all - One Instance
				// Start
				if ((!$this->context->user->isAdministrator()) && (QubitSetting::getByName('open_system') == '0')) {
				    $repositories = new QubitUser;
				    if (0 < count($userRepos = $repositories->getRepositoriesById($this->context->user->getAttribute('user_id')))) {
				        // Combined subquery
				        foreach ($userRepos as $userRepo) {
				            $serviceprovider = QubitServiceProvider::getRepositoriesById($userRepo);
						    foreach ($serviceprovider as $serviceP) {
								$choices[$serviceP->id] = $serviceP;
							}
				        }
				    } else {
						$choices['000'] = "";
					} 
				} else {
					foreach (QubitServiceProvider::getAll() as $item) {
						if ($item->__toString() != "") {
							$choices[$item->id] = $item->__toString();
						}
					}
			    }
				
				$this->form->setValidator('service_provider', new sfValidatorChoice(array(
					'choices' => array_keys($choices)
				)));
				$this->form->setWidget('service_provider', new sfWidgetFormSelect(array(
					'choices' => $choices
				)));
				break;
			
			case 'researcher':
				$choices = array();
				// Show only Repositories linked to user - Administrator can see all - One Instance
				// Start
				if ((!$this->context->user->isAdministrator()) && (QubitSetting::getByName('open_system') == '0')) {
				    $repositories = new QubitUser;
				    if (0 < count($userRepos = $repositories->getRepositoriesById($this->context->user->getAttribute('user_id')))) {
				        foreach ($userRepos as $userRepo) {
				            $researcher = QubitResearcher::getRepositoriesById($userRepo);
						    foreach ($researcher as $researcherList) {
								$choices[$researcherList->id] = $researcherList;
							}
				        }
				    } else {
						foreach (QubitResearcher::getAll() as $item) {
							$choices['0000'] = "";
						}
				    }
			    } else {
					foreach (QubitResearcher::getAll() as $item) {
						if ($item->__toString() != "") {
							$choices[$item->id] = $item->__toString();
						}
					}
			    }
				$this->form->setValidator('researcher', new sfValidatorChoice(array(
					'choices' => array_keys($choices)
				)));
				$this->form->setWidget('researcher', new sfWidgetFormSelect(array(
					'choices' => $choices
				)));
				break;
			
			case 'authority_record':
		        // Get list of authority records
		        $criteria = new Criteria;
		        // Do source culture fallback
		        $criteria = QubitCultureFallback::addFallbackCriteria($criteria, 'QubitActor');
		        // Ignore root repository
		        $criteria->add(QubitActor::ID, QubitRepository::ROOT_ID, Criteria::NOT_EQUAL);

			    $criteria->addJoin(QubitActor::ID, QubitObject::ID);
		        $criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
		        $criteria->addAscendingOrderByColumn('authorized_form_of_name');
		        $criteria = QubitActor::addGetOnlyActorsCriteria($criteria);
		        $criteria->add(QubitActor::PARENT_ID, null, Criteria::ISNOTNULL);

		        $choices = array();
		        foreach (QubitActor::get($criteria) as $authorityRecords) {
		            $choices[$authorityRecords->id] = $authorityRecords->__toString();
		        }
		        $this->form->setValidator('authority_record', new sfValidatorChoice(array('choices' => array_keys($choices))));
		        $this->form->setWidget('authority_record', new sfWidgetFormSelect(array('choices' => $choices)));
		        break;

			case 'requestor':
				$this->form->setDefault('requestor', $this->context->user->getAttribute('user_name')); // bring a value of a 'requestor' field in Bookout
				$this->form->setValidator('requestor', new sfValidatorString);
				$this->form->setWidget('requestor', new sfWidgetFormInput);

				break;
			
			case 'dispatcher':
			$this->form->setDefault('dispatcher', $this->context->user->getAttribute('user_name')); // bring a value of a 'dispatcher' field in Bookout
				$this->form->setValidator('dispatcher', new sfValidatorString);
				$this->form->setWidget('dispatcher', new sfWidgetFormTextArea(array(), array('rows' => 1)));
				break;
			
		    case 'cbReceipt':
				$this->form->setDefault($name, true);
				$this->form->setValidator($name, new sfValidatorBoolean);
				$this->form->setWidget($name, new sfWidgetFormInputCheckbox);

		        break;

			default:
				return parent::addField($name);
		}
	}
	
	protected function processForm() {
		if (null !== $this->form->getValue('name') || 
		null !== $this->form->getValue('time_period') || 
		null !== $this->form->getValue('unique_identifier') || 
		null !== $this->form->getValue('strong_room') || 
		null !== $this->form->getValue('shelf') || 
		null !== $this->form->getValue('row') || 
		null !== $this->form->getValue('location') || 
		null !== $this->form->getValue('availability') || 
		null !== $this->form->getValue('record_condition')) {
			$bookoutObject       = new QubitBookoutObject;
			$bookoutObject->name = $this->form->getValue('name');
			if ($this->form->getValue('time_period') == null || $this->form->getValue('time_period') == "") {
				$time_period = "";
			} else {
				$time_period = $this->form->getValue('time_period');
			}
			
			$bookoutObject->time_period = $time_period;
			if ($this->form->getValue('remarks') == null || $this->form->getValue('remarks') == "") {
				$remarks = "";
			} else {
				$remarks = $this->form->getValue('remarks');
			}
			$bookoutObject->remarks = $remarks;
			
			if ($this->form->getValue('locationBookedOutTo') == null || $this->form->getValue('locationBookedOutTo') == "") {
				$locationBookedOutTo = "";
			} else {
				$locationBookedOutTo = $this->form->getValue('locationBookedOutTo');
			}
			$bookoutObject->location = $locationBookedOutTo;
			
			if ($this->form->getValue('unique_identifier') == null || $this->form->getValue('unique_identifier') == "") {
				$unique_identifier = "";
			} else {
				$unique_identifier = $this->form->getValue('unique_identifier');
			}
			
			$bookoutObject->unique_identifier = $unique_identifier; //Unique identifier
			if ($this->form->getValue('strong_room') == null || $this->form->getValue('strong_room') == "") {
				$strong_room = "";
			} else {
				$strong_room = $this->form->getValue('strong_room');
			}
			
			if ($this->form->getValue('location') == null || $this->form->getValue('location') == "") {
				$location = "";
			} else {
				$location = $this->form->getValue('location');
			}
			$strong_room = $strong_room . " " . $location;
			
			$bookoutObject->strong_room = $strong_room; //new field
			if ($this->form->getValue('shelf') == null || $this->form->getValue('shelf') == "") {
				$shelf = "";
			} else {
				$shelf = $this->form->getValue('shelf');
			}
			
			$bookoutObject->shelf = $shelf; //not used
			if ($this->form->getValue('row') == null || $this->form->getValue('row') == "") {
				$row = "";
			} else {
				$row = $this->form->getValue('row');
			}
			
			$bookoutObject->row = $row;
			
			if ($this->form->getValue('availability') == null || $this->form->getValue('availability') == "") {
				$availability = "";
			} else {
				$availability = $this->form->getValue('availability');
			}
			
			$bookoutObject->availability = $availability; //new field
			if ($this->form->getValue('record_condition') == null || $this->form->getValue('record_condition') == "") {
				$record_condition = "";
			} else {
				$record_condition = $this->form->getValue('record_condition');
			}
			
			$bookoutObject->record_condition = $record_condition; //new field
			$informationObj                  = QubitInformationObject::getById($this->resource->id);
			$bookoutObject->object_id        = $informationObj->id;
			if ($this->form->getValue('serviceProviderFlag') == "true") //
				{
				$bookoutObject->service_provider = $this->form->getValue('service_provider'); //Number of service Provider
				$bookoutObject->requestor_type   = "3";
			} elseif ($this->form->getValue('researcherFlag') == "true") //
				{
				$bookoutObject->service_provider = $this->form->getValue('researcher'); //Number of researcher
				$bookoutObject->requestor_type   = "1";
			} elseif ($this->form->getValue('authorityRecordFlag') == "true") //
				{
				$bookoutObject->service_provider = $this->form->getValue('authority_record'); //Number of authority_record
				$bookoutObject->requestor_type   = "2";
			} else {
				$bookoutObject->service_provider = null; //new field
				$bookoutObject->requestor_type   = "0";
			}
			
			$bookoutObject->name         = $informationObj->title;
			$bookoutObject->requestorId  = $this->form->getValue('requestor');
			$bookoutObject->dispatcherId = $this->form->getValue('dispatcher');
			$bookoutObject->save();
			$this->bookoutObject =  $bookoutObject->id;
			$this->resource->addBookoutObject($bookoutObject);
		}
		
		if (isset($this->request->delete_relations)) {
			foreach ($this->request->delete_relations as $item) {
				$params = $this->context->routing->parse(Qubit::pathInfo($item));
				$params['_sf_route']->resource->delete();
			}
		}
	}
	
	public function execute($request) {
		parent::execute($request);
		if ($request->isMethod('post')) {
			$this->form->bind($request->getPostParameters());
			if ($this->form->isValid()) {
				$this->processForm();
				$this->resource->save();
				$this->informationObject = QubitInformationObject::getById($this->resource->id);
				$this->publish($this->informationObject);
				if ($this->form->getValue('cbReceipt') != 1) {
					$this->redirect(array($this->resource, 'module' => 'informationobject'));
				} else {
					$this->redirect(array($this->resource, 'module' => 'bookoutobject', 'action' => 'receipt', 'source' => $this->bookoutObject ));
				}
			}
		}
	}
	
	public function publish($informationObjPublish) {
		$publish        = QubitXmlImport::translateNameToTermId2('Publish', QubitTerm::PUBLISH_ID, 'Yes');
		$filterCriteria = new Criteria;
		$filterCriteria->add(QubitInformationObject::ID, QubitInformationObject::ROOT_ID, Criteria::NOT_EQUAL);
		$filterCriteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
		$filterCriteria->add(QubitInformationObjectI18n::CULTURE, $this->context->user->getCulture());
		$filterCriteria->add($filterCriteria->getNewCriterion(QubitInformationObject::ID, $informationObjPublish->id, Criteria::LIKE));
		$filterCriteria->addAscendingOrderByColumn(QubitInformationObject::LEVEL_OF_DESCRIPTION_ID);
		$filterCriteria->addAscendingOrderByColumn(QubitInformationObject::IDENTIFIER);
		$filterCriteria->addAscendingOrderByColumn(QubitInformationObjectI18n::TITLE);
		$infoObject = QubitInformationObject::getByCriteria($filterCriteria);
		foreach ($infoObject->getAccessObjects() as $item) {
			if ($item->publishId == $publish) {
				if (isset($item->publishId)) {
					$publishname = "publish_" . $informationObjPublish->id . "_" . date("Y-m-dHis") . ".csv";
					$locations   = array();
					$fileCopied  = false;
					if (null !== ($digitalObject = $infoObject->getDigitalObject())) {
						if (isset($digitalObject->mimeType)) {
							$path_parts = pathinfo(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name);
							if (!isset($digitalObject->name)) {
								throw new sfException(sfContext::getInstance()->i18n->__(sfConfig::get('sf_web_dir') . $digitalObject->path . $digitalObject->name . " No digital image/file available. Contact support/administrator"));
							}
							
							// only copy the thumbnail
							
							if (QubitDigitalObject::isImageFile($digitalObject->getName())) {
								$extension      = "jpg";
								$randomFilename = substr(str_shuffle(MD5(microtime())), 0, 10);
								$filename       = $path_parts['filename'] . "." . $extension;
								$mqPath         = QubitSetting::getByName('mq_path');
								if ($mqPath == null) {
									throw new sfException(sfContext::getInstance()->i18n->__("No MQ path defined. Contact support/administrator"));
									QubitXMLImport::addLog("No MQ path defined. Contact support/administrator", "No MQ path defined. Contact support/administrator", get_class($this), true);
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
											throw new sfException(sfContext::getInstance()->i18n->__("file_exists=false. Contact support/administrator"));
										}
									}
								}
							}
						} else {
							QubitXMLImport::addLog("No digital image/file available Mime Type unknown. Contact support/administrator", "No digital image/file available Mime Type unknown. Contact support/administrator", get_class($this), true);
							throw new sfException(sfContext::getInstance()->i18n->__(" No digital image/file available Mime Type unknown. Contact support/administrator"));
						}
					}
					
					
					if (0 < strlen($value = $infoObject->getTitle(array(
						'cultureFallback' => true
					)))) {
						QubitXMLImport::addLog("Title: " . $value, "Publish Bookout", get_class($this), false);
						$unitTitle = $value;
					} else {
						QubitXMLImport::addLog("Title: Empty" . $infoObject->id, "Publish Bookout", get_class($this), false);
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
							$eDate     = $endDate;
							$dateRange = Qubit::renderDateStartEnd($date->getDate(array(
								'cultureFallback' => true
							)), $date->startDate, $date->endDate);
						}
					}
					
					if (0 < strlen($value = $infoObject->getExtentAndMedium(array(
						'cultureFallback' => true
					)))) {
						$extendAndMedium = BasePeer::esc_specialchars($value);
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
							
							// $ = strtolower($iso639convertor->getID2($languageCode)) . " : " . format_language($languageCode); To Check
							
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
						foreach ($notes as $note) {
							$noteTypeId  = $note->getContent(array(
								'cultureFallback' => true
							));
							$noteGeneral = BasePeer::esc_specialchars($note);
						}
					}
					
					$bibliographyPublicationNotes = "";
					if (0 < count($publicationNotes = $infoObject->getNotesByType(array(
						'noteTypeId' => QubitTerm::PUBLICATION_NOTE_ID
					)))) {
						foreach ($publicationNotes as $note) {
							$bibliographyPublicationNotes = $note;
						}
					}
					
					if (0 < count($archivistsNotes = $infoObject->getNotesByType(array(
						'noteTypeId' => QubitTerm::ARCHIVIST_NOTE_ID
					)))) {
						$aCount = 0;
						foreach ($archivistsNotes as $note) {
							if (0 < strlen($note)) {
								if ($aCount == 0) {
									if ($bibliographyPublicationNotes == "") {
										$bibliographyPublicationNotes = BasePeer::esc_specialchars($note);
									} else {
										$archivistsNote = $archivistsNote . " \n" . BasePeer::esc_specialchars($note);
									}
								} else {
									$archivistsNote = $archivistsNote . " \n" . BasePeer::esc_specialchars($note);
								}
								
								$aCount += 1;
							}
						}
					}
					
					$registry = $infoObject->getRegistry();
					if ($registry == "Unknown") {
						$registry = "";
					}
					
					// $registryIdentifier = $infoObject->getRegistryId(array('cultureFallback' => true ));
					
					$registryIdentifier = $registry->corporateBodyIdentifiers;
					$creators           = $infoObject->getCreators();
					$events             = $infoObject->getActorEvents(array(
						'eventTypeId' => QubitTerm::CREATION_ID
					));
					if (0 < count($creators)) {
						foreach ($events as $date) {
							$creator = QubitActor::getById($date->actorId);
							
							// $bioghist = 'md5-' . ": " . $ead->getMetadataParameter('bioghist');
							// $eadDateFromEvent = $ead->renderEadDateFromEvent('creation', $date); //to check
							
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
							
							// $ = "Origination: " . $ead->getMetadataParameter('origination');  //To Check
							
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
					$publishPath    = QubitSetting::getByName('publish_path');
					if ($publishPath == null) {
						throw new sfException(sfContext::getInstance()->i18n->__("No upload path defined. Contact support/administrator"));
					} else {
						
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
						QubitXMLImport::addLog("this->CSVValues: " . $identifier, "Publish Bookout", get_class($this), false);
						
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
							'availabilityId' => "No",
							'registryIdentifier' => $registryIdentifier,
							'registry' => $registry,
							'filePath' => $filenameRandom,
							'parentid' => $parentid,
							'donorName' => $donorName,
							'legalEntityName' => $legalEntityName,
							'corporateBodyName' => $corporateBodyName
						);
						QubitXMLImport::addLog("addBatchPublishCSV: " . $informationObjPublish->id, "Publish Bookout", get_class($this), false);
						QubitXMLImport::addBatchPublishCSV($accessID, $informationObjPublish->id, $this->CSVValues, $csvfile);
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
