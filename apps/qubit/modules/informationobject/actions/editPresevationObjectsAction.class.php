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
 
class InformationObjectEditPresevationObjectsAction extends DefaultEditAction
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
    $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);

    $this->resource = $this->getRoute()->resource;

    // Check that this isn't the root
    if (!isset($this->resource->parent))
    {
      $this->forward404();
    }

    // Check user authorization
    if (!QubitAcl::check($this->resource, 'update'))
    {
      QubitAcl::forwardUnauthorized();
    }

    // Check user authorization
    if (!QubitAcl::check($this->resource, 'update'))
    {
      QubitAcl::forwardUnauthorized();
    }
  }

  protected function addField($name)
  {
    switch ($name)
    {			    
      case 'name':
	    $this->form->setDefault($name, $this->resource);  // bring a value of a name field in preservation
        $this->form->setValidator($name, new sfValidatorString);
        $this->form->setWidget($name, new sfWidgetFormInput);

        break;		

      case 'condition':
        $this->form->setValidator('condition', new sfValidatorString);
        $this->form->setWidget('condition', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CONDITIONS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
	
      case 'usability':
        $this->form->setValidator('usability', new sfValidatorString);
        $this->form->setWidget('usability', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::USABILITYS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
	
      case 'measure':
        $this->form->setValidator('measure', new sfValidatorString);
        $this->form->setWidget('measure', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::MEASURES_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;

      case 'availability':
        $this->form->setValidator('availability', new sfValidatorString);
        $this->form->setWidget('availability', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::AVAILABILITYS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
      case 'refusal':
        $this->form->setValidator('refusal', new sfValidatorString);
        $this->form->setWidget('refusal', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::REFUSALS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
      case 'restoration':
        $this->form->setValidator('restoration', new sfValidatorString);
        $this->form->setWidget('restoration', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::RESTORATIONS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
      case 'conservation':
        $this->form->setValidator('conservation', new sfValidatorString);
        $this->form->setWidget('conservation', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CONSERVATIONS_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
      case 'type':
        $this->form->setValidator('type', new sfValidatorString);
        $this->form->setWidget('type', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CONTAINER_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
	   case 'sensitivity':
        $this->form->setValidator('sensitivity', new sfValidatorString);
        $this->form->setWidget('sensitivity', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::SENSITIVITY_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
	   case 'publish':
        $this->form->setValidator('publish', new sfValidatorString);
        $this->form->setWidget('publish', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::PUBLISH_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
	   case 'classification':
        $this->form->setValidator('classification', new sfValidatorString);
        $this->form->setWidget('classification', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::CLASSIFICATION_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
		case 'restriction':
        $this->form->setValidator('restriction', new sfValidatorString);
        $this->form->setWidget('restriction', new sfWidgetFormSelect(array('choices' => QubitTerm::getIndentedChildTree(QubitTerm::RESTRICTION_ID, '&nbsp;', array('returnObjectInstances' => true)))));

        break;
		
      default:

        return parent::addField($name);
    }
  }

  protected function processForm()
  {
 foreach ($this->form->getValue('containers') as $item)
    {
      $params = $this->context->routing->parse(Qubit::pathInfo($item));
      $this->resource->addPresevationObject($params['_sf_route']->resource);
    }
  
    if (null !== $this->form->getValue('name'))
    {                                                      
     $presevationObject = new QubitPresevationObject;
     $presevationObject->name = $this->form->getValue('name');
      
     $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('usability')));
     $presevationObject->usability = $params['_sf_route']->resource;

     $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('measure')));
     $presevationObject->measure = $params['_sf_route']->resource;

     $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('availability')));
     $presevationObject->availability = $params['_sf_route']->resource;
	 
     $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('restoration')));
     $presevationObject->restoration = $params['_sf_route']->resource;
	  
     $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('conservation')));
     $presevationObject->conservation = $params['_sf_route']->resource;

     $params = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('type')));
     $presevationObject->type = $params['_sf_route']->resource;	 

     $presevationObject->object_id = $this->resource->id;	 
	 
	 if($presevationObject->classification == 'Secret' || $presevationObject->classification == 'Top Secret' || $presevationObject->classification == 'Confidential') 
	 {
		unset($presevationObject->publish);
		$params = $this->context->routing->parse(Qubit::pathInfo("No"));
		$presevationObject->publish = $params['_sf_route']->resource;
	 }
	 if($presevationObject->sensitivity == 'Yes') 
	 {
		unset($presevationObject->publish);
		$params = $this->context->routing->parse(Qubit::pathInfo("No"));
		$presevationObject->publish = $params['_sf_route']->resource;
	 }

      $presevationObject->save();

      $this->resource->addPresevationObject($presevationObject);	  
    }		

    if (isset($this->request->delete_relations))
    {
      foreach ($this->request->delete_relations as $item)
      {
        $params = $this->context->routing->parse(Qubit::pathInfo($item));
        $params['_sf_route']->resource->delete();
      }
    }
  }

  public function execute($request)
  {
    parent::execute($request);
	
	$this->relations = QubitRelation::getRelationsByObjectId($this->resource->id, array('typeId' => QubitTaxonomy::PRESERVATION_TYPE_ID));

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getPostParameters());
      if ($this->form->isValid())
      {
        $this->processForm();
        
        $this->resource->save();

        $this->redirect(array($this->resource, 'module' => 'informationobject'));
      }
    }
  }
}
