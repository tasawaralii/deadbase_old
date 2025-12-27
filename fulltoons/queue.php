<?php
require_once('db.php');
$sql = "SELECT drive_id FROM EpisodeLinks WHERE NOT EXISTS ( SELECT 1 FROM Links_info WHERE Links_info.id = EpisodeLinks.drive_id )";
$packs_sql = "SELECT drive_id FROM Packs WHERE NOT EXISTS ( SELECT 1 FROM Links_info WHERE Links_info.id = Packs.drive_id )";
$movie_sql = "SELECT movie_drive_id FROM movieLinks WHERE NOT EXISTS ( SELECT 1 FROM Links_info WHERE Links_info.id = movieLinks.movie_drive_id );";
$result = mysqli_query($conn, $sql);
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row['drive_id'];
        }
$packs_result = mysqli_query($conn, $packs_sql);
while ($packs_row = mysqli_fetch_assoc($packs_result)) {
        $data[] = $packs_row['drive_id'];
        }
        
        $movie_result = mysqli_query($conn, $movie_sql);
while ($movie_row = mysqli_fetch_assoc($movie_result)) {
        $data[] = $movie_row['movie_drive_id'];
        }
        
        $jsonResult = json_encode($data);
       
        // NOT Upload To Servers Due to Unknown Error
$not_server_sql = "SELECT * FROM Links_info WHERE NOT EXISTS ( SELECT 1 FROM Servers WHERE Servers.id = Links_info.id )";
$not_server_result = mysqli_query($conn, $not_server_sql);
$not_server = array();
while ($not_server_row = mysqli_fetch_assoc($not_server_result)) {
$not_server[] = $not_server_row['Id'];
}
$not_server_json = json_encode($not_server);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, max-width=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title>Queue</title>
</head>
<body class="p-3">
<div id="uploaded"></div>
<hr>
<button class="btn btn-success mx-auto d-block" onclick="upload()">Upload</button>
<hr>
<div id="not_server"></div>
</body>
<script>
let data = <?php echo $jsonResult.";\n"; ?>
let data_not_server = <?php echo $not_server_json.";\n"; ?>

async function upload() {
    if (data.length > 0) {
        document.getElementById("uploaded").innerHTML = '';
        const uniqueIds = [...new Set(data)]; // Remove duplicates
        const uploadedIds = new Set(); // Track uploaded IDs
        
        for (let id of uniqueIds) {
            if (!uploadedIds.has(id)) {
                try {
                    await sendRequest(id);
                    uploadedIds.add(id); // Add the uploaded ID to the set
                } catch (error) {
                    console.error('Error:', error);
                }
            }
            
        }
        alert("done");
    } else {
        alert('No Url');
    }
}

function sendRequest(id) {
    return new Promise((resolve, reject) => {
        var upload = "/upload.php?id=" + id + "&to=all";
        var xhr = new XMLHttpRequest();
        xhr.open("GET", upload, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    var responseElement = document.createElement("div");
                    responseElement.innerHTML = xhr.responseText;
                    document.getElementById("uploaded").appendChild(responseElement);
                    resolve();
                } else {
                    reject(xhr.statusText);
                }
            }
        };
        xhr.send();
    });
}

// Call the function to start uploading
// upload().catch(error => console.error('Error:', error));


function upload_not_server() {
document.getElementById("not_server").innerHTML = '';
    data_not_server.forEach(id => {
        var upload = "/upload.php?id=" + id + "&to=server";
        var xhr = new XMLHttpRequest();
        xhr.open("GET", upload, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var responseElement = document.createElement("div");
                responseElement.innerHTML = xhr.responseText;
                document.getElementById("not_server").appendChild(responseElement);
            }
        };
        xhr.send();
    });
}
function show() {
if (data.length > 0) {
    data.forEach(id => {
        var idList = document.createElement("div");
        idList.innerHTML = `${id} <hr>`;
        document.getElementById("uploaded").appendChild(idList);
    });
       } else {
       document.getElementById("uploaded").innerHTML= '<p> No URL</p>';
           }
}
function show_not_server() {
if(data_not_server != '') {
    data_not_server.forEach(id => {
        var idList = document.createElement("div");
        idList.innerHTML = `${id} <hr>`;
        document.getElementById("not_server").appendChild(idList);
    });
    var not_server = document.createElement("div");
    not_server.innerHTML = `<button class="btn btn-success mx-auto d-block" onclick="upload_not_server()">Upload Failed Files</button><hr>`;
        document.getElementById("not_server").appendChild(not_server);
    }
}
document.addEventListener("DOMContentLoaded", function() {
    show();
    show_not_server();
});
</script>
</html>