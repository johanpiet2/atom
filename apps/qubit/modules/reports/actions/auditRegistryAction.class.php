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

class reportsAuditRegistryAction extends sfAction
{
  public function execute($request)
  {

    $this->resource = $this->getRoute()->resource;
    // Check user authorization
    if (!$this->getUser()->isAuthenticated())
    {
      QubitAcl::forwardUnauthorized();
    }
    if (!QubitAcl::check($this->resource, 'auditTrail'))
    {
      //QubitAcl::forwardUnauthorized(); //To Fix SITA JJP
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
    BaseAuditObject::addSelectColumns($criteria);
    $criteria->addSelectColumn(QubitActor::CORPORATE_BODY_IDENTIFIERS);
    $criteria->addSelectColumn(QubitActorI18n::AUTHORIZED_FORM_OF_NAME);

    $criteria->add(QubitAuditObject::RECORD_ID, $request->source, Criteria::EQUAL);
    $criteria->addDescendingOrderByColumn(QubitAuditObject::ACTION_DATE_TIME);
 
 
	$criteria->addjoin(QubitAuditObject::RECORD_ID, QubitRegistry::ID);
	$criteria->addjoin(QubitActor::ID, QubitRegistry::ID);
	$criteria->addjoin(QubitActor::ID, QubitActori18n::ID);

    
	$c1 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'access_object', Criteria::NOT_EQUAL);
	$c2 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'relation', Criteria::NOT_EQUAL);
	$c3 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'property', Criteria::NOT_EQUAL);
	$c4 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'object_term_relation', Criteria::NOT_EQUAL);
	$c5 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'object', Criteria::NOT_EQUAL);
	$c6 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'bookin_object', Criteria::NOT_EQUAL);
	$c7 = $criteria->getNewCriterion(QubitAuditObject::DB_TABLE, 'presevation_object_i18n', Criteria::NOT_EQUAL);
	$c1->addAnd($c2);
	$c3->addAnd($c4);
	$c5->addAnd($c6);
	$criteria->addAnd($c1);
	$criteria->addAnd($c3);
	$criteria->addAnd($c5);
	$criteria->addAnd($c7);

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
