<?php

class SpectrumGrapDataForm extends sfForm
{
    public function configure()
    {
        $this->setWidgets(array(
          'recognition_status' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => '',
              'recognised' => 'Recognised',
              'not_recognised' => 'Not Recognised'
            )
          )),

          'recognition_status_reason' => new sfWidgetFormInput(),

          'measurement_basis' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => '',
              'cost_model' => 'Cost Model',
              'revaluation_model' => 'Revaluation Model'
            )
          )),

          'initial_recognition_date' => new sfWidgetFormDate(),

          'initial_recognition_value' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'carrying_amount' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01',
            'readonly' => 'readonly'
          )),

          'acquisition_method_grap' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => '',
              'purchase' => 'Purchase',
              'donation' => 'Donation',
              'transfer' => 'Transfer',
              'exchange' => 'Exchange',
              'other' => 'Other'
            )
          )),

          'cost_of_acquisition' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'fair_value_at_acquisition' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'donor_restrictions' => new sfWidgetFormTextarea(),

          'last_revaluation_date' => new sfWidgetFormDate(),

          'revaluation_amount' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'valuer_credentials' => new sfWidgetFormInput(),

          'valuation_method' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => '',
              'market_approach' => 'Market Approach',
              'cost_approach' => 'Cost Approach',
              'income_approach' => 'Income Approach'
            )
          )),

          'revaluation_frequency' => new sfWidgetFormInput(),

          'depreciation_policy' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => '',
              'not_depreciated' => 'Not Depreciated',
              'depreciated' => 'Depreciated'
            )
          )),

          'useful_life_years' => new sfWidgetFormInput(array(), array(
            'type' => 'number'
          )),

          'residual_value' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'depreciation_method' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => '',
              'straight_line' => 'Straight Line',
              'reducing_balance' => 'Reducing Balance'
            )
          )),

          'accumulated_depreciation' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'last_impairment_assessment_date' => new sfWidgetFormDate(),

          'impairment_indicators' => new sfWidgetFormInputCheckbox(),

          'impairment_indicators_details' => new sfWidgetFormTextarea(),

          'impairment_loss_amount' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'derecognition_date' => new sfWidgetFormDate(),

          'derecognition_reason' => new sfWidgetFormInput(),

          'derecognition_value' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'gain_loss_on_derecognition' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'asset_class' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => '',
              'heritage_asset' => 'Heritage Asset',
              'operational_asset' => 'Operational Asset',
              'investment' => 'Investment'
            )
          )),

          'gl_account_code' => new sfWidgetFormInput(),

          'cost_center' => new sfWidgetFormInput(),

          'fund_source' => new sfWidgetFormInput(),

          'restrictions_use_disposal' => new sfWidgetFormTextarea(),

          'heritage_significance_rating' => new sfWidgetFormInput(),

          'conservation_commitments' => new sfWidgetFormTextarea(),

          'insurance_coverage_required' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),

          'insurance_coverage_actual' => new sfWidgetFormInput(array(), array(
            'type' => 'number',
            'step' => '0.01'
          )),
        ));

        $this->setValidators(array(
          'recognition_status' => new sfValidatorChoice(array(
            'choices' => array('recognised', 'not_recognised'),
            'required' => false
          )),

          'recognition_status_reason' => new sfValidatorString(array('required' => false)),

          'measurement_basis' => new sfValidatorChoice(array(
            'choices' => array('cost_model', 'revaluation_model'),
            'required' => false
          )),

          'initial_recognition_date' => new sfValidatorDate(array('required' => false)),

          'initial_recognition_value' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'carrying_amount' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'acquisition_method_grap' => new sfValidatorChoice(array(
            'choices' => array('purchase', 'donation', 'transfer', 'exchange', 'other'),
            'required' => false
          )),

          'cost_of_acquisition' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'fair_value_at_acquisition' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'donor_restrictions' => new sfValidatorString(array('required' => false)),

          'last_revaluation_date' => new sfValidatorDate(array('required' => false)),

          'revaluation_amount' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'valuer_credentials' => new sfValidatorString(array('required' => false)),

          'valuation_method' => new sfValidatorChoice(array(
            'choices' => array('market_approach', 'cost_approach', 'income_approach'),
            'required' => false
          )),

          'revaluation_frequency' => new sfValidatorString(array('required' => false)),

          'depreciation_policy' => new sfValidatorChoice(array(
            'choices' => array('not_depreciated', 'depreciated'),
            'required' => false
          )),

          'useful_life_years' => new sfValidatorInteger(array(
            'required' => false,
            'min' => 1
          )),

          'residual_value' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'depreciation_method' => new sfValidatorChoice(array(
            'choices' => array('straight_line', 'reducing_balance'),
            'required' => false
          )),

          'accumulated_depreciation' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'last_impairment_assessment_date' => new sfValidatorDate(array('required' => false)),

          'impairment_indicators' => new sfValidatorBoolean(array('required' => false)),

          'impairment_indicators_details' => new sfValidatorString(array('required' => false)),

          'impairment_loss_amount' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'derecognition_date' => new sfValidatorDate(array('required' => false)),

          'derecognition_reason' => new sfValidatorString(array('required' => false)),

          'derecognition_value' => new sfValidatorNumber(array('required' => false)),

          'gain_loss_on_derecognition' => new sfValidatorNumber(array('required' => false)),

          'asset_class' => new sfValidatorChoice(array(
            'choices' => array('heritage_asset', 'operational_asset', 'investment'),
            'required' => false
          )),

          'gl_account_code' => new sfValidatorString(array('required' => false)),

          'cost_center' => new sfValidatorString(array('required' => false)),

          'fund_source' => new sfValidatorString(array('required' => false)),

          'restrictions_use_disposal' => new sfValidatorString(array('required' => false)),

          'heritage_significance_rating' => new sfValidatorString(array('required' => false)),

          'conservation_commitments' => new sfValidatorString(array('required' => false)),

          'insurance_coverage_required' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),

          'insurance_coverage_actual' => new sfValidatorNumber(array(
            'required' => false,
            'min' => 0
          )),
        ));

        $this->widgetSchema->setNameFormat('grap_data[%s]');

        $this->validatorSchema->setPostValidator(
            new sfValidatorCallback(array('callback' => array($this, 'validateGrapLogic')))
        );
    }

    public function validateGrapLogic($validator, $values)
    {
        // Custom validation: donated items need fair value
        if ($values['acquisition_method_grap'] === 'donation' && empty($values['fair_value_at_acquisition'])) {
            throw new sfValidatorError($validator, 'Donated items require fair value at acquisition');
        }

        // Revaluation model needs revaluation data
        if ($values['measurement_basis'] === 'revaluation_model' && empty($values['last_revaluation_date'])) {
            throw new sfValidatorError($validator, 'Revaluation model requires revaluation date');
        }

        // Not recognised items need a reason
        if ($values['recognition_status'] === 'not_recognised' && empty($values['recognition_status_reason'])) {
            throw new sfValidatorError($validator, 'Non-recognised items require a reason');
        }

        return $values;
    }
}
