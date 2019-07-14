<?php

/**
 * Presevation Object edit component.
 *
 * @package    qubit
 * @subpackage Preservation Module
 * @author     Ramaano Ndou <ramaano.ndou@sita.co.za>
 * @author     Johan Pieterse SITA <johan.pieterse@sita.co.za>
 * @version    SVN: $Id
 */
 
class PresevationObjectEditPreservationAction extends DefaultEditAction
{
  public static
    $NAMES = array(
      'name',
      'condition',
	  'usability',
	  'measure',
	  'medium',
	  'availability',
	  'refusal',
	  'restoration',
	  'conservation',
	  'type',
	  'sensitivity',
	  'publish',
	  'object_id',
	  'classification',
	  'restriction');
	  
  protected function earlyExecute()
  {
    $this->resource = new QubitPresevationObject;
    if (isset($this->getRoute()->resource))
    {
      $this->resource = $this->getRoute()->resource;
    }
	
	foreach (QubitRelation::getRelationsBySubjectId($this->resource->id) as $item2)
	{ 
		$this->informationObjects = QubitInformationObject::getById($item2->objectId);
		$this->tt=$this->informationObjects->id;

	}

	$this->informationObj = new QubitInformationObject;
	$this->informationObj = QubitInformationObject::getById($this->informationObjects->id);

    if (!$this->getUser()->isAuthenticated())
    {
      return sfView::NONE;
    }

	// Check user authorization
	if (!QubitAcl::check($this->resource, 'editPreservation'))
	{
	  QubitAcl::forwardUnauthorized();
	}

    $title = $this->context->i18n->__('Add new preservation');
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
      case 'name':
        $this->form->setDefault($name, $this->resource[$name]);
        $this->form->setValidator($name, new sfValidatorString);
        $this->form->setWidget($name, new sfWidgetFormInput);

        break;

      case 'condition':
        $this->form->setDefault('condition', $this->context->routing->generate(null, array($this->resource->condition, 'module' => 'term')));
        $this->form->setValidator('condition', new sfValidatorString);
        $this->form->setWidget('condition', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CONDITIONS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;		
		
      case 'usability':
        $this->form->setDefault('usability', $this->context->routing->generate(null, array($this->resource->usability, 'module' => 'term')));
        $this->form->setValidator('usability', new sfValidatorString);
        $this->form->setWidget('usability', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::USABILITYS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;		
		
      case 'measure':
        $this->form->setDefault('measure', $this->context->routing->generate(null, array($this->resource->measure, 'module' => 'term')));
        $this->form->setValidator('measure', new sfValidatorString);
        $this->form->setWidget('measure', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::MEASURES_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;

    case 'availability':
     $this->form->setDefault('availability', $this->context->routing->generate(null, array($this->resource->availability, 'module' => 'term')));
     $this->form->setValidator('availability', new sfValidatorString);
     $this->form->setWidget('availability', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::AVAILABILITYS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;	
		
    case 'refusal':
     $this->form->setDefault('refusal', $this->context->routing->generate(null, array($this->resource->refusal, 'module' => 'term')));
     $this->form->setValidator('refusal', new sfValidatorString);
     $this->form->setWidget('refusal', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::REFUSALS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;			
		
    case 'restoration':
     $this->form->setDefault('restoration', $this->context->routing->generate(null, array($this->resource->restoration, 'module' => 'term')));
     $this->form->setValidator('restoration', new sfValidatorString);
     $this->form->setWidget('restoration', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::RESTORATIONS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

       break;		
		
    case 'conservation':
	 $this->form->setDefault('conservation', $this->context->routing->generate(null, array($this->resource->conservation, 'module' => 'term')));
	 $this->form->setValidator('conservation', new sfValidatorString);
	 $this->form->setWidget('conservation', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CONSERVATIONS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

      break;
		
    case 'type':
      $this->form->setDefault('type', $this->context->routing->generate(null, array($this->resource->type, 'module' => 'term')));
      $this->form->setValidator('type', new sfValidatorString);
      $this->form->setWidget('type', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CONTAINER_ID, '&nbsp;', array('returnObjectInstances' => true)))));

     break;
	 
	 case 'sensitivity':
      $this->form->setDefault('sensitivity', $this->context->routing->generate(null, array($this->resource->sensitivity, 'module' => 'term')));
      $this->form->setValidator('sensitivity', new sfValidatorString);
      $this->form->setWidget('sensitivity', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::SENSITIVITY_ID, '&nbsp;', array('returnObjectInstances' => true)))));

     break;
	 
	 case 'publish':
      $this->form->setDefault('publish', $this->context->routing->generate(null, array($this->resource->publish, 'module' => 'term')));
      $this->form->setValidator('publish', new sfValidatorString);
      $this->form->setWidget('publish', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::PUBLISH_ID, '&nbsp;', array('returnObjectInstances' => true)))));

     break;
	 
	 case 'classification':
      $this->form->setDefault('classification', $this->context->routing->generate(null, array($this->resource->classification, 'module' => 'term')));
      $this->form->setValidator('classification', new sfValidatorString);
      $this->form->setWidget('classification', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CLASSIFICATION_ID, '&nbsp;', array('returnObjectInstances' => true)))));

     break;
	 
	 case 'restriction':
      $this->form->setDefault('restriction', $this->context->routing->generate(null, array($this->resource->restriction, 'module' => 'term')));
      $this->form->setValidator('restriction', new sfValidatorString);
      $this->form->setWidget('restriction', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::RESTRICTION_ID, '&nbsp;', array('returnObjectInstances' => true)))));

     break;

   default:

        return parent::addField($name);
    }
  }
  protected function processField($field)
  {
    switch ($field->getName())
    {
     case 'condition':
     unset($this->resource->condition);
     $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('condition')));
     $this->resource->condition = $params['_sf_route']->resource;

        break;	
		
	case 'usability':
    unset($this->resource->usability);
    $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('usability')));
    $this->resource->usability = $params['_sf_route']->resource;

        break;		
	
	case 'measure':
        unset($this->resource->measure);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('measure')));
        $this->resource->measure = $params['_sf_route']->resource;

        break;		

	case 'availability':
        unset($this->resource->availability);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('availability')));
        $this->resource->availability = $params['_sf_route']->resource;

        break;
		
	case 'refusal':
        unset($this->resource->refusal);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('refusal')));
        $this->resource->refusal = $params['_sf_route']->resource;

        break;
				
	case 'restoration':
        unset($this->resource->restoration);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('restoration')));
        $this->resource->restoration = $params['_sf_route']->resource;

        break;
		
	case 'conservation':
        unset($this->resource->conservation);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('conservation')));
        $this->resource->conservation = $params['_sf_route']->resource;

        break;
		
	case 'type':
        unset($this->resource->type);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('type')));
        $this->resource->type = $params['_sf_route']->resource;

        break;
		
	case 'sensitivity':
        unset($this->resource->sensitivity);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('sensitivity')));
        $this->resource->sensitivity = $params['_sf_route']->resource;

        break;	
	case 'publish':
        unset($this->resource->publish);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('publish')));
        $this->resource->publish = $params['_sf_route']->resource;

        break;	
		
	case 'classification':
        unset($this->resource->classification);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('classification')));
        $this->resource->classification = $params['_sf_route']->resource;

        break;	
		
	case 'restriction':
        unset($this->resource->restriction);
        $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('restriction')));
        $this->resource->restriction = $params['_sf_route']->resource;

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

		$this->resource->name = $this->informationObj->title;
	    $this->resource->object_id = $this->informationObj->id;

        $this->resource->save();

        if (null !== $next = $this->form->getValue('next'))
        {
          $this->redirect($next);
        }

        $this->redirect(array($this->informationObj, 'module' => 'informationobject'));
      }
    }
  }
}
