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
 * @subpackage Audit Trail report
 * @author Johan Pieterse <johan.pieterse@sita.co.za>
 */

class reportsReportAuditTrailAction extends sfAction
{
    public static $NAMES = array('dateStart', 'dateEnd', 'cbAuditTrail', 'actionUser', 'userAction', 'limit');
	
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

        case 'cbAuditTrail':
		    $this->form->setDefault($name, false);
		    $this->form->setValidator($name, new sfValidatorBoolean);
		    $this->form->setWidget($name, new sfWidgetFormInputCheckbox);

            break;

        case 'userAction':
            $choices = array('0' =>'', 'insert' => "Insert", 'update' => "Update");
            
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormSelect(array('choices' => $choices)));
            break;

		case 'actionUser':
			$values = array();
			$values[] = null;
			foreach (QubitUser::getAll() as $user) {
				$values[$user->username] = $user->__toString();
			}
			
			$this->form->setValidator('actionUser', new sfValidatorString);
			$this->form->setWidget('actionUser', new sfWidgetFormSelect(array(
				'choices' => $values
			)));
			break;
		
        case 'limit':
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormInputHidden);

            break;

        case 'sort':
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormInputHidden);

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
		    'dateStart' => date('Y-m-d', strtotime('-1 month')), 
		    'dateEnd' => date('Y-m-d'), 
		    'limit' => '10');
        
        $this->form->bind($request->getRequestParameters() + $request->getGetParameters() + $defaults);
	    if ($this->form->isValid()) {
	        $this->doSearch($request->limit, $request->page);
	    }
	}
	
    public function doSearch($limit, $page)
    {
        $criteria = new Criteria;
        $this->sort = $this->request->getParameter('sort', 'updatedDown');
       
		$criteria = new Criteria;
		$c1 = new Criteria;
		$c2 = new Criteria;
		$c3 = new Criteria;
		$c4 = new Criteria;
		$c5 = new Criteria;
		$c6 = new Criteria;
		$c7 = new Criteria;
		$c8 = new Criteria;
		$c9 = new Criteria;
		$c10 = new Criteria;
		$c11 = new Criteria;
		$c12 = new Criteria;
		BaseAuditObject::addSelectColumns($criteria);
		if ($this->form->getValue('cbAuditTrail') != 1) {
			$criteria->addSelectColumn(QubitObject::CLASS_NAME);
		    // This join seems to be necessary to avoid cross joining the local table
		    // with the QubitObject table
			$criteria->addJoin(QubitAuditObject::RECORD_ID, QubitObject::ID);
    		$criteria->add(QubitAuditObject::ACTION, 'delete', Criteria::NOT_EQUAL);
			$criteria->add(QubitAuditObject::ID, null, Criteria::ISNOTNULL);

			//$criteria->addGroupByColumn(QubitAuditObject::RECORD_ID);
			$c4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'object_term_relation', Criteria::NOT_EQUAL);
			$c5 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'setting_i18n', Criteria::NOT_EQUAL); //settings is handled different to fix SITA
			$c6 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'relation', Criteria::NOT_EQUAL);
			$c7 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'relation_i18n', Criteria::NOT_EQUAL); 
			$c8 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitRelation', Criteria::NOT_EQUAL); 
			$c9 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'digital_object', Criteria::NOT_EQUAL); 
			$c10 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitDigitalObject', Criteria::NOT_EQUAL); 
			$c11 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitEvent', Criteria::NOT_EQUAL); 
			$c12 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'event', Criteria::NOT_EQUAL); 
			$c13 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, '', Criteria::NOT_EQUAL); 
			$c14 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'event_i18n', Criteria::NOT_EQUAL); 
			$c15 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitAccessObject', Criteria::NOT_EQUAL); //part of Archival description
			$c16 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitPresevationObject', Criteria::NOT_EQUAL);  //part of Archival description
			$c17 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'access_object_i18n', Criteria::NOT_EQUAL);
	 		$c4->addAnd($c5);
			$criteria->addAnd($c4);
	 		$c6->addAnd($c7);
			$criteria->addAnd($c6);
	 		$c8->addAnd($c9);
			$criteria->addAnd($c8);
	 		$c10->addAnd($c11);
			$criteria->addAnd($c10);
	 		$c12->addAnd($c13);
			$criteria->addAnd($c12);
	 		$c14->addAnd($c15);
			$criteria->addAnd($c14);
	 		$c16->addAnd($c17);
			$criteria->addAnd($c16);
        } else {
    		$criteria->add(QubitAuditObject::ACTION, 'delete', Criteria::EQUAL);
        }
        if (null !== $this->form->getValue('dateStart')) {
    		$startDate = $this->form->getValue('dateStart');
        	if (strpos($startDate,"/",0) == 0) {
				$startDate = date('d/m/Y', strtotime("-3 months"));			    
        	}
		    $vDay = substr($startDate, 0, strpos($startDate, "/"));
		    $vRes = substr($startDate, strpos($startDate, "/") + 1);
		    $vMonth = substr($vRes, 0, strpos($vRes, "/"));
		    $vYear = substr($vRes, strpos($vRes, "/") + 1);
            if (checkdate((int)$vDay, (int)$vMonth, (int)$vYear)) {
			    $startDate = date_create($vYear . "-" . $vMonth . "-" . $vDay . " 00.00.00");
		        $startDate = date_format($startDate, 'Y-m-d H:i:s');
            } else {
                $startDate = date('Y-m-d 23:59:59');
            }
		    $startDate = $vYear . "-" . $vMonth . "-" . $vDay . " 00.00.00";
	        $c1 = $criteria->getNewCriterion(QubitAuditObject::ACTION_DATE_TIME, $startDate, Criteria::GREATER_EQUAL);
	        $criteria->addAnd($c1);
        }
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
            $c1 = $criteria->getNewCriterion(QubitAuditObject::ACTION_DATE_TIME, $dateEnd, Criteria::LESS_EQUAL);
            $criteria->addAnd($c1);
        }

        if ($this->form->getValue('actionUser') == '1') {
            $c2 = $criteria->getNewCriterion(QubitAuditObject::USER, $this->form->getValue('actionUser'), Criteria::EQUAL);
            $criteria->addAnd($c2);
		}
        if ($this->form->getValue('userAction') == '1') {
            $c3 = $criteria->getNewCriterion(QubitAuditObject::ACTION, $this->form->getValue('userAction'), Criteria::EQUAL);
            $criteria->addAnd($c3);
		}

	    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION_DATE_TIME);
	    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION);
	    $criteria->addDescendingOrderByColumn(QubitAuditObject::USER);
	    
		if (!isset($limit))
		{
		  $limit = sfConfig::get('app_hits_per_page');
		}
		// Page results
		$this->pager = new QubitPagerAudit("QubitAuditObject");
		$this->pager->setCriteria($criteria);
		$this->pager->setMaxPerPage($limit);
		$this->pager->setPage($page ? $page : 1);
	
		//echo $criteria->toString()."<br>";
    }
}

