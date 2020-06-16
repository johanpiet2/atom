<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

ProjectConfiguration::getActive()->loadHelpers('I18N');

/**
 * Global form definition for settings module - with validation.
 *
 * @package    AccesstoMemory
 * @subpackage settings
 */
class SettingsPathsForm extends sfForm
{
  public function configure()
  {
    // Build widgets
    $this->setWidgets(array(
      'bulk' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'bulk_index' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'bulk_optimize_index' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'bulk_rename' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'bulk_verbose' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'bulk_delete' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'bulk_output' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'bulk_skip_duplicates' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'output_path' => new sfWidgetFormInput,
      'output_filename' => new sfWidgetFormInput,
      'log' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'log_path' => new sfWidgetFormInput,
      'log_filename' => new sfWidgetFormInput,
      'move' => new sfWidgetFormSelectRadio(array('choices'=>array(1=>'yes', 0=>'no')), array('class'=>'radio')),
      'move_path' => new sfWidgetFormInput,
      'upload_path' => new sfWidgetFormInput,
      'download_path' => new sfWidgetFormInput,
      'publish_path' => new sfWidgetFormInput,
      'unpublish_path' => new sfWidgetFormInput,
      'update_path' => new sfWidgetFormInput,
      'mq_path' => new sfWidgetFormInput
    ));

    // Add labels
    $this->widgetSchema->setLabels(array(
      'bulk' => __('Bulk Import'),
      'bulk_index' => __('Index (Bulk Import)'),
      'bulk_optimize_index' => __('Optimize Index (Bulk Import)'),
      'bulk_rename' => __('Rename (Bulk Import)'),
      'bulk_verbose' => __('Verbose (Bulk Import)'),
      'bulk_delete' => __('Delete Imported File (Bulk Import)'),
      'bulk_output' => __('Create Output File (Bulk Import)'),
      'bulk_skip_duplicates' => __('Skip Duplicates (Bulk Import)'),
      'output_path' => __('Output to Path'),
      'output_filename' => __('Output Filename'),
      'log' => __('Log'),
      'log_path' => __('Log to Path'),
      'log_filename' => __('Log to Filename'),
      'move' => __('Move'),
      'move_path' => __('Move to Path'),
      'upload_path' => __('Upload Path'),
      'download_path' => __('Download Path'),
      'publish_path' => __('Publish Path'),
      'unpublish_path' => __('Unpublish Path'),
      'update_path' => __('Updates Path'),
      'mq_path' => __('MQ Path')
    ));

    // Add helper text
     $this->widgetSchema->setHelps(array(
      'bulk' => __('When set to &quot;yes&quot;, this system will use this path settings with Bulk import'),
      'bulk_index' => __('When set to &quot;yes&quot;, this system will index/not index with Bulk import'),
      'bulk_optimize_index' => __('When set to &quot;yes&quot;, this system will optimize index with Bulk import'),
      'bulk_rename' => __('When set to &quot;yes&quot;, this system will rename and move the file to &quot;Move&quot; path with Bulk import'),
      'bulk_verbose' => __('When set to &quot;yes&quot;, this system will display more information with Bulk import'),
      'bulk_delete' => __('When set to &quot;yes&quot;, this system will delete imported file with Bulk import'),
      'bulk_output' => __('When set to &quot;yes&quot;, this system will produce a output file with Bulk import'),
      'bulk_skip_duplicates' => __('When set to &quot;yes&quot;, this system will skip Duplicates with same Identifier and Repository, with Bulk import'),
      'output_path' => __('Folder where Output file will be created'),
      'output_filename' => __('Filename of Output file created'),
      'log' => __('When set to &quot;yes&quot;, this system will log imported items'),
      'log_path' => __('Folder where log file will be created'),
      'log_filename' => __('Filename of Log file created'),
      'move' => __('When set to &quot;yes&quot;, this system will move imported files to the &quot;Move to path&quot; folder'),
      'move_path' => __('Folder where uploaded files will be moved to'),
      'upload_path' => __('Folder where upload items from Web/External system is located'),
      'download_path' => __('Folder where download items to Web/External system is located'),
      'publish_path' => __('Folder where items to Publish to Web/External system is located'),
      'unpublish_path' => __('Folder where items to remove from Web/External system is located'),
      'update_path' => __('Folder where items that is update on Web/External system is located'),
      'mq_path' => __('Folder where items that is send to MQ folder on Web/External system is located')
    ));

    $this->validatorSchema['bulk'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['bulk_index'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['bulk_optimize_index'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['bulk_rename'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['bulk_verbose'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['bulk_delete'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['bulk_output'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['bulk_skip_duplicates'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['output_path'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['output_filename'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['log'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['log_path'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['log_filename'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['move'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['move_path'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['upload_path'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['download_path'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['publish_path'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['unpublish_path'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['update_path'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['mq_path'] = new sfValidatorString(array('required' => false));

    // Set decorator
    $decorator = new QubitWidgetFormSchemaFormatterList($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('list', $decorator);
    $this->widgetSchema->setFormFormatterName('list');

    // Set wrapper text for Paths settings
    $this->widgetSchema->setNameFormat('paths[%s]');
  }
}
