<?php

/**
 * Bookout Object edit component.
 *
 * @package    qubit
 * @subpackage Bookout Module
 * @author     Tsholo Ramesega
 * @author     Johan Pieterse <johan.pieterse@sita.co.za>
 * @version    SVN: $Id
 */


class BookoutObjectEditBookoutAction extends DefaultEditAction {
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
		'strong_room', 
		'shelf', 
		'row', 
		'bin', 
		'object_id', 
		'unique_identifier', 
		'record_condition', 
		'requestor_type',
		'availability',
		'cbReceipt');
	
	protected function earlyExecute() {
		$this->resource = new QubitBookoutObject;
		if (isset($this->getRoute()->resource)) {
			$this->resource = $this->getRoute()->resource;
		}
		
		if (!$this->getUser()->isAuthenticated()) {
			return sfView::NONE;
		}
		
		// Check user authorization
		if (!QubitAcl::check($this->resource, 'bookOut')) {
			//QubitAcl::forwardUnauthorized();
		}
		
		foreach (QubitRelation::getRelationsBySubjectId($this->resource->id) as $item2) {
			$this->informationObjects = QubitInformationObject::getById($item2->objectId);
		}
		
		$this->informationObj = new QubitInformationObject;
		$this->informationObj = QubitInformationObject::getById($this->informationObjects->id);
		
		$title = $this->context->i18n->__('Add new Re-booked Item');
		if (isset($this->getRoute()->resource)) {
			if (1 > strlen($title = $this->resource->__toString())) {
				$title = $this->context->i18n->__('Untitled');
			}
			
			$title = $this->context->i18n->__('Re-book %1%', array(
				'%1%' => $title
			));
		}
		
		$this->response->setTitle("$title - {$this->response->getTitle()}");
	}
	
	protected function addField($name) {
		switch ($name) {
			case 'remarks':
				$this->form->setDefault('remarks', "");
				$this->form->setValidator('remarks', new sfValidatorString);
				$this->form->setWidget('remarks', new sfWidgetFormTextArea(array(), array(
					'rows' => 2
				)));
				
				break;
			
			case 'location':
				foreach ($this->informationObj->getPhysicalObjects() as $item) {
					$this->form->setDefault('location', $item->location); // bring a value of the shelf field in Bookout
				}
				$this->form->setValidator('location', new sfValidatorString);
				$this->form->setWidget('location', new sfWidgetFormInput);
				
				break;
			
			case 'record_condition':
				foreach ($this->informationObj->getAccessObjects() as $item) {
					$this->form->setDefault('record_condition', $item->restriction_condition); // bring a value of the shelf field in Bookout
				}
				$this->form->setValidator('record_condition', new sfValidatorString);
				$this->form->setWidget('record_condition', new sfWidgetFormInput);
				
				break;
			
			case 'availability':
				
				foreach ($this->informationObj->getPresevationObjects() as $item) {
					$this->form->setDefault('availability', $item->availability); // bring a value of the shelf field in Bookout
				}
				$this->form->setValidator('availability', new sfValidatorString);
				$this->form->setWidget('availability', new sfWidgetFormInput);
				
				break;
			
			case 'unique_identifier':
				foreach ($this->informationObj->getPhysicalObjects() as $item) {
					$this->form->setDefault('unique_identifier', $item->uniqueIdentifier); // bring a value of the  field in Bookout
				}
				$this->form->setValidator('unique_identifier', new sfValidatorString);
				$this->form->setWidget('unique_identifier', new sfWidgetFormInput);
				
				break;
			
			case 'strong_room':
				
				foreach ($this->informationObj->getPhysicalObjects() as $item) {
					$this->form->setDefault('strong_room', $item->__toString()); // bring a value of the  field in Bookout
				}
				$this->form->setValidator('strong_room', new sfValidatorString);
				$this->form->setWidget('strong_room', new sfWidgetFormInput);
				
				break;
			
			case 'shelf':
				$this->form->setDefault('shelf', $this->informationObj->shelf); // bring a value of the shelf field in Bookout
				$this->form->setValidator('shelf', new sfValidatorString);
				$this->form->setWidget('shelf', new sfWidgetFormInput);
				
				break;
			
			case 'row':
				$this->form->setDefault('row', $this->informationObj->row); // bring a value of the shelf field in Bookout
				$this->form->setValidator('row', new sfValidatorString);
				$this->form->setWidget('row', new sfWidgetFormInput);
				
				break;
			
			case 'bin':
				$this->form->setDefault('bin', $this->informationObj->bin); // bring a value of the shelf field in Bookout
				$this->form->setValidator('bin', new sfValidatorString);
				$this->form->setWidget('bin', new sfWidgetFormInput);
				
				break;
			
			case 'requestor_type':
				$this->form->setDefault('requestor_type', $this->resource['requestor_type']); // bring a value of the requestor type field in Bookout
				$this->form->setValidator('requestor_type', new sfValidatorString);
				$this->form->setWidget('requestor_type', new sfWidgetFormInput);
				
				break;
			
			case 'name':
				$this->form->setDefault($name, $this->resource[$name]);
				$this->form->setValidator($name, new sfValidatorString);
				$this->form->setWidget($name, new sfWidgetFormInput);
				
				break;
			
			case 'requestor':
				$values = array();
				foreach (QubitUser::getAll() as $user) {
					$values[$user->username] = $user->__toString();
				}
				
				$this->form->setValidator('requestor', new sfValidatorString);
				$this->form->setWidget('requestor', new sfWidgetFormSelect(array(
					'choices' => $values
				)));
				
				break;
			
			case 'dispatcher':
				$this->form->setDefault('dispatcher', $this->context->user->getAttribute('user_name')); // bring a value of a 'dispatcher' field in Bookout
				$this->form->setValidator('dispatcher', new sfValidatorString);
				$this->form->setWidget('dispatcher', new sfWidgetFormInput);
				
				
				break;
			
			case 'time_period':
				$this->form->setValidator('time_period', new sfValidatorString);
				$this->form->setWidget('time_period', new sfWidgetFormInput);
				
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
			
		    case 'cbReceipt':
				$this->form->setDefault($name, true);
				$this->form->setValidator($name, new sfValidatorBoolean);
				$this->form->setWidget($name, new sfWidgetFormInputCheckbox);

		        break;

			default:
				
				return parent::addField($name);
		}
	}
	
	protected function processField($field) {
		switch ($field->getName()) {
			case 'requestor':
			
			case 'dispatcher':
			
			default:
				
				return parent::processField($field);
		}
	}
	
	protected function processForm() {
		
		if (null !== $this->form->getValue('name')) {
			$BookInObject = new QubitBookInObject;
			
			$BookInObject->name = $this->form->getValue('name');
			$BookInObject->time_period = $this->resource['time_period'];
			$BookInObject->remarks = "Re-book:".$this->resource['remarks'];
			if ($this->form->getValue('unique_identifier') == null || $this->form->getValue('unique_identifier') == "") {
				$unique_identifier = "";
			} else {
				$unique_identifier = $this->form->getValue('unique_identifier');
			}
			$BookInObject->unique_identifier = $unique_identifier; //Unique identifier
			if ($this->form->getValue('strong_room') == null || $this->form->getValue('strong_room') == "") {
				$strong_room = "";
			} else {
				$strong_room = $this->form->getValue('strong_room');
			}
			$BookInObject->strong_room = $strong_room; //new field
			if ($this->form->getValue('shelf') == null || $this->form->getValue('shelf') == "") {
				$shelf = "";
			} else {
				$shelf = $this->form->getValue('shelf');
			}
			$BookInObject->shelf = $shelf; //new field
			if ($this->form->getValue('row') == null || $this->form->getValue('row') == "") {
				$row = "";
			} else {
				$row = $this->form->getValue('row');
			}
			$BookInObject->row = $row; //new field
			if ($this->form->getValue('location') == null || $this->form->getValue('location') == "") {
				$location = "";
			} else {
				$location = $this->form->getValue('location');
			}
			$BookInObject->location = $location; //$location
			if ($this->form->getValue('availability') == null || $this->form->getValue('availability') == "") {
				$availability = "";
			} else {
				$availability = $this->form->getValue('availability');
			}
			$BookInObject->availability = $availability; //new field
			if ($this->form->getValue('record_condition') == null || $this->form->getValue('record_condition') == "") {
				$record_condition = "";
			} else {
				$record_condition = $this->form->getValue('record_condition');
			} 
			$BookInObject->record_condition = $record_condition; //new field
			
			$BookInObject->requestor_type = $this->resource['requestor_type'];
			$BookInObject->object_id = $this->informationObj->id; //Parent ID
			
			$BookInObject->requestorId  = $this->form->getValue('requestor');
			$BookInObject->dispatcherId = $this->form->getValue('dispatcher');
			
			$BookInObject->save();
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

				$bookOut = QubitBookoutObject::getById($this->resource->id);
				$this->resource->time_period  = $this->form->getValue('time_period');
				$this->resource->remarks      = "Re-book Out: " . $this->form->getValue('remarks');
				$this->resource->save();
				if ($this->form->getValue('cbReceipt') != 1) {
					$this->redirect(array($this->informationObj, 'module' => 'informationobject'));
				} else {
					$this->redirect(array($this->informationObj, 'module' => 'bookoutobject', 'action' => 'receipt', 'source' => $this->bookoutObject ));
				}
			}
		}
	}
}
