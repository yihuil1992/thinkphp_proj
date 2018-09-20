<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="/thinkphp/Public/js/findByZip.js"></script>
<link rel="stylesheet" type="text/css" href="/thinkphp/Public/css/stylesheet.css" />
	<title>Find Restaurants</title>
	<meta charset="utf-8">
</head>
<body>
<a href="<?php echo U('index');?>" style="position:absolute;right:5px;top: 5px;">Restuarants</a>
<form id="dataForm" action="" method="POST" onsubmit="return validate()">
	<ul>
		<li>
			<input type="number" id="zip" name="zipcode" placeholder="Input your zipcode">
		</li>
		<li>
			<input type="submit" value="Submit">
		</li>
	</ul>
</form>
<div class="result" id="resultForm">
	Restuarants will be shown here
</div>

</body>
</html>