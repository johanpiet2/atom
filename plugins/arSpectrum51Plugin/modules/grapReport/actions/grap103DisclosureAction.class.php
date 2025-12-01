<?php

declare(strict_types=1);

use AtomExtensions\Database\DatabaseBootstrap;

/**
 * GRAP 103 Disclosure Action.
 *
 * Displays GRAP 103 (Heritage Assets) disclosure report.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class grapReportGrap103DisclosureAction extends sfAction
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

        // Get GRAP 103 disclosure data
        $this->disclosureData = $this->grapService->getGrap103DisclosureData();

        // Get reconciliation data
        $this->reconciliationData = $this->grapService->getReconciliationData();

        // Get balance sheet data
        $this->balanceSheetData = $this->grapService->getBalanceSheetData();

        // Set page title
        $this->response->setTitle(
            $this->context->i18n->__('GRAP 103 Heritage Assets Disclosure')
        );
    }
}
