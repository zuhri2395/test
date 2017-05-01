<!DOCTYPE html5>
<html>
    <head>
        <title>Voice to Text</title>
        <link href="http://hayageek.github.io/jQuery-Upload-File/4.0.10/uploadfile.css" rel="stylesheet">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="http://hayageek.github.io/jQuery-Upload-File/4.0.10/jquery.uploadfile.min.js"></script>
    </head>
    <body>
        <!--<form action="speech1.php" method="POST" enctype="multipart/form-data">
            <label for="voice">Input Voice File</label>
            <input type="file" id="voice" name="voice">
            <button type="submit">Submit</button>
        </form>-->
        <div id="fileuploader">Upload</div>
        <h3>Result from server</h3>
        <div id="result"></div>
    </body>
    <script>
        $(document).ready(function() {
	        $("#fileuploader").uploadFile({
	        url:"speech1.php",
	        fileName:"voice",
            allowedTypes:"wav,mp3",
            acceptFiles:"audio/*",
            returnType: "json",
            onSuccess: function(files, response, xhr, pd) {
                $('#result').html(response);
            }
	        });
        });
</script>
</html>