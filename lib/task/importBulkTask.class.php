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
class importBulkTask extends arBaseTask
{
    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('folder', sfCommandArgument::REQUIRED, 'The import folder or file')
        ));
        
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'qubit'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'cli'),
            new sfCommandOption('index', '-i', sfCommandOption::PARAMETER_NONE, 'Set to enable indexing on imported objects'),
            new sfCommandOption('taxonomy', null, sfCommandOption::PARAMETER_OPTIONAL, 'Set the taxonomy id to insert the SKOS concepts into'),
            new sfCommandOption('schema', null, sfCommandOption::PARAMETER_OPTIONAL, 'Schema to use if importing a CSV file'),
            new sfCommandOption('output', null, sfCommandOption::PARAMETER_OPTIONAL, 'Filename to output results in CSV format'),
            new sfCommandOption('verbose', '-v', sfCommandOption::PARAMETER_NONE, 'Verbose output'),
            //jjp SITA 
            //Move and rename or delete imported file - Also log actions
            new sfCommandOption('rename', '-r', sfCommandOption::PARAMETER_OPTIONAL, 'Rename and move imported file'),
            new sfCommandOption('delete', '-d', sfCommandOption::PARAMETER_NONE, 'Delete imported file'),
            new sfCommandOption('user', '-u', sfCommandOption::PARAMETER_NONE, 'Import User file')
        ));
        
        $this->namespace           = 'import';
        $this->name                = 'bulk';
        $this->briefDescription    = 'Bulk import multiple XML/CSV files at once';
        $this->detailedDescription = <<<EOF
