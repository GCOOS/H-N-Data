#!/opt/Python-3.4.1/python
#print("Content-Type:text/html\n\n")

#Date Last Modified: 	02-04-2014
#Module: 				nutrientsApp.py
#Object: 				print javascript code that contains angularjs app for admin form
#Return:				javascript

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
import sqlite3
import pathToDb
import cgi, cgitb

cgitb.enable()

#setup DB
dbconnect = sqlite3.connect(pathToDb.pathToDb)
dbconnect.row_factory = sqlite3.Row
dbh = dbconnect.cursor()

def nutrientsApp():
	print('<script type="text/javascript">')
	print('		var nutrientsApp = angular.module(\'nutrientsApp\', [\'ngRoute\']);')
	print('		var isValidated = false;')
	print('		var signedInUser = "";')
	print('		var orgArrayPosition = -1;')
	print('		var platArrayPosition = -1;')
	print('		var sensArrayPosition = -1;')

	#DATA CONTROLLER
	print('		nutrientsApp.controller(\'DataCtrl\', function($scope){')
	print('		});')

	#LOGIN CONTROLLER
	print('		nutrientsApp.controller(\'LoginCtrl\', function($scope, $rootScope, $http){')
	print('			$scope.userName = null;') #username
	print('			$scope.passWord = null;') #password
	print('			$scope.showAlertMessage = false;') #login alert message flag
	print('			$scope.showInformationMessage = true;') #login information message flag

	#function to verify credentials
	print('			$scope.verifyCredentials = function(){')
	print('				$scope.showAlertMessage = false;')
	#if the user isn't validated and they entered something, then send input to server to validate, else show error message 
	print('				if(!isValidated){')	
	print('					if($scope.userName != "" && $scope.passWord != ""){')
	print('						signedInUser = $scope.userName;')
	print('						$http.post("verifyUser.py", $.param({"username": $scope.userName, "password": $scope.passWord}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('							signedInUser = signedInUser.trim();')
	print('							response = response.trim();')
	#if the credentials match, then log the user in, else show error message
	print('							if(response == signedInUser){')
	print('								isValidated = true;')
	print('								$rootScope.greetStr = response + \': \';')
	print('								$rootScope.actionStr = \'Logout\';')
	print('								$rootScope.dataTargetModal = \'#logoutModal\';')
	print('								$("#loginModal").modal("hide");')
	print('							}')
	print('							else{')
	print('								isValidated = false;')
	print('								$("#loginModal").modal("show");')
	print('								$scope.showAlertMessage = true;')
	print('							}')
	print('						});')
	print('						$scope.userName = "";')
	print('						$scope.passWord = "";')
	print('					}')
	print('					else{')
	print('						$("#loginModal").modal("show");')
	print('						$scope.showAlertMessage = true;')
	print('					}')
	print('				}')
	print('				else{')
	print('					signedInUser = "";')
	print('					$rootScope.greetStr = \'Not Identified: \';')
	print('					$rootScope.actionStr = \'Login\';')
	print('					isValidated = false;')
	print('					$rootScope.dataTargetModal = \'#loginModal\';')
	print('				}')
	print('			};')
	print('		});')

	#TITLE CONTROLLER
	print('		nutrientsApp.controller(\'TitleCtrl\', function($scope, $rootScope){')
	print('			$rootScope.greetStr = \'Not Identified: \';') #greet message containing username
	print('			$rootScope.actionStr = \'Login\';') #action message
	print('			$rootScope.dataTargetModal = \'#loginModal\';') #target modal
	print('			$rootScope.showTitleAlertMessage = false;') #show title alert message flag
	print('		});')

	#FORM CONTROLLER
	print('		nutrientsApp.controller(\'FormCtrl\', function($scope, $http, $window){')
	print('			$scope.globalAlertMessage = "";')
	#form titles
	print('			$scope.platformTitle = "Platforms";') #platform form title
	print('			$scope.sensorTitle = "Sensors";') #sensor form title

	#org variables
	print('			$scope.o_shortNameInput = "";') #shortname dropdown
	print('			$scope.o_shortNameTextInput = "";') #shortname text
	print('			$scope.o_nameInput = "";') #name dropdown
	print('			$scope.o_nameTextInput = "";') #name text
	print('			$scope.o_contactNameInput = "";') #contact name input
	print('			$scope.o_contactEmailInput = "";') #contact email input
	print('			$scope.o_urlInput = "";') #url input
	print('			$scope.o_browseActive = "active";') #active button flags
	print('			$scope.o_addActive = "";') 
	print('			$scope.o_editActive = "";')
	print('			$scope.o_deleteActive = "";')

	#plat variables
	print('			$scope.p_nameInput = "";') #platform name dropdown
	print('			$scope.p_nameTextInput = "";') #platform name input
	print('			$scope.p_descriptionInput = "";') #platform description input
	print('			$scope.p_latitudeInput = "";') #latitude input
	print('			$scope.p_longitudeInput = "";') #longitude input
	print('			$scope.p_organizationInput = "";')
	print('			$scope.p_urlInput = "";') #url input
	print('			$scope.p_rssInput = "";') #rss input
	print('			$scope.p_imageInput = "";') #image input
	print('			$scope.p_urnInput = "";') #urn input
	print('			$scope.p_statusInput = "";') #status input
	print('			$scope.p_browseActive = "active";') #active button flags
	print('			$scope.p_addActive = "";')
	print('			$scope.p_editActive = "";')
	print('			$scope.p_deleteActive = "";')

	#sens variables
	print('			$scope.s_sensorTypeInput = "";') #sensor type dropdown
	print('			$scope.s_verticalPositionInput = "0";') #vertical position input
	print('			$scope.s_numberInput = "";') #number input
	print('			$scope.s_browseActive = "active";') #active button flags
	print('			$scope.s_addActive = "";')
	print('			$scope.s_editActive = "";')
	print('			$scope.s_deleteActive = "";')

	#input boolean variables
	print('			$scope.o_shortNameTextInputActive = "disabled";')
	print('			$scope.o_nameTextInputActive = "disabled";')
	print('			$scope.o_contactNameInputActive = "disabled";')
	print('			$scope.o_contactEmailInputActive = "disabled";')
	print('			$scope.o_urlInputActive = "disabled";')

	print('			$scope.p_nameTextInputActive = "disabled";')
	print('			$scope.p_descriptionInputActive = "disabled";')
	print('			$scope.p_latitudeInputActive = "disabled";')
	print('			$scope.p_longitudeInputActive = "disabled";')
	print('			$scope.p_urlInputActive = "disabled";')
	print('			$scope.p_rssInputActive = "disabled";')
	print('			$scope.p_imageInputActive = "disabled";')
	print('			$scope.p_urnInputActive = "disabled";')

	print('			$scope.s_sensorTypeInputActive = "disabled";')
	print('			$scope.s_verticalPositionInputActive = "disabled";')
	print('			$scope.s_numberInputActive = "disabled";')

	print('			$scope.o_submitBtn = "disabled";')
	print('			$scope.p_submitBtn = "disabled";')
	print('			$scope.s_submitBtn = "disabled";')

	#get orgs
	sqlSelect = 'SELECT rowid, * FROM organization ORDER BY shortname ASC'
	dbh.execute(sqlSelect)
	rows = dbh.fetchall()
	print('	$scope.orgList = [')
	first = True
	rowid = 0
	shortname = ''
	name = ''
	contactName = ''
	contactEmail = ''
	url = ''
	#loop through all rows
	for row in rows:
		#if not the first row, then add comma
		if not first:
			print(',')
		#set 'None' strings to empty
		rowid = row['rowid']
		shortname = row['shortname']
		name = row['name']
		contactName = row['contactName']
		contactEmail = row['contactEmail']
		url = row['url']
		if str(shortname) == 'None':
			shortname = ''
		if str(name) == 'None':
			name = ''
		if str(contactName) == 'None':
			contactName = ''
		if str(contactEmail) == 'None':
			contactEmail = ''
		if str(url) == 'None':
			url = ''
		#print current row to frontend
		print('{rowid: "' + str(rowid) + '", shortname: "' + str(shortname) + '", name: "' + str(name) + '", contactName: "' + str(contactName) + '", contactEmail: "' + str(contactEmail) + '", url: "' + str(url) + '"}')
		first = False
	print('];')

	#get plats
	sqlSelect = 'SELECT rowid, * FROM platform ORDER BY name ASC'
	dbh.execute(sqlSelect)
	rows = dbh.fetchall()
	print('	$scope.platList = [')
	first = True
	rowid = 0
	name = ''
	description = ''
	loc_lat = 0.0
	loc_lon = 0.0
	organizationId = 0
	platformTypeId = 0
	url = ''
	rss = ''
	image = ''
	urn = ''
	status = 0
	#loop through rows
	for row in rows:
		#if not the first row, then add comma
		if not first:
			print(',')
		#set 'None' strings to empty
		rowid = row['rowid']
		name = row['name']
		description = row['description']
		loc_lat = row['loc_lat']
		loc_lon = row['loc_lon']
		organizationId = row['organizationId']
		platformTypeId = row['platformTypeId']
		url = row['url']
		rss = row['rss']
		image = row['image']
		urn = row['urn']
		status = row['status']
		if str(name) == 'None':
			name = ''
		if str(description) == 'None':
			description = ''
		if str(url) == 'None':
			url = ''
		if str(rss) == 'None':
			rss = ''
		if str(image) == 'None':
			image = ''
		if str(urn) == 'None':
			urn = ''
		#print current row to frontend
		print('{rowid: "' + str(rowid) + '", name: "' + str(name) + '", description: "' + str(description) + '", loc_lat: "' + str(loc_lat) + '", loc_lon: "' + str(loc_lon) + '", organizationId: "' + str(organizationId) + '", platformTypeId: "' + str(platformTypeId) + '", url: "' + str(url) + '", rss: "' + str(rss) + '", image: "' + str(image) + '", urn: "' + str(urn) + '", status: "' + str(row['status']) + '"}')
		first = False
	print('];')

	#get sensors
	sqlSelect = 'SELECT rowid, * FROM sensor'
	dbh.execute(sqlSelect)
	rows = dbh.fetchall()
	print('	$scope.sensList = [')
	first = True
	for row in rows:
		if not first:
			print(',')
		print('{rowid: "' + str(row['rowid']) + '", platformId: "' + str(row['platformId']) + '", sensorTypeId: "' + str(row['sensorTypeId']) + '", localId: "' + str(row['localId']) + '", sensorNumber: "' + str(row['sensorNumber']) + '", verticalPosition: "' + str(row['verticalPosition']) + '", firstObsDate: "' + str(row['firstObsDate']) + '", lastObsDate: "' + str(row['lastObsDate']) + '", status: "' + str(row['status']) + '"}')
		first = False
	print('];')

	#get nutrient sensor types
	sqlSelect = 'SELECT rowid, * FROM sensorType WHERE status = 1 ORDER BY shortTypeName ASC'
	dbh.execute(sqlSelect)
	rows = dbh.fetchall()
	print('	$scope.sensTypeList = [')
	first = True
	for row in rows:
		if not first:
			print(',')
		print('{rowid: "' + str(row['rowid']) + '", shortTypeName: "' + str(row['shortTypeName']) + '"}')
		first = False
	print('];')


	#selected lists
	print('			$scope.selectedPlatList = [];') #current selected platform list
	print('			$scope.selectedSensList = [];') #current selected sensor list

	#set active button function
	print('			$scope.setActiveButton = function(btnName){')
	#if the user is browsing the organizations set browse button to active
	print('				if(btnName == "BrowseOrganization"){')
	print('					$scope.o_browseActive = "active";')
	print('					$scope.o_addActive = "";')
	print('					$scope.o_editActive = "";')
	print('					$scope.o_deleteActive = "";')
	print('					$scope.o_shortNameTextInputActive = "disabled";')
	print('					$scope.o_nameTextInputActive = "disabled";')
	print('					$scope.o_contactNameInputActive = "disabled";')
	print('					$scope.o_contactEmailInputActive = "disabled";')
	print('					$scope.o_urlInputActive = "disabled";')
	print('					$scope.o_submitBtn = "disabled";')
	print('				}')
	#if the user is browsing the platforms set browse button to active
	print('				else if(btnName == "BrowsePlatform"){')
	print('					$scope.p_browseActive = "active";')
	print('					$scope.p_addActive = "";')
	print('					$scope.p_editActive = "";')
	print('					$scope.p_deleteActive = "";')
	print('					$scope.p_nameTextInputActive = "disabled";')
	print('					$scope.p_descriptionInputActive = "disabled";')
	print('					$scope.p_latitudeInputActive = "disabled";')
	print('					$scope.p_longitudeInputActive = "disabled";')
	print('					$scope.p_urlInputActive = "disabled";')
	print('					$scope.p_rssInputActive = "disabled";')
	print('					$scope.p_imageInputActive = "disabled";')
	print('					$scope.p_urnInputActive = "disabled";')
	print('					$scope.p_submitBtn = "disabled";')
	print('				}')
	#if the user is browsing the sensors set browse button to active
	print('				else if(btnName == "BrowseSensor"){')
	print('					$scope.s_browseActive = "active";')
	print('					$scope.s_addActive = "";')
	print('					$scope.s_editActive = "";')
	print('					$scope.s_deleteActive = "";')
	print('					$scope.s_sensorTypeInputActive = "disabled";')
	print('					$scope.s_verticalPositionInputActive = "disabled";')
	print('					$scope.s_numberInputActive = "disabled";')
	print('					$scope.s_submitBtn = "disabled";')
	print('				}')
	print('				else{')
	#if the user is authorized
	print('					if(isValidated){')
	#if the user is adding an organization set add button to active
	print('						if(btnName == "AddOrganization"){')
	print('							$scope.o_browseActive = "";')
	print('							$scope.o_addActive = "active";')
	print('							$scope.o_editActive = "";')
	print('							$scope.o_deleteActive = "";')
	print('							$scope.o_shortNameTextInputActive = "";')
	print('							$scope.o_nameTextInputActive = "";')
	print('							$scope.o_contactNameInputActive = "";')
	print('							$scope.o_contactEmailInputActive = "";')
	print('							$scope.o_urlInputActive = "";')
	print('							$scope.o_submitBtn = "";')
	print('						}')
	#if the user is editing an organization set add button to active
	print('						else if(btnName == "EditOrganization"){')
	print('							$scope.o_browseActive = "";')
	print('							$scope.o_addActive = "";')
	print('							$scope.o_editActive = "active";')
	print('							$scope.o_deleteActive = "";')
	print('							$scope.o_shortNameTextInputActive = "";')
	print('							$scope.o_nameTextInputActive = "";')
	print('							$scope.o_contactNameInputActive = "";')
	print('							$scope.o_contactEmailInputActive = "";')
	print('							$scope.o_urlInputActive = "";')
	print('							$scope.o_submitBtn = "";')
	print('						}')
	#if the user is deleting an organization set delete button to active
	print('						else if(btnName == "DeleteOrganization"){')
	print('							$scope.o_browseActive = "";')
	print('							$scope.o_addActive = "";')
	print('							$scope.o_editActive = "";')
	print('							$scope.o_deleteActive = "active";')
	print('							$scope.o_shortNameTextInputActive = "";')
	print('							$scope.o_nameTextInputActive = "";')
	print('							$scope.o_contactNameInputActive = "";')
	print('							$scope.o_contactEmailInputActive = "";')
	print('							$scope.o_urlInputActive = "";')
	print('							$scope.o_submitBtn = "";')
	print('						}')
	#if the user is adding a platform set add button to active
	print('						else if(btnName == "AddPlatform"){')
	print('							$scope.p_browseActive = "";')
	print('							$scope.p_addActive = "active";')
	print('							$scope.p_editActive = "";')
	print('							$scope.p_deleteActive = "";')
	print('							$scope.p_nameTextInputActive = "";')
	print('							$scope.p_descriptionInputActive = "";')
	print('							$scope.p_latitudeInputActive = "";')
	print('							$scope.p_longitudeInputActive = "";')
	print('							$scope.p_urlInputActive = "";')
	print('							$scope.p_rssInputActive = "";')
	print('							$scope.p_imageInputActive = "";')
	print('							$scope.p_urnInputActive = "";')
	print('							$scope.p_submitBtn = "";')
	print('						}')
	#if the user is editing a platform set edit button to active
	print('						else if(btnName == "EditPlatform"){')
	print('							$scope.p_browseActive = "";')
	print('							$scope.p_addActive = "";')
	print('							$scope.p_editActive = "active";')
	print('							$scope.p_deleteActive = "";')
	print('							$scope.p_nameTextInputActive = "";')
	print('							$scope.p_descriptionInputActive = "";')
	print('							$scope.p_latitudeInputActive = "";')
	print('							$scope.p_longitudeInputActive = "";')
	print('							$scope.p_urlInputActive = "";')
	print('							$scope.p_rssInputActive = "";')
	print('							$scope.p_imageInputActive = "";')
	print('							$scope.p_urnInputActive = "";')
	print('							$scope.p_submitBtn = "";')
	print('						}')
	#if the user is deleting a platform set delete button to active
	print('						else if(btnName == "DeletePlatform"){')
	print('							$scope.p_browseActive = "";')
	print('							$scope.p_addActive = "";')
	print('							$scope.p_editActive = "";')
	print('							$scope.p_deleteActive = "active";')
	print('							$scope.p_nameTextInputActive = "";')
	print('							$scope.p_descriptionInputActive = "";')
	print('							$scope.p_latitudeInputActive = "";')
	print('							$scope.p_longitudeInputActive = "";')
	print('							$scope.p_urlInputActive = "";')
	print('							$scope.p_rssInputActive = "";')
	print('							$scope.p_imageInputActive = "";')
	print('							$scope.p_urnInputActive = "";')
	print('							$scope.p_submitBtn = "";')
	print('						}')
	#if the user is adding a sensor set add button to active
	print('						else if(btnName == "AddSensor"){')
	print('							$scope.s_browseActive = "";')
	print('							$scope.s_addActive = "active";')
	print('							$scope.s_editActive = "";')
	print('							$scope.s_deleteActive = "";')
	print('							$scope.s_sensorTypeInputActive = "";')
	print('							$scope.s_verticalPositionInputActive = "";')
	print('							$scope.s_numberInputActive = "";')
	print('							$scope.s_submitBtn = "";')
	print('						}')
	#if the user is editing a sensor set edit button to active
	print('						else if(btnName == "EditSensor"){')
	print('							$scope.s_browseActive = "";')
	print('							$scope.s_addActive = "";')
	print('							$scope.s_editActive = "active";')
	print('							$scope.s_deleteActive = "";')
	print('							$scope.s_sensorTypeInputActive = "";')
	print('							$scope.s_verticalPositionInputActive = "";')
	print('							$scope.s_numberInputActive = "";')
	print('							$scope.s_submitBtn = "";')
	print('						}')
	#if the user is deleting a sensor set delete button to active
	print('						else if(btnName == "DeleteSensor"){')
	print('							$scope.s_browseActive = "";')
	print('							$scope.s_addActive = "";')
	print('							$scope.s_editActive = "";')
	print('							$scope.s_deleteActive = "active";')
	print('							$scope.s_sensorTypeInputActive = "";')
	print('							$scope.s_verticalPositionInputActive = "";')
	print('							$scope.s_numberInputActive = "";')
	print('							$scope.s_submitBtn = "";')
	print('						}')
	print('					}')
	print('					else{')
	print('						$scope.globalAlertMessage = "You must be logged in to " + btnName;')
	print('						$("#errorModal").modal("show");')
	print('					}')
	print('				}')
	print('			};')

	#set the information in the platform form
	print('			$scope.populatePlatform = function(selectedOrg){')
	#empty current selected lists
	print('				$scope.selectedPlatList = [];')
	print('				$scope.selectedSensList = [];')
	print('				var searchId = "";')
	#loop through the list of organizations to find the id of the currently selected organization
	print('				for(var i = 0; i < $scope.orgList.length; i++){')
	print('					if($scope.orgList[i].shortname == selectedOrg.shortname || $scope.orgList[i].name == selectedOrg.name){')
	print('						searchId = $scope.orgList[i].rowid;')
	print('						$scope.o_shortNameInput = $scope.orgList[i];')
	print('						$scope.o_shortNameTextInput = $scope.orgList[i].shortname;')
	print('						$scope.o_nameInput = $scope.orgList[i];')
	print('						$scope.o_nameTextInput = $scope.orgList[i].name;')
	print('						$scope.o_contactNameInput = $scope.orgList[i].contactName;')
	print('						$scope.o_contactEmailInput = $scope.orgList[i].contactEmail;')
	print('						$scope.o_urlInput = $scope.orgList[i].url;')
	print('						orgArrayPosition = i;')
	print('						break;')
	print('					}')
	print('				}')
	#loop through the total list of platforms to find the current organizations platforms
	print('				for(var i = 0; i < $scope.platList.length; i++){')
	print('					if($scope.platList[i].organizationId == searchId){')
	print('						$scope.selectedPlatList.push($scope.platList[i]);')
	print('					}')
	print('				}')
	#reset selected platform array position
	print('				platArrayPosition = 0;')
	#if no platforms were found, then set all fields to null, else assign the fields
	print('				if($scope.selectedPlatList.length == 0){')
	print('					$scope.p_nameInput = null;')
	print('					$scope.p_nameTextInput = null;')
	print('					$scope.p_descriptionInput = null;')
	print('					$scope.p_latitudeInput = null;')
	print('					$scope.p_longitudeInput = null;')
	print('					$scope.p_urlInput = null;')
	print('					$scope.p_rssInput = null;')
	print('					$scope.p_imageInput = null;')
	print('					$scope.p_urnInput = null;')
	print('					$scope.p_statusInput = null;')
	print('				}')
	print('				else{')
	print('					$scope.p_nameInput = $scope.selectedPlatList[0];')
	print('					$scope.p_nameTextInput = $scope.selectedPlatList[0].name;')
	print('					$scope.p_descriptionInput = $scope.selectedPlatList[0].description;')
	print('					$scope.p_latitudeInput = $scope.selectedPlatList[0].loc_lat;')
	print('					$scope.p_longitudeInput = $scope.selectedPlatList[0].loc_lon;')
	print('					$scope.p_urlInput = $scope.selectedPlatList[0].url;')
	print('					$scope.p_rssInput = $scope.selectedPlatList[0].rss;')
	print('					$scope.p_imageInput = $scope.selectedPlatList[0].image;')
	print('					$scope.p_urnInput = $scope.selectedPlatList[0].urn;')
	print('					$scope.p_statusInput = $scope.selectedPlatList[0].status;')
	print('				}')
	#set form titles
	print('				$scope.platformTitle = "Platforms in " + $scope.o_shortNameInput.shortname;')
	print('				$scope.sensorTitle = "Sensors";')
	#call populate sensor function
	print('				$scope.populateSensor($scope.selectedPlatList[0]);')
	print('			};')

	#set the information in the sensor form
	print('			$scope.populateSensor = function(selectedPlat){')
	#empty selected sensor list
	print('				$scope.selectedSensList = [];')
	print('				var searchId = "";')
	#loop through the current selected list of platforms to find the id
	print('				for(var i = 0; i < $scope.selectedPlatList.length; i++){')
	print('					if($scope.selectedPlatList[i].name == selectedPlat.name){')
	print('						searchId = $scope.selectedPlatList[i].rowid;')
	print('						$scope.p_nameTextInput = $scope.selectedPlatList[i].name;')
	print('						$scope.p_descriptionInput = $scope.selectedPlatList[i].description;')
	print('						$scope.p_latitudeInput = $scope.selectedPlatList[i].loc_lat;')
	print('						$scope.p_longitudeInput = $scope.selectedPlatList[i].loc_lon;')
	print('						$scope.p_urlInput = $scope.selectedPlatList[i].url;')
	print('						$scope.p_rssInput = $scope.selectedPlatList[i].rss;')
	print('						$scope.p_imageInput = $scope.selectedPlatList[i].image;')
	print('						$scope.p_urnInput = $scope.selectedPlatList[i].urn;')
	print('						platArrayPosition = i;')
	print('						break;')
	print('					}')
	print('				}')
	#loop through the total list of sensors to find the current platforms sensors
	print('				for(var i = 0; i < $scope.sensList.length; i++){')
	print('					if($scope.sensList[i].platformId == searchId){')
	print('						$scope.selectedSensList.push($scope.sensList[i]);')
	print('					}')
	print('				}')
	#reset selected sensor array position
	print('				sensArrayPosition = 0;')
	#if no sensors were found, then set fields to null, else assign the fields
	print('				if($scope.selectedSensList.length == 0){')
	print('					$scope.s_sensorTypeInput = null;')
	print('					$scope.s_verticalPositionInput = 0;')
	print('					$scope.s_numberInput = null;')
	print('				}')
	print('				else{')
	print('					for(var i = 0; i < $scope.sensTypeList.length; i++){')
	print('						if($scope.sensTypeList[i].rowid == $scope.selectedSensList[0].sensorTypeId){')
	print('							$scope.s_sensorTypeInput = $scope.sensTypeList[i];')
	print('							break;')
	print('						}')
	print('					}')
	print('					$scope.s_verticalPositionInput = $scope.selectedSensList[0].verticalPosition;')
	print('					$scope.s_numberInput = $scope.selectedSensList[0].sensorNumber;')
	print('				}')
	#set sensor form title
	print('				$scope.sensorTitle = "Sensors in " + $scope.p_nameInput.name;')
	print('			};')

	#function to send information to server
	print('			$scope.submitForm = function(whichForm){')
	#if the user is authorized
	print('				if(isValidated){')
	#if the user is submitting the organization form
	print('					if(whichForm == "orgForm"){')
	#if the user is adding an organization
	print('						if($scope.o_addActive == "active"){')
	#if all the required fields have been filled then post the information to the insert script, else report an error
	print('							if($scope.o_shortNameTextInput != "" && $scope.o_nameTextInput != "" && $scope.o_contactNameInput != "" && $scope.o_contactEmailInput != "" && $scope.o_urlInput != ""){')
	print('								$http.post("insert.py", $.param({"type": "org", "shortName": $scope.o_shortNameTextInput, "name": $scope.o_nameTextInput, "contactName": $scope.o_contactNameInput, "contactEmail": $scope.o_contactEmailInput, "url": $scope.o_urlInput}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Organization Added";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Organization Add Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information"')
	print('								$("#errorModal").modal("show")')
	print('							}')
	print('						}')
	#if the user is editing an organization
	print('						else if($scope.o_editActive == "active"){')
	#if all the required fields have been filled then post the information to the edit script, else report an error
	print('							if($scope.o_shortNameInput != "" && $scope.o_nameInput != "" && $scope.o_shortNameTextInput != "" && $scope.o_nameTextInput != "" && $scope.o_contactNameInput != "" && $scope.o_contactEmailInput != "" && $scope.o_urlInput != ""){')
	print('								$http.post("update.py", $.param({"type": "org", "shortName": $scope.o_shortNameInput.shortname, "name": $scope.o_nameInput.name, "shortNameNew": $scope.o_shortNameTextInput, "nameNew": $scope.o_nameTextInput, "contactName": $scope.o_contactNameInput, "contactEmail": $scope.o_contactEmailInput, "url": $scope.o_urlInput}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Organization Edited";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(){')
	print('									$scope.globalAlertMessage = "Organization Edit Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information";')
	print('								$("#errorModal").modal("show");')
	print('							}')
	print('						}')
	#if the user is deleting an organization
	print('						else if($scope.o_deleteActive == "active"){')
	#if all the required fields have been filled then post the information to the delete script, else report an error
	print('							if($scope.o_shortNameInput != "" && $scope.o_nameInput != ""){')
	print('								$http.post("delete.py", $.param({"type": "org", "shortName": $scope.o_shortNameInput.shortname}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Organization Deleted";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(){')
	print('									$scope.globalAlertMessage = "Organization Delete Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information";')
	print('								$("#errorModal").modal("show");')
	print('							}')
	print('						}')
	print('						else{')
	print('							$scope.globalAlertMessage = "You\'re Browsing . . .";')
	print('							$("#errorModal").modal("show");')
	print('						}')
	print('					}')
	#if the user is submitting the platform form
	print('					else if(whichForm == "platForm"){')
	#if the user is adding a platform
	print('						if($scope.p_addActive == "active"){')
	#if all the required fields have been filled then post the information to the insert script, else report an error
	print('							if($scope.p_nameTextInput != "" && $scope.p_descriptionInput != "" && $scope.p_latitudeInput != "" && $scope.p_longitudeInput != "" && $scope.p_urlInput != "" && $scope.p_rssInput != "" && $scope.p_imageInput != ""){')
	print('								$http.post("insert.py", $.param({"type": "plat", "orgShortName": $scope.o_shortNameInput.shortname, "name": $scope.p_nameTextInput, "description": $scope.p_descriptionInput, "loc_lat": $scope.p_latitudeInput, "loc_lon": $scope.p_longitudeInput, "organizationId": $scope.o_shortNameInput.rowid, "url": $scope.p_urlInput, "rss": $scope.p_rssInput, "image": $scope.p_imageInput, "urn": $scope.p_urnInput}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Platform Added";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(){')
	print('									$scope.globalAlertMessage = "Platform Add Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information";')
	print('								$("#errorModal").modal("show");')
	print('							}')
	print('						}')
	#if the user is editing a platform
	print('						else if($scope.p_editActive == "active"){')
	#if all the required fields have been filled then post the information to the edit script, else report an error
	print('							if($scope.p_nameTextInput != "" && $scope.p_descriptionInput != "" && $scope.p_latitudeInput != "" && $scope.p_longitudeInput != "" && $scope.p_urlInput != "" && $scope.p_rssInput != "" && $scope.p_imageInput != ""){')
	print('								$http.post("update.py", $.param({"type": "plat", "orgShortName": $scope.o_shortNameInput.shortname, "nameNew": $scope.p_nameTextInput, "name": $scope.p_nameInput.name, "description": $scope.p_descriptionInput, "loc_lat": $scope.p_latitudeInput, "loc_lon": $scope.p_longitudeInput, "organizationId": $scope.o_shortNameInput.rowid, "url": $scope.p_urlInput, "rss": $scope.p_rssInput, "image": $scope.p_imageInput, "urn": $scope.p_urnInput}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Platform Edited";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(){')
	print('									$scope.globalAlertMessage = "Platform Edit Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information";')
	print('								$("#errorModal").modal("show");')
	print('							}')
	print('						}')
	#if the user is deleting a platform
	print('						else if($scope.p_deleteActive == "active"){')
	#if all the required fields have been filled then post the information to the delete script, else report an error
	print('							if($scope.p_nameInput != ""){')
	print('								$http.post("delete.py", $.param({"type": "plat", "name": $scope.p_nameInput.name}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Platform Deleted";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(){')
	print('									$scope.globalAlertMessage = "Platform Delete Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information";')
	print('								$("#errorModal").modal("show");')
	print('							}')
	print('						}')
	print('						else{')
	print('							$scope.globalAlertMessage = "You\'re Browsing . . .";')
	print('							$("#errorModal").modal("show");')
	print('						}')
	print('					}')
	#if the user is submitting the sensor form
	print('					else if(whichForm == "sensForm"){')
	#if the user is adding a sensor
	print('						if($scope.s_addActive == "active"){')
	#if all the required fields have been filled then post the information to the insert script, else report an error
	print('							if($scope.s_sensorTypeInput != "" && $scope.s_verticalPositionInput != "" && $scope.s_numberInput != ""){')
	print('								$http.post("insert.py", $.param({"type": "sens", "sensorType": $scope.s_sensorTypeInput.rowid, "verticalPosition": $scope.s_verticalPositionInput, "sensorNumber": $scope.s_numberInput, "platformId": $scope.p_nameInput.rowid, "provider": $scope.o_shortNameInput.shortname, "stationLabel": $scope.p_nameInput.name, "sensorTypeName": $scope.s_sensorTypeInput.shortTypeName}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Sensor Added";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(){')
	print('									$scope.globalAlertMessage = "Sensor Add Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information";')
	print('								$("#errorModal").modal("show");')
	print('							}')
	print('						}')
	#if the user is editing a sensor
	print('						else if($scope.s_editActive == "active"){')
	#if all the required fields have been filled then post the information to the edit script, else report an error
	print('							if($scope.s_sensorTypeInput != "" && $scope.s_verticalPositionInput != "" && $scope.s_numberInput != ""){')
	print('								$http.post("update.py", $.param({"type": "sens", "rowid": $scope.selectedSensList[sensArrayPosition].rowid, "sensorType": $scope.s_sensorTypeInput.rowid, "verticalPosition": $scope.s_verticalPositionInput, "sensorNumber": $scope.s_numberInput}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Sensor Edited";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(){')
	print('									$scope.globalAlertMessage = "Sensor Edit Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information";')
	print('								$("#errorModal").modal("show");')
	print('							}')
	print('						}')
	#if the user is deleting a sensor
	print('						else if($scope.s_deleteActive == "active"){')
	#if all the required fields have been filled then post the information to the delete script, else report an error
	print('							if($scope.s_sensorTypeInput != "" && $scope.s_verticalPositionInput != "" && $scope.s_numberInput != ""){')
	print('								$http.post("delete.py", $.param({"type": "sens", "sensorType": $scope.s_sensorTypeInput.shortTypeName, "verticalPosition": $scope.s_verticalPositionInput, "sensorNumber": $scope.s_numberInput}), {headers: {\'Content-type\': \'application/x-www-form-urlencoded\'}}).success(function(response){')
	print('									console.log(response);')
	print('									$scope.globalAlertMessage = "Sensor Deleted";')
	print('									$("#errorModal").modal("show");')
	print('								}).error(function(){')
	print('									$scope.globalAlertMessage = "Sensor Delete Failed";')
	print('									$("#errorModal").modal("show");')
	print('								});')
	print('							}')
	print('							else{')
	print('								$scope.globalAlertMessage = "Missing Information";')
	print('								$("#errorModal").modal("show");')
	print('							}')
	print('						}')
	print('						else{')
	print('							$scope.globalAlertMessage = "You\'re Browsing . . .";')
	print('							$("#errorModal").modal("show");')
	print('						}')
	print('					}')
	print('				}')
	print('				else{')
	print('					$scope.globalAlertMessage = "You must be logged in to submit data";')
	print('					$("#errorModal").modal("show");')
	print('				}')
	print('			};')

	#function to move through the list of organizations, platforms, and sensors
	print('			$scope.traverseList = function(whichList, whichWay){')
	print('				if(whichList == "org"){')
	print('					if(whichWay == "GoBackAll"){')
	print('						orgArrayPosition = 0;')
	print('					}')
	print('					else if(whichWay == "GoBack"){')
	print('						orgArrayPosition = orgArrayPosition - 1;')
	print('					}')
	print('					else if(whichWay == "GoForward"){')
	print('						orgArrayPosition = orgArrayPosition + 1;')
	print('					}')
	print('					else if(whichWay == "GoForwardAll"){')
	print('						orgArrayPosition = $scope.orgList.length - 1;')
	print('					}')
	print('					if(orgArrayPosition < 0){')
	print('						orgArrayPosition = 0;')
	print('					}')
	print('					else if(orgArrayPosition > $scope.orgList.length - 1){')
	print('						orgArrayPosition = $scope.orgList.length - 1;')
	print('					}')
	print('					console.log("org: " + orgArrayPosition);')
	print('					$scope.o_shortNameInput = $scope.orgList[orgArrayPosition];')
	print('					$scope.o_nameInput = $scope.orgList[orgArrayPosition];')
	print('					$scope.o_shortNameTextInput = $scope.orgList[orgArrayPosition].shortname;')
	print('					$scope.o_nameTextInput = $scope.orgList[orgArrayPosition].name;')
	print('					$scope.o_contactNameInput = $scope.orgList[orgArrayPosition].contactName;')
	print('					$scope.o_contactEmailInput = $scope.orgList[orgArrayPosition].contactEmail;')
	print('					$scope.o_urlInput = $scope.orgList[orgArrayPosition].url;')
	print('					$scope.populatePlatform($scope.o_shortNameInput);')
	print('				}')
	print('				else if(whichList == "plat"){')
	print('					if($scope.p_nameInput.name != ""){')
	print('						if(whichWay == "GoBackAll"){')
	print('							platArrayPosition = 0;')
	print('						}')
	print('						else if(whichWay == "GoBack"){')
	print('							platArrayPosition = platArrayPosition - 1;')
	print('						}')
	print('						else if(whichWay == "GoForward"){')
	print('							platArrayPosition = platArrayPosition + 1;')
	print('						}')
	print('						else if(whichWay == "GoForwardAll"){')
	print('							platArrayPosition = $scope.selectedPlatList.length - 1;')
	print('						}')
	print('						if(platArrayPosition < 0){')
	print('							platArrayPosition = 0;')
	print('						}')
	print('						else if(platArrayPosition > $scope.selectedPlatList.length - 1){')
	print('							platArrayPosition = $scope.selectedPlatList.length - 1;')
	print('						}')
	print('						console.log("plat: " + platArrayPosition);')
	print('						$scope.p_nameInput = $scope.selectedPlatList[platArrayPosition];')
	print('						$scope.p_nameTextInput = $scope.selectedPlatList[platArrayPosition].name;')
	print('						$scope.p_descriptionInput = $scope.selectedPlatList[platArrayPosition].description;')
	print('						$scope.p_latitudeInput = $scope.selectedPlatList[platArrayPosition].loc_lat;')
	print('						$scope.p_longitudeInput = $scope.selectedPlatList[platArrayPosition].loc_lon;')
	print('						$scope.p_urlInput = $scope.selectedPlatList[platArrayPosition].url;')
	print('						$scope.p_rssInput = $scope.selectedPlatList[platArrayPosition].rss;')
	print('						$scope.p_imageInput = $scope.selectedPlatList[platArrayPosition].image;')
	print('						$scope.p_urnInput = $scope.selectedPlatList[platArrayPosition].urn;')
	print('						$scope.populateSensor($scope.p_nameInput);')
	print('					}')
	print('				}')
	print('				else if(whichList == "sens"){')
	print('					if($scope.s_sensorTypeInput.rowid != ""){')
	print('						if(whichWay == "GoBackAll"){')
	print('							sensArrayPosition = 0;')
	print('						}')
	print('						else if(whichWay == "GoBack"){')
	print('							sensArrayPosition = sensArrayPosition - 1;')
	print('						}')
	print('						else if(whichWay == "GoForward"){')
	print('							sensArrayPosition = sensArrayPosition + 1;')
	print('						}')
	print('						else if(whichWay == "GoForwardAll"){')
	print('							sensArrayPosition = $scope.selectedSensList.length - 1;')
	print('						}')
	print('						if(sensArrayPosition < 0){')
	print('							sensArrayPosition = 0;')
	print('						}')
	print('						else if(sensArrayPosition > $scope.selectedSensList.length - 1){')
	print('							sensArrayPosition = $scope.selectedSensList.length - 1;')
	print('						}')
	print('						console.log("sens: " + sensArrayPosition);')
	print('						for(var i = 0; i < $scope.sensTypeList.length; i++){')
	print('							if($scope.sensTypeList[i].rowid == $scope.selectedSensList[sensArrayPosition].sensorTypeId){')
	print('								$scope.s_sensorTypeInput = $scope.sensTypeList[i];')
	print('								break;')
	print('							}')
	print('						}')
	print('						$scope.s_verticalPositionInput = $scope.selectedSensList[sensArrayPosition].verticalPosition;')
	print('						$scope.s_numberInput = $scope.selectedSensList[sensArrayPosition].sensorNumber;')
	print('					}')
	print('				}')
	print('			};')

	#function to open a link in a new tab
	print('			$scope.openLink = function(whichLink){')
	print('				if(whichLink == "ORGURL"){')
	print('					$window.open($scope.o_urlInput, "_blank");')
	print('				}')
	print('				if(whichLink == "URL"){')
	print('					$window.open($scope.p_urlInput, "_blank");')
	print('				}')
	print('				else if(whichLink == "RSS"){')
	print('					$window.open($scope.p_rssInput, "_blank");')
	print('				}')
	print('				else if(whichLink == "IMAGE"){')
	print('					$window.open($scope.p_imageInput, "_blank");')
	print('				}')
	print('			};')
	print('		});')
	print('</script>')