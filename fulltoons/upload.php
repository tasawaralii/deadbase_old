<?php
require_once('Functions.php');
require_once('db.php');
require('data.php');

// Get the API key from the data.php file
$key = $data['gdkey'];

// Check if the 'id' parameter is set in the URL
if(isset($_GET['id'])) {
    // Retrieve the file information from Google Drive API
    $id = $_GET['id'];
    $fields = "name,mimeType,shared,fileExtension,size,videoMediaMetadata";
    $gurl = 'https://www.googleapis.com/drive/v3/files/' . $id . '?fields=' . $fields . '&key=' . $key;
    $gfile = file_get_contents($gurl);
    if($gfile === false) {
        exit; 
    }
    $gfjson = json_decode($gfile, true);
    // Proceed with handling the file information if there are no errors
    // ...

    echo $mname = str_replace("'", '', $gfjson['name']);
    echo "<hr>";
    $name = str_replace(' ', '+' ,$mname);
    $type = $gfjson['mimeType'];
    $shared = $gfjson['shared'];
    $extension = $gfjson['fileExtension'];
    $size = $gfjson['size'];
    if (isset($gfjson['videoMediaMetadata'])) {
        $duration = ", '" . $gfjson['videoMediaMetadata']['durationMillis'] . "'";
        $durCol = ", `duration`";
    } else {
        $duration = '';
        $durCol = '';
    }
  
    if($extension != "zip") {
        $doodstream = doodstream($id);
        $streamwish = streamwish($id, $name);
        $filemoon = filemoon($id, $name);
        $voe = voe($id);
        $voe = ($voe == 'error') ? '': $voe;
    }
    $sendcm = sendcm($id, $name);
    $filepress = filepress($id);
    $gdtot = gdtot($id);
    if($_GET['to'] == "all") {
        $query = "INSERT INTO `Links_info`(`user`, `Name`, `Id`, `size`, `Type`, `mimeType` $durCol) VALUES ('2', '$mname','$id','$size','$extension', '$type'$duration)";
        $result = mysqli_query($conn, $query);
    }
    $query2 = "INSERT INTO `Servers`(`Id`, `live`, `FilePress_GDrive`, `GDtot`, `Dood_Stream`, `SendCM`, `FileMoon`, `Streamwish`, `Voe`) VALUES ('$id','5', '$filepress', '$gdtot', '$doodstream', '$sendcm', '$filemoon', '$streamwish', '$voe')";
    $result2 = mysqli_query($conn, $query2);
    mysqli_close($conn); // Close the database connection
}
?>
