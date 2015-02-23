<?php
	// Author: Felimon Gayanilo (fgayanilo@tamu.edu)
	// Date Last Modified: 10-12-2014
	// Module: readSensors_do.php
	// Object: Populate the nutrients sqlite DB with dissolved oxygen records.
	// Return: Void
	
	$db = "/opt/apache-2.2.22/htdocs/nutrients/data/gcoos_data_v3b_wq.sqlite";
	$path = "/opt/apache-2.2.22/htdocs/data/nutrients/florida/Baldwin/DO_sta_res3.txt";
	$state="FL";
	$sensorType = 14; //(14-do;6-sal;25-ent;26-fecal;27-n;28-phos;29-ph;2-temp)
	
	try{

		$dbh = new PDO("sqlite:".$db);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->beginTransaction();
		
		echo "     Processing*****************: " . $path . " \n";
		$count = 0;
		$fh_r = fopen($path, 'r');
		if ($fh_r) {
			while (!feof($fh_r)) {
				$s = fgets($fh_r, 4096);
				$count += 1; // do not process first line
				if ($count > 1) {
					// start processing
					$s_array = explode(",",$s);
					// find organization shortname
					$org_name = $s_array[1];
					$sql = "SELECT shortname FROM organization WHERE name LIKE '".$org_name."'";
					foreach ($dbh->query($sql) as $row){$org_short = $row['shortname'];}
					// find platformId
					
					// find 
					
					// update the sensor table
					$sql = 'INSERT INTO sensor (name,description,loc_lat,loc_lon,organizationId,platformTypeId) VALUES ("'.$platform_name.'","'.$platform_description.'",'.$platform_loc_lat.','.$platform_loc_lon.','.$org_id.',18)';
					echo $sql ." \n";
					$dbh->exec($sql);

					// get variables; $sensorId, $observationDate, $DO, DOConc, $verticalPosition
					$observationDate = $s_array[5].'T'.$s_array[6].'Z';
					$verticalPosition = floatval($s_array[9]);
					$DO = floatval($s_array[12]);
					// update the observation table
					$sql = 'INSERT INTO dissolvedOxygen (sensorId,observationDate,DO,verticalPosition) VALUES ('.$sensorId.','.$observationDate.','.$DO.','.$verticalPosition.')';
					echo $sql ." \n";
					$dbh->exec($sql);
					}
				}
			}
		}
	}






							
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
											$flag = 0;
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
													$flag = 1;
												}
											}
											if ($flag !== 1) {
												if ($flag switch
												echo "RECORD ERROR: File = ".$path."\n";
												echo "             ". $s."\n";
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
		
		//$dbh->commit();	
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
	echo "DONE \n\n";
?>