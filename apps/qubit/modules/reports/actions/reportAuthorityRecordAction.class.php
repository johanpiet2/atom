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
 * Display a list of recently updates to the db
 *
 * @package AccesstoMemory
 * @subpackage search
 * @author David Juhasz <david@artefactual.com>
 * @author Johan Pieterse <johan.pieterse@sita.co.za>
 */
class reportsReportAuthorityRecordAction extends sfAction
{
    public static $NAMES = array('className', 'dateStart', 'dateEnd', 'dateOf', 'limit', 'authorityRecords');
	
    protected function addField($name)
    {
        switch ($name) {
        case 'dateStart':
            $this->form->setDefault('dateStart', Qubit::renderDate($this->resource['dateStart']));
            if (!isset($this->resource->id)) {
                $dt = new DateTime;
                $this->form->setDefault('dateStart', $dt->format('Y-m-d'));
            }
            $this->form->setValidator('dateStart', new sfValidatorString);
            $this->form->setWidget('dateStart', new sfWidgetFormInput);
            break;

        case 'dateEnd':
            $this->form->setDefault('dateEnd', Qubit::renderDate($this->resource['dateEnd']));
            if (!isset($this->resource->id)) {
                $dt = new DateTime;
                $this->form->setDefault('dateEnd', $dt->format('Y-m-d'));
            }
            $this->form->setValidator('dateEnd', new sfValidatorString);
            $this->form->setWidget('dateEnd', new sfWidgetFormInput);
            break;

        case 'dateOf':
            $choices = array('CREATED_AT' => $this->context->i18n->__('Creation'), 'UPDATED_AT' => $this->context->i18n->__('Revision'), 'both' => $this->context->i18n->__('Both'));
            $this->form->setValidator($name, new sfValidatorChoice(array('choices' => array_keys($choices))));
            $this->form->setWidget($name, new arWidgetFormSelectRadio(array('choices' => $choices, 'class' => 'radio inline')));
            break;
		}
    }
    
    public function execute($request)
    {
        $this->form = new sfForm;
        $this->form->getValidatorSchema()->setOption('allow_extra_fields', true);
        foreach ($this::$NAMES as $name) {
            $this->addField($name);
        }
        $defaults = array(
		    'className' => 'QubitActor', 
		    'dateStart' => date('Y-m-d', strtotime('-1 month')), 
		    'dateEnd' => date('Y-m-d'), 'dateOf' => 'CREATED_AT', 
		    'publicationStatus' => 'all', 
		    'limit' => '10', 
		    'sort' => 'updatedDown');
        
        $this->form->bind($request->getRequestParameters() + $request->getGetParameters() + $defaults);
	    if ($this->form->isValid()) {
	        $this->doSearch();
	    }
	}
	
