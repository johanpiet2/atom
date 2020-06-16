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
 * @subpackage User Activity report
 * @author Johan Pieterse <johan.pieterse@sita.co.za>
 */

class reportsReportUserAction extends sfAction
{
    public static $NAMES = array('dateStart', 'dateEnd', 'cbAuditTrailDeleted', 'actionUser', 'userAction', 'userActivity', 'cbAuditTrailSummery', 'limit');

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

        case 'cbAuditTrailDeleted':
		    $this->form->setDefault($name, false);
		    $this->form->setValidator($name, new sfValidatorBoolean);
		    $this->form->setWidget($name, new sfWidgetFormInputCheckbox);

            break;

        case 'cbAuditTrailSummery':
		    $this->form->setDefault($name, false);
		    $this->form->setValidator($name, new sfValidatorBoolean);
		    $this->form->setWidget($name, new sfWidgetFormInputCheckbox);

            break;

        case 'userAction':
            $choices = array('0' =>'', 'insert' => "Insert", 'update' => "Update");
            
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormSelect(array('choices' => $choices)));
            break;

        case 'userActivity':
            $choices = array('0' =>'', 
				'QubitAccessObject' => "Access",
				'QubitInformationObject' => "Archival Description", 
				'QubitRepository' => "Archival Institution",
				'QubitActor' => "Authority Record",
				'QubitBookinObject' => "Book in",
				'QubitBookoutObject' => "Book out",
				'QubitDigitalObject' => "Digital Object",
				'QubitDonor' => "Donor",
				'QubitPhysicalObject' => "Physical Storage",
				'QubitPresevationObject' => "Preservation",
				'QubitRegistry' => "Registry",
				'QubitResearcher' => "Researcher",
				'QubitServiceProvider' => "Service Provider",
				//'QubitTaxonomy' => "Taxonomy",
				//'QubitTerm' => "Term",
				//'QubitObjectTermRelation' => "Term Relation",
				'QubitUser' => "Users"
			);
           
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormSelect(array('choices' => $choices)));
            break;

		case 'actionUser':
			$values = array();
			$values[] = null;
	        $criteria = new Criteria;
			// filter users per users linked to repository
			if ((!$this->context->user->isAdministrator()) && ($this->context->user->isSuperUser())) {
				$repos = new QubitUser;
				$this->userRepos = $repos->getRepositoriesById($this->context->user->getAttribute('user_id'));
				QubitUser::addSelectColumns($criteria);
				$criteria->addJoin(QubitAclPermission::USER_ID, QubitUser::ID, Criteria::LEFT_JOIN);
				$criteria->add(QubitAclPermission::OBJECT_ID, $this->userRepos, Criteria::IN);
			}
			$criteria->addGroupByColumn(QubitUser::USERNAME);
			$criteria->addAscendingOrderByColumn(QubitUser::USERNAME);

			foreach (QubitUser::get($criteria) as $user) {
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
		// Check authorization
		if ((!$this->context->user->isAdministrator()) && (!$this->context->user->isSuperUser()) && (!$this->context->user->isAuditUser())) {
			QubitAcl::forwardUnauthorized();
		}

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
        $this->sort = $this->request->getParameter('sort', 'updatedDown');
       
		$criteria = new Criteria;
		BaseAuditObject::addSelectColumns($criteria);
		if ($this->form->getValue('cbAuditTrailDeleted') != 1) {
			$dbTable = $this->form->getValue('userActivity');
			$criteria->addSelectColumn(QubitObject::CLASS_NAME);
		    // This join seems to be necessary to avoid cross joining the local table with the QubitObject table
			$criteria->addJoin(QubitAuditObject::RECORD_ID, QubitObject::ID);
			if ($dbTable == 'QubitUser') {
				$criteria->addSelectColumn(QubitActor::ID);
				$criteria->addJoin(QubitActor::ID, QubitAuditObject::RECORD_ID);
			}
			$criteria->add(QubitAuditObject::ID, null, Criteria::ISNOTNULL);

			if ($this->form->getValue('userActivity') == '0') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitInformationObject', Criteria::EQUAL);
				$criteria5 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitRepository', Criteria::EQUAL); 
				$criteria6 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitResearcher', Criteria::EQUAL);
				$criteria7 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitServiceProvider', Criteria::EQUAL); 
				$criteria8 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitPhysicalObject', Criteria::EQUAL); 
				$criteria9 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitRegistry', Criteria::EQUAL); 
				$criteria10 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitActor', Criteria::EQUAL); 
				$criteria11 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitDonor', Criteria::EQUAL); 
				$criteria12 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitUser', Criteria::EQUAL); 
				$criteria13 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitAccessObject', Criteria::EQUAL); 
				$criteria14 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitTerm', Criteria::EQUAL); 
				$criteria15 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitBookinObject', Criteria::EQUAL);
				$criteria16 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitBookoutObject', Criteria::EQUAL); 
				$criteria17 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitPresevationObject', Criteria::EQUAL);
				$criteria18 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitTaxonomy', Criteria::EQUAL);
				$criteria19 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitDigitalObject', Criteria::EQUAL);
				$criteria20 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitObjectTermRelation', Criteria::EQUAL);
				$criteria21 = $criteria->getNewCriterion(QubitAuditObject::USER, '', Criteria::NOT_EQUAL);
				
				$criteria4->addOr($criteria5);
				$criteria4->addOr($criteria6);
				$criteria4->addOr($criteria7);
				$criteria4->addOr($criteria8);
				$criteria4->addOr($criteria9);
				$criteria4->addOr($criteria10);
				$criteria4->addOr($criteria11);
				$criteria4->addOr($criteria12);
				$criteria4->addOr($criteria13);
				$criteria4->addOr($criteria14);
				$criteria4->addOr($criteria15);
				$criteria4->addOr($criteria16);
				$criteria4->addOr($criteria17);
				$criteria4->addOr($criteria18);
				$criteria4->addOr($criteria19);
				$criteria4->addOr($criteria20);
				$criteria->addAnd($criteria4);
				$criteria4->addAnd($criteria21);
			} else {
				
				if ($dbTable == 'QubitInformationObject') {
					$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
					$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'information_object', Criteria::EQUAL);
					$criteria4->addOr($criteria22);				
				} else if ($dbTable == 'QubitPresevationObject') {
					$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
					$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'presevation_object', Criteria::EQUAL);
					$criteria4->addOr($criteria22);				
				} else if ($dbTable == 'QubitDonor') {
					$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
					$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'donor', Criteria::EQUAL);
					$criteria4->addOr($criteria22);				
				} else if ($dbTable == 'QubitResearcher') {
					$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
					$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'researcher', Criteria::EQUAL);
					$criteria4->addOr($criteria22);				
				} else if ($dbTable == 'QubitServiceProvider') {
					$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
					$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'service_provider', Criteria::EQUAL);
					$criteria4->addOr($criteria22);				
				} else if ($dbTable == 'QubitAccessObject') {
					$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
					$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'access_object', Criteria::EQUAL);
					$criteria4->addOr($criteria22);				
				} else if ($dbTable == 'QubitRegistry') {
					$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
					$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'registry', Criteria::EQUAL);
					$criteria4->addOr($criteria22);				
				} else {
					$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				}
				$criteria->addAnd($criteria4);					
			}
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
			if ((int)$vMonth < 10) {
				$vMonth = "0".$vMonth;
			}
            if (checkdate((int)$vMonth, (int)$vDay, (int)$vYear)) {
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
			if ((int)$vMonth < 10) {
				$vMonth = "0".$vMonth;
			}
            $vYear = substr($vRes, strpos($vRes, "/") + 1,4);
            if (checkdate((int)$vMonth, (int)$vDay, (int)$vYear)) {
                $dateEnd = date_create($vYear . "-" . $vMonth . "-" . $vDay . " 23.59.59");
                $dateEnd = date_format($dateEnd, 'Y-m-d H:i:s');
            } else {
                $dateEnd = date('Y-m-d 23:59:59');
            }
            $c1 = $criteria->getNewCriterion(QubitAuditObject::ACTION_DATE_TIME, $dateEnd, Criteria::LESS_EQUAL);
            $criteria->addAnd($c1);
        }

        if ($this->form->getValue('actionUser') != '0') {
            $c2 = $criteria->getNewCriterion(QubitAuditObject::USER, $this->form->getValue('actionUser'), Criteria::EQUAL);
            $criteria->addAnd($c2);
		}
		
        if ($this->form->getValue('userAction') != '0') {
            $c3 = $criteria->getNewCriterion(QubitAuditObject::ACTION, $this->form->getValue('userAction'), Criteria::EQUAL);
            $criteria->addAnd($c3);
		}
	    
		if (!isset($limit))
		{
		  $limit = sfConfig::get('app_hits_per_page');
		}
		
		$criteria->addGroupByColumn(QubitAuditObject::ACTION);        
		$criteria->addGroupByColumn(QubitAuditObject::USER);        
		$criteria->addGroupByColumn(QubitAuditObject::ACTION_DATE_TIME);        
	    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION_DATE_TIME);
	    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION);
	    $criteria->addDescendingOrderByColumn(QubitAuditObject::USER);
		if (QubitSetting::getByName('max_row_report') != '-1') {
			$rowToReturn = QubitSetting::getByName('max_row_report');
			$rowToReturn = preg_replace('/\s+/', '', $rowToReturn);
			$rowToReturn = (int)$rowToReturn;
			
			$criteria->setLimit($rowToReturn); //bug not working
		}

		// Page results
		$this->pager = new QubitPagerAudit("QubitUserActions");
		$this->pager->setCriteria($criteria);
		$this->pager->setMaxPerPage($limit);
		$this->pager->setPage($page ? $page : 1);
	
		//echo $criteria->toString()."<br>";
    }
}

