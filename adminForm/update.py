#!/opt/Python-3.4.1/python
#print("Content-Type:text/html\n\n")

#Date Last Modified: 	02-13-2014
#Module: 				delete.py
#Object: 				update requested organization, platform, or sensor in database
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
	shortNameNew = data.getvalue('shortNameNew')
	nameNew = data.getvalue('nameNew')

	contactNameNew = data.getvalue('contactName')
	if str(contactNameNew) == 'None':
		contactNameNew = ''

	contactEmailNew = data.getvalue('contactEmail')
	if str(contactEmailNew) == 'None':
		contactEmailNew = ''

	urlNew = data.getvalue('url')
	if str(urlNew) == 'None':
		urlNew = ''

	#update organization
	sql = 'UPDATE organization SET shortname = "' + str(shortNameNew) + '", name = "' + str(nameNew) + '", contactName = "' + str(contactNameNew) + '", contactEmail = "' + str(contactEmailNew) + '", url = "' + str(urlNew) + '" WHERE shortName = "' + str(shortName) + '"'
	print(sql)
	dbh.execute(sql)
	dbconnect.commit()
	dbconnect.close()
elif formType == 'plat':
	#retrieve all fields from client
	name = data.getvalue('name')
	nameNew = data.getvalue('nameNew')
	description = data.getvalue('description')
	loc_lat = data.getvalue('loc_lat')
	loc_lon = data.getvalue('loc_lon')

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
	urn = 'urn:gcoos:stations:' + orgShortName + ':' + nameNew

	#update platform
	sql = 'UPDATE platform SET name = "' + str(nameNew) + '", description = "' + str(description) + '", loc_lat = ' + str(loc_lat) + ', loc_lon = ' + str(loc_lon) + ', url = "' + str(url) + '", rss = "' + str(rss) + '", image = "' + str(image) + '", urn = "' + str(urn) + '" WHERE name = "' + str(name) + '"'
	print(sql)
	dbh.execute(sql)
	dbconnect.commit()
	dbconnect.close()
elif formType == 'sens':
	#retrieve all fields from client
	sensorType = data.getvalue('sensorType')
	verticalPosition = data.getvalue('verticalPosition')
	sensorNumber = data.getvalue('sensorNumber')
	rowid = data.getvalue('rowid')

	#update sensor
	sql = 'UPDATE sensor SET sensorTypeId = ' + str(sensorType) + ', verticalPosition = ' + str(verticalPosition) + ', sensorNumber = ' + str(sensorNumber) + ' WHERE rowid = ' + str(rowid)
	print(sql)
	dbh.execute(sql)
	dbconnect.commit()
	dbconnect.close()