<?php
/**
 * GRAP Financial Accounting Fieldset.
 *
 * Add this to sfMuseumPlugin/modules/sfMuseumPlugin/templates/editSuccess.php
 * Place it after the other CCO fieldsets (around line 180).
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
?>



<!-- GRAP Financial Accounting (GRAP 103) -->
<fieldset class="collapsible" id="grapAccountingArea">
    <legend><?php echo __('Generally Recognised Accounting Practice (GRAP) Financial Accounting'); ?></legend>
    
    <div class="grap-section">
        <h4><?php echo __('Recognition & Measurement'); ?></h4>
        
        <?php echo render_field($form->grap_recognition_status
            ->label(__('Recognition status'))
            ->help(__('Whether the asset is recognised in the financial statements')), null); ?>
        
        <?php echo render_field($form->grap_recognition_status_reason
            ->label(__('Reason (if not recognised)'))
            ->help(__('Explain why the asset is not recognised')), null); ?>
        
        <?php echo render_field($form->grap_measurement_basis
            ->label(__('Measurement basis'))
            ->help(__('Cost model or revaluation model')), null); ?>
        
        <?php echo render_field($form->grap_initial_recognition_date
            ->label(__('Initial recognition date'))
            ->help(__('Date when asset was first recognised')), null); ?>
        
        <?php echo render_field($form->grap_initial_recognition_value
            ->label(__('Initial recognition value (R)'))
            ->help(__('Value at initial recognition')), null); ?>
        
        <?php echo render_field($form->grap_asset_class
            ->label(__('Asset class'))
            ->help(__('Classification of the asset')), null); ?>
    </div>
    
    <div class="grap-section">
        <h4><?php echo __('Acquisition'); ?></h4>
        
        <?php echo render_field($form->grap_acquisition_method
            ->label(__('Acquisition method'))
            ->help(__('How the asset was acquired')), null); ?>
        
        <?php echo render_field($form->grap_cost_of_acquisition
            ->label(__('Cost of acquisition (R)'))
            ->help(__('Purchase price or exchange value')), null); ?>
        
        <?php echo render_field($form->grap_fair_value_at_acquisition
            ->label(__('Fair value at acquisition (R)'))
            ->help(__('Required for donated assets')), null); ?>
    </div>
    
    <div class="grap-section">
        <h4><?php echo __('Financial Classification'); ?></h4>
        
        <?php echo render_field($form->grap_gl_account_code
            ->label(__('GL account code'))
            ->help(__('General ledger account code')), null); ?>
        
        <?php echo render_field($form->grap_cost_center
            ->label(__('Cost center'))
            ->help(__('Cost center for accounting')), null); ?>
        
        <?php echo render_field($form->grap_fund_source
            ->label(__('Fund source'))
            ->help(__('Source of funding for acquisition')), null); ?>
    </div>
</fieldset>

<fieldset class="collapsible" id="grapDepreciationArea">
    <legend><?php echo __('GRAP Depreciation'); ?></legend>
    
    <?php echo render_field($form->grap_depreciation_policy
        ->label(__('Depreciation policy'))
        ->help(__('Whether the asset is depreciated')), null); ?>
    
    <?php echo render_field($form->grap_depreciation_method
        ->label(__('Depreciation method'))
        ->help(__('Method used to calculate depreciation')), null); ?>
    
    <?php echo render_field($form->grap_useful_life_years
        ->label(__('Useful life (years)'))
        ->help(__('Expected useful life in years')), null); ?>
    
    <?php echo render_field($form->grap_residual_value
        ->label(__('Residual value (R)'))
        ->help(__('Expected value at end of useful life')), null); ?>
    
    <?php echo render_field($form->grap_accumulated_depreciation
        ->label(__('Accumulated depreciation (R)'))
        ->help(__('Total depreciation to date')), null); ?>
</fieldset>

<fieldset class="collapsible" id="grapRevaluationArea">
    <legend><?php echo __('GRAP Revaluation'); ?></legend>
    
    <?php echo render_field($form->grap_last_revaluation_date
        ->label(__('Last revaluation date'))
        ->help(__('Date of most recent revaluation')), null); ?>
    
    <?php echo render_field($form->grap_revaluation_amount
        ->label(__('Revaluation amount (R)'))
        ->help(__('Current revalued amount')), null); ?>
    
    <?php echo render_field($form->grap_valuation_method
        ->label(__('Valuation method'))
        ->help(__('Method used for valuation')), null); ?>
    
    <?php echo render_field($form->grap_valuer_credentials
        ->label(__('Valuer credentials'))
        ->help(__('Qualifications of the valuer')), null); ?>
</fieldset>

<fieldset class="collapsible" id="grapDisclosureArea">
    <legend><?php echo __('GRAP 103 Disclosure'); ?></legend>
    
    <?php echo render_field($form->grap_heritage_significance_rating
        ->label(__('Heritage significance rating'))
        ->help(__('Rating of heritage significance')), null); ?>
    
    <?php echo render_field($form->grap_restrictions_use_disposal
        ->label(__('Restrictions on use/disposal'))
        ->help(__('Any restrictions on how the asset can be used or disposed')), null, ['class' => 'resizable']); ?>
    
    <?php echo render_field($form->grap_conservation_commitments
        ->label(__('Conservation commitments'))
        ->help(__('Ongoing conservation obligations')), null, ['class' => 'resizable']); ?>
    
    <?php echo render_field($form->grap_insurance_coverage_required
        ->label(__('Insurance coverage required (R)'))
        ->help(__('Required insurance coverage amount')), null); ?>
    
    <?php echo render_field($form->grap_insurance_coverage_actual
        ->label(__('Insurance coverage actual (R)'))
        ->help(__('Current insurance coverage amount')), null); ?>
</fieldset>