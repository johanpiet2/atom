<?php

/*
 * This file is part of Qubit Toolkit.
 *
 * Qubit Toolkit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Qubit Toolkit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Qubit Toolkit.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Display a list of recently updates to the db
 *
 * @package AccesstoMemory
 * @subpackage Audit Trail Actor report
 * @author Johan Pieterse <johan.pieterse@sita.co.za>
 */


class reportsAuditActorAction extends sfAction
{
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

  public function execute($request)
  {
    // Check user authorization
    if (!$this->getUser()->isAuthenticated())
    {
      QubitAcl::forwardUnauthorized();
    }

	// Check authorization
	if ((!sfContext::getInstance()->getUser()->hasGroup(QubitAclGroup::ADMINISTRATOR_ID)) && !$this->getUser()->hasGroup(QubitAclGroup::AUDIT_ID)) {
	  $this->redirect('admin/secure');
	}

    if (!isset($request->limit))
    {
      $request->limit = sfConfig::get('app_hits_per_page');
    }
    
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
    BaseAuditObject::addSelectColumns($criteria);
    $criteria->addSelectColumn(QubitActor::CORPORATE_BODY_IDENTIFIERS);
    BaseActorI18n::addSelectColumns($criteria);

    $criteria->add(QubitAuditObject::RECORD_ID, $request->source, Criteria::EQUAL);
    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION_DATE_TIME);
 
 
	$criteria->addjoin(QubitAuditObject::RECORD_ID, QubitActor::ID);
	$criteria->addjoin(QubitActor::ID, QubitActori18n::ID);

	$c4 = $criteria->getNewCriterion(QubitAuditObject::ACTION, 'delete', Criteria::NOT_EQUAL);
	$c5 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'access_object', Criteria::NOT_EQUAL);
	$c6 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'relation', Criteria::NOT_EQUAL);
	$c7 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'property', Criteria::NOT_EQUAL); 
	$c8 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'object_term_relation', Criteria::NOT_EQUAL); 
	$c9 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'object', Criteria::NOT_EQUAL);
	$c10 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'bookin_object', Criteria::NOT_EQUAL);
	$c11 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'presevation_object_i18n', Criteria::NOT_EQUAL);
	$c12 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'access_log', Criteria::NOT_EQUAL);
	$c13 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'property_i18n', Criteria::NOT_EQUAL);

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

		//echo $criteria->toString();


    // Page results
    $this->pager = new QubitPagerAudit("QubitAuditObject");
    $this->pager->setCriteria($criteria);
    $this->pager->setMaxPerPage(1);
    $this->pager->setPage($request->page);

    $this->auditObjectsOlder = $this->pager->getResults();

    $c2 = clone $criteria;
    $this->foundcount = BasePeer::doCount($c2)->fetchColumn(0); 
  }
}
