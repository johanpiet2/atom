<?php

class arSpectrumPluginDeleteAction extends sfAction
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

        $procedure = $request->getParameter('procedure');
        $id = $request->getParameter('id');

        if (!$id || !$procedure) {
            $this->forward404();
        }

        // Initialize service
        $culture = $this->getUser()->getCulture();
        $userId = $this->getUser()->getAttribute('user_id');
        $spectrumService = new SpectrumService($culture, $userId);

        // Delete based on procedure type
        $deleteMethodMap = [
            'entry' => 'deleteObjectEntry',
            'acquisition' => 'deleteAcquisition',
            'location' => 'deleteLocation',
            'movement' => 'deleteMovement',
            'loan_in' => 'deleteLoanIn',
            'loan_out' => 'deleteLoanOut',
            'condition' => 'deleteConditionCheck',
            'conservation' => 'deleteConservation',
            'exit' => 'deleteObjectExit',
            'deaccession' => 'deleteDeaccession',
            'valuation' => 'deleteValuation',
        ];

        $method = $deleteMethodMap[$procedure] ?? null;

        if ($method && method_exists($spectrumService, $method)) {
            $spectrumService->{$method}($id);
        }

        $this->redirect('spectrum_index', ['slug' => $this->resource->slug]);
    }
}
