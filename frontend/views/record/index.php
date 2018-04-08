<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Document</title>
</head>
<body>
    <input type="file" onchange="uploadfile(this);" >
</body>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
    function uploadfile(fileobj)
    {
        var filesa = fileobj.files['0'];
	var formdata = new FormData();
	formdata.append('file', filesa);
	console.log(filesa);
        $.ajax({
            url: '/record/upload',
            type: 'POST',
            dataType: 'json',
            data: formdata,
	    cache: false,//上传文件无需缓存
            processData: false,//用于对data参数进行序列化处理 这里必须false
            contentType: false, //必须
        })
        .done(function(res) {
            console.log(res);
        })
        .fail(function(res) {
            console.log(res);
        })
        .always(function() { });
    }
</script>
</html>
