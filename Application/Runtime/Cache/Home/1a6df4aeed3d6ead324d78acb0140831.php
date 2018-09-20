<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="/Application/Home/Public/Js/restPage.js"></script>
<link rel="stylesheet" type="text/css" href="/Application/Home/Public/CSS/stylesheet.css" />
	<title>Restaurant Page</title>
</head>
<body>
	<a href="/index.php/Home/Rest" style="position:absolute;right:5px;top: 5px;">Restuarants</a>
	<h1>Welcome to <?php echo ($data["rest_name"]); ?></h1>
	<p>Id: <?php echo ($data["rest_id"]); ?><br>Addr: <?php echo ($data["rest_addr"]); ?><br>Price: <ins id="price"><?php echo ($data["rest_price"]); ?></ins></p>
	<table>
		<tr>
			<th>Functions:</th>
		</tr>
		<tr>
			<td><button onclick="alipay()">Pay with Alipay</button></td>
		</tr>
		<tr>
			<td><button onclick="wechat()">Pay with WeChat Pay</button></td>
		</tr>
		<tr>
			<td><button onclick="exportExcel()">Export transaction log</button></td>
		</tr>
	</table>
	<form action="payment" method="POST" id="payform">
		<input type="hidden" name="transaction_id" value="test1236"/>
		<input type="hidden" name="payment_method" id="payment_method"/>
		<input type="hidden" name="amount" id="amount"/>
	</form>
</body>
</html>