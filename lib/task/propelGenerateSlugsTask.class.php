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
 * Generate missing slugs
 *
 * @package    symfony
 * @subpackage task
 * @author     David Juhasz <david@artefactual.com>
 */
class propelGenerateSlugsTask extends sfBaseTask
{
    /**
     * @see sfTask
     */
    protected function configure()
    {
        $this->addArguments(array());
        
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'cli'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel')
        ));
        
        $this->namespace        = 'propel';
        $this->name             = 'generate-slugs';
        $this->briefDescription = 'Generate slugs for all slug-less objects.';
        
        $this->detailedDescription = <<<EOF
Generate slugs for all slug-less objects.
EOF;
    }
    
    /**
     * @see sfTask
     */
    public function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $conn            = $databaseManager->getDatabase('propel')->getConnection();
        
        $tables = array(
            'actor' => 'QubitActor',
            'information_object' => 'QubitInformationObject',
            'physical_object' => 'QubitPhysicalObject',
            'term' => 'QubitTerm',
            'event' => 'QubitEvent',
            'accession' => 'QubitAccession'
        );
        
        // Create hash of slugs already in database
        $sql = "SELECT slug FROM slug ORDER BY slug";
        //    foreach ($conn->query($sql, PDO::FETCH_NUM) as $row)
        //    {
        //      $slugs[$row[0]] = true;
        //    }
        
        //SITA JJP - The PDO::FETCH_NUM fails with 5 mil+ records
        /*	try {
        $slugRows = $conn->prepare($sql);
        $slugRows->execute();
        }
        
        catch (PDOException $e) {
        print "Whoops! Something went wrong!"."\n";
        print "Query with error: ".$sql."\n";
        print "Reason given:".$e->getMessage()."\n";
        print false;
        }
        
        $countSlugs = 0;
        while ($row = $slugRows->fetch())
        {
        $countSlugs++;
        $slugs[$row[0]] = true;
        print " Existing Slugs counter=".$countSlugs." \n";
        }
        */
        // end SITA JJP
        
        foreach ($tables as $table => $classname) {
            $this->logSection('propel', "Generate $table slugs...");
            $newRows = array(); // reset
            
            switch ($table) {
                case 'actor':
                    $sql = 'SELECT base.id, i18n.authorized_form_of_name';
                    break;
                
                case 'information_object':
                    $sql = 'SELECT base.id, i18n.title';
                    break;
                
                case 'accession':
                    $sql = 'SELECT base.id, base.identifier';
                    break;
                
                default:
                    $sql = 'SELECT base.id, i18n.name';
            }
            
            $sql .= ' FROM ' . constant($classname . '::TABLE_NAME') . ' base';
            $sql .= ' INNER JOIN ' . constant($classname . 'I18n::TABLE_NAME') . ' i18n';
            $sql .= '  ON base.id = i18n.id';
            $sql .= ' LEFT JOIN ' . QubitSlug::TABLE_NAME . ' sl';
            $sql .= '  ON base.id = sl.object_id';
            $sql .= ' WHERE base.id > 3';
            $sql .= '  AND base.source_culture = i18n.culture';
            $sql .= '  AND sl.id is NULL';
            print "Query : " . $sql . "\n";
            //SITA JJP - The fetch fails with 5 mil+ records
            try {
                $slugRows = $conn->prepare($sql);
                $slugRows->execute();
            }
            
            catch (PDOException $e) {
                print "Whoops! Something went wrong!" . "\n";
                print "Query with error: " . $sql . "\n";
                print "Reason given:" . $e->getMessage() . "\n";
                print false;
            }
            
            $countSlugs = 0;
            while ($row = $slugRows->fetch()) {
                $countSlugs++;
                // Get unique slug
                if (null !== $row[1]) {
                    $slug = QubitSlug::slugify($row[1]);
                    
                    // Truncate at 250 chars
                    if (250 < strlen($slug)) {
                        $slug = substr($slug, 0, 250);
                    }
                    
                    $count  = 0;
                    $suffix = '';
                     
                    // use to much memory JJP SITA
                    /*          
                    while (isset($slugs[$slug.$suffix]))
                    {
                    	$count++;
                    	$suffix = '-'.$count;
                    }
                    */
                    $slugNotFound = "false";
                    try {
                        while ($slugNotFound == "false") {
                            $sqlSlug    = "select slug from slug where slug = '" . $slug . $suffix . "';";
                            $slugUnique = $conn->prepare($sqlSlug);
                            $slugUnique->execute();
                            if ($slugUnique->rowCount() == 0) {
                                $slugNotFound = "true";
                            } else {
                                $count++;
                                $suffix = '-' . $count;
                            }
                        }
                    }
                     
                    catch (PDOException $e) {
                        print "Whoops! Something went wrong!" . "\n";
                        print "Query with error: " . $sql . "\n";
                        print "Reason given:" . $e->getMessage() . "\n";
                        print false;
                    }
                    $slug .= $suffix;

		            $sqlSlug    = "select slug from myslug where slug = '" . $slug . "';";
		            $slugUnique = $conn->prepare($sqlSlug);
		            $slugUnique->execute();
		            if ($slugUnique->rowCount() != 0) {

		                $slugNotFound = "false";
		                try {
		                    while ($slugNotFound == "false") {
		                        $sqlSlug    = "select slug from myslug where slug = '" . $slug . $suffix . "';";
		                        $slugUnique = $conn->prepare($sqlSlug);
		                        $slugUnique->execute();
		                        if ($slugUnique->rowCount() == 0) {
		                            $slugNotFound = "true";
									$sqlSlug    = "insert into myslug (slug) VALUES ('" . $slug . $suffix . "');";
									$slugUnique = $conn->prepare($sqlSlug);
									$slugUnique->execute();
					                $slug .= $suffix;
		                        } else {
		                            $count++;
		                            $suffix = '-' . $count;
		                        }
		                    }
		                }
		                 
		                catch (PDOException $e) {
		                    print "Whoops! Something went wrong!" . "\n";
		                    print "Query with error: " . $sql . "\n";
		                    print "Reason given:" . $e->getMessage() . "\n";
		                    print false;
		                }
		            }
		            else
					{
			            $sqlSlug    = "insert into myslug (slug) VALUES ('" . $slug . "');";
		            	$slugUnique = $conn->prepare($sqlSlug);
		            	$slugUnique->execute();
					}		            
                } else {
                    $slug = QubitSlug::random();
                    print "Slug else--------------------------------------------: " . $slug . "\n";
                    
                    $slugNotFound = "false";
                    try {
                        while ($slugNotFound == "false") {
                            $sqlSlug    = "select slug from slug where slug = '" . $slug . "';";
                            $slugUnique = $conn->prepare($sqlSlug);
                            $slugUnique->execute();
                            if ($slugUnique->rowCount() == 0) {
                                $slugNotFound = "true";
                            } else {
                                $slug = QubitSlug::random();
                            }
                        }
                    }
                    
                    catch (PDOException $e) {
                        print "Whoops! Something went wrong!" . "\n";
                        print "Query with error: " . $sql . "\n";
                        print "Reason given:" . $e->getMessage() . "\n";
                        print false;
                    }
                    /*
                    while (isset($slugs[$slug]))
                    {
                    $slug = QubitSlug::random();
                    }
                    */
                }
                
                print "Generated Slug: " . $slug . "\n";

                //$slugs[$slug] = true; // Add to lookup table
                $newRows[] = array(
                    $row[0],
                    $slug
                ); // To add to slug table
            }
            
            //     foreach($conn->query($sql, PDO::FETCH_NUM) as $row)
            //     {
            // Get unique slug
            //       if (null !== $row[1])
            //       {
            //         $slug = QubitSlug::slugify($row[1]);
            
            // Truncate at 250 chars
            //         if (250 < strlen($slug))
            //         {
            //           $slug = substr($slug, 0, 250);
            //         }
            
            //          $count = 0;
            //          $suffix = '';
            /*
            while (isset($slugs[$slug.$suffix]))
            {
            $count++;
            $suffix = '-'.$count;
            }
            
            $slug .= $suffix;
            }
            else
            {
            $slug = QubitSlug::random();
            
            while (isset($slugs[$slug]))
            {
            $slug = QubitSlug::random();
            }
            }
            */
            //        $slugs[$slug] = true; // Add to lookup table
            //        $newRows[] = array($row[0], $slug); // To add to slug table
            //      }
            
            // Do inserts
                  
            $inc = 1000;
            for ($i = 0; $i < count($newRows); $i += $inc)
            {
		        $sql = "INSERT INTO slug (object_id, slug) VALUES ";
		        
		        $last = min($i+$inc, count($newRows));
		        for ($j = $i; $j < $last; $j++)
		        {
		            $sqlSlug    = "select slug from slug where slug = '" .$newRows[$j][1] . "';";
		            $slugUnique = $conn->prepare($sqlSlug);
		            $slugUnique->execute();
		            if ($slugUnique->rowCount() != 0) {
		                $nowUnique = QubitSlug::random().$j."_".$last."_".$this.generateRandomString();

			        	$sql .= '('.$newRows[$j][0].', \''.$nowUnique.'\'), ';
		            }
		            else
		            {
			        	$sql .= '('.$newRows[$j][0].', \''.$newRows[$j][1].'\'), ';
					}
		        }
		        
		        $sql = substr($sql, 0, -2).';';
		        
		        $conn->exec($sql);
		        print "Sql: ".$sql."\n";
            }
            
        }
        
        $this->logSection('propel', 'Done!');
    }
    
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}	    
}
