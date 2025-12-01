<?php

/**
 * Spectrum Index Action.
 *
 * Displays Spectrum procedures summary for an information object.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
 
use Illuminate\Database\Capsule\Manager as Capsule;
 
class arSpectrumPluginIndexAction extends sfAction
{
    public function execute($request)
    {
        if (!$this->getUser()->isAuthenticated()) {
            $this->redirect(['module' => 'user', 'action' => 'login']);
        }

		// Initialize database
		if (\AtomExtensions\Database\DatabaseBootstrap::getCapsule() === null) {
			\AtomExtensions\Database\DatabaseBootstrap::initializeFromAtom();
		}

		$slugData = Capsule::table('slug')
			->where('slug', $request->slug)
			->first();

		if (!$slugData) {
			$this->forward404();
		}

		$this->resource = Capsule::table('information_object')
			->where('id', $slugData->object_id)
			->first();

		if (!$this->resource) {
			$this->forward404();
		}

		// Add slug property manually
		$this->resource->slug = $request->slug;

        if (!$this->resource) {
            $this->forward404();
        }

        $culture = $this->getUser()->getCulture();
        $userId = $this->getUser()->getAttribute('user_id');

        $this->spectrumService = new SpectrumService($culture, $userId);

        // Load all procedure data
		$objectId = (int)$this->resource->id;
		
        $this->objectEntry = $this->spectrumService->getObjectEntries($objectId);
        $this->acquisitions = $this->spectrumService->getAcquisitions($objectId);
        $this->locations = $this->spectrumService->getLocations($objectId);
        $this->currentLocation = $this->spectrumService->getCurrentLocation($objectId);
        $this->locationHistory = $this->spectrumService->getLocationHistory($objectId);
        $this->movementHistory = $this->spectrumService->getMovementHistory($objectId);
        $this->loansIn = $this->spectrumService->getLoansIn($objectId);
        $this->loansOut = $this->spectrumService->getLoansOut($objectId);
        $this->conditionHistory = $this->spectrumService->getConditionHistory($objectId);
        $this->conservationHistory = $this->spectrumService->getConservationHistory($objectId);
        $this->objectExits = $this->spectrumService->getObjectExits($objectId);
        $this->deaccessions = $this->spectrumService->getDeaccessions($objectId);
        $this->valuationHistory = $this->spectrumService->getValuationHistory($objectId);
        $this->currentValuation = $this->spectrumService->getCurrentValuation($objectId);
        $this->auditLog = $this->spectrumService->getAuditLog($objectId);
        $this->summary = $this->spectrumService->getObjectSummary($objectId);

        // Set title
        $objectId = (int)$this->resource->id;
		$this->objectEntry = $this->spectrumService->getObjectEntries($objectId);
        if (1 > strlen($title)) {
            $title = $this->context->i18n->__('Untitled');
        }
        $this->response->setTitle($this->context->i18n->__('Spectrum Procedures - %1%', ['%1%' => $title]));
    }
}
