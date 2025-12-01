<?php

declare(strict_types=1);

use AtomExtensions\Database\DatabaseBootstrap;

/**
 * GRAP Compliance Check Action.
 *
 * Runs and displays GRAP compliance check results.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class grapReportComplianceCheckAction extends sfAction
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

        // Run compliance check
        $this->complianceResults = $this->grapService->runComplianceCheck();

        // Get non-compliant items with details
        $this->nonCompliantItems = $this->grapService->search([
            'compliance_status' => 'non_compliant',
        ]);

        // Set page title
        $this->response->setTitle(
            $this->context->i18n->__('GRAP Compliance Check')
        );
    }
}
