<?php

declare(strict_types=1);

namespace AtomExtensions\Extensions\MetadataExtraction\Controllers;

/**
 * Settings Controller.
 *
 * Handles metadata extraction settings.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class SettingsController
{
    /**
     * Handle settings page.
     */
    public function handleSettings($request, $action): void
    {
        $action->response->setTitle('Metadata extraction settings - ' . $action->response->getTitle());

        // Create the form
        $action->form = new \sfForm();

        // Add all settings fields
        $this->addFormFields($action->form);

        // Load current values
        $this->loadFormDefaults($action->form);

        // Handle POST
        if ($request->isMethod('post')) {
            $action->form->bind($request->getPostParameters());

            if ($action->form->isValid()) {
                $this->saveSettings($action->form);
                $action->getUser()->setFlash('notice', 'Metadata extraction settings saved.');
                $action->redirect('settings/metadataExtraction');
            }
        }
    }

    /**
     * Add form fields.
     */
    private function addFormFields(\sfForm $form): void
    {
        // Boolean checkboxes
        $checkboxSettings = [
            'metadata_extraction_enabled',
            'extract_exif',
            'extract_iptc',
            'extract_xmp',
            'overwrite_title',
            'overwrite_description',
            'auto_generate_keywords',
            'extract_gps_coordinates',
            'add_technical_metadata',
        ];

        foreach ($checkboxSettings as $name) {
            $form->setWidget($name, new \sfWidgetFormInputCheckbox());
            $form->setValidator($name, new \sfValidatorBoolean());
        }

        // Target field dropdown
        $form->setWidget(
            'technical_metadata_target_field',
            new \sfWidgetFormSelect([
                'choices' => [
                    'physicalCharacteristics' => 'Physical characteristics',
                    'scopeAndContent' => 'Scope and content',
                    'appraisal' => 'Appraisal, destruction and scheduling',
                    'archivistsNotes' => 'Archivist\'s notes',
                    'generalNote' => 'General note',
                ],
            ])
        );

        $form->setValidator(
            'technical_metadata_target_field',
            new \sfValidatorChoice([
                'choices' => [
                    'physicalCharacteristics',
                    'scopeAndContent',
                    'appraisal',
                    'archivistsNotes',
                    'generalNote',
                ],
            ])
        );
    }

    /**
     * Load default values from database.
     */
    private function loadFormDefaults(\sfForm $form): void
    {
        $defaults = [
            'metadata_extraction_enabled' => true,
            'extract_exif' => true,
            'extract_iptc' => true,
            'extract_xmp' => true,
            'overwrite_title' => false,
            'overwrite_description' => false,
            'auto_generate_keywords' => true,
            'extract_gps_coordinates' => true,
            'add_technical_metadata' => true,
            'technical_metadata_target_field' => 'physicalCharacteristics',
        ];

        foreach ($defaults as $name => $default) {
            $setting = \QubitSetting::getByNameAndScope($name, 'metadata_extraction');

            if ($setting) {
                $value = $setting->getValue(['sourceCulture' => true]);

                // Convert string boolean to actual boolean
                if ($value === '1') {
                    $value = true;
                } elseif ($value === '0') {
                    $value = false;
                }

                $form->setDefault($name, $value);
            } else {
                $form->setDefault($name, $default);
            }
        }
    }

    /**
     * Save settings to database.
     */
    private function saveSettings(\sfForm $form): void
    {
        foreach ($form as $field) {
            $name = $field->getName();
            $value = $form->getValue($name);

            // Convert boolean to string
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }

            // Find or create setting
            $setting = \QubitSetting::getByNameAndScope($name, 'metadata_extraction');

            if (!$setting) {
                $setting = new \QubitSetting();
                $setting->setName($name);
                $setting->setScope('metadata_extraction');
            }

            $setting->setValue($value, ['sourceCulture' => true]);
            $setting->save();
        }
    }
}
