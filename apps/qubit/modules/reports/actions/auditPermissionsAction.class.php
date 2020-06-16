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
 * @subpackage Audit Trail Permissions/Groups report
 * @author Johan Pieterse <johan.pieterse@sita.co.za>
 */


class reportsAuditPermissionsAction extends sfAction
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

    BaseAuditObject::addSelectColumns($criteria);

    $criteria->add(QubitAuditObject::RECORD_ID, $request->source, Criteria::EQUAL);
    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION_DATE_TIME);
 
	$auditObjects = self::doSelect($criteria);

    // Page results
    $this->pager = new QubitPagerAudit("QubitAuditObject");
    $this->pager->setCriteria($criteria);
    $this->pager->setMaxPerPage(10000);
    $this->pager->setPage($request->page);

    $this->auditObjects = $this->pager->getResults();
    $this->auditObjectsOlder = $this->pager->getResults();
  	if (0 == count($this->auditObjects))
	{
      return sfView::ERROR;
    }

    $c2 = clone $criteria;
    $this->foundcount = BasePeer::doCount($c2)->fetchColumn(0); 
  }
}
