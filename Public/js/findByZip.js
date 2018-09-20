function validate() {
    if ($('#zip').val() == "") {
        alert("Please input zipcode.");
        return false;
    } else {
        $.ajax({
            cache: true,
            type: 'POST',
            url: 'findByZip',
            data: $('#dataForm').serialize(),
            async: true,
            error: function (request) {
                alert('Connection error:' + request.error);
            },
            success: function (data) {
                if (data != "No restuarant") {
                    result = JSON.parse(data);
                    $("#resultForm").html('zipcode: ' + $('#zip').val() + '<br><br>');
                    for (i = 0; i < result.length; i++) {
                        $("#resultForm").html($("#resultForm").html() + '<a href="restPage?id='
                            + result[i].rest_id + '">' + result[i].rest_name + '</a><br>');
                    }
                } else {
                    alert("No restuarant found in this area.");
                }
            }
        });
        return false;
    }
}