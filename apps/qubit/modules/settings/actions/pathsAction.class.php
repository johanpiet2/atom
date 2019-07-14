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

/**
 * Security
 *
 * @package    AccesstoMemory
 * @subpackage settings
 * @author     
 */

class SettingsPathsAction extends sfAction
{
  public function execute($request)
  {
    $this->pathsForm = new SettingsPathsForm;

    // Handle POST data (form submit)
    if ($request->isMethod('post'))
    {
      QubitCache::getInstance()->removePattern('settings:i18n:*');

      // Handle paths form submission
      if (null !== $request->paths)
      {
        $this->pathsForm->bind($request->paths);
        if ($this->pathsForm->isValid())
        {
          // Do update and redirect to avoid repeat submit wackiness
          $this->updatePathsSettings($this->pathsForm);
          $this->redirect('settings/paths');
        }
      }
    }

    $this->populatePathsForm($this->pathsForm);
  }

  /**
   * Populate the security form
   */
  protected function populatePathsForm()
  {
    $bulk = QubitSetting::getByNameAndScope('bulk', 'upload_download_paths');
    $bulk_index = QubitSetting::getByNameAndScope('bulk_index', 'upload_download_paths');
    $bulk_optimize_index = QubitSetting::getByNameAndScope('bulk_optimize_index', 'upload_download_paths');
    $bulk_rename = QubitSetting::getByNameAndScope('bulk_rename', 'upload_download_paths');
    $bulk_verbose = QubitSetting::getByNameAndScope('bulk_verbose', 'upload_download_paths');
    $bulk_output = QubitSetting::getByNameAndScope('bulk_output', 'upload_download_paths');
    $bulk_skip_duplicates = QubitSetting::getByNameAndScope('bulk_skip_duplicates', 'upload_download_paths');
    $outputPath = QubitSetting::getByNameAndScope('output_path', 'upload_download_paths');
    $outputFilename = QubitSetting::getByNameAndScope('output_filename', 'upload_download_paths');
    $bulk_delete = QubitSetting::getByNameAndScope('bulk_delete', 'upload_download_paths');
    $log = QubitSetting::getByNameAndScope('log', 'upload_download_paths');
    $logPath = QubitSetting::getByNameAndScope('log_path', 'upload_download_paths');
    $logFilename = QubitSetting::getByNameAndScope('log_filename', 'upload_download_paths');
    $move = QubitSetting::getByNameAndScope('move', 'upload_download_paths');
    $movePath = QubitSetting::getByNameAndScope('move_path', 'upload_download_paths');
    $uploadPath = QubitSetting::getByNameAndScope('upload_path', 'upload_download_paths');
    $downloadPath = QubitSetting::getByNameAndScope('download_path', 'upload_download_paths');
    $unpublishPath = QubitSetting::getByNameAndScope('unpublish_path', 'upload_download_paths');
    $publishPath = QubitSetting::getByNameAndScope('publish_path', 'upload_download_paths');
    $updatePath = QubitSetting::getByNameAndScope('update_path', 'upload_download_paths');
    $mqPath = QubitSetting::getByNameAndScope('mq_path', 'upload_download_paths');

    $this->pathsForm->setDefaults(array(
      'bulk' => (isset($bulk)) ? intval($bulk->getValue(array('sourceCulture'=>true))) : 1,
      'bulk_index' => (isset($bulk_index)) ? intval($bulk_index->getValue(array('sourceCulture'=>true))) : 0,
      'bulk_optimize_index' => (isset($bulk_optimize_index)) ? intval($bulk_optimize_index->getValue(array('sourceCulture'=>true))) : 0,
      'bulk_rename' => (isset($bulk_rename)) ? intval($bulk_rename->getValue(array('sourceCulture'=>true))) : 1,
      'bulk_verbose' => (isset($bulk_verbose)) ? intval($bulk_verbose->getValue(array('sourceCulture'=>true))) : 1,
      'bulk_output' => (isset($bulk_output)) ? intval($bulk_output->getValue(array('sourceCulture'=>true))) : 1,
      'bulk_skip_duplicates' => (isset($bulk_skip_duplicates)) ? intval($bulk_skip_duplicates->getValue(array('sourceCulture'=>true))) : 1,
      'output_path' => (isset($outputPath)) ? $outputPath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/uploads/error/",
      'output_filename' => (isset($outputFilename)) ? $outputFilename->getValue(array('sourceCulture'=>true)) : "Output.log",
      'bulk_delete' => (isset($bulk_delete)) ? intval($bulk_delete->getValue(array('sourceCulture'=>true))) : 0,
      'log' => (isset($log)) ? intval($log->getValue(array('sourceCulture'=>true))) : 1,
      'log_path' => (isset($logPath)) ? $logPath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/log/",
      'log_filename' => (isset($logFilename)) ? $logFilename->getValue(array('sourceCulture'=>true)) : "NAAIRS.log",
      'move' => (isset($move)) ? intval($move->getValue(array('sourceCulture'=>true))) : 1,
      'move_path' => (isset($movePath)) ? $movePath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/uploads/imported/",
      'upload_path' => (isset($uploadPath)) ? $uploadPath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/uploads/upload/",
      'download_path' => (isset($downloadPath)) ?  $downloadPath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/uploads/download/",
      'unpublish_path' => (isset($unpublishPath)) ?  $unpublishPath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/uploads/unpublish/",
      'publish_path' => (isset($publishPath)) ?  $publishPath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/uploads/publish/",
      'update_path' => (isset($updatePath)) ?  $updatePath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/uploads/update/",
      'mq_path' => (isset($mqPath)) ?  $mqPath->getValue(array('sourceCulture'=>true)) : "/var/www/html/atom/uploads/mq/"
    ));
  }

