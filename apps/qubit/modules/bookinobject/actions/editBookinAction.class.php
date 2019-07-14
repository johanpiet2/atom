<?php

/**
 * Bookin Object edit component.
 *
 * @package    qubit
 * @subpackage Bookin Module
 * @author     Tsholo Ramesega
 * @version    SVN: $Id
 */
 
 
class BookinObjectEditBookinAction extends DefaultEditAction
{
  public static
    $NAMES = array(
      'time_period',
      'name',
	  'remarks',	  	   	  
      'requestor',
	  'receiver',
	  'location');
	  
  protected function earlyExecute()
  {
    $this->resource = new QubitBookinObject;
    if (isset($this->getRoute()->resource))
    {
      $this->resource = $this->getRoute()->resource;
    }

    if (!$this->getUser()->isAuthenticated())
    {
      return sfView::NONE;
    }
 
	// Check user authorization
	if (!QubitAcl::check($this->resource, 'bookIn'))
	{
	  QubitAcl::forwardUnauthorized();
	}

 	foreach (QubitRelation::getRelationsBySubjectId($this->resource->id) as $item2)
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
      $title = $this->context->i18n->__('Edit %1%', array('%1%' => $title));
    }
    $this->response->setTitle("$title - {$this->response->getTitle()}");
  }
  protected function addField($name)
  {
    switch ($name)
    {  
	  case 'remarks':
      case 'time_period':
	  case 'location':
	
      case 'name':
        $this->form->setDefault($name, $this->resource[$name]);
        $this->form->setValidator($name, new sfValidatorString);
        $this->form->setWidget($name, new sfWidgetFormInput);

        break;

	  case 'requestor':
		$values = array();
		foreach (QubitUser::getAll() as $user)
		{
			$values[$user->id] = $user->__toString();
		}  
        $this->form->setValidator('requestor', new sfValidatorString);
        $this->form->setWidget('requestor', new sfWidgetFormSelect(array('choices' => $values)));

        break;
		
	  case 'receiver':
		$values = array();
		foreach (QubitUser::getAll() as $user)
		{
			$values[$user->id] = $user->__toString();
		}  
        $this->form->setValidator('receiver', new sfValidatorString);
        $this->form->setWidget('receiver', new sfWidgetFormSelect(array('choices' => $values)));

        break;
		
      default:

        return parent::addField($name);
    }
  }
  
  protected function processField($field)
  {
    switch ($field->getName())
    {
      case 'requestor':
        unset($this->resource->requestor);

        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('requestor')));
        $this->resource->requestor = $params['_sf_route']->resource;

        break;
		
		
      case 'receiver':
        unset($this->resource->receiver);

        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('receiver')));
        $this->resource->receiver = $params['_sf_route']->resource;

        break;

      default:

        return parent::processField($field);
    }
  }

  public function execute($request)
  {
    parent::execute($request);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getPostParameters());
      if ($this->form->isValid())
      {
        $this->processForm();

        $this->resource->save();

        $this->redirect(array($this->informationObj, 'module' => 'informationobject'));
      }
    }
  }
}
