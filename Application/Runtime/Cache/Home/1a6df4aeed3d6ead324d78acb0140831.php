<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="/thinkphp/Public/js/restPage.js"></script>
<link rel="stylesheet" type="text/css" href="/thinkphp/Public/css/stylesheet.css" />
	<title>Restaurant Page</title>
</head>
<body>
	<a href="<?php echo U('index');?>" style="position:absolute;right:5px;top: 5px;">Restuarants</a>
	<h1>Welcome to <?php echo ($data["rest_name"]); ?></h1>
	<p>Id: <?php echo ($data["rest_id"]); ?><br>Addr: <?php echo ($data["rest_addr"]); ?></p>

</body>
</html>