  /**
   * Update the LDAP settings
   */
  protected function updatePathsSettings()
  {
    $thisForm = $this->pathsForm;
    // Bulk Yes/No
    $setting = QubitSetting::getByNameAndScope('bulk', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('bulk'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "bulk";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('bulk'), array('sourceCulture' => true));
		$setting->save();
    }

    // Bulk index Yes/No
    $setting = QubitSetting::getByNameAndScope('bulk_index', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('bulk_index'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "bulk_index";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('bulk_index'), array('sourceCulture' => true));
		$setting->save();
    }
    // Bulk optimize index Yes/No
    $setting = QubitSetting::getByNameAndScope('bulk_optimize_index', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('bulk_optimize_index'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "bulk_optimize_index";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('bulk_optimize_index'), array('sourceCulture' => true));
		$setting->save();
    }

    // Bulk rename Yes/No
    $setting = QubitSetting::getByNameAndScope('bulk_rename', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('bulk_rename'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "bulk_rename";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('bulk_rename'), array('sourceCulture' => true));
		$setting->save();
    }

    // Bulk delete Yes/No
    $setting = QubitSetting::getByNameAndScope('bulk_delete', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('bulk_delete'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "bulk_delete";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('bulk_delete'), array('sourceCulture' => true));
		$setting->save();
    }

    // Bulk verbose Yes/No
    $setting = QubitSetting::getByNameAndScope('bulk_verbose', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('bulk_verbose'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "bulk_verbose";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('bulk_verbose'), array('sourceCulture' => true));
		$setting->save();
    }

    // Bulk Output File Yes/No
    $setting = QubitSetting::getByNameAndScope('bulk_output', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('bulk_output'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "bulk_output";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('bulk_output'), array('sourceCulture' => true));
		$setting->save();
    }

    // Bulk Output File Yes/No
    $setting = QubitSetting::getByNameAndScope('bulk_skip_duplicates', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('bulk_skip_duplicates'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "bulk_skip_duplicates";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('bulk_skip_duplicates'), array('sourceCulture' => true));
		$setting->save();
    }

    // Output Path
    $setting = QubitSetting::getByNameAndScope('output_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('output_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "output_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('output_path'), array('sourceCulture' => true));
		$setting->save();
    }

    // Output filename
    $setting = QubitSetting::getByNameAndScope('output_filename', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('output_filename'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "output_filename";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('output_filename'), array('sourceCulture' => true));
		$setting->save();
    }

    // Log Path
    $setting = QubitSetting::getByNameAndScope('log_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('log_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "log_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('log_path'), array('sourceCulture' => true));
		$setting->save();
    }

    // Log
    $setting = QubitSetting::getByNameAndScope('log', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('log'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "log";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('log'), array('sourceCulture' => true));
		$setting->save();
    }

    // Log Filename
    $setting = QubitSetting::getByNameAndScope('log_filename', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('log_filename'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "log_filename";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('log_filename'), array('sourceCulture' => true));
		$setting->save();
    }

    // Move Yes/No
    $setting = QubitSetting::getByNameAndScope('move', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('move'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "move";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('move'), array('sourceCulture' => true));
		$setting->save();
    }
    // Move Path
    $setting = QubitSetting::getByNameAndScope('move_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('move_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "move_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('move_path'), array('sourceCulture' => true));
		$setting->save();
    }

    $setting = QubitSetting::getByNameAndScope('upload_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('upload_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting;
		$setting->name = "upload_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('upload_path'), array('sourceCulture' => true));
		$setting->save();
    }

    // Download Path
    $setting = QubitSetting::getByNameAndScope('download_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('download_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting; 	
		$setting->name = "download_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('download_path'), array('sourceCulture' => true));
		$setting->save();
    }

    // Unpublish Path
    $setting = QubitSetting::getByNameAndScope('unpublish_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('unpublish_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting; 	
		$setting->name = "unpublish_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('unpublish_path'), array('sourceCulture' => true));
		$setting->save();
    }

    // Publish Path
    $setting = QubitSetting::getByNameAndScope('publish_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('publish_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting; 	
		$setting->name = "publish_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('publish_path'), array('sourceCulture' => true));
		$setting->save();
    }

    // Update Path
    $setting = QubitSetting::getByNameAndScope('update_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('update_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting; 	
		$setting->name = "update_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('update_path'), array('sourceCulture' => true));
		$setting->save();
    }

    // MQ Path
    $setting = QubitSetting::getByNameAndScope('mq_path', 'upload_download_paths');
    if (isset($setting))
    {
		// Force sourceCulture update to prevent discrepency in settings between cultures
		$setting->setValue($thisForm->getValue('mq_path'), array('sourceCulture' => true));
		$setting->scope = "upload_download_paths";
		$setting->save();
    }
    else
    {
    	$setting = new QubitSetting; 	
		$setting->name = "mq_path";
		$setting->scope = "upload_download_paths";
		$setting->setValue($thisForm->getValue('mq_path'), array('sourceCulture' => true));
		$setting->save();
    }

    return $this;
  }

}
