<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Drive Links</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            
            padding: 20px 20px 20px 20px;
            
        
           
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
          //  max-width: 400px;
            width: 95%;
            margin:5px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        textarea {
            width: calc(100% - 16px);
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
input{border-radius:3px}
        input[type="checkbox"] {
            margin-right: 8px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }
    </style>
    <script>
    function copyToClipboard() {
        var copyText = document.getElementById("driveLinks");
        copyText.select();
        document.execCommand("copy");
    }
</script>
</head>
<body>


<?php
require_once('Functions.php');
$ddrive_dom = "https://drive.deadtoons.online/file/";
$key = "AIzaSyCH4I2OxQnocrzdiZLeLOSI6GOnkuUR-yI";
function upload($id, $key) {
$conn = new mysqli('localhost', 'zikanim1_gd', '6@7A8a9a', 'zikanim1_file_upload_system');

    $fields = "name,mimeType,shared,fileExtension,size,videoMediaMetadata";
    $gurl = 'https://www.googleapis.com/drive/v3/files/' .$id . '?fields=' .$fields .'&key=' .$key;
    $gfile = file_get_contents($gurl);
    $gfjson = json_decode($gfile, true);
    $mname = str_replace("'", '', $gfjson['name']);
    $name = str_replace(' ', '+' ,$mname);
    $type = $gfjson['mimeType'];
    $shared = $gfjson['shared'];
    $extension = $gfjson['fileExtension'];
    $size = $gfjson['size'];
    $duration = mstos($gfjson['videoMediaMetadata']['durationMillis']);
  #  echo "<pre>";
  #   print_r($gfjson);
  #  echo "</pre>";
    if($extension != "zip") {
    
    $doodstream = doodstream($id);
    $streamwish = streamwish($id, $name);
    $filemoon = filemoon($id, $name);
    $voe = voe($id);
  }
    $sendcm = sendcm($id, $name);
    $filepress = filepress($id);
    # echo vidguard($id, $name);
    # echo $streamtape = streamtape($id, $name);
    
    $query = "INSERT INTO `links_for_deadtoons`(`Name`, `Id`, `size`, `Type`, `mimeType`, `duration` ) VALUES ('$mname','$id','$size','$extension', '$type', '$duration')";
    if($conn) {
$result = mysqli_query($conn, $query);
if ($result) {
    $query2 = "INSERT INTO `Servers`(`Id`, `FilePress_GDrive`, `Dood_Stream`, `SendCM`, `FileMoon`, `Streamwish`, `Voe`, `Vidguard`, `Streamtape`) VALUES ('$id','$filepress', '$doodstream', '$sendcm', '$filemoon', '$streamwish', '$voe', '$vidguard', '$streamtape')";
    $result2 = mysqli_query($conn, $query2);
    if ($result2) {
        echo '<a href="/file/' .$id .'">'.$mname.'</a><hr>';
        $conn->close();
    } else {
        // Handle the case when the second query fails
        echo "Error executing the second query: " . mysqli_error($conn);
        $conn->close();

    }
} else {
    // Handle the case when the first query fails
    echo "Error executing the first query: " . mysqli_error($conn);
    $conn->close();

}

}
else echo "nocon";
}

function extractDriveId($url) {
    $pattern = '/\/d\/(.*?)(\/|$)/';
    preg_match($pattern, $url, $matches);

    return isset($matches[1]) ? $matches[1] : null;
}
function extractFolderId($url) {
    $pattern = '/\/folders\/(.*?)(\/|$)/';
    preg_match($pattern, $url, $matches);

    return isset($matches[1]) ? $matches[1] : null;
}



if (isset($_POST['fsubmit'])) {
$folId = extractFolderId($_POST['folder']);
$folurl = 'https://www.googleapis.com/drive/v3/files?q=%27' .$folId .'%27+in+parents&key='.$key;
$fjson = file_get_contents($folurl);
# echo $fjson;
$fdjson = json_decode($fjson, true);

$files = $fdjson['files'];
echo "<pre>";
print_r($fdjson);
echo "</pre>";
echo '<div id="driveLinks" style="text-align:center;">';
foreach ($files as $file) {
    $id = $file['id'];
    upload($id, $key);
}
echo "</div>";
}




if (isset($_POST['submit'])) {
    // Get the user input and explode it into an array based on the selected option
    $inputText = $_POST['inputText'];
   /* $separator = isset($_POST['separator']) ? $_POST['separator'] : 'comma';

    // Remove spaces and empty lines
    $inputText = preg_replace('/\s+/', '', $inputText);

    if ($separator === 'comma') {
        $dataArray = explode(',', $inputText);
    } else {
        $dataArray = preg_split('/\r\n|\r|\n/', $inputText);
    }
*/
$dataArray = explode(',', $inputText);
    // Extract Google Drive IDs using the provided function
    $driveIds = array_map('extractDriveId', $dataArray);

    // Remove null values (invalid links)
    $driveIds = array_filter($driveIds);

    // Print the resulting array of Google Drive IDs
    # echo '<pre>';
    # print_r($driveIds);
   # echo '</pre>';
    # echo "<br>";
    
    foreach ($driveIds as $link) {
    
    upload($link, $key);
    
}

}

function mstos($milliseconds) {
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);

    $formattedTime = '';

    if ($hours > 0) {
        $formattedTime .= $hours . 'h ';
    }

    if ($minutes > 0 || $hours === 0) {
        $formattedTime .= ($minutes % 60) . 'min';
    }

    return $formattedTime;
}
?>



<button class="btn btn-success" onclick="copyToClipboard()">Copy All Links</button>
<form method="post">
<label for="gd_folder">Enter Folder Link</label>
<input value="" id="gd_folder" rows="2 cols="4" type="text" name="folder" placeholder="Folder Link">
<br><br>
<input type="submit" name="fsubmit" value="Submit">
</form>
<hr>Nhi Bro
<form method="post">
    <label for="inputText">Enter links:</label>
    <textarea id="inputText" name="inputText" rows="4" cols="50"></textarea>

    <br>
    <!--

    <label>
        <input type="checkbox" name="separator" value="comma">
        Separate by commas
    </label>

    <br>

    <label>
        <input type="checkbox" name="separator" value="newline">
        Separate by new lines
    </label>

    <br>
-->
    <input type="submit" name="submit" value="Submit">
</form>

</body>
</html>
