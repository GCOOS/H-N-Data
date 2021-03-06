#!/usr/local/bin/python3
#print("Content-Type:text/html\n\n")

#Date Last Modified: 	02-13-2014
#Module: 				adminForm.py
#Object: 				application that allows authorized user to modify the database
#Return:				

# Copyright (c) 2015, Gulf of Mexico Coastal and Ocean Observing System
# All rights reserved.
# 
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
# 
# * Redistributions of source code must retain the above copyright notice, this
#   list of conditions and the following disclaimer.
# 
# * Redistributions in binary form must reproduce the above copyright notice,
#   this list of conditions and the following disclaimer in the documentation
#   and/or other materials provided with the distribution.
# 
# * Neither the name of H-N-Data nor the names of its
#   contributors may be used to endorse or promote products derived from
#   this software without specific prior written permission.
# 
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
# AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
# IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
# FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
# DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
# SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
# CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
# OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
# OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
import os
import sys
import cgi, cgitb
import nutrientsApp
import gcoos_footer

cgitb.enable()

#HEADER
print('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">')
print('<html xmlns="http://www.w3.org/1999/xhtml">')
print('		<head>')
print('    		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">')
print('    		<title>GCOOS Water Quality and Nutrients</title>')
print('    		<link href="css/gcoos_03.css" rel="stylesheet" type="text/css">')
print('    		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">')
print('    		<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">')
#google analytics script
print('			<script>')
print('			  	(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){')
print('			  	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),')
print('			  	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)')
print('			  	})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');')
print('			  	ga(\'create\', \'UA-5033874-2\', \'auto\');')
print('			  	ga(\'send\', \'pageview\');')
print('			</script>')
#piwik script
print('  		<script type="text/javascript">')
print('          	var _paq = _paq || [];')
print('          	_paq.push([\'trackPageView\']);')
print('          	_paq.push([\'enableLinkTracking\']);')
print('          	(function() {')
print('          	  var u="//data.gcoos.org/piwik/";')
print('          	  _paq.push([\'setTrackerUrl\', u+\'piwik.php\']);')
print('          	  _paq.push([\'setSiteId\', 1]);')
print('          	  var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0];')
print('          	  g.type=\'text/javascript\'; g.async=true; g.defer=true; g.src=u+\'piwik.js\'; s.parentNode.insertBefore(g,s);')
print('          	})();')
print('        </script>')
print('        <noscript><p><img src="//data.gcoos.org/piwik/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>')
print('		</head>')

#BODY
print('		<body class="thrColFixHdr" ng-app="nutrientsApp">')

#ng-cloak is used to hide page while angularjs loads
print('		<div id="container" ng-cloak>')
print('			<div id="header">')
print('				<div align="right">')
print('             	   <img src="images/gcoos_header_wq_nutrients.png" width="1200" height="120" border="0" usemap="#MapGCOOS" />')
print('                		<map name="MapGCOOS">')
print('                  		<area shape="rect" coords="4,3,1048,32" href="http://www.ioos.noaa.gov/" target="_blank" alt="About IOOS" />')
print('                  	 	<area shape="rect" coords="1055,2,1085,31" href="http://gcoos.org" target="_blank" alt="GCOOS Home" />')
print('                      	<area shape="rect" coords="1090,1,1120,30" href="../index.php" target="_blank" alt="GCOOS Data Portal" />')
print('                  	 	<area shape="rect" coords="1127,0,1157,29" href="http://gcoos.org/products/" target="_blank" alt="GCOOS Products" />')
print('                  		<area shape="rect" coords="1161,2,1191,31" href="https://www.facebook.com/GCOOS" target="_blank" alt="GCOOS Facebook" />')
print('                		</map>')
print('				</div>')
print('      	<!-- end header --></div>')

#title controller section which displays username and a link to login/logout
print('			<div ng-controller="TitleCtrl">')
print('				<div class="row">')
print('					<div ng-show="showTitleAlertMessage" class="alert alert-danger alert-error">')
print('						<p>Most be logged in</p>')
print('					</div>')
print('				</div>')
print('				<div class="row">')
print('					<div class="col-sm-offset-10">')
print('						<h5>{{greetStr}}<a href="" data-toggle="modal" data-target="{{dataTargetModal}}">{{actionStr}}</a></h5>')
print('					</div>')
print('				</div>')
print('			</div>')

