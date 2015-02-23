<?php
	// Author: Felimon Gayanilo (fgayanilo@tamu.edu)
	// Date Last Modified: 10-12-2014
	// Module: readOrganization.php
	// Object: Populate the nutrients sqlite DB with organizational records.
	// Return: Void
	
	$db = "/opt/apache-2.2.22/htdocs/nutrients/data/gcoos_data_v3_wq.sqlite";
	$dir = "/opt/apache-2.2.22/htdocs/data/nutrients/others";
	$state="LA";
	
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
											$org_short=explode("-",$s_array[0]);
											if ($org_short[0] == "USGS") {
												$org_short="USGS-".$state;
											} else {
												$org_short=$org_short[0];
											}
											$org_long = $s_array[1];
											// check presence
											if (strlen(trim($org_short))>0) {
												$sql_chk = "select count(name) as shortCount FROM organization WHERE shortname like '".$org_short."'";
												//echo $sql_chk."\n";
												foreach ($dbh->query($sql_chk) as $row){
													$scount = $row['shortCount'];
												}
												//echo "scount=".$scount."\n";
												if ($scount == 0) {
													//echo "inside\n";
													// get record count
													$sql_count="SELECT COUNT(rowid) AS ccount FROM organization";
													foreach ($dbh->query($sql_count) as $row){
														$rcount = $row['ccount'];
													}
													
													$rcount += 1;
													
													$sql = "INSERT INTO organization (shortname,name) VALUES ('".$org_short."','".$org_long."')";
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
	$dbh = null;
	echo 'DONE!';
?>