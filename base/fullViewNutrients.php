<?php 
	// Module:             fullViewNutrients.php
	// Object:             Full window view of GCOOS nutrients assets 
	// Return:             xHTML/Display
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="144000">
<meta name="google-site-verification" content="vGXfERc9U04ySZxnVfTL4djuv4Q_8hcTZySQf-uvvEk" />
<title>GCOOS H-N Data Portal</title>
<link href="../css/gcoos_04_full.css" rel="stylesheet" type="text/css" />
<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>
<script src="http://jawj.github.io/OverlappingMarkerSpiderfier/bin/oms.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <!-- Google Analytics -->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    
      ga('create', 'UA-5033874-2', 'auto');
      ga('send', 'pageview');
    
    </script>
    <!-- End GA Code -->
</head>

<body class="thrColFixHdr" style="height: 100%; width: 100%;">
<div style="width:100%; height:100%">
  
  <div id="mainContent">
	<div>
		  <div align="center">
          	<?php include("nutrientsMapFull.php") ?>
          </div>          
          <p>&nbsp;</p>
    </div>
    
  </div>
<!-- end #container --></div>
</body>
</html>