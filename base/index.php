<?php 
	// Module:             INDEX.php
	// Object:             Form to query GCOOS DB / formulate the REQUEST to get data
	// Return:             xHTML/Display
	//
	//
	// Copyright (c) 2015, Gulf of Mexico Coastal Ocean Observing System (GCOOS) Regional Association, College Station, TX
	// All rights reserved.
	//
	// Redistribution and use in source and binary forms, with or without modification, are permitted provided that 
	// the following conditions are met:
	//
	// 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the 
	//    following disclaimer.
	//
	// 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the 
	//    following disclaimer in the documentation and/or other materials provided with the distribution.
	//
	// 3. Neither the name of the Gulf of Mexico Coastal Ocean Observing System (GCOOS) Regional Association nor the 
	//    names of its contributors and developers may be used to endorse or promote products derived from this software 
	//    without specific prior written permission.
	//
	// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, 
	// INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
	// DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
	// SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR 
	// SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
	// WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE 
	// USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.	
	
	// error_reporting(0);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>GCOOS Hypoxia-Nutrients Data Portal</title>
    <link href="css/gcoos.css" rel="stylesheet" type="text/css">
    <link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  
	<script type="text/javascript">
    
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-5033874-2']);
      _gaq.push(['_trackPageview']);
    
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    
    </script>    
	<script>
		$(function() {
			$( "#tabs" ).tabs();
		});
    </script>    
</head>