Bulk import multiple XML/CSV files at once
EOF;
    }
    protected function execute($arguments = array(), $options = array())
    {
        parent::execute($arguments, $options);
        $timer = new QubitTimer; // overall timing
        sfContext::createInstance($this->configuration);
        $bulkImport = 0;
        $uploadPath = 0;
        $bulk_index = 0;
        $bulk_optimize_index = 0;
        $bulk_rename = 0;
        $movePath = 0;
        $bulk_verbose = 0;
        $bulk_output = 0;
        $bulk_delete = 0;
        $bulkImport = QubitSetting::getByName('bulk');
        if ($bulkImport != null) {
            if ($bulkImport == "1") {
                $uploadPath = QubitSetting::getByName('upload_path');
                if ($uploadPath == null) {
                    throw new sfException(sfContext::getInstance()->i18n->__("No upload path/folder defined. Contact support/administrator"));
                } else {
                    $arguments['folder'] = $uploadPath;
                }

                $bulk_rename = QubitSetting::getByName('bulk_rename');
                if ($bulk_rename == "0") {
                    $options['rename'] = false;
                } else {
                    $options['rename'] = true;
                }
                $movePath = QubitSetting::getByName('move_path');
                if ($movePath == null) {
                    throw new sfException(sfContext::getInstance()->i18n->__("No move path/folder defined. Contact support/administrator"));
                } else {
                    $arguments['rename'] = $movePath;
                }
                $bulk_delete = QubitSetting::getByName('bulk_delete');
                if ($bulk_delete != null) {
                    if ($bulk_delete == "0") {
                        $options['delete'] = false;
                    } else {
                        $options['delete'] = true;
                    }
                } else {
                    $options['delete'] = false;
                }
                $bulk_verbose = QubitSetting::getByName('bulk_verbose');
                if ($bulk_verbose != null) {
                    if ($bulk_verbose == "0") {
                        $options['verbose'] = false;
                    } else {
                        $options['verbose'] = true;
                    }
                } else {
                    $options['verbose'] = false;
                }
                $bulk_output = QubitSetting::getByName('bulk_output');
                if ($bulk_output != null) {
                    if ($bulk_output == "1") {
                        $bulk_output = true;
                        $outputFilename = QubitSetting::getByName('output_filename');
                        if ($outputFilename == null) {
                            throw new sfException(sfContext::getInstance()->i18n->__("No Output Filename defined. Contact support/administrator"));
                        }
                        if (rtrim($outputFilename) == "") {
                            throw new sfException(sfContext::getInstance()->i18n->__("No Output Filename defined. Contact support/administrator"));
                        } else {
                            $options['output'] = $outputFilename;
                        }
                        $outputPath = QubitSetting::getByName('output_path');
                        if ($outputPath == null) {
                            throw new sfException(sfContext::getInstance()->i18n->__("No Output path defined. Contact support/administrator"));
                        }
                        if (rtrim($outputPath) == "") {
                            throw new sfException(sfContext::getInstance()->i18n->__("No Output path defined. Contact support/administrator"));
                        }
                    }
                }
            }
        }
        
        $bulk_index = QubitSetting::getByName('bulk_index');
        if ($bulk_index != null) {
            if ($bulk_index == "0") {
                $options['index'] = false;
            } else {
                $options['index'] = true;
            }
        } else {
            $options['index'] = false;
        }
        
        $bulk_optimize_index = QubitSetting::getByName('bulk_optimize_index');
        if ($bulk_optimize_index != null) {
            if ($bulk_optimize_index == "0") {
                $options['noindex'] = false;
            } else {
                $options['noindex'] = true;
            }
        } else {
            $options['noindex'] = $bulk_optimize_index;
        }

        //Log options
        QubitXMLImport::addLog("bulk= " . $bulkImport, "", get_class($this), false);
        QubitXMLImport::addLog("Upload Path= " . $arguments['folder'], "", get_class($this), false);
        QubitXMLImport::addLog("Index= " . $options['index'], "", get_class($this), false);
        QubitXMLImport::addLog("Optimize Index= " . $options['noindex'], "", get_class($this), false);
        QubitXMLImport::addLog("Rename/move= " . $options['rename'], "", get_class($this), false);
        QubitXMLImport::addLog("Move Path= " . $movePath, "", get_class($this), false);
        QubitXMLImport::addLog("Verbose= " . $options['verbose'], "", get_class($this), false);
        QubitXMLImport::addLog("Output= " . $bulk_output, "", get_class($this), false);
        QubitXMLImport::addLog("Output File= " . $options['output'], "", get_class($this), false);
        QubitXMLImport::addLog("Output File Path= " . $outputPath, "", get_class($this), false);
        QubitXMLImport::addLog("Delete= " . $bulk_delete, "", get_class($this), false);
        if (empty($arguments['folder']) || !file_exists($arguments['folder'])) {
            throw new sfException('You must specify a valid import folder or file');
        }
 
        // Set indexing preference
        if (!$options['index']) {
            QubitSearch::disable();
        }
        else
        {
            QubitSearch::enable();
        }
        
        if (is_dir($arguments['folder'])) {
            // Recurse into the import folder
            $files = $this->dir_tree(rtrim($arguments['folder'], '/'));
        } else {
            $files = array($arguments['folder']);
        }
        // TODO: Add some colour
        $this->log("Possible import file/s " . count($files) . " from " . $arguments['folder'] . " (importing 100 at a time...), (indexing is " . ($options['index'] ? "ENABLED" : "DISABLED") . ") ...\n");
        QubitXMLImport::addLog("Possible import file/s " . count($files) . " from " . $arguments['folder'] . " (importing 100 at a time...), (indexing is " . ($options['index'] ? "ENABLED" : "DISABLED") . ") ...\n", "", get_class($this), true);
        $count = 0;
        $total = count($files);
        foreach ($files as $file) {
            $start = microtime(true);
            // Choose import type based on file extension, eg. csv, xml
            if ('csv' == pathinfo($file, PATHINFO_EXTENSION)) {
                $importer = new QubitCsvImport;
                $importer->import($file, $options);
            } elseif ('xml' == pathinfo($file, PATHINFO_EXTENSION)) {
                $options['strictXmlParsing'] = false;
                if ($options['user']) {
                    $options['user'] = true;
                } else {
                    $options['user'] = false;
                }
                QubitXMLImport::addLog("Importing: " . $file, "", get_class($this), true);
                $importer = new QubitXmlImport;
                $importer->import($file, array('strictXmlParsing' => false), strtolower(pathinfo($file, PATHINFO_EXTENSION)));
                QubitXMLImport::addLog("Import completed: " . $file, "", get_class($this), true);
            } else {
                QubitXMLImport::addLog("Import not of supported type: " . $file, "", get_class($this), true);
                // Move on to the next file
                continue;
            }
            if (isset($options['completed-dir']) && !empty($importer)) {
                $path_info = pathinfo($file);
                $move_source = $path_info['dirname'] . '/' . $path_info['basename'];
                $move_destination = $options['completed-dir'] . '/' . $path_info['basename'];
                rename($file, $move_destination);
            }
            if (!$options['verbose']) {
                print '.';
            }
            if ($importer->hasErrors()) {
                foreach ($importer->getErrors() as $error) {
                    $this->log('Error (' . $file . '): ' . $error);
                }
            }
            //print '.';
            // Try to free up memory
            unset($importer);
            $count++;
            if ($count == 100) {
	            // Try to free up memory
	            unset($importer);
                break;
            }
            $split = round(microtime(true) - $start, 2);
            // Store details if output is specified
            if ($options['output']) {
                $rows[] = array($file, $split . 's', memory_get_usage() . 'B');
            }
            if ($options['verbose']) {
                $this->log(basename($file) . " imported (" . round($split, 2) . " s) (" . $count . "/" . $total . ")");
            }
        } 
        // jjp SITA
        // Added options to delete the imported file
        if ($options['delete']) {
            unlink($file);
            $this->log(basename($file) . " deleted " . $file . " on " . date('c'));
        }
        // jjp SITA
        // Added options to move the imported file to another location
		try
		{
		   if ($bulkImport == "1") {
		        if ($options['rename']) {
		            $name = basename($file) . date("Y-m-d His") . ".loaded";
		            $result=rename($file, $movePath."/".$name );
            		if (!$result) 
            		{
            			echo "ERROR renaming ".$file." -> ".$movePath.$name."\n";
		                $this->log("Error: ---- " . $file . " moved to " . $movePath.$name);
		            }
		            else
		            {
		            	$this->log(basename($file) . " moved to (" . $movePath . " and renamed to " . $name . " on " . date('c'));
		        	}
		        }
		    } else {
		        if ($options['rename']) {
		            $name = basename($file) . date("Y-m-d His") . ".loaded";
		            rename($file, $options['rename'] . "/$name");
		            if (!rename($file, $options['rename'] . "/$name")) {
		                $error = error_get_last();
		                $this->log("Error:" . $error . " ---- " . basename($file) . " moved to (" . $options['rename'] . " and renamed to " . $name . " on " . date('c'));
		            }
		            $this->log(basename($file) . " moved to (" . $options['rename'] . " and renamed to " . $name . " on " . date('c'));
		        }
		    }
	    }

		// catch normal PHP Exceptions
		catch(Exception $e)
		{
            $this->log(basename($file) . " moved to (" . $options['rename'] . " and renamed to " . $name . " on " . date('c') . " with error: " . $e);
		}

        // Create/open output file if specified
        if ($options['output']) {
            $fh = fopen($options['output'], 'w+');
            fputcsv($fh, array('File', 'Time elapsed (secs)', 'Memory used'));
            foreach ($rows as $row) {
                fputcsv($fh, $row);
            }
            fputcsv($fh, array()); // Blank row to separate our summary info
            fputcsv($fh, array('Total time elapsed:', $timer->elapsed() . 's'));
            fputcsv($fh, array('Peak memory usage:', round(memory_get_peak_usage() / 1048576, 2) . 'MB'));
            fclose($fh);
        }
        // Optimize index if enabled
        if (!$options['noindex']) {
            QubitSearch::getInstance()->optimize();
        }
        $this->log("\nImported " . $count . " XML/CSV files in " . $timer->elapsed() . " s. " . memory_get_peak_usage() . " bytes used.");
    }
    
    protected function dir_tree($dir)
    {
        $path = '';
        $stack[] = $dir;
        while ($stack) {
            $thisdir = array_pop($stack);
            if ($dircont = scandir($thisdir)) {
                $i = 0;
                while (isset($dircont[$i])) {
                    if ($dircont[$i] !== '.' && $dircont[$i] !== '..' && !preg_match('/^\..*/', $dircont[$i])) {
                        $current_file = "{$thisdir}/{$dircont[$i]}";
                        if (is_file($current_file)) {
                            $path[] = "{$thisdir}/{$dircont[$i]}";
                        } elseif (is_dir($current_file)) {
                            $stack[] = $current_file;
                        }
                    }
                    $i++;
                    if ($i > 101)
                    {
                    	break;
                	}
                }
            }
        }
        return $path;
    }
}

