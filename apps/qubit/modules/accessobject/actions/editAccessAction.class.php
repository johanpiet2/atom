<?php

/**
 * Access Object edit component.
 *
 * @package    qubit
 * @subpackage Access Module
 * @author     Johan Pieterse SITA <johan.pieterse@sita.co.za>
 * @version    SVN: $Id
 */

class AccessObjectEditAccessAction extends DefaultEditAction
{
    public static $NAMES = array('name', 'refusal', 'sensitivity', 'publish', 'classification', 'object_id', 'restriction');
    
    protected function earlyExecute()
    {
        $this->resource = new QubitAccessObject;
        if (isset($this->getRoute()->resource)) {
            $this->resource = $this->getRoute()->resource;
        }
        
        foreach (QubitRelation::getRelationsBySubjectId($this->resource->id) as $item2) {
            $this->informationObjects = QubitInformationObject::getById($item2->objectId);
        }
        
        $this->informationObj = new QubitInformationObject;
        $this->informationObj = QubitInformationObject::getById($this->informationObjects->id);
        
        if (!$this->getUser()->isAuthenticated()) {
            return sfView::NONE;
        }
        
        // Check user authorization
        if (!QubitAcl::check($this->resource, 'createAccess')) {
            QubitAcl::forwardUnauthorized();
        }
        
        $title = $this->context->i18n->__('Add new access');
        if (isset($this->getRoute()->resource)) {
            if (1 > strlen($title = $this->resource->__toString())) {
                $title = $this->context->i18n->__('Untitled');
            }
            $title = $this->context->i18n->__('Edit %1%', array(
                '%1%' => $title
            ));
        }
        $this->response->setTitle("$title - {$this->response->getTitle()}");
    }
    
    protected function addField($name)
    {
        switch ($name) {
            case 'name':
                $this->form->setDefault($name, $this->resource[$name]);
                $this->form->setValidator($name, new sfValidatorString);
                $this->form->setWidget($name, new sfWidgetFormInput);
                break;
            
            case 'object_id':
                $this->form->setDefault($name, $this->resource[$name]);
                $this->form->setValidator($name, new sfValidatorString);
                $this->form->setWidget($name, new sfWidgetFormInput);
                break;
            
            case 'refusal':
                $this->form->setDefault('refusal', $this->context->routing->generate(null, array(
                    $this->resource->refusal,
                    'module' => 'term'
                )));
                $this->form->setValidator('refusal', new sfValidatorString);
                $this->form->setWidget('refusal', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::REFUSALS_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            case 'sensitivity':
                $this->form->setDefault('sensitivity', $this->context->routing->generate(null, array(
                    $this->resource->sensitivity,
                    'module' => 'term'
                )));
                $this->form->setValidator('sensitivity', new sfValidatorString);
                $this->form->setWidget('sensitivity', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::SENSITIVITY_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            case 'publish':
                $this->form->setDefault('publish', $this->context->routing->generate(null, array(
                    $this->resource->publish,
                    'module' => 'term'
                )));
                $this->form->setValidator('publish', new sfValidatorString);
                $this->form->setWidget('publish', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::PUBLISH_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            case 'classification':
                $this->form->setDefault('classification', $this->context->routing->generate(null, array(
                    $this->resource->classification,
                    'module' => 'term'
                )));
                $this->form->setValidator('classification', new sfValidatorString);
                $this->form->setWidget('classification', new sfWidgetFormSelect(array(
                    'choices' => QubitTerm::getIndentedChildTree(QubitTerm::CLASSIFICATION_ID, '&nbsp;', array(
                        'returnObjectInstances' => true
                    ))
                )));
                
                break;
            
            case 'restriction':
                $this->form->setDefault('restriction', $this->context->routing->generate(null, array(
                    $this->resource->restriction,
                    'module' => 'term'
                )));
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
    protected function processField($field)
    {
        switch ($field->getName()) {
            case 'object_id':
                unset($this->resource->object_id);
                $this->resource->object_id = $this->informationObj->id;
                
                break;
            
            case 'refusal':
                unset($this->resource->refusal);
                $params                  = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('refusal')));
                $this->resource->refusal = $params['_sf_route']->resource;
                
                break;
            
            case 'sensitivity':
                unset($this->resource->sensitivity);
                $params                      = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('sensitivity')));
                $this->resource->sensitivity = $params['_sf_route']->resource;
                
                break;
            case 'publish':
                unset($this->resource->publish);
                $params                  = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('publish')));
                $this->resource->publish = $params['_sf_route']->resource;
                
                break;
            
            case 'classification':
                unset($this->resource->classification);
                $params                         = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('classification')));
                $this->resource->classification = $params['_sf_route']->resource;
                break;
            
            case 'restriction':
                unset($this->resource->restriction);
                $params                      = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('restriction')));
                $this->resource->restriction = $params['_sf_route']->resource;
                break;
            
            default:
                
                return parent::processField($field);
        }
    }
    
    public function execute($request)
    {
        parent::execute($request);
        
        if ($request->isMethod('post')) {
            $this->form->bind($request->getPostParameters());
            if ($this->form->isValid()) {
                $this->processForm();
                
                $classification = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('classification')));
                
                if ($classification['_sf_route']->resource != 'Public') {
                    unset($this->resource->publish);
                    $params                  = $this->context->routing->parse(Qubit::pathInfo("No"));
                    $this->resource->publish = $params['_sf_route']->resource;
                } else {
                    unset($this->resource->publish);
                    $publish                 = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('publish')));
                    $this->resource->publish = $publish['_sf_route']->resource;
                }
                
                $sensitivity = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('sensitivity')));
                
                if ($sensitivity['_sf_route']->resource == 'Yes') {
		            unset($this->resource->publish);
//                        $params                  = $this->context->routing->parse(Qubit::pathInfo("No"));
		            $params                  = $this->context->routing->parse(Qubit::pathInfo('no-3'));  //BUG to fix SITA
		            $this->resource->publish = $params['_sf_route']->resource; 
                } else {
                    $classification = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('classification')));
                    if ($classification['_sf_route']->resource != 'Public') {
                        unset($this->resource->publish);
//                        $params                  = $this->context->routing->parse(Qubit::pathInfo("No"));
                        $params                  = $this->context->routing->parse('no-3');  //BUG to fix SITA
                        $this->resource->publish = $params['_sf_route']->resource;
                    } else {
                        unset($this->resource->publish);
                        $params                  = $this->context->routing->parse(Qubit::pathInfo($this->form->getValue('publish')));
                        $this->resource->publish = $params['_sf_route']->resource;
                    }
                }
                
                if ($this->resource->refusal != 'None') {
                    if ($this->resource->refusal != 'Please Select') {
                        unset($this->resource->publish);
//                        $params                  = $this->context->routing->parse(Qubit::pathInfo("No"));
                        $params                  = $this->context->routing->parse('no-3');  //BUG to fix SITA
                        $this->resource->publish = $params['_sf_route']->resource;
                    }
                }
                
                $this->resource->name      = $this->informationObj->title;
                $this->resource->object_id = $this->informationObj->id;
                $this->resource->save();
                
                if (null !== $next = $this->form->getValue('next')) {
                    $this->redirect($next);
                }
                
                $this->redirect(array(
                    $this->informationObj,
                    'module' => 'informationobject'
                ));
            }
        }
    }
}
