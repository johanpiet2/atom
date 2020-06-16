<?php

/**
 * Access Object edit component.
 *
 * @package    qubit
 * @subpackage Access Module
 * @author     Johan Pieterse SITA <johan.pieterse@sita.co.za>
 * @version    SVN: $Id
 */

class InformationObjectEditAccessObjectsAction extends DefaultEditAction
{
    public static $NAMES = array('name', 'refusal', 'sensitivity', 'publish', 'object_id', 'classification', 'restriction');
    
    protected function earlyExecute()
    {
        $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);
        
        $this->resource = $this->getRoute()->resource;
        
        // Check that this isn't the root
        if (!isset($this->resource->parent)) {
            $this->forward404();
        }
        
        // Check user authorization
        if (!QubitAcl::check($this->resource, 'createAccess')) {
            QubitAcl::forwardUnauthorized();
        }
    }
    
    protected function addField($name)
    {
        switch ($name) {
            case 'name':
                $this->form->setDefault($name, $this->resource); // bring a value of a name field in preservation
                $this->form->setValidator($name, new sfValidatorString);
                $this->form->setWidget($name, new sfWidgetFormInput);
                
                break;
            
            case 'object_id':
                $this->form->setValidator('object_id', new sfValidatorString);
                $this->form->setWidget('object_id', new sfWidgetFormInput);
                break;
            
            case 'refusal':
                $this->form->setValidator('refusal', new sfValidatorString);
                $this->form->setWidget('refusal', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::REFUSALS_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            case 'sensitivity':
                $this->form->setValidator('sensitivity', new sfValidatorString);
                $this->form->setWidget('sensitivity', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::SENSITIVITY_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            case 'publish':
                $this->form->setValidator('publish', new sfValidatorString);
                $this->form->setWidget('publish', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::PUBLISH_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            case 'classification':
                $this->form->setValidator('classification', new sfValidatorString);
                $this->form->setWidget('classification', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::CLASSIFICATION_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            case 'restriction':
                $this->form->setValidator('restriction', new sfValidatorString);
                $this->form->setWidget('restriction', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::RESTRICTION_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            default:
                
                return parent::addField($name);
        }
    }
    
    protected function processForm()
    {
        foreach ($this->form->getValue('containers') as $item) {
            $params = $this->context->routing->parse(Qubit::pathInfo($item));
            $this->resource->addAccessObject($params['_sf_route']->resource);
        }
        
        if (null !== $this->form->getValue('name')) {
            $accessObject       = new QubitAccessObject;
            $accessObject->name = $this->form->getValue('name');
            
            $params                = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('refusal')));
            $accessObject->refusal = $params['_sf_route']->resource;
            
            $params                    = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('sensitivity')));
            $accessObject->sensitivity = $params['_sf_route']->resource;
            
            $params                = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('publish')));
            $accessObject->publish = $params['_sf_route']->resource;
            
            $params                       = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('classification')));
            $accessObject->classification = $params['_sf_route']->resource;
            
            $params                    = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('restriction')));
            $accessObject->restriction = $params['_sf_route']->resource;
            
            $accessObject->object_id = $this->resource->id;
            
            if ($accessObject->classification == 'Secret' || $accessObject->classification == 'Top Secret' || $accessObject->classification == 'Confidential') {
                unset($accessObject->publish);
                //      $params                  = $this->context->routing->parse(Qubit::pathInfo("No"));
                $params                = $this->context->routing->parse('no-3'); //BUG to fix SITA
                $accessObject->publish = $params['_sf_route']->resource;
            }
            if ($accessObject->sensitivity == 'Yes') {
                unset($accessObject->publish);
                //      $params                  = $this->context->routing->parse(Qubit::pathInfo("No"));
                $params                = $this->context->routing->parse('no-3'); //BUG to fix SITA
                $accessObject->publish = $params['_sf_route']->resource;
            }
            
            if ($accessObject->refusal != 'None') {
                if ($accessObject->refusal != 'Please Select') {
                    unset($accessObject->publish);
                    //      $params                  = $this->context->routing->parse(Qubit::pathInfo("No"));
                    $params                = $this->context->routing->parse('no-3'); //BUG to fix SITA
                    $accessObject->publish = $params['_sf_route']->resource;
                }
            }
            
            $accessObject->save();
            
            // creates relation
            $this->resource->addAccessObject($accessObject);
        }
        
        if (isset($this->request->delete_relations)) {
            foreach ($this->request->delete_relations as $item) {
                $params = $this->context->routing->parse(Qubit::pathInfo($item));
                $params['_sf_route']->resource->delete();
            }
        }
    }
    
    public function execute($request)
    {
        parent::execute($request);
        
        $this->relations = QubitRelation::getRelationsByObjectId($this->resource->id, array(
            'typeId' => QubitTaxonomy::ACCESS_TYPE_ID
        ));
        
        if ($request->isMethod('post')) {
            $this->form->bind($request->getPostParameters());
            if ($this->form->isValid()) {
                $this->processForm();
                
                $this->resource->save();
                
                $this->redirect(array(
                    $this->resource,
                    'module' => 'informationobject'
                ));
            }
        }
    }
    
}
