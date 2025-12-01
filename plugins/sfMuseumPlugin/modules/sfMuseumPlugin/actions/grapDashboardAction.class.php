<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * GRAP Financial Compliance Dashboard
 * Provides overview of GRAP 103 compliance across all heritage assets
 */

use Illuminate\Database\Capsule\Manager as DB;

class sfMuseumPlugingrapDashboardAction extends sfAction
{
    public function execute($request)
    {
        // Check user has permission
        if (!QubitAcl::check(QubitInformationObject::getRoot(), 'read')) {
            QubitAcl::forwardUnauthorized();
        }

        try {
            // Get overall statistics
            $this->stats = $this->getComplianceStats();
            
            // Get recognition breakdown
            $this->recognitionBreakdown = $this->getRecognitionBreakdown();
            
            // Get asset class breakdown
            $this->assetClassBreakdown = $this->getAssetClassBreakdown();
            
            // Get depreciation status
            $this->depreciationStats = $this->getDepreciationStats();
            
            // Get revaluation status
            $this->revaluationStats = $this->getRevaluationStats();
            
            // Get insurance coverage analysis
            $this->insuranceStats = $this->getInsuranceStats();
            
            // Get acquisition method breakdown
            $this->acquisitionStats = $this->getAcquisitionStats();
            
            // Get recent additions
            $this->recentObjects = $this->getRecentGrapObjects(10);
            
            // Get incomplete records
            $this->incompleteRecords = $this->getIncompleteRecords(10);
            
            // Get top valued assets
            $this->topValuedAssets = $this->getTopValuedAssets(10);
            
            // Get objects needing revaluation (over 3 years old)
            $this->needsRevaluation = $this->getObjectsNeedingRevaluation(10);

        } catch (Exception $e) {
            error_log('GRAP Dashboard Error: ' . $e->getMessage());
            $this->getUser()->setFlash('error', 'Error loading dashboard data.');
        }
    }

