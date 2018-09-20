// $(document).ready(function(){
// 	alert('我正在被执行！');
// });
function validate(){
	if ($('#zip').val()==""){
		alert("Please input zipcode.");
		return false;
	} else{
		$.ajax({
			cache:true,
			type:'POST',
			url:'findByZip',
			data:$('#dataForm').serialize(),
			async:true,
			error:function(request){
				alert('Connection error:'+request.error);
			},
			success:function(data){
				if(data!=""){
					$("#resultForm").html('zipcode: '+$('#zip').val()+'<br><br>');
					for(i=0;i<data.length;i++){
						$("#resultForm").html($("#resultForm").html()+'<a href="restPage?id='
							+data[i].rest_id+'">'+data[i].rest_name+'</a><br>');	
					}
				}else{
					alert("No restuarant found in this area.");
				}
			}
		});
		return false;
	}
}