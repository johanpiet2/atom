<?php

declare(strict_types=1);

use AtomExtensions\Database\DatabaseBootstrap;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * GRAP API Actions.
 *
 * REST API endpoints for GRAP data management.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class apiActions extends sfActions
{
    protected GrapService $grapService;

    /**
     * Pre-execute setup.
     */
    public function preExecute()
    {
        // Initialize database
        if (null === DatabaseBootstrap::getCapsule()) {
            DatabaseBootstrap::initializeFromAtom();
        }

        $culture = $this->getUser()->getCulture();
        $userId = $this->getUser()->isAuthenticated()
            ? (int) $this->getUser()->getAttribute('user_id')
            : null;

        $this->grapService = new GrapService($culture, $userId);

        // Set JSON content type
        $this->response->setContentType('application/json');
    }

    /**
     * GET /api/grap/items - List all GRAP items.
     */
    public function executeGetItems(sfWebRequest $request)
    {
        $filters = [];

        if ($request->hasParameter('asset_class')) {
            $filters['asset_class'] = $request->getParameter('asset_class');
        }

        if ($request->hasParameter('recognition_status')) {
            $filters['recognition_status'] = $request->getParameter('recognition_status');
        }

        if ($request->hasParameter('measurement_basis')) {
            $filters['measurement_basis'] = $request->getParameter('measurement_basis');
        }

        $page = (int) $request->getParameter('page', 1);
        $perPage = (int) $request->getParameter('per_page', 20);

        $results = $this->grapService->paginate($filters, $perPage, $page);

        return $this->renderJson([
            'success' => true,
            'data' => $results['items']->map(fn ($item) => (array) $item)->values()->all(),
            'pagination' => [
                'total' => $results['total'],
                'per_page' => $results['per_page'],
                'current_page' => $results['current_page'],
                'last_page' => $results['last_page'],
            ],
        ]);
    }

    /**
     * GET /api/grap/items/:id - Get single GRAP item.
     */
    public function executeGetItem(sfWebRequest $request)
    {
        $id = (int) $request->getParameter('id');
        $item = $this->grapService->getById($id);

        if (!$item) {
            $this->response->setStatusCode(404);

            return $this->renderJson([
                'success' => false,
                'error' => 'Item not found',
            ]);
        }

        // Get compliance issues
        $issues = $this->grapService->getComplianceIssues((int) $item->information_object_id);

        return $this->renderJson([
            'success' => true,
            'data' => (array) $item,
            'compliance' => [
                'is_compliant' => 0 === count($issues),
                'issues' => $issues,
            ],
        ]);
    }

    /**
     * POST /api/grap/items - Create or update GRAP item.
     */
    public function executeCreateOrUpdateItem(sfWebRequest $request)
    {
        if (!$this->getUser()->isAuthenticated()) {
            $this->response->setStatusCode(401);

            return $this->renderJson([
                'success' => false,
                'error' => 'Authentication required',
            ]);
        }

        $data = $this->getJsonRequestData($request);

        if (empty($data['information_object_id'])) {
            $this->response->setStatusCode(400);

            return $this->renderJson([
                'success' => false,
                'error' => 'information_object_id is required',
            ]);
        }

        $informationObjectId = (int) $data['information_object_id'];
        unset($data['information_object_id']);

        // Validate information object exists
        $io = DB::table('information_object')
            ->where('id', $informationObjectId)
            ->first();

        if (!$io) {
            $this->response->setStatusCode(404);

            return $this->renderJson([
                'success' => false,
                'error' => 'Information object not found',
            ]);
        }

        $id = $this->grapService->updateOrCreate($informationObjectId, $data);

        return $this->renderJson([
            'success' => true,
            'data' => [
                'id' => $id,
                'information_object_id' => $informationObjectId,
            ],
            'message' => 'GRAP data saved successfully',
        ]);
    }

    /**
     * PUT /api/grap/items/:id - Update GRAP item.
     */
    public function executeUpdateItem(sfWebRequest $request)
    {
        if (!$this->getUser()->isAuthenticated()) {
            $this->response->setStatusCode(401);

            return $this->renderJson([
                'success' => false,
                'error' => 'Authentication required',
            ]);
        }

        $id = (int) $request->getParameter('id');
        $existing = $this->grapService->getById($id);

        if (!$existing) {
            $this->response->setStatusCode(404);

            return $this->renderJson([
                'success' => false,
                'error' => 'Item not found',
            ]);
        }

        $data = $this->getJsonRequestData($request);

        // Remove fields that shouldn't be updated directly
        unset($data['id'], $data['information_object_id'], $data['created_at']);

        $success = $this->grapService->update($id, $data);

        return $this->renderJson([
            'success' => $success,
            'message' => $success ? 'GRAP data updated successfully' : 'Update failed',
        ]);
    }

    /**
     * DELETE /api/grap/items/:id - Delete GRAP item.
     */
    public function executeDeleteItem(sfWebRequest $request)
    {
        if (!$this->getUser()->isAuthenticated()) {
            $this->response->setStatusCode(401);

            return $this->renderJson([
                'success' => false,
                'error' => 'Authentication required',
            ]);
        }

        $id = (int) $request->getParameter('id');
        $existing = $this->grapService->getById($id);

        if (!$existing) {
            $this->response->setStatusCode(404);

            return $this->renderJson([
                'success' => false,
                'error' => 'Item not found',
            ]);
        }

        $success = $this->grapService->delete($id);

        return $this->renderJson([
            'success' => $success,
            'message' => $success ? 'GRAP data deleted successfully' : 'Delete failed',
        ]);
    }

    /**
     * GET /api/grap/journal-entries - Get journal entries.
     */
    public function executeGetJournalEntries(sfWebRequest $request)
    {
        $dateFrom = $request->getParameter('date_from');
        $dateTo = $request->getParameter('date_to');

        $entries = $this->grapService->getJournalEntries($dateFrom, $dateTo);

        return $this->renderJson([
            'success' => true,
            'data' => array_map(fn ($item) => (array) $item, $entries),
            'count' => count($entries),
        ]);
    }

    /**
     * GET /api/grap/balance-sheet - Get balance sheet data.
     */
    public function executeGetBalanceSheet(sfWebRequest $request)
    {
        $data = $this->grapService->getBalanceSheetData();

        return $this->renderJson([
            'success' => true,
            'data' => [
                'by_class' => array_map(fn ($item) => (array) $item, $data['by_class']),
                'totals' => $data['totals'],
            ],
        ]);
    }

    /**
     * GET /api/grap/reconciliation - Get reconciliation data.
     */
    public function executeGetReconciliation(sfWebRequest $request)
    {
        $data = $this->grapService->getReconciliationData();

        return $this->renderJson([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Parse JSON request body.
     *
     * @return array<string, mixed>
     */
    protected function getJsonRequestData(sfWebRequest $request): array
    {
        $content = $request->getContent();

        if (empty($content)) {
            return $request->getParameterHolder()->getAll();
        }

        $data = json_decode($content, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $request->getParameterHolder()->getAll();
        }

        return $data ?? [];
    }

    /**
     * Render JSON response.
     *
     * @param array<string, mixed> $data
     */
    protected function renderJson(array $data): string
    {
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return sfView::NONE;
    }
}