    /**
     * Get overall GRAP compliance statistics
     */
    protected function getComplianceStats()
    {
        $stats = [
            'total_objects' => 0,
            'recognised' => 0,
            'not_recognised' => 0,
            'with_revaluation' => 0,
            'heritage_assets' => 0,
            'operational_assets' => 0,
            'total_value' => 0,
            'total_accumulated_depreciation' => 0,
            'net_book_value' => 0,
        ];

        $stats['total_objects'] = DB::table('grap_heritage_asset')->count();
        
        $stats['recognised'] = DB::table('grap_heritage_asset')
            ->where('recognition_status', 'recognised')
            ->count();
        
        $stats['not_recognised'] = DB::table('grap_heritage_asset')
            ->where('recognition_status', 'not_recognised')
            ->count();
        
        $stats['with_revaluation'] = DB::table('grap_heritage_asset')
            ->whereNotNull('last_revaluation_date')
            ->count();
        
        $stats['heritage_assets'] = DB::table('grap_heritage_asset')
            ->where('asset_class', 'heritage_asset')
            ->count();
        
        $stats['operational_assets'] = DB::table('grap_heritage_asset')
            ->where('asset_class', 'operational_asset')
            ->count();

        $totals = DB::table('grap_heritage_asset')
            ->selectRaw('
                COALESCE(SUM(initial_recognition_value), 0) as total_value,
                COALESCE(SUM(accumulated_depreciation), 0) as total_depreciation
            ')
            ->first();

        if ($totals) {
            $stats['total_value'] = $totals->total_value;
            $stats['total_accumulated_depreciation'] = $totals->total_depreciation;
            $stats['net_book_value'] = $stats['total_value'] - $stats['total_accumulated_depreciation'];
        }

        return $stats;
    }

    /**
     * Get recognition status breakdown
     */
    protected function getRecognitionBreakdown()
    {
        return DB::table('grap_heritage_asset')
            ->select('recognition_status', DB::raw('COUNT(*) as count'))
            ->groupBy('recognition_status')
            ->get()
            ->keyBy('recognition_status')
            ->toArray();
    }

    /**
     * Get asset class breakdown
     */
    protected function getAssetClassBreakdown()
    {
        return DB::table('grap_heritage_asset')
            ->select('asset_class', DB::raw('COUNT(*) as count'), 
                     DB::raw('COALESCE(SUM(initial_recognition_value), 0) as total_value'))
            ->groupBy('asset_class')
            ->get()
            ->keyBy('asset_class')
            ->toArray();
    }

    /**
     * Get depreciation statistics
     */
    protected function getDepreciationStats()
    {
        return DB::table('grap_heritage_asset')
            ->select('depreciation_policy', DB::raw('COUNT(*) as count'),
                     DB::raw('COALESCE(SUM(accumulated_depreciation), 0) as total_depreciation'))
            ->groupBy('depreciation_policy')
            ->get()
            ->keyBy('depreciation_policy')
            ->toArray();
    }

    /**
     * Get revaluation statistics
     */
    protected function getRevaluationStats()
    {
        $stats = [];
        
        $stats['with_revaluation'] = DB::table('grap_heritage_asset')
            ->whereNotNull('last_revaluation_date')
            ->count();
        
        $stats['without_revaluation'] = DB::table('grap_heritage_asset')
            ->whereNull('last_revaluation_date')
            ->count();
        
        $stats['total_revaluation_amount'] = DB::table('grap_heritage_asset')
            ->sum('revaluation_amount') ?? 0;
        
        // Assets revalued in last year
        $stats['revalued_last_year'] = DB::table('grap_heritage_asset')
            ->where('last_revaluation_date', '>=', date('Y-m-d', strtotime('-1 year')))
            ->count();
        
        return $stats;
    }

    /**
     * Get insurance coverage statistics
     */
    protected function getInsuranceStats()
    {
        $stats = [];
        
        $coverage = DB::table('grap_heritage_asset')
            ->selectRaw('
                COALESCE(SUM(insurance_coverage_required), 0) as total_required,
                COALESCE(SUM(insurance_coverage_actual), 0) as total_actual
            ')
            ->first();
        
        $stats['total_required'] = $coverage->total_required ?? 0;
        $stats['total_actual'] = $coverage->total_actual ?? 0;
        $stats['gap'] = $stats['total_required'] - $stats['total_actual'];
        
        $stats['underinsured_count'] = DB::table('grap_heritage_asset')
            ->whereRaw('insurance_coverage_actual < insurance_coverage_required')
            ->count();
        
        $stats['fully_insured_count'] = DB::table('grap_heritage_asset')
            ->whereRaw('insurance_coverage_actual >= insurance_coverage_required')
            ->count();
        
        return $stats;
    }

    /**
     * Get acquisition method breakdown
     */
    protected function getAcquisitionStats()
    {
        return DB::table('grap_heritage_asset')
            ->select('acquisition_method_grap', DB::raw('COUNT(*) as count'))
            ->groupBy('acquisition_method_grap')
            ->get()
            ->keyBy('acquisition_method_grap')
            ->toArray();
    }

    /**
     * Get recently added GRAP objects
     */
    protected function getRecentGrapObjects($limit = 10)
    {
        $results = DB::table('grap_heritage_asset as g')
            ->join('information_object as i', 'g.object_id', '=', 'i.id')
            ->join('information_object_i18n as ii', function($join) {
                $join->on('i.id', '=', 'ii.id')
                     ->where('ii.culture', '=', 'en');
            })
            ->select('g.object_id', 'ii.title', 'g.initial_recognition_value', 
                     'g.asset_class', 'g.recognition_status')
            ->orderBy('g.id', 'desc')
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * Get incomplete GRAP records
     */
    protected function getIncompleteRecords($limit = 10)
    {
        $results = DB::table('grap_heritage_asset as g')
            ->join('information_object as i', 'g.object_id', '=', 'i.id')
            ->join('information_object_i18n as ii', function($join) {
                $join->on('i.id', '=', 'ii.id')
                     ->where('ii.culture', '=', 'en');
            })
            ->select('g.object_id', 'ii.title', 'g.recognition_status', 
                     'g.measurement_basis', 'g.asset_class')
            ->where(function($query) {
                $query->whereNull('recognition_status')
                      ->orWhereNull('measurement_basis')
                      ->orWhereNull('asset_class');
            })
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * Get top valued assets
     */
    protected function getTopValuedAssets($limit = 10)
    {
        $results = DB::table('grap_heritage_asset as g')
            ->join('information_object as i', 'g.object_id', '=', 'i.id')
            ->join('information_object_i18n as ii', function($join) {
                $join->on('i.id', '=', 'ii.id')
                     ->where('ii.culture', '=', 'en');
            })
            ->select('g.object_id', 'ii.title', 'g.initial_recognition_value',
                     'g.accumulated_depreciation', 'g.asset_class')
            ->whereNotNull('g.initial_recognition_value')
            ->orderBy('g.initial_recognition_value', 'desc')
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * Get objects needing revaluation (over 3 years old)
     */
    protected function getObjectsNeedingRevaluation($limit = 10)
    {
        $threeYearsAgo = date('Y-m-d', strtotime('-3 years'));
        
        $results = DB::table('grap_heritage_asset as g')
            ->join('information_object as i', 'g.object_id', '=', 'i.id')
            ->join('information_object_i18n as ii', function($join) {
                $join->on('i.id', '=', 'ii.id')
                     ->where('ii.culture', '=', 'en');
            })
            ->select('g.object_id', 'ii.title', 'g.last_revaluation_date',
                     'g.initial_recognition_value', 'g.measurement_basis')
            ->where(function($query) use ($threeYearsAgo) {
                $query->whereNull('last_revaluation_date')
                      ->orWhere('last_revaluation_date', '<', $threeYearsAgo);
            })
            ->where('measurement_basis', 'revaluation_model')
            ->orderBy('g.last_revaluation_date', 'asc')
            ->limit($limit)
            ->get();

        return $results;
    }
}