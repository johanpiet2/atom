<?php

declare(strict_types=1);

use AtomExtensions\Database\DatabaseBootstrap;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * GRAP Report Index Action.
 *
 * Displays GRAP report with filtering and statistics.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class grapReportIndexAction extends sfAction
{
    public function execute($request)
    {
        if (!$this->getUser()->isAuthenticated()) {
            $this->redirect(['module' => 'user', 'action' => 'login']);
        }

        // Initialize database
        if (null === DatabaseBootstrap::getCapsule()) {
            DatabaseBootstrap::initializeFromAtom();
        }

        $culture = $this->getUser()->getCulture();
        $userId = (int) $this->getUser()->getAttribute('user_id');

        $this->grapService = new GrapService($culture, $userId);

        // Process filter form
        $this->filterForm = new GrapReportFilterForm();

        $filters = [];
        if ($request->isMethod('post') || $request->hasParameter('filters')) {
            $filterData = $request->getParameter('filters', []);
            $this->filterForm->bind($filterData);

            if ($this->filterForm->isValid()) {
                $filters = array_filter($this->filterForm->getValues());
            }
        }

        // Get paginated results
        $page = (int) $request->getParameter('page', 1);
        $perPage = (int) $request->getParameter('per_page', 20);

        $this->results = $this->grapService->paginate($filters, $perPage, $page);
        $this->filters = $filters;

        // Get statistics
        $this->statistics = $this->grapService->getStatistics();

        // Set page title
        $this->response->setTitle(
            $this->context->i18n->__('GRAP Report - Heritage Asset Register')
        );
    }
}
