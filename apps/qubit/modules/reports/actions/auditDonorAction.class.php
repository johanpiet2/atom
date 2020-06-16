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

class reportsAuditDonorAction extends sfAction
{
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
    $criteria->add(QubitAuditObject::DB_TABLE, 'contact_information', Criteria::NOT_EQUAL);
    $criteria->add(QubitAuditObject::DB_TABLE, 'actor', Criteria::NOT_EQUAL);
    $criteria->add(QubitAuditObject::DB_TABLE, 'donor', Criteria::NOT_EQUAL);
    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION_DATE_TIME);
 
    // Page results
    $this->pager = new QubitPagerAudit("QubitAuditObject");
    $this->pager->setCriteria($criteria);
    $this->pager->setMaxPerPage(10000);
    $this->pager->setPage($request->page);

    $this->auditObjectsOlder = $this->pager->getResults();

    $c2 = clone $criteria;
    $this->foundcount = BasePeer::doCount($c2)->fetchColumn(0); 
  }
}