#login controller that contains modals to login and logout
print('			<div ng-controller="LoginCtrl">')
#login modal
print('				<div class="modal fade" id="loginModal" role="dialog">')
print('					<div class="modal-dialog">')
print('						<div class="modal-content">')
print('							<div class="modal-header">')
print('								<h4 class="modal-title">System Authentication</h4>')
print('							</div>')
print('							<div class="modal-body">')
print('								<form class="form-horizontal" role="form">')
print('									<div class="form-group">')
print('										<label class="col-sm-2" for="user">Username:</label>')
print('										<div class="col-sm-10">')
print('											<input ng-model="userName" type="user" class="form-control" id="user">')
print('										</div>')
print('									</div>')
print('									<div class="form-group">')
print('										<label class="col-sm-2" for="paswd">Password:</label>')
print('										<div class="col-sm-10">')
print('											<input ng-model="passWord" type="password" class="form-control" id="paswd">')
print('										</div>')
print('									</div>')
print('								</form>')
print('							</div>')
print('							<div class="modal-footer">')
print('								<div ng-show="showInformationMessage" class="alert alert-info">')
print('									<p align="center">Please enter username and password combination to authenticate</p>')
print('								</div>')
print('								<div ng-show="showAlertMessage" class="alert alert-danger alert-error">')
print('									<p align="center"><strong>ERROR!</strong> The username and password combination did not validate. Please try again or contact the GCOOS Data Manager (matthew.howard@gcoos.org) for assistance.</p>')
print('								</div>')
print('								<button ng-click="verifyCredentials()" type="button" class="btn btn-primary btn-lg">Submit</button>')
print('							</div>')
print('						</div>')
print('					</div>')
print('				</div>')
#logout modal
print('				<div class="modal fade" id="logoutModal" role="dialog">')
print('					<div class="modal-dialog modal-sm">')
print('						<div class="modal-content">')
print('							<div class="modal-header">')
print('								<h4 class="modal-title">Action Confirmation!</h4>')
print('							</div>')
print('							<div class="modal-body">')
print('								<p>Click the \'Logout\' button to confirm action.</p>')
print('							</div>')
print('							<div class="modal-footer">')
print('								<button ng-click="verifyCredentials()" type="button" class="btn btn-primary btn-lg" data-dismiss="modal">Logout</button>')
print('							</div>')
print('						</div>')
print('					</div>')
print('				</div>')
print('			</div>')

