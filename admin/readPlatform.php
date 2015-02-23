<?php
	// Author: Felimon Gayanilo (fgayanilo@tamu.edu)
	// Date Last Modified: 10-12-2014
	// Module: readPlatform.php
	// Object: Populate the nutrients sqlite DB with organizational records.
	// Return: Void
	
	$db = "/opt/apache-2.2.22/htdocs/nutrients/data/gcoos_data_v3b_wq.sqlite";
	$dir = "/opt/apache-2.2.22/htdocs/data/nutrients/texas";
	
	try{

		$dbh = new PDO("sqlite:".$db);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->beginTransaction();
		
		echo "Directory :".$dir." \n";
		if($handle = opendir($dir))  {	
	 		// and scan through the items inside
			while(($file = readdir($handle)) !== false) {
				// we build the new path
				$path = $dir.'/'.$file;
					 
			 	// if the filepointer is not the current directory or the parent directory
				if($file != '.' && $file != '..' && $file != '.DS_Store') {
					// if the new path is a file
					if(is_file($path)) {
						if (filesize($path) > 0) {
							try {
								echo "     Processing*****************: " . $file . " \n";
								// start processing the files
								$fh_r = fopen($path, 'r');
								$count = 0;
								$rcount = 0;
								
								if ($fh_r) {
									while (!feof($fh_r)) {
										$s = fgets($fh_r, 4096);
										//echo $s."\n";
										$count += 1; // do not process first line
										if ($count > 1) {
											$s_array = explode(",",$s);
											$org_name = $s_array[1];
											// find $org_id																						
											$sql = "SELECT rowid FROM organization WHERE name LIKE '".$org_name."'";
											foreach ($dbh->query($sql) as $row){
												$org_id = $row['rowid'];
											}
											// get platform name
											$platform_name = $s_array[0];
											// get platform description
											$platform_description = $s_array[2];
											// get lat
											$platform_loc_lat = $s_array[3];
											// get lon
											$platform_loc_lon = $s_array[4];
											
											if (strlen(trim($platform_name))>0 && ($platform_loc_lat>0) &&($platform_loc_lon<0)) {
												// check duplicate
												$sql="SELECT count(*) as count FROM platform WHERE name='".$platform_name."'";
												foreach ($dbh->query($sql) as $row){
													$scount = $row['count'];
												}
												if ($scount == 0) {
													//echo "inside\n";
													$sql = 'INSERT INTO platform (name,description,loc_lat,loc_lon,organizationId,platformTypeId) VALUES ("'.$platform_name.'","'.$platform_description.'",'.$platform_loc_lat.','.$platform_loc_lon.','.$org_id.',18)';
													echo $sql ." \n";
													$dbh->exec($sql);
												}
											}
										}
									}
									if (!feof($fh_r)) {
        								echo "Error: unexpected fgets() fail\n";
    								}
								}
								fclose($fh_r);
							} catch (Exception $e){
								echo "Read Error!" . $e->getMessage() . "\n";	
							}
						}
					}
				} 
			} 
		} // end looop for list of files in a folder
		closedir($handle);
		
		$dbh->commit();	
		echo 'Committed! ******************* \n';	
	} catch (PDOException $e) {
		$dbh->rollBack();
		echo "Error!" . $e->getMessage() . "<br/>";
		die();
	}

	// terminal messages and actions
	$sql="SELECT count(*) as count FROM platform";
	foreach ($dbh->query($sql) as $row){
		$rcount = $row['count'];
	}

	$dbh = null;
	echo "Total=".$rcount."\n";
	echo "Dir  =".$dir."\n";
	echo "DONE \n\n";
?>