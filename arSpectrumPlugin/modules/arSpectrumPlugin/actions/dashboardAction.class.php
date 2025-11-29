<?php

/**
 * Spectrum Dashboard Action.
 *
 * Displays collections management dashboard with key metrics.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class arSpectrumPluginDashboardAction extends sfAction
{
    public function execute($request)
    {
        $culture = $this->getUser()->getCulture();
        $userId = $this->getUser()->getAttribute('user_id');

        $this->spectrumService = new SpectrumService($culture, $userId);

        // Dashboard statistics
        $this->stats = $this->spectrumService->getDashboardStats();

        // Overdue items
        $this->overdueLoansIn = $this->spectrumService->getOverdueLoansIn();
        $this->overdueLoansOut = $this->spectrumService->getOverdueLoansOut();

        // Pending condition checks
        $this->pendingConditionChecks = $this->spectrumService->getObjectsNeedingConditionCheck();

        // Active loans
        $this->activeLoansIn = $this->spectrumService->getActiveLoansIn();
        $this->activeLoansOut = $this->spectrumService->getActiveLoansOut();

        $this->response->setTitle($this->context->i18n->__('Collections Management Dashboard'));
    }
}
