<?php

/*
 * GRAP Export Report Action
 * Exports GRAP data to CSV for Excel/reporting
 */

use Illuminate\Database\Capsule\Manager as DB;

class sfMuseumPlugingrapExportReportAction extends sfAction
{
    public function execute($request)
    {
        // Check user has permission
        if (!QubitAcl::check(QubitInformationObject::getRoot(), 'read')) {
            QubitAcl::forwardUnauthorized();
        }

        try {
            // Get all GRAP data with object information
            $results = DB::table('grap_heritage_asset as g')
                ->join('information_object as i', 'g.object_id', '=', 'i.id')
                ->join('information_object_i18n as ii', function($join) {
                    $join->on('i.id', '=', 'ii.id')
                         ->where('ii.culture', '=', 'en');
                })
                ->leftJoin('slug as s', function($join) {
                    $join->on('i.id', '=', 's.object_id')
                         ->where('s.slug', '!=', '');
                })
                ->select(
                    'g.*',
                    'ii.title',
                    's.slug'
                )
                ->orderBy('ii.title', 'asc')
                ->get();

            // Set headers for CSV download
            $filename = 'grap_compliance_report_' . date('Y-m-d_His') . '.csv';
            
            $this->getResponse()->setHttpHeader('Content-Type', 'text/csv; charset=utf-8');
            $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            // Open output stream
            $output = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write CSV headers
            fputcsv($output, [
                'Object ID',
                'Title',
                'Slug',
                'Recognition Status',
                'Recognition Reason',
                'Measurement Basis',
                'Initial Recognition Date',
                'Initial Recognition Value',
                'Asset Class',
                'Acquisition Method',
                'Cost of Acquisition',
                'Fair Value at Acquisition',
                'GL Account Code',
                'Cost Center',
                'Fund Source',
                'Depreciation Policy',
                'Depreciation Method',
                'Useful Life (Years)',
                'Residual Value',
                'Accumulated Depreciation',
                'Net Book Value',
                'Last Revaluation Date',
                'Revaluation Amount',
                'Valuation Method',
                'Valuer Credentials',
                'Heritage Significance Rating',
                'Restrictions on Use/Disposal',
                'Conservation Commitments',
                'Insurance Coverage Required',
                'Insurance Coverage Actual',
                'Insurance Gap',
            ]);
            
            // Write data rows
            foreach ($results as $row) {
                $netBookValue = ($row->initial_recognition_value ?? 0) - ($row->accumulated_depreciation ?? 0);
                $insuranceGap = ($row->insurance_coverage_required ?? 0) - ($row->insurance_coverage_actual ?? 0);
                
                fputcsv($output, [
                    $row->object_id,
                    $row->title,
                    $row->slug,
                    $row->recognition_status,
                    $row->recognition_status_reason,
                    $row->measurement_basis,
                    $row->initial_recognition_date,
                    $row->initial_recognition_value,
                    $row->asset_class,
                    $row->acquisition_method_grap,
                    $row->cost_of_acquisition,
                    $row->fair_value_at_acquisition,
                    $row->gl_account_code,
                    $row->cost_center,
                    $row->fund_source,
                    $row->depreciation_policy,
                    $row->depreciation_method,
                    $row->useful_life_years,
                    $row->residual_value,
                    $row->accumulated_depreciation,
                    $netBookValue,
                    $row->last_revaluation_date,
                    $row->revaluation_amount,
                    $row->valuation_method,
                    $row->valuer_credentials,
                    $row->heritage_significance_rating,
                    $row->restrictions_use_disposal,
                    $row->conservation_commitments,
                    $row->insurance_coverage_required,
                    $row->insurance_coverage_actual,
                    $insuranceGap,
                ]);
            }
            
            fclose($output);
            
            // Stop Symfony from rendering a template
            return sfView::NONE;

        } catch (Exception $e) {
            error_log('GRAP Export Error: ' . $e->getMessage());
            $this->getUser()->setFlash('error', 'Error exporting GRAP data.');
            $this->redirect(['module' => 'sfMuseumPlugin', 'action' => 'grapDashboard']);
        }
    }
}