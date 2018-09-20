function alipay(){
	$('#amount').val((parseFloat($('#price').text())*100).toFixed());
	$('#payment_method').val("alipay");
	$('#payform').submit();
}

function wechat(){
	$('#amount').val((parseFloat($('#price').text())*100).toFixed());
	$('#payment_method').val("wechatpay");
	$('#payform').submit();
}

function exportExcel(){
	alert('log');
}