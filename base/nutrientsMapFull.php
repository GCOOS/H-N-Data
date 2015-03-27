<?php 
	// Module:             nutrientsMapFull.php
	// Object:             Display Google Map for full view
	// Return:             void 
	//
	//
	// Copyright (c) 2013, Gulf of Mexico Coastal Ocean Observing System (GCOOS) Regional Association, College Station, TX
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
?>

<script type="text/javascript" language="javascript">
			var map;
			var mapOptions;
			var marker;
			var infoWindow;

			var USGSALMarkers;
			var NPSWRDMarkers;
			var AWICMarkers;
			var FLBFA_WQXMarkers;
			var ALBCHMarkers;
			var USGSFLMarkers;
			var FLBRAMarkers;
			var FLFMRIMarkers;
			var FLPNS_WQXMarkers;
			var FLSEASMarkers;
			var FLWQA_WQXMarkers;
			var FLGW_WQXMarkers;
			var FLNWFDMarkers;
			var FLDOH_WQXMarkers;
			var FLCHARMarkers;
			var FLSWFD_WQXMarkers;
			var CHNEPCHEMarkers;
			var FLPRMRWSMarkers;
			var SWFMDDEPMarkers;
			var FLFTM_WQXMarkers;
			var FLPCSWMarkers;
			var FLTPA_WQXMarkers;
			var FLCCZM_WQXMarkers;
			var FLCOLLMarkers;
			var FLCONS_WQXMarkers;
			var FLEECO_WQXMarkers;
			var FLSFWMMarkers;
			var FLSUWMarkers;
			var FLSUW_WQXMarkers;
			var FLGBO1Markers;
			var FLGFWFMarkers;
			var FWCLOCALMarkers;
			var FLANERMarkers;
			var FLWQSP_WQXMarkers;
			var FLCPSJMarkers;
			var FLBSGMarkers;
			var FLHILLMarkers;
			var FLTBWMarkers;
			var FLTBW_WQXMarkers;
			var FLSBLMarkers;
			var FLSCCFMarkers;
			var FLA_WQXMarkers;
			var FLMANAMarkers;
			var FLPDEMMarkers;
			var FLDADE_WQXMarkers;
			var FWC_WQMPMarkers;
			var FLKNMSMarkers;
			var FLKEYWMarkers;
			var FLCBAMarkers;
			var FLCMP_WQXMarkers;
			var FLPDEM_WQXMarkers;
			var FLSARAMarkers;
			var FLSARA_WQXMarkers;
			var LABCHMarkers;
			var LADEQWPDMarkers;
			var TCEQMarkers;
			var USGSLAMarkers;
			var EPAORDMarkers;
			var USGSMSMarkers;
			var MSWQ_WQXMarkers;
			var R4ATHENSMarkers;
			var MSBCHMarkers;
			var UTMSIMarkers;
			var USGSTXMarkers;
			var TXHGACMarkers;
			var UNKNOWNMarkers;
			var TXNURAMarkers;
			var TXGALVESTONMarkers;
			var TAMUCCMarkers;
			var TXDSHSMarkers;
			var NARS_WQXMarkers;
			var LADEQWPD_WQXMarkers;
			var TXBCHMarkers;
			var LUMCONMarkers;
			var AllMarkers = [];

			var previousZoomLevel = 0;
			var currentZoomLevel = 0;
			
			var blueShadowIcon = {url: "images/printShadow.gif", anchor: new google.maps.Point(8, 20)}; 

			var oms;

			function initialize(){
				mapOptions = {zoom: 6, center: new google.maps.LatLng(26, -85), mapTypeControl: false, mapTypeId: google.maps.MapTypeId.TERRAIN, styles: [{elementType: "labels", featureType: "all", stylers: [{visibility: "off"}]}]}; // ************ stylers changed to styles
				map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
				google.maps.visualRefresh = false;
				previousZoomLevel = map.getZoom();

				oms = new OverlappingMarkerSpiderfier(map);

				createPlatformMarkers();
			}

			function initializeWithMarkers(){
				var labelCheckBox = document.getElementById("map_label_div");

				if(labelCheckBox.checked == false){
					map.setOptions({styles: [{elementType: "labels", featureType: "all", stylers: [{visibility: "off"}]}]});
				}
				else{
					map.setOptions({styles: [{elementType: "labels", featureType: "road", stylers: [{visibility: "off"}]}]}); // ************ stylers changed to styles
				}
			}

			google.maps.event.addDomListener(window, "load", initialize);

			$(document).ready(function(){
				$(function(){
					$("#disclaimer_container").draggable({containment: "parent"});
				});
				$("#btn_minimize_disclaimer").click(function(){
					$("#disclaimer_container").slideToggle("slow");
				});
			});

			$(window).resize(resizeWindow);

			$(window).load(function(){
				resizeWindow();
			});

			function resizeWindow(){
				$('#main_container').css({height: ($(window).height())+'px'});
			}

			function createPlatformMarkers(){
				<?php
					$frontStr = "";
					$xmlPlat = new DOMDocument();

					$xmlPlat->load("/opt/apache-2.2.22/htdocs/nutrients/data/nutrients.xml");

					$frontStr = 'var blueShadowIcon = {url: "images/shadow.png", anchor: new google.maps.Point(8, 20)};';

					$organizations = $xmlPlat->getElementsByTagName("organization");
					$count = 0;
					foreach($organizations as $organization){
						$shortname = $organization->getElementsByTagName("shortname")->item(0)->nodeValue;
						$platforms = $organization->getElementsByTagName("platform");
						
						foreach ($platforms as $platform) {
							$name = $platform->getElementsByTagName("name")->item(0)->nodeValue;
							$description = $platform->getElementsByTagName("description")->item(0)->nodeValue;
							$loc_lat = $platform->getElementsByTagName("loc_lat")->item(0)->nodeValue;
							$loc_lon = $platform->getElementsByTagName("loc_lon")->item(0)->nodeValue;
							$count +=1;
							$frontStr .= 'point = new google.maps.LatLng(' . $loc_lat . ',' . $loc_lon . ');';
							$frontStr .= 'marker'.$count.' = new google.maps.Marker({position: point, map: map, icon: "images/image.png", shadow: blueShadowIcon});';
							$frontStr .= 'infoWindow = new google.maps.InfoWindow();';
							$frontStr .= 'google.maps.event.addListener(marker'.$count.', "mouseover", function(){';
							$frontStr .= '	infoWindow.setContent("Organization: ' . $shortname . '<br/>Platform: ' . $name . '");';
							$frontStr .= '	infoWindow.open(map, marker'.$count.');';
							$frontStr .= '});';
							$frontStr .= 'AllMarkers.push(marker);';
						}
					}
					echo $frontStr;
				?>
			}
		</script>
		<div id="main_container" style="height: 900px; width: initial; position: relative;">
			<div id="map-canvas" style="height: 100%; width: 100%;"></div>
			<div id="disclaimer_container" style="position: absolute; opacity: 0.7; left: 5px; bottom: 50px; visibility: visible">
				<div id="minimize_button_disclaimer">
					<button id="btn_minimize_disclaimer" style="float: left; color: red">Close</button>
				</div>
				<table width="100%" border="0">
					  <tr >
					    <td width="80%" bgcolor="#000000" style="color:#FFF; padding-left:20px; font-family: arial; font-size: 14px">DISCLAIMER</td>
					  </tr>
				</table>
				<div id="disclaimer_output" style="width:300px">
					<p>&nbsp;</p>
					<p style="margin-left: 10px; margin-top: 10px">The Gulf of Mexico Coastal Ocean Observing System (GCOOS) Data Portal aggregates data from the regional data providers
	 				for the convenience of all data users. Data published on this website should not to be used for navigation or certain other uses
	 				as we cannot guarantee data accuracy or availability. The data and delivery services are provided “as is” without warranty of any kind.</p>
	 				<p style="margin-left: 10px; margin-bottom: 10px"><a href="http://gcoos.tamu.edu/?page_id=5960" target="blank">Click here for full statement</a></p>
	 				<p style="margin-left: 10px; margin-bottom: 10px">NOTE: click the button above the label to hide message. Labels can be dragged.</p>
	 				<p>&nbsp;</p>
				</div>
			</div>
			<div style="position: absolute; opacity: 0.8; left: 80px; top: 20px; visibility: visible">
				<a href="http://www.gcoos.org/" target="blank"><img src="images/gcoos_logo.jpg"></a>
			</div>
		</div>
