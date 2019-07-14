<?php

/**
 * Presevation Object edit component.
 *
 * @package    qubit
 * @subpackage Preservation Module
 * @author     Ramaano Ndou <ramaano.ndou@sita.co.za>
 * @version    SVN: $Id
 */

class InformationObjectPresevationObjectsAction extends sfAction
{
  public function execute($request)
  {
    if (!isset($request->limit))
    {
      $request->limit = sfConfig::get('app_hits_per_page');
    }

    $this->resource = $this->getRoute()->resource;

    // Check that this isn't the root
    if (!isset($this->resource->parent))
    {
      $this->forward404();
    }

    if (!$this->getUser()->isAuthenticated())
    {
      return sfView::NONE;
    }

    $criteria = new Criteria;
    $criteria->add(QubitRelation::OBJECT_ID, $this->resource->id);
    $criteria->add(QubitRelation::CONDITION_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::USABILITY_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::MEASURE_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::MEDIUM_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::AVAILABILITY_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::HARD_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::DIGITAL_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
    $criteria->add(QubitRelation::REFUSAL_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::RESTORATION_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::CONSERVATION_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::TYPE_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::SENSITIVITY_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::PUBLISH_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
	$criteria->add(QubitRelation::CLASSIFICATION_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
    $criteria->add(QubitRelation::RESTRICTION_ID, QubitTaxonomy::PRESERVATION_TYPE_ID);
    $criteria->addJoin(QubitRelation::SUBJECT_ID, QubitPresevationObject::ID);

    $this->pager = new QubitPager('QubitPresevationObject');
    $this->pager->setCriteria($criteria);
    $this->pager->setMaxPerPage($request->limit);
    $this->pager->setPage($request->page);

    $this->presevationObjects = $this->pager->getResults();
  }
}
