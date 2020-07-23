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
class reportsReportRegistryAction extends sfAction
{
    public static $NAMES = array('className', 'dateStart', 'dateEnd', 'dateOf', 'limit');
	
    protected function addField($name)
    {
        switch ($name) {
        case 'className':
            $choices = array(
            'QubitRegistry' => "Register");
            
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormSelect(array('choices' => $choices)));
            break;

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
		    'className' => 'QubitRegistry', 
		    'dateStart' => date('Y-m-d', strtotime('-1 week')), 
		    'dateEnd' => date('Y-m-d'), 'dateOf' => 'CREATED_AT', 
		    'publicationStatus' => 'all', 
		    'limit' => '10', 
		    'sort' => 'updatedDown');
        
        $this->form->bind($request->getRequestParameters() + $request->getGetParameters() + $defaults);
	    if ($this->form->isValid()) {
	        $this->className = $this->form->getValue('className');
	        $this->doSearch();
	    }
	}
	
    public function doSearch()
    {
        $criteria = new Criteria;
        $this->sort = $this->request->getParameter('sort', 'updatedDown');
        // This join seems to be necessary to avoid cross joining the local table
        // with the QubitObject table
        if ($this->form->getValue('className') != 'QubitAuditObject') {
        	$criteria->addJoin(constant($this->className . '::ID'), QubitObject::ID);
        }
        
        switch ($this->form->getValue('className')) {
        case 'QubitActor':
            $nameColumn = 'authorized_form_of_name';
            $this->nameColumnDisplay = 'Name';
            $criteria = QubitActor::addGetOnlyActorsCriteria($criteria);
            $criteria->add(QubitActor::PARENT_ID, null, Criteria::ISNOTNULL);
            break;

        case 'QubitRegistry':
            $nameColumn = 'authorized_form_of_name';
            $this->nameColumnDisplay = 'Name';
            $criteria = QubitRegistry::addGetOnlyRegisterObjectCriteria($criteria);
            break;

        // Default: information object
        default:
            $nameColumn = 'title';
            $this->nameColumnDisplay = 'Title';
            $criteria->add(QubitInformationObject::PARENT_ID, null, Criteria::ISNOTNULL);
            $criteria->addJoin(QubitInformationObject::ID, QubitInformationObjectI18n::ID);

            if (null !== $this->form->getValue('repositories')) //Get from specific repository
            {
                $criteria->add(QubitInformationObject::REPOSITORY_ID, $this->form->getValue('repositories'), Criteria::EQUAL);
            }

			if (null !== $this->form->getValue('registries')) //Get from specific registry
            {
                $criteria->add(QubitInformationObject::REGISTRY_ID, $this->form->getValue('registries'), Criteria::EQUAL);
            }

			if (null !== $this->form->getValue('authorityRecords')) //Get from specific authority records
            {
	           $criteria->addJoin(QubitInformationObject::ID, QubitEvent::OBJECT_ID);
               $criteria->add(QubitEvent::ACTOR_ID, $this->form->getValue('authorityRecords'), Criteria::EQUAL);
            }

			if (null !== $this->form->getValue('levelOfDescription')) //Get from Level Of Description
            {
                $criteria->add(QubitInformationObject::LEVEL_OF_DESCRIPTION_ID, $this->form->getValue('levelOfDescription'), Criteria::EQUAL);
            }
            
			if (null !== $this->form->getValue('condition') && $this->form->getValue('condition') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitPresevationObject::OBJECT_ID);
				$criteria->addJoin(QubitPresevationObject::ID, QubitPresevationObjectI18n::ID);
				$criteria->add(QubitPresevationObject::CONDITION_ID, $this->form->getValue('condition'), Criteria::EQUAL);
            }
			
			if (null !== $this->form->getValue('usability') && $this->form->getValue('usability') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitPresevationObject::OBJECT_ID);
				$criteria->addJoin(QubitPresevationObject::ID, QubitPresevationObjectI18n::ID);
				$criteria->add(QubitPresevationObject::USABILITY_ID, $this->form->getValue('usability'), Criteria::EQUAL);
            }

			if (null !== $this->form->getValue('measure') && $this->form->getValue('measure') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitPresevationObject::OBJECT_ID);
				$criteria->addJoin(QubitPresevationObject::ID, QubitPresevationObjectI18n::ID);
				$criteria->add(QubitPresevationObject::MEASURE_ID, $this->form->getValue('measure'), Criteria::EQUAL);
				
            }

			if (null !== $this->form->getValue('availability') && $this->form->getValue('availability') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitPresevationObject::OBJECT_ID);
				$criteria->addJoin(QubitPresevationObject::ID, QubitPresevationObjectI18n::ID);
				$criteria->add(QubitPresevationObject::AVAILABILITY_ID, $this->form->getValue('availability'), Criteria::EQUAL);
				
            }

			if (null !== $this->form->getValue('restoration') && $this->form->getValue('restoration') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitPresevationObject::OBJECT_ID);
				$criteria->addJoin(QubitPresevationObject::ID, QubitPresevationObjectI18n::ID);
				$criteria->add(QubitPresevationObject::RESTORATION_ID, $this->form->getValue('restoration'), Criteria::EQUAL);
            }
            
			if (null !== $this->form->getValue('conservation') && $this->form->getValue('conservation') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitPresevationObject::OBJECT_ID);
				$criteria->addJoin(QubitPresevationObject::ID, QubitPresevationObjectI18n::ID);
				$criteria->add(QubitPresevationObject::CONSERVATION_ID, $this->form->getValue('conservation'), Criteria::EQUAL);
            }
			
			if (null !== $this->form->getValue('refusal') && $this->form->getValue('refusal') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAccessObject::ID);
				$criteria->addJoin(QubitAccessObject::ID, QubitAccessObjectI18n::ID);
				$criteria->add(QubitAccessObjectI18n::REFUSAL_ID, $this->form->getValue('refusal'), Criteria::EQUAL);
            }

			if (null !== $this->form->getValue('sensitivity') && $this->form->getValue('sensitivity') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAccessObject::ID);
				$criteria->addJoin(QubitAccessObject::ID, QubitAccessObjectI18n::ID);
				$criteria->add(QubitAccessObjectI18n::SENSITIVITY_ID, $this->form->getValue('sensitivity'), Criteria::EQUAL);
            }

			if (null !== $this->form->getValue('publish') && $this->form->getValue('publish') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAccessObject::ID);
				$criteria->addJoin(QubitAccessObject::ID, QubitAccessObjectI18n::ID);
				if ($this->form->getValue('publish') == "No action")
				{
					$criteria->add(QubitAccessObjectI18n::PUBLISHED, 0, Criteria::EQUAL);
				}
				else
				{
					$criteria->add(QubitAccessObjectI18n::PUBLISHED, $this->form->getValue('publish'), Criteria::EQUAL);
				}
            }

			if (null !== $this->form->getValue('restriction') && $this->form->getValue('restriction') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAccessObject::ID);
				$criteria->addJoin(QubitAccessObject::ID, QubitAccessObjectI18n::ID);
				$criteria->add(QubitAccessObjectI18n::RESTRICTION_ID, $this->form->getValue('restriction'), Criteria::EQUAL);
            }

            if (null !== $this->form->getValue('physicalStorage')) //Get for specific physical storage
            {
                $criteria->addJoin(QubitInformationObject::ID, QubitRelation::OBJECT_ID);
                $criteria->addJoin(QubitRelation::SUBJECT_ID, QubitPhysicalObject::ID);
                $criteria->add(QubitPhysicalObject::ID, $this->form->getValue('physicalStorage'), Criteria::EQUAL);
            }
            if (null !== $this->form->getValue('formats')) {
                $criteria->add(QubitInformationObject::FORMAT_ID, $this->form->getValue('formats'), Criteria::EQUAL);
            }

			if (null !== $this->form->getValue('classification') && $this->form->getValue('classification') != '') 
            {
				$criteria->addJoin(QubitRelation::OBJECT_ID, QubitInformationObject::ID);
				$criteria->addJoin(QubitRelation::SUBJECT_ID, QubitAccessObject::ID);
				$criteria->addJoin(QubitAccessObject::ID, QubitAccessObjectI18n::ID);
				$criteria->add(QubitAccessObjectI18n::CLASSIFICATION_ID, $this->form->getValue('classification'), Criteria::EQUAL);
            }
        }
		
        if ('QubitInformationObject' == $this->className && 'all' != $this->form->getValue('publicationStatus')) {
            $criteria->addJoin(QubitObject::ID, QubitStatus::OBJECT_ID);
            $criteria->add(QubitStatus::STATUS_ID, $this->form->getValue('publicationStatus'));
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
        }
        if ($this->form->getValue('className') != 'QubitAuditObject') {
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
					if ((int)$vMonth < 10) {
						$vMonth = "0".$vMonth;
					}
					if (checkdate((int)$vMonth, (int)$vDay, (int)$vYear)) {
						$startDate = date_create($vYear . "-" . $vMonth . "-" . $vDay . " 00.00.00");
						$startDate = date_format($startDate, 'Y-m-d H:i:s');
					} else {
						$startDate = date('2020-01-01 23.59.59');
					}
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
        if ($this->form->getValue('className') != 'QubitAuditObject') {
		    $this->pager = new QubitPager($this->form->getValue('className'));
		    $this->pager->setCriteria($criteria);
		    $this->pager->setMaxPerPage($this->form->getValue('limit'));
		    $this->pager->setPage($this->request->getParameter('page', 1));
		} else {
			$criteria = new Criteria;
			BaseAuditObject::addSelectColumns($criteria);

			if ($this->form->getValue('cbAuditTrail') != 1) {
            	$criteria = QubitAuditObject::getAllJoined($criteria);
            } else {
            	$criteria = QubitAuditObject::getAllDeleted($criteria);
            }

			if ($this->form->getValue('cbAuditTrail') != 1) {
		    	//$criteria->addDescendingOrderByColumn(QubitInformationObject::IDENTIFIER);
		    }
		    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION_DATE_TIME);
			$this->auditObjects = self::doSelect($criteria);
		}
    }
    
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$dbMap = Propel::getDatabaseMap($criteria->getDbName());
		$db = Propel::getDB($criteria->getDbName());

		if ($con === null) {
			$con = Propel::getConnection($criteria->getDbName(), Propel::CONNECTION_READ);
		}

		$stmt = null;

		if ($criteria->isUseTransaction()) $con->beginTransaction();

		try {

			$params = array();
			$sql = BasePeer::createSelectSql($criteria, $params);

			$stmt = $con->prepare($sql);
			BasePeer::populateStmtValues($stmt, $params, $dbMap, $db);

			$stmt->execute();

			if ($criteria->isUseTransaction()) $con->commit();

		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			if ($criteria->isUseTransaction()) $con->rollBack();
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}

		return $stmt;
	}
    
    
}
