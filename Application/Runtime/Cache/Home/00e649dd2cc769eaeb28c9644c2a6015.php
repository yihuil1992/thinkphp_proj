<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title>Map</title>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBNBFH1IlNnjDNnu12pe009XUbEgCdyDEY&language=en&region=US&callback=initMap"
    async defer></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="/Application/Home/Public/Js/getDistance.js"></script>
<link rel="stylesheet" type="text/css" href="/Application/Home/Public/CSS/stylesheet.css" />
</head>
<body>
<a href="/index.php/Home/Rest" style="position:absolute;right:5px;top: 5px;">Restuarants</a>
<h1>This a google Map Module.</h1>
<p>please select three points for calculating distance:</p>
<div id="map"></div>
<form id="dataForm" action="" onsubmit="return validate()" method="POST">
<table>
	<tr>
		<th colspan="3">Or Input Map coordinates:</th>
	</tr>
	<tr>
		<td>Pos1:</td>
		<td><input class="coord" type="text" id="lat1" name="lat1"></td>
		<td><input class="coord" type="text" id="lng1" name="lng1"></td>
	</tr>
	<tr>
		<td>Pos2:</td>
		<td><input class="coord" type="text" id="lat2" name="lat2"></td>
		<td><input class="coord" type="text" id="lng2" name="lng2"></td>
	</tr>
	<tr>
		<td>Pos3:</td>
		<td><input class="coord" type="text" id="lat3" name="lat3"></td>
		<td><input class="coord" type="text" id="lng3" name="lng3"></td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit" value="Calculate"></td>
		<td id="result">Distance is: </td>
	</tr>
</table>
</form>
<p></p>
</body>
</html>