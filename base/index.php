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
	
	error_reporting(0);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>GCOOS Hypoxia-Nutrients Data Portal</title>
    <link href="css/gcoos.css" rel="stylesheet" type="text/css">
    <link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">

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
        	           
        	<h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin">Statistics: Assets/Inventory</h1>
            <table width="75%" border="0" cellspacing="0" cellpadding="0" style="margin-left:30px">
              <tr align="center" bgcolor="#CCCCCC">
                <td width="34%">Item</td>
                <td width="9%"><div align="center">Count</div></td>
                <td width="3%">&nbsp;</td>
                <td width="54%">Remarks</td>
              </tr>
              <tr>
                <td>Organizations</td>
                <td align="right"><div align="right">74</div></td>
                <td>&nbsp;</td>
                <td>Unique list of organizations or departments that reported data to a repository</td>
              </tr>
              <tr>
                <td>Platforms </td>
                <td align="right"><div align="right">23,542</div></td>
                <td>&nbsp;</td>
                <td>Moored platforms or study areas where data were collected </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><div align="right"></div></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><em>Variable</em>: Chlorophyll</td>
                <td><div align="right">151,231</div></td>
                <td>&nbsp;</td>
                <td>Chlorophyll-a concentration in a liter of sampled water (ug L-1)</td>
              </tr>
              <tr>
                <td><em>Variable</em>: Dissolved oxygen</td>
                <td><div align="right">423,021</div></td>
                <td>&nbsp;</td>
                <td>Dissolved oxygen measures and concentration at a specified depth (ug L-1; m)</td>
              </tr>
              <tr>
                <td><em>Variable</em>: Enterococcus</td>
                <td><div align="right">321,189</div></td>
                <td>&nbsp;</td>
                <td>Count of common commensal organism in human, enterococcus</td>
              </tr>
              <tr>
                <td><em>Variable</em>: Fecal colliform</td>
                <td><div align="right">302,201</div></td>
                <td>&nbsp;</td>
                <td>Count of fecal colliform bacteria in a volume of water sample</td>
              </tr>
              <tr>
                <td><em>Variable</em>: Nitrogen</td>
                <td><div align="right">454,112</div></td>
                <td>&nbsp;</td>
                <td>This include nitrite, nitrate, amoniua and organic nitrogen concentration </td>
              </tr>
              <tr>
                <td><em>Variable</em>: pH</td>
                <td><div align="right">389,781</div></td>
                <td>&nbsp;</td>
                <td>Measure of the acidity or basicity of the sample of water</td>
              </tr>
              <tr>
                <td><em>Variable</em>: Phosphorus</td>
                <td><div align="right">411,098</div></td>
                <td>&nbsp;</td>
                <td>Orthophosphate phosphate and total phosphate measures</td>
              </tr>
              <tr>
                <td><em>Variable</em>: Salinity</td>
                <td><div align="right">399,005</div></td>
                <td>&nbsp;</td>
                <td>Measure of salt content following UNESCO standards</td>
              </tr>
              <tr>
                <td><em>Variable</em>: Water temperature</td>
                <td><div align="right">454,206</div></td>
                <td>&nbsp;</td>
                <td>In situ water temperature measured in degrees Celsius</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><hr /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><em><strong>TOTAL RECORDS</strong></em></td>
                <td><div align="right"><strong>3,783,666</strong></div></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
