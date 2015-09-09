<?php 
	// Module:             get_data.php
	// Object:             Extracts GCOOS data from query  
	// Return:             REQUEST/CSV
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
    set_time_limit(0);
	$lon1 = -98.4;
	$lon2 = -80.5;
	$lat1 = 21.7;
	$lat2 = 31.0;
	date_default_timezone_set('UTC');
	$date1 = '1900-01-01T00:00:00Z';
	$date2 = date('Y-m-d\TH:i:s\Z');
	$cr = "\015\012";

	// prepare the database
	
	include_once "/opt/apache-2.2.22/htdocs/nutrients/bin/config.php";
	try{
		$dbh = new PDO("sqlite:".$db_sensor);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction();
		
	} catch (PDOException $e) {
		$dbh->rollBack();
		echo "Error!" . $e->getMessage() . "<br/>";
		die();
	}
	
	// extract the assets
	
	$source = "{not set}";	
	if(isset($_GET["assets"])) { 
		$source = $_GET['assets'];
		
		if ($source=='organization') {
			// list the organizations
			$sql="SELECT * FROM organization ORDER BY shortname";
			$output = "abbreviation,name,contactName,contactEmail,URL";
			$output .=	 $cr;
			
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['shortname'] . "," ;
				$output .= $row['name'] . "," ;
				$output .= $row['contactName'] . "," ;
				$output .= $row['contactEmail'] . "," ;
				$output .= $row['url'] . "," ;
				$output .=	 $cr;
			}
			
		} elseif ($source=='stations') {
			// list the stations
			$sql="SELECT o.name as organization, p.name as station,p.description,p.loc_lat,p.loc_lon FROM platform p JOIN organization o ON (p.organizationId=o.rowid) ORDER BY p.name";
			$output = "organization,station,description,latitude,longtitude,URL";
			$output .=	 $cr;
			
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['organization'] . "," ;
				$output .= $row['station'] . "," ;
				$output .= $row['description'] . "," ;
				$output .= number_format($row['loc_lat'],4) . "," ;
				$output .= number_format($row['loc_lon'],4) . "," ;
				$output .= $row['url'] . "," ;
				$output .=	 $cr;
			}
		} else {
			$output = "ERROR! Please check the syntax. The parameter 'assets' set to ".$source." is not supported.";
			$output .=	 $cr;
		}
	} else {
		// start working on the data
		if(isset($_GET["north"])) { 
			$lon1 = $_GET['west'];
			$lon2 = $_GET['east'];
			$lat1 = $_GET['south'];
			$lat2 = $_GET['north'];
		} elseif (isset($_GET["bbox"])) {
			$bbox = $_GET["bbox"];
			$coords = explode(",", $bbox);
			$lon1 = $coords[0];
			$lon2 = $coords[2];
			$lat1 = $coords[1];
			$lat2 = $coords[3];
		}
		
		if(isset($_GET['source'])) { 
			$source = $_GET['source'];
		} else {
			$source = 'All';
		}
	
		// correct the day if smaller
		$fromYear = $_GET['fromYear'];
		$toYear = $_GET['toYear'];
		$year_now = date('Y');
	
		if ($fromYear > $year_now){ 
			$fromYear = $year_now;
			}
		if ($toYear > $year_now){ 
			$toYear = $year_now;
			}
	
		$fromMonth = $_GET['fromMonth'];
		$toMonth = $_GET['toMonth'];
		if (strlen(trim($fromMonth)) == 1) {
			$fromMonth = '0' . $fromMonth;
		}
		if (strlen(trim($toMonth)) == 1) {
			$toMonth = '0' . $toMonth;
		}
	
		$fromDay = $_GET['fromDay'];
		$toDay = $_GET['toDay'];
		if (strlen(trim($fromDay)) == 1) {
			$fromDay = '0' . $fromDay;
		}
		if (strlen(trim($toDay)) == 1) {
			$toDay = '0' . $toDay;
		}
		
		$fromHour = $_GET['fromHour'];
		$toHour = $_GET['toHour'];
		if (strlen(trim($fromHour)) == 1) {
			$fromHour = '0' . $fromHour;
		}
		if (strlen(trim($toHour)) == 1) {
			$toHour = '0' . $toHour;
		}
	
		$fromMinutes = $_GET['fromMinutes'];
		$toMinutes = $_GET['toMinutes'];
		if (strlen(trim($fromMinutes)) == 1) {
			$fromMinutes = '0' . $fromMinutes;
		}
		if (strlen(trim($toMinutes)) == 1) {
			$toMinutes = '0' . $toMinutes;
		}
	
		$fromSeconds = $_GET['fromSeconds'];
		$toSeconds = $_GET['toSeconds'];
		if (strlen(trim($fromSeconds)) == 1) {
			$fromSeconds = '0' . $fromSeconds;
		}
		if (strlen(trim($toSeconds)) == 1) {
			$toSeconds = '0' . $toSeconds;
		}
	
		// extract the bounding dates
		if(isset($_GET['fromYear'])) {
			$startdt =  $fromYear . "-" . $fromMonth. "-" . $fromDay . "T" . $fromHour . ":" . $fromMinutes . ":" . $fromSeconds ."Z";
		} elseif(isset($_GET["start"])) {
			$start=  $_GET["start"];
			$startdt = substr($start, 0,4) . "-" . substr($start, 5,2) . "-" . substr($start, 8,2) . "T" . substr($start, 11,2) . ":" . substr($start, 14,2) . ":" . substr($start, 17,2) ."Z";
		}
		if(isset($_GET['toYear'])) {
			$stopdt =  $toYear . "-" . $toMonth . "-" . $toDay . "T" . $toHour . ":" . $toMinutes . ":" . $toSeconds ."Z";
		} elseif(isset($_GET["stop"])) {
			$stop = $_GET["stop"];
			$stopdt = substr($stop, 0,4) . "-" . substr($stop, 5,2) . "-" . substr($stop, 8,2) . "T" . substr($stop, 11,2) . ":" . substr($stop, 14,2) . ":" . substr($stop, 17,2) ."Z";
		}
		$date1 = $startdt;
		$date2 = $stopdt;
		
		// check what record is requested
		if(isset($_GET["obs"])) {
			$obs = $_GET["obs"];
		}
		// check qcFlag
		switch ($_GET["qcFlag"]){
		case 'passed':
			$qc=1;
			break;
		case 'all':
			$qc=0;
			break;
		case 'failed':
			$qc=4;
			break;
		default:
			$qc=0;
		}
		
		// check the sort order; deafult is dates
		$order = 'dates';
		if (isset($_GET["sortBy"])) {
			$order = $_GET["sortBy"];
		}
		
		$output = "";
		
		//dissolved oxygen
		switch ($obs) {
		case "dissolved_oxygen":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.DO, ";
			$sql .= "  d.qc ";
			$sql .= "FROM ";
			$sql .= "dissolvedOxygen d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum,dissolvedOxygen,qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
			   $output .= $row['owner'] . "," ;
			   $output .= $row['platform'] . "," ;
			   $output .= number_format($row['lat'],4) . "," ;
			   $output .= number_format($row['lon'],4) . "," ;
			   $output .= $row['observationDate'] . "," ;
			   $output .= $row['verticalPosition'] . "," ;
			   $output .= $row['DO'] . "," ;
			   $output .= $row['qc'];	
			   $output .= $cr;
			}
			break;
		case "nitrogen":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.n, ";
			$sql .= "  d.characteristics, ";
			$sql .= "  d.remarks, ";
			$sql .= "  d.uom, ";
			$sql .= "  d.qc ";
			$sql .= "FROM ";
			$sql .= "nitrogen d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum (m),nitrogen,characteristics,remarks,uom,qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['owner'] . "," ;
				$output .= $row['platform'] . "," ;
				$output .= number_format($row['lat'],4) . "," ;
				$output .= number_format($row['lon'],4) . "," ;
				$output .= $row['observationDate'] . "," ;
				$output .= $row['verticalPosition'] . "," ;
				$output .= $row['n'] . "," ;	
				$output .= $row['characteristics'] . "," ;	
				$output .= $row['remarks'] . "," ;	
				$output .= $row['uom'] . "," ;	
				$output .= $row['qc'];	
				$output .=	 $cr;
			}
			break;
		case "water_temperature":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.temperature, ";
			$sql .= "  d.qc ";
			$sql .= "FROM ";
			$sql .= "waterTemperature d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum (m),water_temperature (C),qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['owner'] . "," ;
				$output .= $row['platform'] . "," ;
				$output .= number_format($row['lat'],4) . "," ;
				$output .= number_format($row['lon'],4) . "," ;
				$output .= $row['observationDate'] . "," ;
				$output .= $row['verticalPosition'] . "," ;
				$output .= $row['temperature'] . "," ;
				$output .= $row['qc']  ;	
				$output .=	 $cr;
			}
			break;
		case "phosphorus":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.p, ";
			$sql .= "  d.remarks, ";
			$sql .= "  d.uom, ";
			$sql .= "  d.qc ";
			$sql .= "FROM ";
			$sql .= "phosphorus d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum (m),phosphorus,uom,remarks,qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['owner'] . "," ;
				$output .= $row['platform'] . "," ;
				$output .= number_format($row['lat'],4) . "," ;
				$output .= number_format($row['lon'],4) . "," ;
				$output .= $row['observationDate'] . "," ;
				$output .= $row['verticalPosition'] . "," ;
				$output .= $row['p'] . "," ;	
				$output .= $row['uom'] . "," ;	
				$output .= $row['remarks'] . "," ;	
				$output .= $row['qc'] ;	
				$output .=	 $cr;
			}
			break;
		case "salinity":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.salinity, ";
			$sql .= "  d.qc ";
			$sql .= "FROM ";
			$sql .= "salinity d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum (m),salinity (PSU),qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['owner'] . "," ;
				$output .= $row['platform'] . "," ;
				$output .= number_format($row['lat'],4) . "," ;
				$output .= number_format($row['lon'],4) . "," ;
				$output .= $row['observationDate'] . "," ;
				$output .= $row['verticalPosition'] . "," ;
				$output .= $row['salinity'] . "," ;
				$output .= $row['qc'] ;	
				$output .=	 $cr;
			}
			break;
		case "ph":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.ph, ";
			$sql .= "  d.qc ";
			$sql .= "FROM ";
			$sql .= "ph d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum (m),ph,qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['owner'] . "," ;
				$output .= $row['platform'] . "," ;
				$output .= number_format($row['lat'],4) . "," ;
				$output .= number_format($row['lon'],4) . "," ;
				$output .= $row['observationDate'] . "," ;
				$output .= $row['verticalPosition'] . "," ;
				$output .= $row['ph'] . "," ;
				$output .= $row['qc'];	
				$output .=	 $cr;
			}
			break;
		case "enterococcus":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.enterococcus, ";
			$sql .= "  d.qc ";
			$sql .= "FROM ";
			$sql .= "enterococcus d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum (m),enterococcus(cfu/100ml),qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['owner'] . "," ;
				$output .= $row['platform'] . "," ;
				$output .= number_format($row['lat'],4) . "," ;
				$output .= number_format($row['lon'],4) . "," ;
				$output .= $row['observationDate'] . "," ;
				$output .= $row['verticalPosition'] . "," ;
				$output .= $row['enterococcus'] . "," ;
				$output .= $row['qc'];	
				$output .=	 $cr;
			}
			break;
		case "chlorophyll":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.qc, ";
			$sql .= "  d.chlorophyll ";
			$sql .= "FROM ";
			$sql .= "chlorophyll d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum (m),chlorophyll(mg/m^3),qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['owner'] . "," ;
				$output .= $row['platform'] . "," ;
				$output .= number_format($row['lat'],4) . "," ;
				$output .= number_format($row['lon'],4) . "," ;
				$output .= $row['observationDate'] . "," ;
				$output .= $row['verticalPosition'] . "," ;
				$output .= $row['chlorophyll'] . "," ;
				$output .= $row['qc'];	
				$output .=	 $cr;
			}
			break;
		case "fecal_coliform":
			$sql = "";
			$sql .= "SELECT ";
			$sql .= "  o.name AS owner, ";
			$sql .= "  p.name AS platform, ";
			$sql .= "  p.loc_lat AS lat, ";
			$sql .= "  p.loc_lon AS lon, ";
			$sql .= "  d.verticalPosition, ";
			$sql .= "  d.observationDate, ";
			$sql .= "  d.fecalcoli, ";
			$sql .= "  d.qc ";
			$sql .= "FROM ";
			$sql .= "fecalcoli d ";
			$sql .= "JOIN sensor s ON (d.sensorId=s.rowid) ";
			$sql .= "JOIN platform p ON (s.platformId=p.rowid) ";
			$sql .= "JOIN organization o ON (p.organizationId=o.rowid) ";
			$sql .= "WHERE ";
			$sql .= "  (lat BETWEEN " . $lat1 . " AND " . $lat2 . ") AND  ";
			$sql .= "  (lon BETWEEN " . $lon1 . " AND " . $lon2 . ") AND  ";
			$sql .= "  (observationDate BETWEEN '" . $date1 . "' AND '" . $date2 . "') ";
			
			if ($qc > 0) {
				$sql .= "  AND (qc=".$qc.") ";
			}
			
			if ($source == 'All') {
			} else {
				$sql .= " AND (owner = '" . $source . "') ";
			}
			$sql .= "ORDER BY ";
			if ($order == 'dates'){
				$sql .= "observationDate, ";
				$sql .= "owner, ";
				$sql .= "platform ";
			}
			if ($order == 'provider'){
				$sql .= "owner, ";
				$sql .= "platform, ";
				$sql .= "observationDate ";
			}
			if ($order == 'station'){
				$sql .= "platform, ";
				$sql .= "owner, ";
				$sql .= "observationDate ";
			}	
			// put the header
			$output = "owner,platform,lat,lon,observationDate,verticalDatum (m),fecal coliform (cfu/100ml),qcFlag";
			$output .= $cr;
			// put the body
			foreach ($dbh->query($sql) as $row) {
				$output .= $row['owner'] . "," ;
				$output .= $row['platform'] . "," ;
				$output .= number_format($row['lat'],4) . "," ;
				$output .= number_format($row['lon'],4) . "," ;
				$output .= $row['observationDate'] . "," ;
				$output .= $row['verticalPosition'] . "," ;
				$output .= $row['feclcoli'] . "," ;
				$output .= $row['qc'];	
				$output .=	 $cr;
			}
			break;
		} //end on switch
	}
	$dbh = null;
	
	// generate the file or simply print the file on screen
	header("Content-type: text/plain");
	//header("Content-disposition: attachment; filename=gcoos_" . date("Y-m-d") .  ".csv");
	header("Content-disposition: attachment; filename=gcoos_for_".$obs."_".$date1."_to_".$date2."_from_".$source."_data_source_qc_".$_GET["qcFlag"].".csv");
	
	$output .=	 $cr;
	$output .= "DISCLAIMER: The Gulf of Mexico Coastal and Ocean Observing (GCOOS) Hypoxia-Nutrients Data Portal aggregates data ";
	$output .= "from the regional data providers for the convenience of all data users. Data published on this website should ";
	$output .= "not be used for navigation or certain other uses as we cannot guarantee data accuracy or availability. The data ";
	$output .= "and delivery services are provided 'as is' without warranty of any kind. ";
	$output .= "Data Source: Gulf of Mexico Coastal Ocean Observing System Hypoxia-Nutrients Data Portal (http://data.gcoos.org/nutrients)";
	$output .=	 $cr;	

	print $output;
	
?>
