<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title>Find Restaurants</title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="/thinkphp/Public/css/stylesheet.css" />
<script type="text/javascript" src="/thinkphp/Public/js/findByAddr.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBNBFH1IlNnjDNnu12pe009XUbEgCdyDEY&language=en&region=US&libraries=places&callback=initMap" async defer></script>
</head>
<body>
	<a href="<?php echo U('index');?>" style="position:absolute;right:5px;top: 5px;">Restuarants</a>
	<h1>Welcome.</h1>
	<p>Please click your position on the map</p>
	<div id="map" style="width: 48%;float: left;margin-right: 15px;"></div>
	<form id="dataForm" action="" onsubmit="return validate()" method="POST" style="width: 48%;float: right;">
	<table >
		<tr>
			<th style="text-align: left">Or input your address here:</th>
		</tr>
		<tr>
			<td>
				<input class="addr" type="text" id="addr" name="addr" >
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value="Find">
				<input type="hidden" id="zipcode" name="zipcode">
			</td>
		</tr>
	</table>
	<div class="result" id="resultForm" style="height:318px;float: bottom;">
		Restuarants will be shown here
	</div>
	</form>
</body>
</html>