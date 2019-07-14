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
 * Display a list of selectable reports
 *
 * @package AccesstoMemory
 * @subpackage search
 * @author Johan Pieterse <johan.pieterse@sita.co.za>
 */

class reportsreportSelectAction extends DefaultEditAction
{
    // Arrays not allowed in class constants
    public static $NAMES = array('collection');
    
    protected function earlyExecute()
    {
        $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);
        
        if (isset($this->getRoute()->resource)) {
            $this->resource = $this->getRoute()->resource;
            
            $this->form->setDefault('parent', $this->context->routing->generate(null, array(
                $this->resource
            )));
            $this->form->setValidator('parent', new sfValidatorString);
            $this->form->setWidget('parent', new sfWidgetFormInputHidden);
        }
    }
    
    protected function addField($name)
    {
        switch ($name) {
            case 'collection':
                $this->form->setValidator($name, new sfValidatorString);
                $choices = array();
                
                if (isset($this->getParameters['collection']) && ctype_digit($this->getParameters['collection']) && null !== $collection = QubitInformationObject::getById($this->getParameters['collection'])) {
                    sfContext::getInstance()->getConfiguration()->loadHelpers(array(
                        'Url'
                    ));
                    $collectionUrl = url_for($collection);
                    $this->form->setDefault($name, $collectionUrl);
                    
                    $choices[$collectionUrl] = $collection;
                }
                $this->form->setWidget($name, new sfWidgetFormSelect(array(
                    'choices' => $choices
                )));
                
                break;
            
            default:
                return parent::addField($name);
        }
    }
    
    protected function processField($field)
    {
        switch ($field->getName()) {
            case 'collection':
                $url = $this->request->getPostParameter('collection');
                if (!empty($url)) {
                    $parts                = explode('/', $url);
                    $this->collectionSlug = end($parts);
                }
                
                break;
        }
    }
    
    public function execute($request)
    {
        parent::execute($request);
        
        if ($request->isMethod('post')) {
            $this->form->bind($request->getPostParameters());
            
            if ($this->form->isValid()) {
                $this->processForm();
                if ($request->getParameter('objectType') == 'audit_trail') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportAuditTrail'
                    );
                } else if ($request->getParameter('objectType') == 'access') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportAccess'
                    );
                } else if ($request->getParameter('objectType') == 'accession') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportUpdates'
                    );
                } else if ($request->getParameter('objectType') == 'booked_in') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportBookIn'
                    );
                } else if ($request->getParameter('objectType') == 'booked_out') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportBookOut',
                        'reportType' => $request->getParameter('objectType')
                    );
                } else if ($request->getParameter('objectType') == 'informationObject') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportInformationObject'
                    );
                } else if ($request->getParameter('objectType') == 'repository') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportRepository'
                    );
                } else if ($request->getParameter('objectType') == 'preservation') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportPreservation'
                    );
                } else if ($request->getParameter('objectType') == 'authorityRecord') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportAuthorityRecord'
                    );
                } else if ($request->getParameter('objectType') == 'registry') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportRegistry'
                    );
                } else if ($request->getParameter('objectType') == 'researcher') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportResearcher'
                    );
                } else if ($request->getParameter('objectType') == 'donor') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportDonor'
                    );
                } else if ($request->getParameter('objectType') == 'physical_storage') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportPhysicalStorage'
                    );
                } else if ($request->getParameter('objectType') == 'service_provider') {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportServiceProvider'
                    );
                } else {
                    $reportSelectRoute = array(
                        $this->getRoute()->resource,
                        'module' => 'reports',
                        'action' => 'reportUpdates'
                    );
                }
                $this->redirect($reportSelectRoute);
            }
        } 
    }
}
