<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/thinkphp/Public/css/stylesheet.css" />
	<title>Restaurants</title>
	<meta charset="utf-8">
</head>
<body>
	<a href="./" style="position:absolute;right:5px;top: 5px;">Home Page</a>
	<h1>Welcome to restaurants page</h1>
	<p>Firstly, please select a <a href="<?php echo U('findByZip');?>">zipcode</a>.</p>
	<p>There is a <a href="<?php echo U('googleMap');?>">tool</a> to calculate distance between two locations.<br></p>
	<p>Or you can try to input an <a href="<?php echo U('findByAddr');?>">address</a>.</p>

</body>
</html>