<body class="thrColFixHdr">
	<div id="container">
		<div id="header">
			<div align="right">
                <img src="images/gcoos_header_wq_nutrients.png" width="1200" height="120" border="0" usemap="#MapGCOOS" />
                <map name="MapGCOOS">
                  	  <area shape="rect" coords="1055,2,1085,31" href="http://gcoos.org" target="_blank" alt="GCOOS Home" />
                      <area shape="rect" coords="1090,1,1120,30" href="../index.php" target="_blank" alt="GCOOS Data Portal" />
                  <area shape="rect" coords="1127,0,1157,29" href="http://gcoos.org/products/" target="_blank" alt="GCOOS Products" />
                  <area shape="rect" coords="1161,2,1191,31" href="https://www.facebook.com/GCOOS" target="_blank" alt="GCOOS Facebook" />
                </map>
			</div>
            
      <!-- end header --></div>
        
		<div id="mainContent">
            <div id="tabs">
              <ul>
                <li><a href="#tabs-1">Assets and Inventory</a></li>
                <li><a href="#tabs-2">Interactive Access</a></li>
                <li><a href="#tabs-3">Direct Access</a></li>
                <li><a href="#tabs-4">Tools and Administration</a></li>
              </ul>

              <div id="tabs-1">
              <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin"><img src="images/pin.png" width="25" height="27" alt="pin" />Statistics: Assets/Inventory</h1>
                <table width="95%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                	<td width="70%" valign="top">
				   	<?php
                    	$db_sensor = "/opt/apache-2.2.22/htdocs/nutrients/data/gcoos_data_wq.sqlite";
						$totalCount = 0;
                    	try{
                        	$dbh = new PDO("sqlite:".$db_sensor);
                        	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        	$dbh->beginTransaction();
                   			// define table
                			echo '<table width="750px" border="0" cellspacing="0" cellpadding="0" style="margin-left:30px">';
                  			echo '<tr align="center" bgcolor="#CCCCCC">';
								// table header
								echo '<td width="25%">Item</td>';
								echo '<td width="12%"><div align="center">Count</div></td>';
								echo '<td width="2%">&nbsp;</td>';
								echo '<td width="61%">Remarks</td>';
                  			echo '</tr>';
							// put organization
                  			echo '<tr>';
								echo '<td>Organizations</td>';
								echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM organization";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										echo number_format($count);
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Organizations or departments that reported data to a repository.</td>';
                  			echo '</tr>';
							// put platforms
                  			echo '<tr>';
								echo '<td>Platforms </td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM platform";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										echo number_format($count);
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Moored platforms or study areas where data were collected.</td>';
							echo '</tr>';

							// put spacer for observations
							echo '<tr>';
								echo '<td>&nbsp;</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>&nbsp;</td>';
					  		echo '</tr>';

							// put chlorophyll
                  			echo '<tr bgcolor="#CFC">';
								echo '<td><em>Variable</em>: Chlorophyll </td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM chlorophyll";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										echo number_format($count);
										$totalCount =$totalCount + $count;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Chlorophyll-a concentration in a liter of sampled water (ug L-1).</td>';
							echo '</tr>';
							// dissolved oxygen
                  			echo '<tr bgcolor="#FFFFCC">';
								echo '<td><em>Variable</em>: Dissolved Oxygen </td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM dissolvedOxygen";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										echo number_format($count);
										$totalCount =$totalCount + $count;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Dissolved oxygen measures and concentration at a specified depth (ug L-1; m).</td>';
							echo '</tr>';
							// enterococcus
                  			echo '<tr bgcolor="#CFC">';
								echo '<td><em>Variable</em>: Enterococcus </td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM enterococcus";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										echo number_format($count);
										$totalCount =$totalCount + $count;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Count of common commensal organism in human, enterococcus.</td>';
							echo '</tr>';
							// fecal colliform
                  			echo '<tr bgcolor="#FFFFCC">';
								echo '<td><em>Variable</em>: Fecal colliform</td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM fecalcoli";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										echo number_format($count);
										$totalCount =$totalCount + $count;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Count of fecal colliform bacteria in a volume of water sample.</td>';
							echo '</tr>';
							// nitrogen
                  			echo '<tr bgcolor="#CFC">';
								echo '<td><em>Variable</em>: Nitrogen</td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
									    $nCount = 0;
										$sql="SELECT count(*) as countS FROM nTotal";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										$nCount=$nCount+$count;
										$sql="SELECT count(*) as countS FROM nh3";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										$nCount=$nCount+$count;
										$sql="SELECT count(*) as countS FROM nh4";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										$nCount=$nCount+$count;
										$sql="SELECT count(*) as countS FROM no2";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										$nCount=$nCount+$count;
										$sql="SELECT count(*) as countS FROM no3";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										$nCount=$nCount+$count;
										
										if ($nCount>0) {
											echo number_format($nCount);
										} else {
											echo '{processing}';
										}

										$totalCount =$totalCount + $nCount;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>This include nitrite, nitrate, amoniua and organic nitrogen concentration.</td>';
							echo '</tr>';
							// pH
                  			echo '<tr bgcolor="#FFFFCC">';
								echo '<td><em>Variable</em>: pH</td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM ph";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										echo number_format($count);
										$totalCount =$totalCount + $count;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Measure of the acidity or basicity of the sample of water.</td>';
							echo '</tr>';
							// phosphorus
                  			echo '<tr bgcolor="#CFC">';
								echo '<td><em>Variable</em>: Phosphorus</td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM pTotal";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										if ($count>0) {
											echo number_format($count);
										} else {
											echo '{processing}';
										}

										$totalCount =$totalCount + $count;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Orthophosphate phosphate and total phosphate measures.</td>';
							echo '</tr>';
							// salinity
                  			echo '<tr bgcolor="#FFFFCC">';
								echo '<td><em>Variable</em>: Salinity</td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM salinity";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										echo number_format($count);
										$totalCount =$totalCount + $count;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>Measure of salt content following UNESCO standards.</td>';
							echo '</tr>';
							// water temperature
                  			echo '<tr bgcolor="#CFC">';
								echo '<td><em>Variable</em>: Water temperature</td>';
	                    		echo '<td align="right">';
									echo '<div align="right">';
										$sql="SELECT count(*) as countS FROM waterTemperature";
										foreach ($dbh->query($sql) as $row){
											$count = $row['countS'];
										}
										if ($count>0) {
											echo number_format($count);
										} else {
											echo '{processing}';
										}
										$totalCount =$totalCount + $count;
									echo '</div>';
								echo '</td>';
								echo '<td>&nbsp;</td>';
								echo '<td>In situ water temperature measured in degrees Celsius.</td>';
							echo '</tr>';

							echo '<tr>';
								echo '<td>&nbsp;</td>';
								echo '<td><hr /></td>';
								echo '<td>&nbsp;</td>';
								echo '<td>&nbsp;</td>';
					  		echo '</tr>';
					  		echo '<tr bgcolor="#CCCCCC">';
								echo '<td><strong>Total observation records</strong></td>';
								echo '<td><div align="right"><strong>'.number_format($totalCount).'</strong></div></td>';
								echo '<td>&nbsp;</td>';
								echo '<td>&nbsp;</td>';
					  		echo '</tr>';
							echo '</table>';
						} catch (PDOException $e) {
							$dbh->rollBack();
							echo "Error!" . $e->getMessage() . "\n";
							die();
						}
						$dbh = null;
					?>                    
                    <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin"><img src="images/pin.png" width="25" height="27" alt="pin" />Direct Access: Assets/Inventory</h1>
                    <p>The get a list of all the organizations and/or stations, their labels, description and coordinates, use the following call syntax:</p>
                      <p style="margin-left:30px"><em>http://data.gcoos.org/nutrients/get_data.php?assets={organization || stations}</em></p>
                      <p><span class="style8"><strong>Example</strong>: </span></p>
                      <ul>
                        <li>To list all organizations contributing data to the portal: <a href="http://data.gcoos.org/nutrients/get_data.php?assets=organization" target="_blank">http://data.gcoos.org/nutrients/get_data.php?assets=organization</a></li>
                        <li>To list all stations contributing data to the portal:<a href="http://data.gcoos.org/nutrients/get_data.php?assets=stations" target="_blank">http://data.gcoos.org/nutrients/get_data.php?assets=stations</a></li>
                      </ul>
                    <td width="4%">&nbsp;</td>
                    <td width="26%" valign="top">
                      <div style="background-color:#CFC">
                      <br />
                      <p style="margin-left:10px; margin-right:10px">Click on the map below to enlarge the map of H-N stations. </p>
                      <p style="margin-left:10px; margin-right:10px"><span style="color:#F00">WARNING! </span>Due to the number of stations, this can take a minute to render.</p>
                    <div align="center">
                      <p><a href="fullViewNutrients.php" target="_blank"><img src="images/map.png" width="274" height="211" /></a></p>
                      <p>&nbsp;</p>
                    </div>
                    </div>
                    </td>
                  </tr>
                </table>

              </div>
              <!-- end of tabs-1 -->
              <div id="tabs-2">
                <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin"><img src="images/pin.png" width="25" height="27" alt="pin" />Data Access: Interactive Call</h1>
                <p class="style13" style="margin-bottom:0px">The following is a form to interactively retrieve data using a form. To retrieve data, simply provide the inputs and click on the [<strong>Retrieve Data</strong>] button to generate the CSV file.</p>
                <p class="style13" style="margin-bottom:0px">&nbsp;</p>
                    <div style="margin-left:50px; margin-right:50px">
                        <form action="get_data.php" method="post" name="getData" id="getData" style="background-color:#CCFFCC">
                        <span class="style13"><strong>Spatial Coverage</strong></span><br />
                        <span class="style2">(The default values provided are the coordinates for a full coverage of the Gulf of Mexico. </span>
                        <table width="338" border="0"  style="margin-left:30px">
                            <tr>
                                <td width="108">&nbsp;</td>
                                <td width="108">North <label><input name="north" type="text" id="north" value="31.0" size="10" /></label></td>
                                <td width="108">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>West <label><input name="west" type="text" id="west" value="-98.0" size="10" /></label></td>
                                <td><div align="center" class="style1">+</div></td>
                                <td>East <label><input name="east" type="text" id="east" value="-81.0" size="10" /></label></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>South <label><input name="south" type="text" id="south" value="23.5" size="10" /></label></td>
                                <td>&nbsp;</td>
                            </tr>
                    </table>
                    <p><span class="style13"><strong>Temporal Coverage</strong></span><br />
                        <span class="style2">(The date and time inputs will not be validated when retrieving records.</span>)<br />
                    </p>
                    <div style="margin-left:30px">
                    <?php
    
                        //$nowYear = date("Y");
                        $nowYear = "2013";    // put the last year recorded
                        //$nowMonth = date("F");
                        $nowMonth = "December";
                        //$nowDay = date("j");
                        $nowDay = "01";
                        //echo "today is: " . trim($nowMonth) . "; " . date("F") . "<br />";
                        //echo $today;
                        echo '<table width="300" border="0">';
                        echo '  <tr>';
                        echo '	<td>&nbsp;</td>';
                        echo '	<td>Year (YYYY)</td>';
                        echo '	<td>Month (Selection)</td>';
                        echo '	<td>Day (DD)</td>';
                        echo '	<td>Hour (HH)</td>';
                        echo '	<td>Minutes (MM)</td>';
                        echo '	<td>Seconds (SS)</td>';
                        echo '  </tr>';
                        echo '  <tr>';
                        echo '	<td>From</td>';
                        echo '	<td><input name="fromYear" type="text" id="fromYear" value="2002" size="10" /></td>';
                        echo '	<td><select name="fromMonth" size="1" id="fromMonth">';
                        echo '		<option value="01" selected="selected">January</option>';
                        echo '		<option value="02">February</option>';
                        echo '		<option value="03">March</option>';
                        echo '		<option value="04">April</option>';
                        echo '		<option value="05">May</option>';
                        echo '		<option value="06">June</option>';
                        echo '		<option value="07">July</option>';
                        echo '		<option value="08">August</option>';
                        echo '		<option value="09">September</option>';
                        echo '		<option value="10">October</option>';
                        echo '		<option value="11">November</option>';
                        echo '		<option value="12">December</option>';
                        
                        echo '	</select></td>';
                        echo '	<td><input name="fromDay" type="text" id="fromDay" value="01" size="10" /></td>';
                        echo '	<td><input name="fromHour" type="text" id="fromHour" value="00" size="10" /></td>';
                        echo '	<td><input name="fromMinutes" type="text" id="fromMinutes" value="00" size="10" /></td>';
                        echo '	<td><input name="fromSeconds" type="text" id="fromSeconds" value="00" size="10" /></td>';
                        echo '  </tr>';
                        echo '  <tr>';
                        echo '	<td>To</td>';
                        echo '	<td><input name="toYear" type="text" id="toYear" value="2013" size="10" /></td>';
                        echo '	<td><select name="toMonth" size="1" id="toMonth">';
                        if (trim($nowMonth) === "January") {
                            echo '		<option value="01" selected="selected">January</option>';
                        } else {
                            echo '		<option value="01">January</option>';
                        }
                        if (trim($nowMonth) === "February") {
                            echo '		<option value="02" selected="selected">February</option>';
                        } else {
                            echo '		<option value="02">February</option>';
                        }
                        if (trim($nowMonth) === "March") {
                            echo '		<option value="03" selected="selected">March</option>';
                        } else {
                            echo '		<option value="03">March</option>';
                        }
                        if (trim($nowMonth) === "April") {
                            echo '		<option value="04" selected="selected">April</option>';
                        } else {
                            echo '		<option value="04">April</option>';
                        }
                        if (trim($nowMonth) === "May") {
                            echo '		<option value="05" selected="selected">May</option>';
                        } else {
                            echo '		<option value="05">May</option>';
                        }
                        if (trim($nowMonth) === "June") {
                            echo '		<option value="06" selected="selected">June</option>';
                        } else {
                            echo '		<option value="06">June</option>';
                        }
                        if (trim($nowMonth) === "July") {
                            echo '		<option value="07" selected="selected">July</option>';
                        } else {
                            echo '		<option value="07">July</option>';
                        }
                        if (trim($nowMonth) === "August") {
                            echo '		<option value="08" selected="selected">August</option>';
                        } else {
                            echo '		<option value="08">August</option>';
                        }
                        if (trim($nowMonth) === "September") {
                            echo '		<option value="09" selected="selected">September</option>';
                        } else {
                            echo '		<option value="09">September</option>';
                        }
                        if (trim($nowMonth) === "October") {
                            echo '		<option value="10" selected="selected">October</option>';
                        } else {
                            echo '		<option value="10">October</option>';
                        }
                        if (trim($nowMonth) === "November") {
                            echo '		<option value="11" selected="selected">November</option>';
                        } else {
                            echo '		<option value="11">November</option>';
                        }
                        if (trim($nowMonth) === "December") {
                            echo '		<option value="12" selected="selected">December</option>';
                        } else {
                            echo '		<option value="12">December</option>';
                        }
                        echo '	</select></td>';
                        echo '	<td><input name="toDay" type="text" id="toDay" value="'.$nowDay.'" size="10" /></td>';
                        echo '	<td><input name="toHour" type="text" id="toHour" value="23" size="10" /></td>';
                        echo '	<td><input name="toMinutes" type="text" id="toMinutes" value="59" size="10" /></td>';
                        echo '	<td><input name="toSeconds" type="text" id="toSeconds" value="59" size="10" /></td>';
                        echo '  </tr>';
                        echo '</table>';
                    ?>
                    </div>
                    <p>
                      <label></label>
                      <span class="style13"><strong>Observation</strong></span><br />
                      <span class="style2">NOTE: All observations are served by this portal as they become available. </span></p>
                      <p style="margin-left:30px">Select the data to retrieve:</p>
                      <table width="75%" border="0" cellspacing="0" cellpadding="0" style="margin-left:30px">
                        <tr>
                          <td><input name="obs" type="radio" value="do" />dissolved oxygen (DO)</td>
                          <td><input name="obs" type="radio" value="no2" />nitrite (NO2)</td>
                          <td><input name="obs" type="radio" value="ent" />enterococcus</td>
                        </tr>
                        <tr>
                          <td><input name="obs" type="radio" value="water_temperature" />water temperature (C)</td>
                          <td><input name="obs" type="radio" value="no3" />nitrate (NO3)</td>
                          <td><input name="obs" type="radio" value="fecal" />fecal colliform</td>
                        </tr>
                        <tr>
                          <td><input name="obs" type="radio" value="salinity" />salinity</td>
                          <td><input name="obs" type="radio" value="ntotal" />total N (NO2+NO3)</td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td><input name="obs" type="radio" value="ph" />pH</td>
                          <td><input name="obs" type="radio" value="nh3" />amonia (NH3)</td>
                          <td><input name="obs" type="radio" value="p" />phosporus</td>
                        </tr>
                        <tr>
                          <td><input name="obs" type="radio" value="chla" />chlorophyll-a</td>
                          <td><input name="obs" type="radio" value="nh4" />organic N (NH4+)</td>
                          <td>&nbsp;</td>
                        </tr>
                      </table>
                      <p>
                  <span class="style13"><strong>Data Source</strong></span><br />
                    <span class="style2">NOTE: Only data obtained by the GCOOS data portal can be served.<br />
                    </span>
                    <div  style="margin-left:30px">
                  Select the data source to retrieve:
                    <label>
                      <select name="source" size="1" id="source">
                      <option value='All' selected='selected'>All Data Sources in the Region</option>
                      <?php
                        include "bin/config.php";
                        $dbh = new PDO("sqlite:".$db);
                        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $sql="SELECT shortname, name FROM organization";
                        foreach ($dbh->query($sql) as $row){
                            echo "<option value='".$row['shortname']."'>".$row['name']."</option>";
                        }
                        $dbh = null;
                      ?>
                      </select>
                    </label>
                    </div>
                  </p>
                  <hr />
                  <p><span class="style13"><strong>Data Quality</strong></span><br />NOTE: Only range-check was made to the entries.</p>
                  <div  style="margin-left:30px">
		     <table width="75%" border="0" cellspacing="0" cellpadding="0" style="margin-left:30px">
                       <tr>
                           <td width="24%"><input name="qcFlag" type="radio" value="passed" />Passed QC test records</td>
                	   <td width="22%"><input name="qcFlag" type="radio" value="all" checked/>All records</td>
			   <td width="54%"><input name="qcFlag" type="radio" value="failed" />Failed QC test records</td>
                       </tr>
                    </table>

                  </div>
                  
                  <p><span class="style13"></span><span class="style13"><strong>Output Format</strong></span><br />
                    <span class="style2">NOTE: Only CSV format is supported to date.</span></p>
                    <p style="margin-left:30px">Select the output format:
                      <label>
                        <select name="fmt" size="1" id="fmt">
                          <option value="csv">Comma Separated Values</option>
                        </select>
                      </label>
                      and sorted by:
                      <select name="sortBy" size="1" id="sortBy">
                        <option value="dates" selected="selected">Dates</option>
                        <option value="provider">Provider</option>
                        <option value="station">Station</option>
                      </select>
                    </p>
                    <p>
                      <label style="text-align:center">
                      <input type="submit" value="Retrieve Data" />
                      </label>
                    </p>
                    <p>&nbsp;</p>
                  </form>
              </div>
              </div>
              <!-- end of tabs-2 -->
              
              <div id="tabs-3">
              <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin"><img src="images/pin.png" width="25" height="27" alt="pin" />Data Access: Direct Call for Observations</h1>
              The following is the syntax for direct data retrieval from GCOOS repository for the Hypoxia-Nutrients data portal. 
                <p class="style7"><em>Syntax:</em></p>
                <blockquote> http://data.gcoos.org/nutrients/get_data.php?bbox={1}&amp;start={2}&amp;stop={3}&amp;obs={4}&amp;source={5}&amp;fmt={6}&amp;sortBy={7}
                  <p>where:</p>
                  <p>{1} westlon,southlat,eastlon,northlat, where: </p>
                  <ul>
                    <li>westlon = longitude of western edge of bounding box expressed as a floating point number</li>
                    <li>southlat = latitude of southern edge of bounding box expressed as a floating point number</li>
                    <li>eastlon = longitude of eastern edge of bounding box expressed as a floating point number</li>
                    <li>northlat = latitude of northern edge of bounding box expressed as a floating point number</li>
                  </ul>
                  <p>{2} start date formatted as YYYY-MM-DDTHH:MM:SSZ</p>
                  <p>{3} stop date formatted as YYYY-MM-DDTHH:MM:SSZ</p>
                  <p>{4} observations to retrieve</p>
                  <ul>
                    <li>do: for disolved oxygen and concentrations</li>
                    <li>water_temperature: for water temperature data</li>
                    <li>salinity: for salinity measurements</li>
                    <li>ph: measure of acidity or basicity (pH)</li>
                    <li>chla: chlorophyll-a</li>
                    <li>no2: for nitrite</li>
                    <li>no3: for nitrate</li>
                    <li>ntotal: for nitrite and nitrate</li>
                    <li>nh3: for amonia</li>
                    <li>nh4: for organic nitrogen</li>
                    <li>p: for phosphorus</li>
                    <li>ent: count of common commensal organisms in human, enterococcus</li>
                    <li>fecal: count of fecal colliform bacteria measured</li>
                  </ul>
                  <p>{5} data source which may either be: 
                  <ul>
                        <?php 
                            include "bin/config.php";
                            $dbh = new PDO("sqlite:".$db);
                            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sql="SELECT shortname, name FROM organization ORDER BY shortname";
                            $source = "<li> All (All Data Sources in the Region) </li>";
                            foreach ($dbh->query($sql) as $row){
                                $source = $source . "<li>" . $row['shortname']." (".$row['name'].") </li>";
                            }
                            $dbh = null;
                            echo $source;
                        ?>
                  </ul>
                  </p>
                  <p>{6} desired output format. Only csv is currently supported. </p>
                  <p>{7} ascending sort order: </p>
                  <blockquote>
                    <p>dates: sort the output by dates;<br />
                      provider: sort the ouput by data provider, then dates; or<br />
                      station: sort the output by the name of the station.</p>
                  </blockquote>
              </blockquote>
                <p><span class="style8"><strong>Example</strong>: </span></p>
                <blockquote>
                  <p>To access the water temperature data in the repository for all the Gulf region for the period January 01, 1998 (time: 00:00:00 UTC) to December 31, 1999 (23:59:59 UTC), for enterococcus, fecal coliform, nitrite and ph, and sorted by dates:</p>
                  <p><a href="http://data.gcoos.org/nutrients/get_data.php?bbox=-98.4,21.7,-80.5,31.0&amp;start=2002-01-01T00:00:00Z&amp;stop=2010-12-31T23:59:59Z&amp;obs=do;source=All&amp;fmt=csv&amp;sortBy=dates" target="_blank">http://data.gcoos.org/nutrients/get_data.php?bbox=-98.4,21.7,-80.5,31.0&amp;start=2002-01-01T00:00:00Z&amp;stop=2010-12-31T23:59:59Z&amp;obs=do&amp;source=All&amp;fmt=csv&amp;sortBy=dates</a></p>
              </blockquote>


              </div>
              <!-- end of tabs-3 -->
              
              <div id="tabs-4">
              <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin"><img src="images/pin.png" width="25" height="27" alt="pin" />Administrative Form</h1>
              <blockquote>
              <p>This form requires administrative credential to login and manipulate the record headers. It also have a link to map point generator to create the station dots for the map. <a href="http://data.gcoos.org/py/adminForm.py" target="_blank">Click here</a> to access the form.</p>
              </blockquote>

              <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin"><img src="images/pin.png" width="25" height="27" alt="pin" />Data Structure</h1>
              <blockquote>
              <p><a href="http://data.gcoos.org/nutrients/data/Tables/Index.html" target="_blank">Click here</a> to access the database structure used for this portal or <a href="http://data.gcoos.org/nutrients/data/gcoos_data_wq.sql" target="_blank">here</a> to download the SQL statement to recreate teh database.</p>
              </blockquote>
              
              <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin"><img src="images/pin.png" width="25" height="27" alt="pin" />Open Source Codes</h1>
                <blockquote>
                The GCOOS H-N portal was developed and will continue to be developed in open source envirnment. The codes are deposited and synchronized via the GitHub (<a href="https://github.com/GCOOS/H-N-Data" target="_blank">https://github.com/GCOOS/H-N-Data</a>).
                </blockquote>

              </div>
              <!-- end of tabs-4 -->
            </div>
        <p>&nbsp;</p>
      </div>
      <div id="footer"><?php include ("gcoos_footer_base.php");?></div>

	<!-- end #container --> </div>
</body>
</html>
