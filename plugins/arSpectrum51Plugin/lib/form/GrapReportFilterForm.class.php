<?php

class GrapReportFilterForm extends sfForm
{
    public function configure()
    {
        $this->setWidgets(array(
          'asset_class' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => 'All',
              'heritage_asset' => 'Heritage Asset',
              'operational_asset' => 'Operational Asset',
              'investment' => 'Investment'
            )
          )),

          'recognition_status' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => 'All',
              'recognised' => 'Recognised',
              'not_recognised' => 'Not Recognised'
            )
          )),

          'measurement_basis' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => 'All',
              'cost_model' => 'Cost Model',
              'revaluation_model' => 'Revaluation Model'
            )
          )),

          'date_from' => new sfWidgetFormDate(),
          'date_to' => new sfWidgetFormDate(),

          'cost_center' => new sfWidgetFormInputText(),

          'compliance_status' => new sfWidgetFormSelect(array(
            'choices' => array(
              '' => 'All',
              'compliant' => 'GRAP Compliant Only',
              'non_compliant' => 'Non-Compliant Only'
            )
          ))
        ));

        $this->setValidators(array(
          'asset_class' => new sfValidatorChoice(array(
            'choices' => array('', 'heritage_asset', 'operational_asset', 'investment'),
            'required' => false
          )),
          'recognition_status' => new sfValidatorChoice(array(
            'choices' => array('', 'recognised', 'not_recognised'),
            'required' => false
          )),
          'measurement_basis' => new sfValidatorChoice(array(
            'choices' => array('', 'cost_model', 'revaluation_model'),
            'required' => false
          )),
          'date_from' => new sfValidatorDate(array('required' => false)),
          'date_to' => new sfValidatorDate(array('required' => false)),
          'cost_center' => new sfValidatorString(array('required' => false)),
          'compliance_status' => new sfValidatorChoice(array(
            'choices' => array('', 'compliant', 'non_compliant'),
            'required' => false
          ))
        ));

        $this->widgetSchema->setNameFormat('filters[%s]');
    }
}