    public function doSearch()
    {
        $criteria = new Criteria;
        $this->sort = $this->request->getParameter('sort', 'updatedDown');
        // This join seems to be necessary to avoid cross joining the local table
        // with the QubitObject table
    	$criteria->addJoin(QubitActor::ID, QubitObject::ID);
        
        $nameColumn = 'authorized_form_of_name';
        $this->nameColumnDisplay = 'Name';
        $criteria = QubitActor::addGetOnlyActorsCriteria($criteria);
        $criteria->add(QubitActor::PARENT_ID, null, Criteria::ISNOTNULL);
		
        // End date at midnight
        if (null != $this->form->getValue('dateEnd')) {
            $vDay = substr($this->form->getValue('dateEnd'), 0, strpos($this->form->getValue('dateEnd'), "/"));
            $vRes = substr($this->form->getValue('dateEnd'), strpos($this->form->getValue('dateEnd'), "/") + 1);
            $vMonth = substr($vRes, 0, strpos($vRes, "/"));
            $vYear = substr($vRes, strpos($vRes, "/") + 1,4);
            if (checkdate((int)$vDay, (int)$vMonth, (int)$vYear)) {
                $dateEnd = date_create($vYear . "-" . $vMonth . "-" . $vDay . " 23.59.59");
                $dateEnd = date_format($dateEnd, 'Y-m-d H:i:s');
            } else {
                $dateEnd = date('Y-m-d 23:59:59');
            }
        }
	    // Add date criteria
	    switch ($dateOf = $this->form->getValue('dateOf')) {
	    case 'CREATED_AT':
	    case 'UPDATED_AT':
	        if (null !== $this->form->getValue('dateStart')) {
	            $vDay = substr($this->form->getValue('dateStart'), 0, strpos($this->form->getValue('dateStart'), "/"));
	            $vRes = substr($this->form->getValue('dateStart'), strpos($this->form->getValue('dateStart'), "/") + 1);
	            $vMonth = substr($vRes, 0, strpos($vRes, "/"));
	            $vYear = substr($vRes, strpos($vRes, "/") + 1, 4);
	            $startDate2 = date_create("2001-01-01 23.59.59");
	            $startDate = date_format($startDate2, 'Y-m-d H:i:s');
	            $criteria->addAnd(constant('QubitObject::' . $dateOf), $startDate, Criteria::GREATER_EQUAL);
	        }
	        if (isset($dateEnd)) {
	            $criteria->addAnd(constant('QubitObject::' . $dateOf), $dateEnd, Criteria::LESS_EQUAL);
	        }
	        break;

	    default:
	        if (null !== $this->form->getValue('dateStart')) {
	            $vDay = substr($this->form->getValue('dateStart'), 0, strpos($this->form->getValue('dateStart'), "/"));
	            $vRes = substr($this->form->getValue('dateStart'), strpos($this->form->getValue('dateStart'), "/") + 1);
	            $vMonth = substr($vRes, 0, strpos($vRes, "/"));
	            $vYear = substr($vRes, strpos($vRes, "/") + 1);
	            $startDate = date_create($vYear . "-" . $vMonth . "-" . $vDay . " 00.00.00");
	            $startDate = date_format($startDate, 'Y-m-d H:i:s');
	            $c1 = $criteria->getNewCriterion(QubitObject::CREATED_AT, $startDate, Criteria::GREATER_EQUAL);
	            $c2 = $criteria->getNewCriterion(QubitObject::UPDATED_AT, $startDate, Criteria::GREATER_EQUAL);
	            $c1->addOr($c2);
	            $criteria->addAnd($c1);
	        }
	        if (isset($dateEnd)) {
	            $c3 = $criteria->getNewCriterion(QubitObject::CREATED_AT, $dateEnd, Criteria::LESS_EQUAL);
	            $c4 = $criteria->getNewCriterion(QubitObject::UPDATED_AT, $dateEnd, Criteria::LESS_EQUAL);
	            $c3->addOr($c4);
	            $criteria->addAnd($c3);
	        }

		    // Add sort criteria
		    switch ($this->sort) {
		    case 'nameDown':
		        $criteria->addDescendingOrderByColumn($nameColumn);
		        break;

		    case 'nameUp':
		        $criteria->addAscendingOrderByColumn($nameColumn);
		        break;

		    case 'updatedUp':
		        $criteria->addAscendingOrderByColumn(QubitObject::UPDATED_AT);
		        break;

		    case 'updatedDown':
		    default:
		        $criteria->addDescendingOrderByColumn(QubitObject::UPDATED_AT);
		    }
		    // Add fallback criteria for name
		    if ('nameDown' == $this->sort || 'nameUp' == $this->sort) {
		        $criteria = QubitCultureFallback::addFallbackCriteria($criteria, $this->form->getValue('className'));
		    }
        }

	    $this->pager = new QubitPager('QubitActor');
	    $this->pager->setCriteria($criteria);
	    $this->pager->setMaxPerPage($this->form->getValue('limit'));
	    $this->pager->setPage($this->request->getParameter('page', 1));
    }
}

