#!/opt/Python-3.4.1/python
#print("Content-Type:text/html\n\n")

#Date Last Modified: 	02-13-2014
#Module: 				delete.py
#Object: 				insert requested organization, platform, or sensor into database
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
import sqlite3
import pathToDb
import cgi, cgitb

cgitb.enable()

#setup DB
dbconnect = sqlite3.connect(pathToDb.pathToDb)
dbconnect.row_factory = sqlite3.Row
dbh = dbconnect.cursor()

#get form data
data = cgi.FieldStorage()
print(data)
formType = data.getvalue('type')

#check which form the user is submitting
if formType == 'org':
	#retrieve all fields from client
	shortName = data.getvalue('shortName')
	name = data.getvalue('name')

	contactName = data.getvalue('contactName')
	if str(contactName) == 'None':
		contactName = ''

	contactEmail = data.getvalue('contactEmail')
	if str(contactEmail) == 'None':
		contactEmail = ''

	url = data.getvalue('url')
	if str(url) == 'None':
		url = ''

	#insert organization
	sql = 'INSERT INTO organization VALUES ("' + str(shortName) + '","' + str(name) + '","' + str(contactName) + '","' + str(contactEmail) + '","' + str(url) + '")'
	print(sql)
	dbh.execute(sql)
	dbconnect.commit()
	dbconnect.close()
elif formType == 'plat':
	#retrieve all fields from client
	name = data.getvalue('name')
	description = data.getvalue('description')
	loc_lat = data.getvalue('loc_lat')
	loc_lon = data.getvalue('loc_lon')
	organizationId = data.getvalue('organizationId')

	url = data.getvalue('url')
	if str(url) == 'None':
		url = ''

	rss = data.getvalue('rss')
	if str(rss) == 'None':
		rss = ''

	image = data.getvalue('image')
	if str(image) == 'None':
		image = ''

	orgShortName = data.getvalue('orgShortName')
	urn = 'urn:gcoos:stations:' + orgShortName + ':' + name

	#insert platform
	sql = 'INSERT INTO platform VALUES ("' + str(name) + '", "' + str(description) + '", ' + str(loc_lat) + ', ' + str(loc_lon) + ',' + str(organizationId) + ', 18, "' + str(url) + '", "' + str(rss) + '", "' + str(image) + '", "' + str(urn) + '", NULL)'
	print(sql)
	dbh.execute(sql)
	dbconnect.commit()
	dbconnect.close()
elif formType == 'sens':
	#retrieve all fields from client
	sensorType = data.getvalue('sensorType')
	verticalPosition = data.getvalue('verticalPosition')
	platformId = data.getvalue('platformId')

	sqlSelect = 'SELECT sensorTypeId FROM sensor WHERE platformId = ' + str(platformId)
	dbh.execute(sqlSelect)
	rows = dbh.fetchall()
	sensorCounter = 0
	for row in rows:
		if row['sensorTypeId'] == int(sensorType):
			sensorCounter = sensorCounter + 1

	sensorNumber = sensorCounter + 1
	provider = data.getvalue('provider')
	stationLabel = data.getvalue('stationLabel')
	sensorTypeName = data.getvalue('sensorTypeName')
	localId = str(provider) + '.' + str(stationLabel) + '.' + str(sensorTypeName) + '.' + str(sensorNumber)

	#find current highest sensor number for a given type
	sqlSelect = 'SELECT rowid,* FROM sensor'
	dbh.execute(sqlSelect)
	rows = dbh.fetchall()
	for row in rows:
		maxId = row['rowid']

	#insert sensor
	sql = 'INSERT INTO sensor VALUES (' + str(maxId + 1) + ',' + str(platformId) + ',' + str(sensorType) + ',"' + str(localId) + '",' + str(sensorNumber) + ',' + str(verticalPosition) + ', "2008-01-01T00:00:00Z", "2008-01-01T00:00:00Z", 2)'
	print(sql)
	dbh.execute(sql)
	dbconnect.commit()
	dbconnect.close()