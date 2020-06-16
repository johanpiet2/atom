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

class reportsReportAuditTrailDeleteAction extends sfAction
{
    public static $NAMES = array('className', 'dateStart', 'dateEnd', 'cbAuditTrail', 'actionUser', 'userAction');
	
    protected function addField($name)
    {
        switch ($name) {
        case 'className':
            $choices = array('QubitAuditObject' => "Audit Trail");
            
            $this->form->setValidator($name, new sfValidatorString);
            $this->form->setWidget($name, new sfWidgetFormInputHidden);
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
		    'className' => 'QubitAuditObject', 
		    'dateStart' => date('Y-m-d', strtotime('-1 month')), 
		    'dateEnd' => date('Y-m-d'), 
		    'limit' => '10');
        
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

		$criteria = new Criteria;
		$c1 = new Criteria;
		$c2 = new Criteria;
		$c3 = new Criteria;
		BaseAuditObject::addSelectColumns($criteria);
		if ($this->form->getValue('cbAuditTrail') != 1) {
			$criteria->addSelectColumn(QubitObject::CLASS_NAME);
		    // This join seems to be necessary to avoid cross joining the local table
		    // with the QubitObject table
			$criteria->addJoin(QubitAuditObject::RECORD_ID, QubitObject::ID);
        	$criteria = QubitAuditObject::getAllJoined($criteria);
        } else {
        	$criteria = QubitAuditObject::getAllDeleted($criteria);
        }

        if (null !== $this->form->getValue('dateStart')) {
            $vDay = substr($this->form->getValue('dateStart'), 0, strpos($this->form->getValue('dateStart'), "/"));
            $vRes = substr($this->form->getValue('dateStart'), strpos($this->form->getValue('dateStart'), "/") + 1);
            $vMonth = substr($vRes, 0, strpos($vRes, "/"));
            $vYear = substr($vRes, strpos($vRes, "/") + 1);
            $startDate = date_create($vYear . "-" . $vMonth . "-" . $vDay . " 00.00.00");
            $startDate = date_format($startDate, 'Y-m-d H:i:s');
            $c1 = $criteria->getNewCriterion(QubitAuditObject::ACTION_DATE_TIME, $startDate, Criteria::GREATER_EQUAL);
            $criteria->addAnd($c1);
        }
        if (isset($dateEnd)) {
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
	    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION_DATE_TIME);
		$criteria->add(QubitAuditObject::DB_TABLE, 'other_name', Criteria::NOT_EQUAL);
	    
		$this->auditObjects = self::doSelect($criteria);
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

