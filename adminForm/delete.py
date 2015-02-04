#!/opt/Python-3.4.1/python
#print("Content-Type:text/html\n\n")

#Date Last Modified: 	02-04-2014
#Module: 				delete.py
#Object: 				delete requested organization, platform, or sensor from database
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
	shortName = data.getvalue('shortName')
	sql = 'DELETE FROM organization WHERE shortname = "' + str(shortName) + '"'
	print(sql)
	dbh.execute(sql)
	dbconnect.commit()
	dbconnect.close()
elif formType == 'plat':
	name = data.getvalue('name')
	sql = 'DELETE FROM platform WHERE name = "' + str(name) + '"'
	print(sql)
	dbh.execute(sql)
	dbconnect.commit()
	dbconnect.close()