#form controller which contains organization, platform, and sensor forms
print('			<div ng-controller="FormCtrl">')
#error modal used to display error messages
print('				<div class="modal fade" id="errorModal" role="dialog">')
print('					<div class="modal-dialog modal-sm">')
print('						<div class="modal-content">')
print('							<div class="modal-header">')
print('								<h4 class="modal-title">NOTE:</h4>')
print('							</div>')
print('							<div class="modal-body">')
print('								<p>{{globalAlertMessage}}</p>')
print('							</div>')
print('							<div class="modal-footer">')
print('								<button ng-click="" type="button" class="btn btn-primary btn-lg" data-dismiss="modal">Close</button>')
print('							</div>')
print('						</div>')
print('					</div>')
print('				</div>')
print('				<p style="font-size: 11px;">NOTE: Asterisk following a field label are required fields. The [Delete] button was provided only to delete duplicates. Records should never be deleted from the registry.</p>')
print(' 			<hr class="formBorder"/>')
#organization form
print(' 			<h4>Organizations</h4>')
print('				<div>')
print('					<form id="nutrientsMainForm" class="form-horizontal" role="form">')
#shortname input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{o_shortNameTextInputLabelColor}};" class="col-sm-2" for="shortname">Shortname<strong style="font-size: 14px;">{{o_shortNameTextAsterisk}}</strong>:</label>')
print('							<div class="col-sm-4">')
print('								<input ng-model="o_shortNameTextInput" placeholder="Select from the dropdown list on the right" class="form-control" ng-disabled="o_shortNameTextInputActive">')
print('							</div>')
print('							<label style="font-weight: normal; color: {{o_shortNameInputLabelColor}};" class="col-sm-2" for="orgListt">Current List<strong style="font-size: 14px;">{{o_shortNameAsterisk}}</strong>:</label>')
print('							<div class="col-sm-4">')
print('								<select ng-model="o_shortNameInput" ng-options="org.shortname for org in orgList" ng-change="populatePlatform(o_shortNameInput)" type="shortname" class="form-control" id="shortname">')
print('								</select>')
print('							</div>')
print('						</div>')
#name input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{o_nameTextInputLabelColor}};" class="col-sm-2" for="name">Name<strong style="font-size: 14px;">{{o_nameTextAsterisk}}</strong>:</label>')
print('							<div class="col-sm-4">')
print('								<input ng-model="o_nameTextInput" placeholder="Select from the dropdown list on the right" class="form-control" ng-disabled="o_nameTextInputActive">')
print('							</div>')
print('							<label style="font-weight: normal; color: {{o_nameInputLabelColor}};" class="col-sm-2" for="orgListt">Current List<strong style="font-size: 14px;">{{o_nameAsterisk}}</strong>:</label>')
print('							<div class="col-sm-4">')
print('								<select ng-model="o_nameInput" ng-options="org.name for org in orgList" ng-change="populatePlatform(o_nameInput)" type="name" class="form-control" id="name">')
print('								</select>')
print('							</div>')
print('						</div>')
#contact name input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{o_contactNameInputLabelColor}};" class="col-sm-2" for="contactName">Contact Name<strong style="font-size: 14px;">{{o_contactNameAsterisk}}</strong>:</label>')
print('							<div class="col-sm-10">')
print('								<input ng-model="o_contactNameInput" type="contactName" class="form-control" id="contactName" ng-disabled="o_contactNameInputActive">')
print('							</div>')
print('						</div>')
#contact email input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{o_contactEmailInputLabelColor}}" class="col-sm-2" for="contactEmail">Contact Email<strong style="font-size: 14px;">{{o_contactEmail}}</strong>:</label>')
print('							<div class="col-sm-10">')
print('								<input ng-model="o_contactEmailInput" type="contactEmail" class="form-control" id="contactEmail" ng-disabled="o_contactEmailInputActive">')
print('							</div>')
print('						</div>')
#organization url input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{o_urlInputLabelColor}}" class="col-sm-2" for="orgUrl">URL<strong style="font-size: 14px;">{{o_urlAsterisk}}</strong>:</label>')
print('							<div class="col-sm-9">')
print('								<input ng-model="o_urlInput" type="orgUrl" class="form-control" id="orgUrl" ng-disabled="o_urlInputActive">')
print('							</div>')
print('							<div class="col-sm-1">')
print('								<button type="button" class="btn btn-default" ng-click="openLink(\'ORGURL\')">Test Link</button>')
print('							</div>')
print('						</div>')
print('					</form>')
#control buttons
print('					<div class="row">')
print('						<div class="btn-group btn-group-md col-sm-offset-2 col-sm-2">')
print('							<button ng-click="traverseList(\'org\', \'GoBackAll\')" type="button" class="btn btn-primary">|&lt</button>')
print('							<button ng-click="traverseList(\'org\', \'GoBack\')" type="button" class="btn btn-primary">&lt</button>')
print('							<button ng-click="traverseList(\'org\', \'GoForward\')" type="button" class="btn btn-primary">&gt</button>')
print('							<button ng-click="traverseList(\'org\', \'GoForwardAll\')" type="button" class="btn btn-primary">&gt|</button>')
print('						</div>')
print('						<div class="btn-group btn-group-md col-sm-3">')
print('							<button ng-click="setActiveButton(\'BrowseOrganization\')" type="button" class="btn btn-primary {{o_browseActive}}">Browse</button>')
print('							<button ng-click="setActiveButton(\'AddOrganization\')" type="button" class="btn btn-primary {{o_addActive}}">Add</button>')
print('							<button ng-click="setActiveButton(\'EditOrganization\')" type="button" class="btn btn-primary {{o_editActive}}">Edit</button>')
print('							<button ng-click="setActiveButton(\'DeleteOrganization\')" type="button" class="btn btn-primary {{o_deleteActive}}">Delete</button>')
print('						</div>')
print('						<div class="col-sm-1">')
print('							<button ng-click="submitForm(\'orgForm\')" type="button" class="btn btn-primary btn-md" ng-disabled="o_submitBtn">Submit</button>')
print('						</div>')
print('					</div>')
print('				</div>')
print(' 			<hr class="formBorder"/>')
#platform form
print('				<h4>{{platformTitle}}</h4>')
print('				<div>')
print('					<form class="form-horizontal" role="form">')
#name input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{p_nameTextInputLabelColor}};" class="col-sm-2" for="platName">Name<strong style="font-size: 14px;">{{p_nameTextAsterisk}}</strong>:</label>')
print('							<div class="col-sm-4">')
print('								<input ng-model="p_nameTextInput" placeholder="Select from the dropdown list on the right" class="form-control" ng-disabled="p_nameTextInputActive">')
print('							</div>')
print('							<label style="font-weight: normal; color: {{p_nameInputLabelColor}};" class="col-sm-2" for="platListt">Current List<strong style="font-size: 14px;">{{p_nameAsterisk}}</strong>:</label>')
print('							<div class="col-sm-4">')
print('								<select ng-model="p_nameInput" ng-options="plat.name for plat in selectedPlatList" ng-change="populateSensor(p_nameInput)" type="platName" class="form-control" id="platName">')
print('								</select>')
print('							</div>')
print('						</div>')
#description input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{p_descriptionInputLabelColor}};" class="col-sm-2" for="description">Description<strong style="font-size: 14px;">{{p_descriptionAsterisk}}</strong>:</label>')
print('							<div class="col-sm-10">')
print('								<input ng-model="p_descriptionInput" type="description" class="form-control" id="description" ng-disabled="p_descriptionInputActive">')
print('							</div>')
print('						</div>')
#latitude input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{p_latitudeInputLabelColor}};" class="col-sm-2" for="latitude">Latitude<strong style="font-size: 14px;">{{p_latitudeAsterisk}}</strong>:</label>')
print('							<div class="col-sm-10">')
print('								<input ng-model="p_latitudeInput" type="latitude" class="form-control" id="latitude" ng-disabled="p_latitudeInputActive">')
print('							</div>')
print('						</div>')
#longitude input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{p_longitudeInputLabelColor}};" class="col-sm-2" for="longitude">Longitude<strong style="font-size: 14px;">{{p_longitudeAsterisk}}</strong>:</label>')
print('							<div class="col-sm-10">')
print('								<input ng-model="p_longitudeInput" type="longitude" class="form-control" id="longitude" ng-disabled="p_longitudeInputActive">')
print('							</div>')
print('						</div>')
#url input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{p_urlInputLabelColor}};" class="col-sm-2" for="platUrl">URL<strong style="font-size: 14px;">{{p_urlAsterisk}}</strong>:</label>')
print('							<div class="col-sm-9">')
print('								<input ng-model="p_urlInput" type="platUrl" class="form-control" id="platUrl" ng-disabled="p_urlInputActive">')
print('							</div>')
print('							<div class="col-sm-1">')
print('								<button type="button" class="btn btn-default" ng-click="openLink(\'URL\')">Test Link</button>')
print('							</div>')
print('						</div>')
#rss input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{p_rssInputLabelColor}};" class="col-sm-2" for="rss">RSS<strong style="font-size: 14px;">{{p_rssAsterisk}}</strong>:</label>')
print('							<div class="col-sm-9">')
print('								<input ng-model="p_rssInput" type="rss" class="form-control" id="rss" ng-disabled="p_rssInputActive">')
print('							</div>')
print('							<div class="col-sm-1">')
print('								<button type="button" class="btn btn-default" ng-click="openLink(\'RSS\')">Test Link</button>')
print('							</div>')
print('						</div>')
#image url input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{p_imageInputLabelColor}};" class="col-sm-2" for="platImg">Image<strong style="font-size: 14px;">{{p_imageAsterisk}}</strong>:</label>')
print('							<div class="col-sm-9">')
print('								<input ng-model="p_imageInput" type="platImg" class="form-control" id="platImg" ng-disabled="p_imageInputActive">')
print('							</div>')
print('							<div class="col-sm-1">')
print('								<button type="button" class="btn btn-default" ng-click="openLink(\'IMAGE\')">Test Link</button>')
print('							</div>')
print('						</div>')
#urn input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{p_urnInputLabelColor}};" class="col-sm-2" for="urn">URN:</label>')
print('							<div class="col-sm-10">')
print('								<input ng-model="p_urnInput" type="urn" class="form-control" id="urn" placeholder="This is an auto-generated field" disabled>')
print('							</div>')
print('						</div>')
print('					</form>')
#control buttons
print('					<div class="row">')
print('						<div class="btn-group btn-group-md col-sm-offset-2 col-sm-2">')
print('							<button ng-click="traverseList(\'plat\', \'GoBackAll\')" type="button" class="btn btn-primary">|&lt</button>')
print('							<button ng-click="traverseList(\'plat\', \'GoBack\')" type="button" class="btn btn-primary">&lt</button>')
print('							<button ng-click="traverseList(\'plat\', \'GoForward\')" type="button" class="btn btn-primary">&gt</button>')
print('							<button ng-click="traverseList(\'plat\', \'GoForwardAll\')" type="button" class="btn btn-primary">&gt|</button>')
print('						</div>')
print('						<div class="btn-group btn-group-md col-sm-3">')
print('							<button ng-click="setActiveButton(\'BrowsePlatform\')" type="button" class="btn btn-primary {{p_browseActive}}">Browse</button>')
print('							<button ng-click="setActiveButton(\'AddPlatform\')" type="button" class="btn btn-primary {{p_addActive}}">Add</button>')
print('							<button ng-click="setActiveButton(\'EditPlatform\')" type="button" class="btn btn-primary {{p_editActive}}">Edit</button>')
print('							<button ng-click="setActiveButton(\'DeletePlatform\')" type="button" class="btn btn-primary {{p_deleteActive}}">Delete</button>')
print('						</div>')
print('						<div class="col-sm-1">')
print('							<button ng-click="submitForm(\'platForm\')" type="button" class="btn btn-primary btn-md" ng-disabled="p_submitBtn">Submit</button>')
print('						</div>')
print('						<div class="col-sm-1">')
print('							<button ng-click="addToPlot()" type="button" class="btn btn-primary btn-md" ng-disabled="p_addMarkerBtn">Regenerate Map Markers</button>')
print('						</div>')
print('					</div>')
print('				</div>')
print(' 			<hr class="formBorder"/>')
#sensor form
print('				<h4>{{sensorTitle}}</h4>')
print('				<div>')
print('					<form class="form-horizontal" role="form">')
#sensor type input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{s_sensorTypeInputLabelColor}};" class="col-sm-2" for="sensorType">Sensor Type<strong style="font-size: 14px;">{{s_sensorTypeAsterisk}}</strong>:</label>')
print('							<div class="col-sm-10">')
print('								<select ng-model="s_sensorTypeInput" ng-options="sens.shortTypeName for sens in sensTypeList" ng-change="" type="sensorType" class="form-control" id="sensorType" ng-disabled="s_sensorTypeInputActive">')
print('								</select>')
print('							</div>')
print('						</div>')
#vertical position input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{s_verticalPositionInputLabelColor}};" class="col-sm-2" for="verticalPosition">Vertical Position (meters)<strong style="font-size: 14px;">{{s_verticalPositionAsterisk}}</strong>:</label>')
print('							<div class="col-sm-10">')
print('								<input ng-model="s_verticalPositionInput" type="verticalPosition" class="form-control" id="verticalPosition" ng-disabled="s_verticalPositionInputActive">')
print('							</div>')
print('						</div>')
#sensor number input
print('						<div class="form-group">')
print('							<label style="font-weight: normal; color: {{s_numberInputLabelColor}};" class="col-sm-2" for="sensorNumber">Sensor Number:</label>')
print('							<div class="col-sm-10">')
print('								<input ng-model="s_numberInput" type="sensorNumber" class="form-control" id="sensorNumber" placeholder="This is an auto-generated count of the sensor type per platform." disabled>')
print('							</div>')
print('						</div>')
print('					</form>')
#control buttons
print('					<div class="row">')
print('						<div class="btn-group btn-group-md col-sm-offset-2 col-sm-2">')
print('							<button ng-click="traverseList(\'sens\', \'GoBackAll\')" type="button" class="btn btn-primary">|&lt</button>')
print('							<button ng-click="traverseList(\'sens\', \'GoBack\')" type="button" class="btn btn-primary">&lt</button>')
print('							<button ng-click="traverseList(\'sens\', \'GoForward\')" type="button" class="btn btn-primary">&gt</button>')
print('							<button ng-click="traverseList(\'sens\', \'GoForwardAll\')" type="button" class="btn btn-primary">&gt|</button>')
print('						</div>')
print('						<div class="btn-group btn-group-md col-sm-3">')
print('							<button ng-click="setActiveButton(\'BrowseSensor\')" type="button" class="btn btn-primary {{s_browseActive}}">Browse</button>')
print('							<button ng-click="setActiveButton(\'AddSensor\')" type="button" class="btn btn-primary {{s_addActive}}">Add</button>')
print('							<button ng-click="setActiveButton(\'EditSensor\')" type="button" class="btn btn-primary {{s_editActive}}">Edit</button>')
print('							<button ng-click="setActiveButton(\'DeleteSensor\')" type="button" class="btn btn-primary {{s_deleteActive}}">Delete</button>')
print('						</div>')
print('						<div class="col-sm-1">')
print('							<button ng-click="submitForm(\'sensForm\')" type="button" class="btn btn-primary btn-md" ng-disabled="s_submitBtn">Submit</button>')
print('						</div>')
print('					</div>')
print('				</div>')
print('				<hr class="formBorder"/>')
print('			</div>')
print('		<!-- end #container --> </div>')

#external scripts
print('		<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>')
print('		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.26/angular.min.js"></script>')
print('		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.26/angular-route.min.js"></script>')
print('		<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>')
nutrientsApp.nutrientsApp()
print('</body>')
print('</html>')