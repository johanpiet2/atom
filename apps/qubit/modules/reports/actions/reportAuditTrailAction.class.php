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
    public static $NAMES = array('dateStart', 'dateEnd', 'actionUser', 'userAction', 'userActivity', 'limit');
	
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

        case 'userAction':
            $choices = array('0' =>'', 'insert' => "Insert", 'update' => "Update", 'delete' => "Delete");
            
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormSelect(array('choices' => $choices)));
            break;

        case 'userActivity':
            $choices = array( '0' => "",
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
				'QubitTaxonomy' => "Taxonomy",
				'QubitAccession' => "Accession",
				'QubitDeaccession' => "Deaccession",
				'QubitTerm' => "Term",
				'QubitObjectTermRelation' => "Term Relation",
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
		if ((!sfContext::getInstance()->getUser()->hasGroup(QubitAclGroup::ADMINISTRATOR_ID)) && !$this->getUser()->hasGroup(QubitAclGroup::AUDIT_ID)) {
		  $this->redirect('admin/secure');
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
		$dbTable = $this->form->getValue('userActivity');
		if ($this->form->getValue('userAction') != 'delete') {
			$criteria->addSelectColumn(QubitObject::CLASS_NAME);
		    // This join seems to be necessary to avoid cross joining the local table
		    // with the QubitObject table
			$criteria->addJoin(QubitAuditObject::RECORD_ID, QubitObject::ID);
			if ($dbTable == 'QubitInformationObject') {
				$criteria->addJoin(QubitAuditObject::RECORD_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
				$criteria->addJoin(QubitRepository::ID, QubitInformationObject::REPOSITORY_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitInformationObject::IDENTIFIER);
				$criteria->addSelectColumn(QubitInformationObjectI18n::TITLE);
				$criteria->addSelectColumn(QubitRepository::DESC_IDENTIFIER);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
			} else if ($dbTable == 'QubitAccessObject') {
				$criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitInformationObject::ID, QubitRelation::OBJECT_ID);
				$criteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
				$criteria->addJoin(QubitRepository::ID, QubitInformationObject::REPOSITORY_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitInformationObject::IDENTIFIER);
				$criteria->addSelectColumn(QubitInformationObjectI18n::TITLE);
				$criteria->addSelectColumn(QubitRepository::DESC_IDENTIFIER);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitPresevationObject') {
				$criteria->addJoin(QubitPresevationObject::ID, QubitAuditObject::SECONDARY_VALUE);
				$criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAuditObject::SECONDARY_VALUE);
				$criteria->addJoin(QubitInformationObject::ID, QubitRelation::OBJECT_ID);
				$criteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
				$criteria->addJoin(QubitRepository::ID, QubitInformationObject::REPOSITORY_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitInformationObject::IDENTIFIER);
				$criteria->addSelectColumn(QubitInformationObjectI18n::TITLE);
				$criteria->addSelectColumn(QubitRepository::DESC_IDENTIFIER);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitBookoutObject') {
				$criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitInformationObject::ID, QubitRelation::OBJECT_ID);
				$criteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
				$criteria->addJoin(QubitRepository::ID, QubitInformationObject::REPOSITORY_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
				$criteria->addSelectColumn(QubitInformationObject::IDENTIFIER);
				$criteria->addSelectColumn(QubitInformationObjectI18n::TITLE);
    		} else if ($dbTable == 'QubitResearcher') {
				$criteria->addJoin(QubitResearcher::ID, QubitAuditObject::SECONDARY_VALUE);
				$criteria->addJoin(QubitRepository::ID, QubitResearcher::REPOSITORY_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActor::ID);
				$criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitActor::CORPORATE_BODY_IDENTIFIERS);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitServiceProvider') {
				$criteria->addJoin(QubitServiceProvider::ID, QubitAuditObject::SECONDARY_VALUE);
				$criteria->addJoin(QubitRepository::ID, QubitServiceProvider::REPOSITORY_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActorI18n::ID);
				$criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitActor::CORPORATE_BODY_IDENTIFIERS);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitPhysicalObject') {
				$criteria->addJoin(QubitPhysicalObjectI18n::ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitPhysicalObject::ID, QubitPhysicalObjectI18n::ID);
				$criteria->addJoin(QubitRepository::ID, QubitPhysicalObjectI18n::REPOSITORY_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActor::ID);
				$criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitPhysicalObjectI18n::NAME);
				$criteria->addSelectColumn(QubitPhysicalObjectI18n::UNIQUEIDENTIFIER);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitRepository') {
				$criteria->addJoin(QubitRepository::ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActor::ID);
				$criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitRepository::IDENTIFIER);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitActor') {
				$criteria->addJoin(QubitActor::ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitDonor') {
				$criteria->addJoin(QubitDonor::ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitActor::ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitRegistry') {
				$criteria->addJoin(QubitRegistry::ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitActor::ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitActor::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
				$criteria->addSelectColumn(QubitActor::CORPORATE_BODY_IDENTIFIERS);
    		} else if ($dbTable == 'QubitDigitalObject') {
				$criteria->addJoin(QubitDigitalObject::ID, QubitAuditObject::RECORD_ID);
				$criteria->addJoin(QubitInformationObject::ID, QubitDigitalObject::OBJECT_ID);
				$criteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);
				$criteria->addJoin(QubitRepository::ID, QubitInformationObject::REPOSITORY_ID);
				$criteria->addJoin(QubitRepository::ID, QubitActorI18n::ID);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
				$criteria->addSelectColumn(QubitInformationObject::IDENTIFIER);
				$criteria->addSelectColumn(QubitInformationObjectI18n::TITLE);
				$criteria->addSelectColumn(QubitDigitalObject::NAME);
				$criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);
    		} else if ($dbTable == 'QubitUser') {
				$repos = new QubitUser;
				$this->userRepos = $repos->getRepositoriesById($this->context->user->getAttribute('user_id'));
				QubitUser::addSelectColumns($criteria);
				$criteria->addJoin(QubitAclPermission::USER_ID, QubitUser::ID, Criteria::LEFT_JOIN);
    		}

			//get repositories lined to user logged in
			$userRepos = array();
			$userRepos = QubitRepository::filteredUser($this->context->user->getAttribute('user_id'), $this->context->user->isAdministrator());

			$userReposStrip = array();
			foreach ($userRepos as $key => $value) {
				$userReposStrip[] = $key;
			}

			// Only show repositories linked to user
			if (!$this->context->user->isAdministrator()) {
				if (($dbTable == 'QubitInformationObject') || ($dbTable == 'QubitAccessObject') || ($dbTable == 'QubitPresevationObject')) {
					$criteria->add(QubitInformationObject::REPOSITORY_ID, $userReposStrip, Criteria::IN);
				} else if ($dbTable == 'QubitPhysicalObject') {
					$criteria->add(QubitPhysicalObjectI18n::REPOSITORY_ID, $userReposStrip, Criteria::IN);
				} else if ($dbTable == 'QubitResearcher') {
					$criteria->add(QubitResearcher::REPOSITORY_ID, $userReposStrip, Criteria::IN);
				} else if ($dbTable == 'QubitBookoutObject') {
					$criteria->add(QubitInformationObject::REPOSITORY_ID, $userReposStrip, Criteria::IN);
				} else if ($dbTable == 'QubitServiceProvider') {
					$criteria->add(QubitServiceProvider::REPOSITORY_ID, $userReposStrip, Criteria::IN);
				} else if ($dbTable == 'QubitRepository') {
					$criteria->add(QubitRepository::ID, $userReposStrip, Criteria::IN);
				} else if ($dbTable == 'QubitDigitalObject') {
					$criteria->add(QubitInformationObject::REPOSITORY_ID, $userReposStrip, Criteria::IN);
				} else if ($dbTable == 'QubitDigitalObject') {
			} 		$criteria->add(QubitAclPermission::OBJECT_ID, $this->userRepos, Criteria::IN);
				}


			if ($dbTable == 'QubitInformationObject') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'information_object', Criteria::EQUAL);
				$criteria4->addOr($criteria22);				
			} else if ($dbTable == 'QubitPresevationObject') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'presevation_object', Criteria::EQUAL);
				$criteria4->addOr($criteria22);				
			} else if ($dbTable == 'QubitRepository') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'repository_object', Criteria::EQUAL);
				$criteria4->addOr($criteria22);				
			} else if ($dbTable == 'QubitResearcher') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'researcher', Criteria::EQUAL);
				$criteria4->addOr($criteria22);				
			} else if ($dbTable == 'QubitServiceProvider') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'service_provider', Criteria::EQUAL);
				$criteria4->addOr($criteria22);				
			} else if ($dbTable == 'QubitPhysicalObject') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'physical_object', Criteria::EQUAL);
				$criteria4->addOr($criteria22);				
			} else if ($dbTable == 'QubitDonor') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'donor', Criteria::EQUAL);
				$criteria4->addOr($criteria22);				
			} else if ($dbTable == 'QubitBookoutObject') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'bookout_object', Criteria::EQUAL);
				$criteria4->addOr($criteria22);				
			} else if ($dbTable == 'QubitBookinObject') {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$dbTable.'', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'bookin_object', Criteria::EQUAL);
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
		} else { //deleted items
		echo $this->form->getValue('userActivity')."<br>";
			if ($this->form->getValue('userActivity') != '0') {
				$criteria4 = new Criteria;
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, ''.$this->form->getValue('userActivity').'', Criteria::EQUAL);
			} else {
				$criteria4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitInformationObject', Criteria::EQUAL);
				$criteria22 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitActor', Criteria::EQUAL);
				$criteria4->addOr($criteria22);		
				$criteria23 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitRepository', Criteria::EQUAL);
				$criteria4->addOr($criteria23);		
				$criteria24 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitUser', Criteria::EQUAL);
				$criteria4->addOr($criteria24);
				$criteria25 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitServiceProvider', Criteria::EQUAL);
				$criteria4->addOr($criteria25);		
				$criteria26 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitPhysicalObject', Criteria::EQUAL);
				$criteria4->addOr($criteria26);
				$criteria27 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitTaxonomy', Criteria::EQUAL);
				$criteria4->addOr($criteria27);		
				$criteria28 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitTerm', Criteria::EQUAL);
				$criteria4->addOr($criteria28);
				$criteria29 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitRightsHolder', Criteria::EQUAL);
				$criteria4->addOr($criteria29);		
				$criteria30 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitAccession', Criteria::EQUAL);
				$criteria4->addOr($criteria30);
				$criteria31 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitDeaccession', Criteria::EQUAL);
				$criteria4->addOr($criteria31);		
				$criteria32 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'QubitFunction', Criteria::EQUAL);
				$criteria4->addOr($criteria32);				
			}

			$criteria->addAnd($criteria4);					

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
			    $startDate = date_create($vYear . "-" . $vMonth . "-" . $vDay . " 00:00:00");
		        $startDate = date_format($startDate, 'Y-m-d H:i:s');
            } else {
                $startDate = date('Y-m-d 23:59:59');
            }
		    $startDate = $vYear . "-" . $vMonth . "-" . $vDay . " 00:00:00";
	        $c1 = $criteria->getNewCriterion(QubitAuditObject::ACTION_DATE_TIME, $startDate, Criteria::GREATER_EQUAL);
	        $criteria->addAnd($c1);
        }
        // End date at midnight
        if (null != $this->form->getValue('dateEnd')) {
            $vDay = substr($this->form->getValue('dateEnd'), 0, strpos($this->form->getValue('dateEnd'), "/"));
            $vRes = substr($this->form->getValue('dateEnd'), strpos($this->form->getValue('dateEnd'), "/") + 1);
            $vMonth = substr($vRes, 0, strpos($vRes, "/"));
            $vYear = substr($vRes, strpos($vRes, "/") + 1,4);
			if ((int)$vMonth < 10) {
				$vMonth = "0".$vMonth;
			}
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
		
        if ($this->form->getValue('userAction') != '0' && $this->form->getValue('userAction') != 'delete') {
            $c3 = $criteria->getNewCriterion(QubitAuditObject::ACTION, $this->form->getValue('userAction'), Criteria::EQUAL);
            $criteria->addAnd($c3);
		}
	    
		if (!isset($limit))
		{
		  $limit = sfConfig::get('app_hits_per_page');
		}
		
		if ($this->form->getValue('userAction') != 'delete') { //group only items not deleted
			$criteria->addGroupByColumn(QubitAuditObject::ACTION);        
			$criteria->addGroupByColumn(QubitAuditObject::USER);        
			//$criteria->addGroupByColumn(QubitAuditObject::ACTION_DATE_TIME);        
			$criteria->addGroupByColumn(QubitAuditObject::RECORD_ID);        
		}
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
		$this->pager = new QubitPagerAudit("QubitAuditTrailObject");
		$this->pager->setCriteria($criteria);
		$this->pager->setMaxPerPage($limit);
		$this->pager->setPage($page ? $page : 1);
	
		//echo $criteria->toString()."<br>";
    }
}

