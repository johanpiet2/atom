<?php

/**
 * Bookout Object edit component.
 *
 * @package    qubit
 * @subpackage Bookout Module
 * @author     Tsholo Ramesega
 * @version    SVN: $Id
 */
 
class BookoutObjectBrowseAction extends sfAction
{
  public function execute($request)
  {
    if (!$this->getUser()->isAuthenticated())
    {
      QubitAcl::forwardUnauthorized();
    }

    if (!isset($request->limit))
    {
      $request->limit = sfConfig::get('app_hits_per_page');
    }

    $criteria = new Criteria;

    // Do source culture fallback
    $criteria = QubitCultureFallback::addFallbackCriteria($criteria, 'QubitBookoutObject');

    switch ($request->sort)
    {
      case 'nameDown':
        $criteria->addDescendingOrderByColumn('name');

        break;

      case 'remarksDown':
        $criteria->addDescendingOrderByColumn('remarks');

        break;

      case 'remarksUp':
        $criteria->addAscendingOrderByColumn('remarks');

        break;

      case 'timeDown':
        $criteria->addDescendingOrderByColumn('time_period');

        break;

      case 'timeUp':
        $criteria->addAscendingOrderByColumn('time_period');

        break;
      case 'nameUp':
      default:
        $request->sort = 'nameUp';
        $criteria->addAscendingOrderByColumn('name');
    }

    // Page results
    $this->pager = new QubitPager('QubitBookoutObject');
    $this->pager->setCriteria($criteria);
    $this->pager->setMaxPerPage($request->limit);
    $this->pager->setPage($request->page);
  }
}
