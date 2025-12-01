<?php

declare(strict_types=1);

use AtomExtensions\Database\DatabaseBootstrap;

/**
 * GRAP Report Export Action.
 *
 * Exports GRAP data to CSV format.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class grapReportExportAction extends sfAction
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

        $grapService = new GrapService($culture, $userId);

        // Get filter parameters
        $filters = $request->getParameter('filters', []);

        // Get all data (no pagination for export)
        $data = $grapService->search($filters);

        // Set headers for CSV download
        $filename = 'grap_report_' . date('Y-m-d_His') . '.csv';

        $this->response->setHttpHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHttpHeader(
            'Content-Disposition',
            'attachment; filename="' . $filename . '"'
        );

        // Output CSV
        $output = fopen('php://output', 'w');

        // CSV headers
        $headers = [
            'ID',
            'Identifier',
            'Title',
            'Asset Class',
            'Recognition Status',
            'Recognition Reason',
            'Measurement Basis',
            'Initial Recognition Date',
            'Initial Recognition Value',
            'Carrying Amount',
            'Acquisition Method',
            'Cost of Acquisition',
            'Fair Value at Acquisition',
            'Last Revaluation Date',
            'Revaluation Amount',
            'Valuation Method',
            'Depreciation Policy',
            'Useful Life (Years)',
            'Residual Value',
            'Depreciation Method',
            'Accumulated Depreciation',
            'Impairment Loss',
            'GL Account Code',
            'Cost Center',
            'Fund Source',
            'Heritage Significance Rating',
            'Insurance Required',
            'Insurance Actual',
            'Created At',
            'Updated At',
        ];

        fputcsv($output, $headers);

        // Data rows
        foreach ($data as $row) {
            $csvRow = [
                $row->id,
                $row->identifier ?? '',
                $row->title ?? '',
                $row->asset_class ?? '',
                $row->recognition_status ?? '',
                $row->recognition_status_reason ?? '',
                $row->measurement_basis ?? '',
                $row->initial_recognition_date ?? '',
                $row->initial_recognition_value ?? '',
                $this->calculateCarryingAmount($row),
                $row->acquisition_method_grap ?? '',
                $row->cost_of_acquisition ?? '',
                $row->fair_value_at_acquisition ?? '',
                $row->last_revaluation_date ?? '',
                $row->revaluation_amount ?? '',
                $row->valuation_method ?? '',
                $row->depreciation_policy ?? '',
                $row->useful_life_years ?? '',
                $row->residual_value ?? '',
                $row->depreciation_method ?? '',
                $row->accumulated_depreciation ?? '',
                $row->impairment_loss_amount ?? '',
                $row->gl_account_code ?? '',
                $row->cost_center ?? '',
                $row->fund_source ?? '',
                $row->heritage_significance_rating ?? '',
                $row->insurance_coverage_required ?? '',
                $row->insurance_coverage_actual ?? '',
                $row->created_at ?? '',
                $row->updated_at ?? '',
            ];

            fputcsv($output, $csvRow);
        }

        fclose($output);

        return sfView::NONE;
    }

    /**
     * Calculate carrying amount for a row.
     */
    protected function calculateCarryingAmount(object $row): float
    {
        $baseValue = 'revaluation_model' === $row->measurement_basis && $row->revaluation_amount
            ? (float) $row->revaluation_amount
            : (float) ($row->initial_recognition_value ?? 0);

        return $baseValue - (float) ($row->accumulated_depreciation ?? 0);
    }
}
