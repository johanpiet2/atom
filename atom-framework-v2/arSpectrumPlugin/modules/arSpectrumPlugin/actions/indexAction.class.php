<?php

/**
 * Spectrum Index Action.
 *
 * Displays Spectrum procedures summary for an information object.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class arSpectrumPluginIndexAction extends sfAction
{
    public function execute($request)
    {
        if (!$this->getUser()->isAuthenticated()) {
            $this->redirect(['module' => 'user', 'action' => 'login']);
        }
        $this->resource = QubitInformationObject::getBySlug($request->slug);

        if (!$this->resource) {
            $this->forward404();
        }

        $culture = $this->getUser()->getCulture();
        $userId = $this->getUser()->getAttribute('user_id');

        $this->spectrumService = new SpectrumService($culture, $userId);

        // Load all procedure data
        $this->objectEntry = $this->spectrumService->getObjectEntries($this->resource->id);
        $this->acquisitions = $this->spectrumService->getAcquisitions($this->resource->id);
        $this->locations = $this->spectrumService->getLocations($this->resource->id);
        $this->currentLocation = $this->spectrumService->getCurrentLocation($this->resource->id);
        $this->locationHistory = $this->spectrumService->getLocationHistory($this->resource->id);
        $this->movementHistory = $this->spectrumService->getMovementHistory($this->resource->id);
        $this->loansIn = $this->spectrumService->getLoansIn($this->resource->id);
        $this->loansOut = $this->spectrumService->getLoansOut($this->resource->id);
        $this->conditionHistory = $this->spectrumService->getConditionHistory($this->resource->id);
        $this->conservationHistory = $this->spectrumService->getConservationHistory($this->resource->id);
        $this->objectExits = $this->spectrumService->getObjectExits($this->resource->id);
        $this->deaccessions = $this->spectrumService->getDeaccessions($this->resource->id);
        $this->valuationHistory = $this->spectrumService->getValuationHistory($this->resource->id);
        $this->currentValuation = $this->spectrumService->getCurrentValuation($this->resource->id);
        $this->auditLog = $this->spectrumService->getAuditLog($this->resource->id);
        $this->summary = $this->spectrumService->getObjectSummary($this->resource->id);

        // Set title
        $title = $this->resource->__toString();
        if (1 > strlen($title)) {
            $title = $this->context->i18n->__('Untitled');
        }
        $this->response->setTitle($this->context->i18n->__('Spectrum Procedures - %1%', ['%1%' => $title]));
    }
}
