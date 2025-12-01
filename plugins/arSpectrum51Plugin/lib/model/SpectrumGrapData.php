<?php

class SpectrumGrapData extends BaseSpectrumGrapData
{
    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * Calculate the current carrying amount based on measurement basis
     *
     * @return float
     */
    public function calculateCarryingAmount()
    {
        if ($this->measurement_basis === 'revaluation_model' && $this->revaluation_amount) {
            return $this->revaluation_amount - ($this->accumulated_depreciation ?? 0);
        }

        return $this->initial_recognition_value - ($this->accumulated_depreciation ?? 0);
    }

    /**
     * Check if the item is GRAP compliant
     *
     * @return bool
     */
    public function isGrapCompliant()
    {
        // Basic validation for GRAP compliance
        $required = [
          'recognition_status',
          'measurement_basis',
          'initial_recognition_date',
          'initial_recognition_value',
          'acquisition_method_grap'
        ];

        foreach ($required as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // Check for specific validation rules
        $issues = $this->getGrapComplianceIssues();

        return count($issues) === 0;
    }

    /**
     * Get a list of GRAP compliance issues
     *
     * @return array
     */
    public function getGrapComplianceIssues()
    {
        $issues = [];

        if (empty($this->recognition_status)) {
            $issues[] = 'Recognition status not set';
        }

        if (empty($this->measurement_basis)) {
            $issues[] = 'Measurement basis not set';
        }

        if (empty($this->initial_recognition_date)) {
            $issues[] = 'Initial recognition date not set';
        }

        if (empty($this->initial_recognition_value)) {
            $issues[] = 'Initial recognition value not set';
        }

        if (empty($this->acquisition_method_grap)) {
            $issues[] = 'Acquisition method not set';
        }

        if ($this->acquisition_method_grap === 'donation' && empty($this->fair_value_at_acquisition)) {
            $issues[] = 'Donated items require fair value at acquisition';
        }

        if ($this->measurement_basis === 'revaluation_model' && empty($this->last_revaluation_date)) {
            $issues[] = 'Revaluation model requires revaluation date';
        }

        if ($this->recognition_status === 'not_recognised' && empty($this->recognition_status_reason)) {
            $issues[] = 'Non-recognised items require a reason';
        }

        if (empty($this->asset_class)) {
            $issues[] = 'Asset class not set';
        }

        if (empty($this->gl_account_code)) {
            $issues[] = 'GL account code not set';
        }

        return $issues;
    }

    /**
     * Calculate annual depreciation amount
     *
     * @return float
     */
    public function calculateAnnualDepreciation()
    {
        if ($this->depreciation_policy !== 'depreciated') {
            return 0;
        }

        if (empty($this->useful_life_years) || $this->useful_life_years <= 0) {
            return 0;
        }

        $depreciableAmount = $this->initial_recognition_value - ($this->residual_value ?? 0);

        if ($this->depreciation_method === 'straight_line' || empty($this->depreciation_method)) {
            return $depreciableAmount / $this->useful_life_years;
        }

        // Add other depreciation methods as needed
        return 0;
    }
}
