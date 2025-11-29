<?php

/**
 * Spectrum Edit Action.
 *
 * Handles adding and editing Spectrum procedures.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class arSpectrumPluginEditAction extends sfAction
{
    protected $spectrumService;
    protected $procedureId;

    public function execute($request)
    {
		
        $this->resource = QubitInformationObject::getBySlug($request->slug);

        if (!$this->resource) {
            $this->forward404();
        }

        $culture = $this->getUser()->getCulture();
        $userId = $this->getUser()->getAttribute('user_id');

        $this->spectrumService = new SpectrumService($culture, $userId);

        $this->procedureType = $request->getParameter('procedure');
        $this->procedureId = $request->getParameter('procedure_id');
        // Build form based on procedure type
        $this->form = $this->buildForm();

        // Load existing data if editing
        if ($this->procedureId) {
            $this->loadExistingData();
        }

        // Process form submission
        if ($request->isMethod('post')) {
            $this->form->bind($request->getPostParameters());

            if ($this->form->isValid()) {
                $this->processForm();

                $this->redirect([$this->resource, 'module' => 'arSpectrumPlugin']);
            }
        }

        // Set title
        $action = $this->procedureId ? 'Edit' : 'Add';
        $this->response->setTitle($this->context->i18n->__('%1% %2%', [
            '%1%' => $action,
            '%2%' => $this->getProcedureLabel(),
        ]));
    }

    protected function buildForm()
    {
        $form = new sfForm();

        switch ($this->procedureType) {
            case 'entry':
                $this->addEntryFields($form);
                break;

            case 'acquisition':
                $this->addAcquisitionFields($form);
                break;

            case 'location':
                $this->addLocationFields($form);
                break;

            case 'movement':
                $this->addMovementFields($form);
                break;

            case 'loan_in':
                $this->addLoanInFields($form);
                break;

            case 'loan_out':
                $this->addLoanOutFields($form);
                break;

            case 'condition':
                $this->addConditionFields($form);
                break;

            case 'conservation':
                $this->addConservationFields($form);
                break;

            case 'exit':
                $this->addExitFields($form);
                break;

            case 'deaccession':
                $this->addDeaccessionFields($form);
                break;

            case 'valuation':
                $this->addValuationFields($form);
                break;

            default:
                throw new sfException('Unknown procedure type: '.$this->procedureType);
        }

        return $form;
    }

    protected function addEntryFields($form)
    {
        $form->setWidget('entry_number', new sfWidgetFormInput());
        $form->setValidator('entry_number', new sfValidatorString(['required' => true]));

        $form->setWidget('entry_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('entry_date', new sfValidatorDate(['required' => true]));

        $entryMethods = [
            '' => '',
            'deposit' => $this->context->i18n->__('Deposit'),
            'loan_in' => $this->context->i18n->__('Loan In'),
            'purchase' => $this->context->i18n->__('Purchase'),
            'donation' => $this->context->i18n->__('Donation'),
            'found' => $this->context->i18n->__('Found on Premises'),
        ];
        $form->setWidget('entry_method', new sfWidgetFormSelect(['choices' => $entryMethods]));
        $form->setValidator('entry_method', new sfValidatorChoice(['choices' => array_keys($entryMethods), 'required' => false]));

        $form->setWidget('entry_reason', new sfWidgetFormTextarea());
        $form->setValidator('entry_reason', new sfValidatorString(['required' => false]));

        $form->setWidget('depositor_name', new sfWidgetFormInput());
        $form->setValidator('depositor_name', new sfValidatorString(['required' => false]));

        $form->setWidget('depositor_contact', new sfWidgetFormTextarea());
        $form->setValidator('depositor_contact', new sfValidatorString(['required' => false]));

        $form->setWidget('current_owner', new sfWidgetFormInput());
        $form->setValidator('current_owner', new sfValidatorString(['required' => false]));

        $form->setWidget('return_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('return_date', new sfValidatorDate(['required' => false]));

        $form->setWidget('received_by', new sfWidgetFormInput());
        $form->setValidator('received_by', new sfValidatorString(['required' => false]));

        $form->setWidget('entry_note', new sfWidgetFormTextarea());
        $form->setValidator('entry_note', new sfValidatorString(['required' => false]));
    }

    protected function addAcquisitionFields($form)
    {
        $form->setWidget('acquisition_number', new sfWidgetFormInput());
        $form->setValidator('acquisition_number', new sfValidatorString(['required' => true]));

        $form->setWidget('acquisition_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('acquisition_date', new sfValidatorDate(['required' => false]));

        $methods = [
            '' => '',
            'purchase' => $this->context->i18n->__('Purchase'),
            'gift' => $this->context->i18n->__('Gift'),
            'bequest' => $this->context->i18n->__('Bequest'),
            'exchange' => $this->context->i18n->__('Exchange'),
            'field_collection' => $this->context->i18n->__('Field Collection'),
            'transfer' => $this->context->i18n->__('Transfer'),
        ];
        $form->setWidget('acquisition_method', new sfWidgetFormSelect(['choices' => $methods]));
        $form->setValidator('acquisition_method', new sfValidatorChoice(['choices' => array_keys($methods), 'required' => false]));

        $form->setWidget('acquisition_source', new sfWidgetFormInput());
        $form->setValidator('acquisition_source', new sfValidatorString(['required' => false]));

        $form->setWidget('acquisition_reason', new sfWidgetFormTextarea());
        $form->setValidator('acquisition_reason', new sfValidatorString(['required' => false]));

        $form->setWidget('purchase_price', new sfWidgetFormInput(['type' => 'number']));
        $form->setValidator('purchase_price', new sfValidatorNumber(['required' => false]));

        $form->setWidget('price_currency', new sfWidgetFormInput());
        $form->setValidator('price_currency', new sfValidatorString(['required' => false]));
        $form->setDefault('price_currency', 'ZAR');

        $form->setWidget('accession_number', new sfWidgetFormInput());
        $form->setValidator('accession_number', new sfValidatorString(['required' => false]));

        $form->setWidget('accession_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('accession_date', new sfValidatorDate(['required' => false]));

        $form->setWidget('acquisition_note', new sfWidgetFormTextarea());
        $form->setValidator('acquisition_note', new sfValidatorString(['required' => false]));
		
        $form->setWidget('provenance_note', new sfWidgetFormTextarea());
        $form->setValidator('provenance_note', new sfValidatorString(['required' => false]));

        $form->setWidget('legal_title', new sfWidgetFormInput());
        $form->setValidator('legal_title', new sfValidatorString(['required' => false]));
				
		$form->setWidget('conditions_of_acquisition', new sfWidgetFormTextarea());
		$form->setValidator('conditions_of_acquisition', new sfValidatorString(['required' => false]));
				
    }

    protected function addLocationFields($form)
    {
        $types = [
            'current' => $this->context->i18n->__('Current Location'),
            'normal' => $this->context->i18n->__('Normal Location'),
            'temporary' => $this->context->i18n->__('Temporary Location'),
        ];
        $form->setWidget('location_type', new sfWidgetFormSelect(['choices' => $types]));
        $form->setValidator('location_type', new sfValidatorChoice(['choices' => array_keys($types), 'required' => false]));
        $form->setDefault('location_type', 'current');

        $form->setWidget('location_name', new sfWidgetFormInput());
        $form->setValidator('location_name', new sfValidatorString(['required' => true]));

        $form->setWidget('location_building', new sfWidgetFormInput());
        $form->setValidator('location_building', new sfValidatorString(['required' => false]));

        $form->setWidget('location_floor', new sfWidgetFormInput());
        $form->setValidator('location_floor', new sfValidatorString(['required' => false]));

        $form->setWidget('location_room', new sfWidgetFormInput());
        $form->setValidator('location_room', new sfValidatorString(['required' => false]));

        $form->setWidget('location_unit', new sfWidgetFormInput());
        $form->setValidator('location_unit', new sfValidatorString(['required' => false]));

        $form->setWidget('location_shelf', new sfWidgetFormInput());
        $form->setValidator('location_shelf', new sfValidatorString(['required' => false]));

        $form->setWidget('location_note', new sfWidgetFormTextarea());
        $form->setValidator('location_note', new sfValidatorString(['required' => false]));

        $form->setWidget('is_current', new sfWidgetFormInputCheckbox());
        $form->setValidator('is_current', new sfValidatorBoolean(['required' => false]));
        $form->setDefault('is_current', true);
		
		$form->setWidget('location_coordinates', new sfWidgetFormInput());
		$form->setValidator('location_coordinates', new sfValidatorString(['required' => false]));
		
		$securityLevels = [
			'' => '',
			'public' => $this->context->i18n->__('Public'),
			'restricted' => $this->context->i18n->__('Restricted'),
			'secure' => $this->context->i18n->__('Secure'),
			'high_security' => $this->context->i18n->__('High Security'),
		];
		$form->setWidget('security_level', new sfWidgetFormSelect(['choices' => $securityLevels]));
		$form->setValidator('security_level', new sfValidatorChoice(['choices' => array_keys($securityLevels), 'required' => false]));
    }

    protected function addMovementFields($form)
    {
        $form->setWidget('movement_reference', new sfWidgetFormInput());
        $form->setValidator('movement_reference', new sfValidatorString(['required' => false]));

        $form->setWidget('movement_date', new sfWidgetFormInput(['type' => 'datetime-local']));
        $form->setValidator('movement_date', new sfValidatorString(['required' => true]));

        $reasons = [
            '' => '',
            'exhibition' => $this->context->i18n->__('Exhibition'),
            'loan' => $this->context->i18n->__('Loan'),
            'conservation' => $this->context->i18n->__('Conservation'),
            'storage_reorganization' => $this->context->i18n->__('Storage Reorganization'),
            'photography' => $this->context->i18n->__('Photography'),
            'research' => $this->context->i18n->__('Research'),
        ];
        $form->setWidget('movement_reason', new sfWidgetFormSelect(['choices' => $reasons]));
        $form->setValidator('movement_reason', new sfValidatorChoice(['choices' => array_keys($reasons), 'required' => false]));

        // Load locations for dropdowns
        $locations = $this->getLocationChoices();
        $form->setWidget('location_from', new sfWidgetFormSelect(['choices' => $locations]));
        $form->setValidator('location_from', new sfValidatorInteger(['required' => false]));

        $form->setWidget('location_to', new sfWidgetFormSelect(['choices' => $locations]));
        $form->setValidator('location_to', new sfValidatorInteger(['required' => false]));

        $form->setWidget('handler_name', new sfWidgetFormInput());
        $form->setValidator('handler_name', new sfValidatorString(['required' => false]));

        $form->setWidget('movement_note', new sfWidgetFormTextarea());
        $form->setValidator('movement_note', new sfValidatorString(['required' => false]));
		
		$locations = $this->getLocationChoices();
		$form->setWidget('from_location_id', new sfWidgetFormSelect(['choices' => $locations]));
		$form->setValidator('from_location_id', new sfValidatorInteger(['required' => false]));

		$form->setWidget('to_location_id', new sfWidgetFormSelect(['choices' => $locations]));
		$form->setValidator('to_location_id', new sfValidatorInteger(['required' => false]));
		
		$methods = [
			'' => '',
			'hand_carried' => $this->context->i18n->__('Hand Carried'),
			'trolley' => $this->context->i18n->__('Trolley'),
			'vehicle' => $this->context->i18n->__('Vehicle'),
			'courier' => $this->context->i18n->__('Courier'),
			'specialist_transport' => $this->context->i18n->__('Specialist Transport'),
		];
		$form->setWidget('movement_method', new sfWidgetFormSelect(['choices' => $methods]));
		$form->setValidator('movement_method', new sfValidatorChoice(['choices' => array_keys($methods), 'required' => false]));
		
		$form->setWidget('moved_by', new sfWidgetFormInput());
		$form->setValidator('moved_by', new sfValidatorString(['required' => false]));
		
    }

    protected function addLoanInFields($form)
    {
        $form->setWidget('loan_in_number', new sfWidgetFormInput());
        $form->setValidator('loan_in_number', new sfValidatorString(['required' => true]));

        $form->setWidget('lender_name', new sfWidgetFormInput());
        $form->setValidator('lender_name', new sfValidatorString(['required' => true]));

        $form->setWidget('lender_contact', new sfWidgetFormTextarea());
        $form->setValidator('lender_contact', new sfValidatorString(['required' => false]));

        $form->setWidget('loan_in_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('loan_in_date', new sfValidatorDate(['required' => true]));

        $form->setWidget('loan_return_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('loan_return_date', new sfValidatorDate(['required' => false]));

        $purposes = [
            '' => '',
            'exhibition' => $this->context->i18n->__('Exhibition'),
            'research' => $this->context->i18n->__('Research'),
            'photography' => $this->context->i18n->__('Photography'),
            'education' => $this->context->i18n->__('Education'),
        ];
        $form->setWidget('loan_purpose', new sfWidgetFormSelect(['choices' => $purposes]));
        $form->setValidator('loan_purpose', new sfValidatorChoice(['choices' => array_keys($purposes), 'required' => false]));

        $form->setWidget('loan_conditions', new sfWidgetFormTextarea());
        $form->setValidator('loan_conditions', new sfValidatorString(['required' => false]));

        $form->setWidget('insurance_value', new sfWidgetFormInput(['type' => 'number']));
        $form->setValidator('insurance_value', new sfValidatorNumber(['required' => false]));

        $form->setWidget('insurance_currency', new sfWidgetFormInput());
        $form->setValidator('insurance_currency', new sfValidatorString(['required' => false]));
        $form->setDefault('insurance_currency', 'ZAR');

        $form->setWidget('loan_note', new sfWidgetFormTextarea());
        $form->setValidator('loan_note', new sfValidatorString(['required' => false]));
				
		$form->setWidget('loan_start_date', new sfWidgetFormInput(['type' => 'date']));
		$form->setValidator('loan_start_date', new sfValidatorDate(['required' => false]));

		$form->setWidget('loan_end_date', new sfWidgetFormInput(['type' => 'date']));
		$form->setValidator('loan_end_date', new sfValidatorDate(['required' => false]));		

		$form->setWidget('loan_in_note', new sfWidgetFormTextarea());
		$form->setValidator('loan_in_note', new sfValidatorString(['required' => false]));

    }

    protected function addLoanOutFields($form)
    {
        $form->setWidget('loan_out_number', new sfWidgetFormInput());
        $form->setValidator('loan_out_number', new sfValidatorString(['required' => true]));

        $form->setWidget('borrower_name', new sfWidgetFormInput());
        $form->setValidator('borrower_name', new sfValidatorString(['required' => true]));

        $form->setWidget('borrower_contact', new sfWidgetFormTextarea());
        $form->setValidator('borrower_contact', new sfValidatorString(['required' => false]));

        $form->setWidget('venue_name', new sfWidgetFormInput());
        $form->setValidator('venue_name', new sfValidatorString(['required' => false]));

        $form->setWidget('loan_out_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('loan_out_date', new sfValidatorDate(['required' => true]));

        $form->setWidget('loan_return_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('loan_return_date', new sfValidatorDate(['required' => false]));

        $purposes = [
            '' => '',
            'exhibition' => $this->context->i18n->__('Exhibition'),
            'research' => $this->context->i18n->__('Research'),
            'photography' => $this->context->i18n->__('Photography'),
            'education' => $this->context->i18n->__('Education'),
        ];
        $form->setWidget('loan_purpose', new sfWidgetFormSelect(['choices' => $purposes]));
        $form->setValidator('loan_purpose', new sfValidatorChoice(['choices' => array_keys($purposes), 'required' => false]));

        $form->setWidget('exhibition_title', new sfWidgetFormInput());
        $form->setValidator('exhibition_title', new sfValidatorString(['required' => false]));

        $form->setWidget('insurance_value', new sfWidgetFormInput(['type' => 'number']));
        $form->setValidator('insurance_value', new sfValidatorNumber(['required' => false]));

        $form->setWidget('courier_required', new sfWidgetFormInputCheckbox());
        $form->setValidator('courier_required', new sfValidatorBoolean(['required' => false]));

        $form->setWidget('loan_note', new sfWidgetFormTextarea());
        $form->setValidator('loan_note', new sfValidatorString(['required' => false]));
		
		$form->setWidget('loan_start_date', new sfWidgetFormInput(['type' => 'date']));
		$form->setValidator('loan_start_date', new sfValidatorDate(['required' => false]));

		$form->setWidget('loan_end_date', new sfWidgetFormInput(['type' => 'date']));
		$form->setValidator('loan_end_date', new sfValidatorDate(['required' => false]));
		
		$form->setWidget('insurance_currency', new sfWidgetFormInput());
		$form->setValidator('insurance_currency', new sfValidatorString(['required' => false]));
		$form->setDefault('insurance_currency', 'ZAR');
		
		$form->setWidget('insurance_policy', new sfWidgetFormInput());
		$form->setValidator('insurance_policy', new sfValidatorString(['required' => false]));

		$form->setWidget('loan_conditions', new sfWidgetFormTextarea());
		$form->setValidator('loan_conditions', new sfValidatorString(['required' => false]));

		$form->setWidget('loan_out_note', new sfWidgetFormTextarea());
		$form->setValidator('loan_out_note', new sfValidatorString(['required' => false]));
		
    }

    protected function addConditionFields($form)
    {
        $form->setWidget('condition_reference', new sfWidgetFormInput());
        $form->setValidator('condition_reference', new sfValidatorString(['required' => false]));

        $form->setWidget('check_date', new sfWidgetFormInput(['type' => 'datetime-local']));
        $form->setValidator('check_date', new sfValidatorString(['required' => true]));

        $reasons = [
            '' => '',
            'acquisition' => $this->context->i18n->__('Acquisition'),
            'loan_in' => $this->context->i18n->__('Loan In'),
            'loan_out' => $this->context->i18n->__('Loan Out'),
            'return' => $this->context->i18n->__('Return'),
            'routine' => $this->context->i18n->__('Routine'),
            'conservation' => $this->context->i18n->__('Conservation'),
            'damage_report' => $this->context->i18n->__('Damage Report'),
        ];
        $form->setWidget('check_reason', new sfWidgetFormSelect(['choices' => $reasons]));
        $form->setValidator('check_reason', new sfValidatorChoice(['choices' => array_keys($reasons), 'required' => false]));

        $form->setWidget('checked_by', new sfWidgetFormInput());
        $form->setValidator('checked_by', new sfValidatorString(['required' => true]));

        $conditions = [
            '' => '',
            'excellent' => $this->context->i18n->__('Excellent'),
            'good' => $this->context->i18n->__('Good'),
            'fair' => $this->context->i18n->__('Fair'),
            'poor' => $this->context->i18n->__('Poor'),
            'unacceptable' => $this->context->i18n->__('Unacceptable'),
        ];
        $form->setWidget('overall_condition', new sfWidgetFormSelect(['choices' => $conditions]));
        $form->setValidator('overall_condition', new sfValidatorChoice(['choices' => array_keys($conditions), 'required' => false]));

        $form->setWidget('condition_note', new sfWidgetFormTextarea());
        $form->setValidator('condition_note', new sfValidatorString(['required' => false]));

        $form->setWidget('recommended_treatment', new sfWidgetFormTextarea());
        $form->setValidator('recommended_treatment', new sfValidatorString(['required' => false]));

        $priorities = [
            '' => '',
            'urgent' => $this->context->i18n->__('Urgent'),
            'high' => $this->context->i18n->__('High'),
            'medium' => $this->context->i18n->__('Medium'),
            'low' => $this->context->i18n->__('Low'),
            'none' => $this->context->i18n->__('None'),
        ];
        $form->setWidget('treatment_priority', new sfWidgetFormSelect(['choices' => $priorities]));
        $form->setValidator('treatment_priority', new sfValidatorChoice(['choices' => array_keys($priorities), 'required' => false]));

        $form->setWidget('next_check_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('next_check_date', new sfValidatorDate(['required' => false]));
		
		$form->setWidget('condition_check_reference', new sfWidgetFormInput());
		$form->setValidator('condition_check_reference', new sfValidatorString(['required' => false]));
	
		$completeness = [
			'' => '',
			'complete' => $this->context->i18n->__('Complete'),
			'incomplete' => $this->context->i18n->__('Incomplete'),
			'fragmented' => $this->context->i18n->__('Fragmented'),
		];
		$form->setWidget('completeness', new sfWidgetFormSelect(['choices' => $completeness]));
		$form->setValidator('completeness', new sfValidatorChoice(['choices' => array_keys($completeness), 'required' => false]));

		$form->setWidget('condition_description', new sfWidgetFormTextarea());
		$form->setValidator('condition_description', new sfValidatorString(['required' => false]));

		$form->setWidget('hazards_noted', new sfWidgetFormTextarea());
		$form->setValidator('hazards_noted', new sfValidatorString(['required' => false]));

		$form->setWidget('recommendations', new sfWidgetFormTextarea());
		$form->setValidator('recommendations', new sfValidatorString(['required' => false]));
    }

    protected function addConservationFields($form)
    {
        $form->setWidget('conservation_reference', new sfWidgetFormInput());
        $form->setValidator('conservation_reference', new sfValidatorString(['required' => false]));

        $form->setWidget('treatment_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('treatment_date', new sfValidatorDate(['required' => true]));

        $form->setWidget('treatment_end_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('treatment_end_date', new sfValidatorDate(['required' => false]));

        $form->setWidget('conservator_name', new sfWidgetFormInput());
        $form->setValidator('conservator_name', new sfValidatorString(['required' => true]));

        $form->setWidget('conservator_organization', new sfWidgetFormInput());
        $form->setValidator('conservator_organization', new sfValidatorString(['required' => false]));

        $form->setWidget('condition_before', new sfWidgetFormTextarea());
        $form->setValidator('condition_before', new sfValidatorString(['required' => false]));

        $form->setWidget('treatment_performed', new sfWidgetFormTextarea());
        $form->setValidator('treatment_performed', new sfValidatorString(['required' => false]));

        $form->setWidget('materials_used', new sfWidgetFormTextarea());
        $form->setValidator('materials_used', new sfValidatorString(['required' => false]));

        $form->setWidget('condition_after', new sfWidgetFormTextarea());
        $form->setValidator('condition_after', new sfValidatorString(['required' => false]));

        $form->setWidget('treatment_cost', new sfWidgetFormInput(['type' => 'number']));
        $form->setValidator('treatment_cost', new sfValidatorNumber(['required' => false]));

        $form->setWidget('treatment_note', new sfWidgetFormTextarea());
        $form->setValidator('treatment_note', new sfValidatorString(['required' => false]));
		
		$types = [
			'' => '',
			'cleaning' => $this->context->i18n->__('Cleaning'),
			'stabilization' => $this->context->i18n->__('Stabilization'),
			'repair' => $this->context->i18n->__('Repair'),
			'restoration' => $this->context->i18n->__('Restoration'),
			'preventive' => $this->context->i18n->__('Preventive'),
		];
		$form->setWidget('treatment_type', new sfWidgetFormSelect(['choices' => $types]));
		$form->setValidator('treatment_type', new sfValidatorChoice(['choices' => array_keys($types), 'required' => false]));

        $form->setWidget('recommendations', new sfWidgetFormTextarea());
        $form->setValidator('recommendations', new sfValidatorString(['required' => false]));
		
        $form->setWidget('conservation_note', new sfWidgetFormTextarea());
        $form->setValidator('conservation_note', new sfValidatorString(['required' => false]));
		
    }

    protected function addExitFields($form)
    {
        $form->setWidget('exit_number', new sfWidgetFormInput());
        $form->setValidator('exit_number', new sfValidatorString(['required' => true]));

        $form->setWidget('exit_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('exit_date', new sfValidatorDate(['required' => true]));

        $reasons = [
            '' => '',
            'loan_out' => $this->context->i18n->__('Loan Out'),
            'return_to_owner' => $this->context->i18n->__('Return to Owner'),
            'deaccession' => $this->context->i18n->__('Deaccession'),
            'disposal' => $this->context->i18n->__('Disposal'),
            'transfer' => $this->context->i18n->__('Transfer'),
            'destruction' => $this->context->i18n->__('Destruction'),
        ];
        $form->setWidget('exit_reason', new sfWidgetFormSelect(['choices' => $reasons]));
        $form->setValidator('exit_reason', new sfValidatorChoice(['choices' => array_keys($reasons), 'required' => false]));

        $form->setWidget('exit_destination', new sfWidgetFormInput());
        $form->setValidator('exit_destination', new sfValidatorString(['required' => false]));

        $form->setWidget('recipient_name', new sfWidgetFormInput());
        $form->setValidator('recipient_name', new sfValidatorString(['required' => false]));

        $form->setWidget('authorization_name', new sfWidgetFormInput());
        $form->setValidator('authorization_name', new sfValidatorString(['required' => false]));

        $form->setWidget('authorization_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('authorization_date', new sfValidatorDate(['required' => false]));

        $form->setWidget('exit_note', new sfWidgetFormTextarea());
        $form->setValidator('exit_note', new sfValidatorString(['required' => false]));
    }

    protected function addDeaccessionFields($form)
    {
        $form->setWidget('deaccession_number', new sfWidgetFormInput());
        $form->setValidator('deaccession_number', new sfValidatorString(['required' => true]));

        $form->setWidget('deaccession_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('deaccession_date', new sfValidatorDate(['required' => true]));

        $form->setWidget('authorized_by', new sfWidgetFormInput());
        $form->setValidator('authorized_by', new sfValidatorString(['required' => false]));

        $form->setWidget('deaccession_reason', new sfWidgetFormTextarea());
        $form->setValidator('deaccession_reason', new sfValidatorString(['required' => false]));

        $methods = [
            '' => '',
            'transfer' => $this->context->i18n->__('Transfer'),
            'sale' => $this->context->i18n->__('Sale'),
            'exchange' => $this->context->i18n->__('Exchange'),
            'destruction' => $this->context->i18n->__('Destruction'),
            'return' => $this->context->i18n->__('Return'),
            'loss' => $this->context->i18n->__('Loss'),
        ];
        $form->setWidget('disposal_method', new sfWidgetFormSelect(['choices' => $methods]));
        $form->setValidator('disposal_method', new sfValidatorChoice(['choices' => array_keys($methods), 'required' => false]));

        $form->setWidget('disposal_recipient', new sfWidgetFormInput());
        $form->setValidator('disposal_recipient', new sfValidatorString(['required' => false]));

        $form->setWidget('disposal_price', new sfWidgetFormInput(['type' => 'number']));
        $form->setValidator('disposal_price', new sfValidatorNumber(['required' => false]));

        $form->setWidget('legal_requirements_met', new sfWidgetFormInputCheckbox());
        $form->setValidator('legal_requirements_met', new sfValidatorBoolean(['required' => false]));

        $form->setWidget('deaccession_note', new sfWidgetFormTextarea());
        $form->setValidator('deaccession_note', new sfValidatorString(['required' => false]));
    }

    protected function addValuationFields($form)
    {
        $form->setWidget('valuation_reference', new sfWidgetFormInput());
        $form->setValidator('valuation_reference', new sfValidatorString(['required' => false]));

        $form->setWidget('valuation_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('valuation_date', new sfValidatorDate(['required' => true]));

        $types = [
            '' => '',
            'insurance' => $this->context->i18n->__('Insurance'),
            'indemnity' => $this->context->i18n->__('Indemnity'),
            'auction' => $this->context->i18n->__('Auction'),
            'probate' => $this->context->i18n->__('Probate'),
            'donation' => $this->context->i18n->__('Donation'),
            'internal' => $this->context->i18n->__('Internal'),
        ];
        $form->setWidget('valuation_type', new sfWidgetFormSelect(['choices' => $types]));
        $form->setValidator('valuation_type', new sfValidatorChoice(['choices' => array_keys($types), 'required' => false]));

        $form->setWidget('valuation_amount', new sfWidgetFormInput(['type' => 'number']));
        $form->setValidator('valuation_amount', new sfValidatorNumber(['required' => true]));

        $form->setWidget('valuation_currency', new sfWidgetFormInput());
        $form->setValidator('valuation_currency', new sfValidatorString(['required' => false]));
        $form->setDefault('valuation_currency', 'ZAR');

        $form->setWidget('valuer_name', new sfWidgetFormInput());
        $form->setValidator('valuer_name', new sfValidatorString(['required' => false]));

        $form->setWidget('valuer_organization', new sfWidgetFormInput());
        $form->setValidator('valuer_organization', new sfValidatorString(['required' => false]));

        $form->setWidget('renewal_date', new sfWidgetFormInput(['type' => 'date']));
        $form->setValidator('renewal_date', new sfValidatorDate(['required' => false]));

        $form->setWidget('is_current', new sfWidgetFormInputCheckbox());
        $form->setValidator('is_current', new sfValidatorBoolean(['required' => false]));
        $form->setDefault('is_current', true);

        $form->setWidget('valuation_note', new sfWidgetFormTextarea());
        $form->setValidator('valuation_note', new sfValidatorString(['required' => false]));
    }

protected function loadExistingData()
{
    error_log("Loading data for procedure: " . $this->procedureType . ", ID: " . $this->procedureId);
    
	error_log("=== LOAD EXISTING DATA ===");
error_log("Procedure type: " . $this->procedureType);
error_log("Procedure ID: " . $this->procedureId);

    $methodMap = [
        'entry' => 'getObjectEntry',
        'acquisition' => 'getAcquisition',
        'location' => 'getLocation',
        'movement' => 'getMovement',
        'loan_in' => 'getLoanIn',
        'loan_out' => 'getLoanOut',
        'condition' => 'getConditionCheck',
        'conservation' => 'getConservation',
        'exit' => 'getObjectExit',
        'deaccession' => 'getDeaccession',
        'valuation' => 'getValuation',
    ];

	$method = $methodMap[$this->procedureType] ?? null;

	if ($method && method_exists($this->spectrumService, $method)) {
		
		$data = $this->spectrumService->$method($this->procedureId);
		if ($data) {
			error_log("Data is truthy");
		} else {
			error_log("Data is false");
			return;
		}

		if ($data) {
			foreach (get_object_vars($data) as $key => $value) {
				if ($this->form->getWidgetSchema()->offsetExists($key)) {
					$this->form->setDefault($key, $value);
				}
			}
		}
    }
}

	protected function processForm()
	{
		$data = $this->form->getValues();
		
		if ($this->procedureId) {
			// Update existing
			$updateMethodMap = [
				'entry' => 'updateObjectEntry',
				'acquisition' => 'updateAcquisition',
				'location' => 'updateLocation',
				'movement' => 'updateMovement',
				'loan_in' => 'updateLoanIn',
				'loan_out' => 'updateLoanOut',
				'condition' => 'updateConditionCheck',
				'conservation' => 'updateConservation',
				'exit' => 'updateObjectExit',
				'deaccession' => 'updateDeaccession',
				'valuation' => 'updateValuation',
			];
			
			$method = $updateMethodMap[$this->procedureType] ?? null;
			if ($method && method_exists($this->spectrumService, $method)) {
				$this->spectrumService->$method($this->procedureId, $data);
			}
		} else {
			// Create new
			$methodMap = [
				'entry' => 'createObjectEntry',
				'acquisition' => 'createAcquisition',
				'location' => 'createLocation',
				'movement' => 'createMovement',
				'loan_in' => 'createLoanIn',
				'loan_out' => 'createLoanOut',
				'condition' => 'createConditionCheck',
				'conservation' => 'createConservation',
				'exit' => 'createObjectExit',
				'deaccession' => 'createDeaccession',
				'valuation' => 'createValuation',
			];

			$method = $methodMap[$this->procedureType] ?? null;
			if ($method && method_exists($this->spectrumService, $method)) {
				$this->spectrumService->$method($this->resource->id, $data);
			}
		}
	}

    protected function getLocationChoices()
    {
        $locations = $this->spectrumService->getLocationHistory($this->resource->id);
        $choices = ['' => ''];

        foreach ($locations as $loc) {
            $choices[$loc->id] = $loc->location_name;
        }

        return $choices;
    }

    protected function getProcedureLabel()
    {
        $labels = [
            'entry' => $this->context->i18n->__('Object Entry'),
            'acquisition' => $this->context->i18n->__('Acquisition'),
            'location' => $this->context->i18n->__('Location'),
            'movement' => $this->context->i18n->__('Movement'),
            'loan_in' => $this->context->i18n->__('Loan In'),
            'loan_out' => $this->context->i18n->__('Loan Out'),
            'condition' => $this->context->i18n->__('Condition Check'),
            'conservation' => $this->context->i18n->__('Conservation'),
            'exit' => $this->context->i18n->__('Object Exit'),
            'deaccession' => $this->context->i18n->__('Deaccession'),
            'valuation' => $this->context->i18n->__('Valuation'),
        ];

        return $labels[$this->procedureType] ?? $this->procedureType;
    }

    protected function camelCase($string)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }
}