<h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin">Direct Access: Assets/Inventory</h1>
            <p>The get a list of all the organizations and/or stations, their labels, description and coordinates, use the following call syntax:</p>
            <p style="margin-left:30px"><em>http://data.gcoos.org/nutrients/get_data.php?assets={organization || stations}</em></p>
            <p><span class="style8"><strong>Example</strong>: </span></p>
            <ul>
            	<li>To list all organizations contributing data to the portal: <a href="http://data.gcoos.org/nutrients/get_data.php?assets=organization" target="_blank">http://data.gcoos.org/nutrients/get_data.php?assets=organization</a></li>
            	<li>To list all stations contributing data to the portal:<a href="http://data.gcoos.org/nutrients/get_data.php?assets=stations" target="_blank">http://data.gcoos.org/nutrients/get_data.php?assets=stations</a></li>
            </ul>
            
            <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin">Data Access: Interactive Call</h1>
            <p class="style13" style="margin-bottom:0px">The following is a form to interactively retrieve data using a form. To retrieve data, simply provide the inputs and click on the [<strong>Retrieve Data</strong>] button to generate the CSV file.</p>
            <p class="style13" style="margin-bottom:0px">&nbsp;</p>
        		<div style="margin-left:50px; margin-right:50px">
              		<form action="get_data.php" method="get" name="getData" id="getData" style="background-color:#CCFFCC">
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

					$nowYear = date("Y");
					$nowMonth = date("F");
					$nowDay = date("j");
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
					echo '	<td><input name="fromYear" type="text" id="fromYear" value="1995" size="10" /></td>';
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
					echo '	<td><input name="toYear" type="text" id="toYear" value="'.$nowYear.'" size="10" /></td>';
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
                      <td><input name="do" type="checkbox" value="unchecked" disabled="disabled"/>dissolved oxygen (DO)</td>
                      <td><input name="no2" type="checkbox" value="unchecked" disabled="disabled" />nitrite (NO2)</td>
                      <td><input name="ent" type="checkbox" value="unchecked" disabled="disabled" />enterococcus</td>
                    </tr>
                    <tr>
                      <td><input name="water_temperature" type="checkbox" value="unchecked" disabled="disabled" />water temperature (C)</td>
                      <td><input name="no3" type="checkbox" value="unchecked" disabled="disabled" />nitrate (NO3)</td>
                      <td><input name="fecal" type="checkbox" value="unchecked" disabled="disabled" />fecal colliform</td>
                    </tr>
                    <tr>
                      <td><input name="salinity" type="checkbox" value="unchecked" disabled="disabled" />salinity</td>
                      <td><input name="ntotal" type="checkbox" value="unchecked" disabled="disabled" />total N (NO2+NO3)</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td><input name="ph" type="checkbox" value="unchecked" disabled="disabled" />pH</td>
                      <td><input name="nh3" type="checkbox" value="unchecked" disabled="disabled" />amonia (NH3)</td>
                      <td><input name="po4" type="checkbox" value="unchecked" disabled="disabled" />orthophosphate phosphate</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td><input name="nh4" type="checkbox" value="unchecked" disabled="disabled" />organic N (NH4+)</td>
                      <td><input name="po4t" type="checkbox" value="unchecked" disabled="disabled" />total phosphate</td>
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
          <h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin">Data Access: Direct Call for Observations</h1>
          The following is the syntax for direct data retrieval from GCOOS repository for the Hypoxia-Nutrients data portal. 
            <p class="style7"><em>Syntax:</em></p>
            <blockquote> http://data.gcoos.org/nutrients/get_data.php?bbox={1}&amp;start={2}&amp;stop={3}&amp;obs={4}&amp;source={5}&amp;fmt={6}&amp;sortBy={7}
              <p>where:</p>
              <p>{1} westlon,southlat,eastlon,northlat, where: </p>
              <ul>
                <li>westlon = longitude of western edge of bounding box expressed as a floating   point number</li>
                <li>southlat = latitude of southern edge of bounding box expressed as a floating   point number</li>
                <li>eastlon = longitude of eastern edge of bounding box expressed as a floating   point number</li>
                <li>northlat = latitude of northern edge of bounding box expressed as a floating   point number</li>
              </ul>
              <p>{2} start date beginning at 00:00:00T00:00:00Z UTC formatted as yyyy-mm-ddThh:mm:ssZ</p>
              <p>{3} stop date ending at 23:59:59T23:59:59Z UTC formatted as yyyy-mm-ddThh:mm:ssZ</p>
              <p>{4} seggregated by a comma, list of observations to retrieve</p>
              <ul>
                <li>do: for disolved oxygen and concentrations</li>
                <li>water_temperature: for water temperature data</li>
                <li>salinity: for salinity measurements</li>
                <li>ph: measure of acidity or basicity (pH)</li>
                <li>no2: for nitrite</li>
                <li>no3: for nitrate</li>
                <li>ntotal: for nitrite and nitrate</li>
                <li>nh3: for amonia</li>
                <li>nh4: for organic nitrogen</li>
                <li>po4: for orthophosphate phosphate</li>
                <li>po4t: for total phosphate</li>
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
              <p><a href="http://data.gcoos.org/nutrients/get_data.php?bbox=-98.4,21.7,-80.5,31.0&amp;start=2008-11-01T00:00:00Z&amp;stop=2008-11-15T23:59:59Z&amp;obs=water_temperature&amp;source=All&amp;fmt=csv&amp;sortBy=dates" target="_blank">http://data.gcoos.org/nutrients/get_data.php?bbox=-98.4,21.7,-80.5,31.0&amp;start=1998-01-01T00:00:00Z&amp;stop=1999-12-31T23:59:59Z&amp;obs=ent,fecal,no2,ph&amp;source=All&amp;fmt=csv&amp;sortBy=dates</a></p>
          </blockquote>
			<h1 id="bigtitle" style="border-bottom:solid; border-bottom-width: thin">Open Source Codes</h1>
          	<blockquote>
            The GCOOS H-N portal was developed and will continue to be developed in open source envirnment. The codes are deposited and synchronized via the GitHub (<a href="https://github.com/GCOOS/H-N-Data" target="_blank">https://github.com/GCOOS/H-N-Data</a>).
            </blockquote>
      </div>
      <div id="footer"><?php include ("gcoos_footer_base.php");?></div>

	<!-- end #container --> </div>

</body>
</html>
