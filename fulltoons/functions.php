<?php 

function base_url($slug) {
    echo 'https://drive.deadtoons.online/'.$slug;
}

function fetchContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // echo "cURL Error: " . curl_error($ch);
        return false;
    }

    curl_close($ch);
    return $response;
}

function telegram($msj) {
    $msj = str_replace('\n', PHP_EOL, $msj);
    $botToken = '7071121072:AAHiGKQEf2AmGyUStg9B_qAzPIymTfy8TZY';
    $privateChatId = '5962388220';
    $telegramApiUrl = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';
    $dataPrivate = [
    'chat_id' => $privateChatId,
    'text' => $msj,
    'parse_mode' => 'HTML',
];

$optionsPrivate = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($dataPrivate),
    ],
];

$contextPrivate = stream_context_create($optionsPrivate);
$resultPrivate = file_get_contents($telegramApiUrl, false, $contextPrivate);
}



function sendcm($id, $name) {
    $gurl = 'https://pt-dream-7d7b.codephp5677.workers.dev/?id=' .$id .'/' .$name;
    $apiUrl = "https://send.cm/api/upload/url?key=173108uc11b2454ijotcp1&url=$gurl";
    $result = file_get_contents($apiUrl);

    // Check if the request was successful
    if ($result !== false) {
        // Now $result contains the response from the API
        $response = json_decode($result, true);
        $slug = $response['result']['filecode'];
        return $slug;
    
    } else {
        // Handle the case when the request fails
        return "Error accessing the API.";
       return $apiUrl;
    }
}

function streamtape($id, $name) {
   $gurl = 'https://pt-dream-7d7b.codephp5677.workers.dev/?id='.$id .'/' .$name;
    $apiUrl = "https://api.streamtape.com/remotedl/add?login=ea6e2be422b3d4684c68&key=P89VglQ1X1t0Q6l&url=$gurl";
    $result = file_get_contents($apiUrl);
    // Check if the request was successful
   if ($result !== false) {
        // Now $result contains the response from the API
        $response = json_decode($result, true);
        $slug = $response['result']['id'];
        return $slug;
    } else {
        // Handle the case when the request fails
        return "Error accessing the API.";
    }
    
}


function vidguard($id, $name)
{
    $url = 'https://api.vidguard.to/v1/remote/upload';
    $key = 'Xkarj6GyY73PlBVEZ1m6xDv98NWRg0JwenA';
    $fileUrl = 'https://pt-dream-7d7b.codephp5677.workers.dev/?id=' . $id . '/' . $name;

    $data = [
        'key' => $key,
        'url' => $fileUrl
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);
    
    

    $json = json_decode($response, true);
    $slug = $json['result'][0]['id'];
    return $slug;
}


    


function voe($id) {
    $gurl = 'https://drive.google.com/file/d/' . $id;
    $apiUrl = "https://voe.sx/api/upload/url?key=yr5hpJOdpHk2b4O7lggYwBLRtdGD9tM07nqQBUpzysGJZUsa4fONLsejrl5zU70N&url=$gurl";

    // Use error suppression to prevent displaying errors
    $result = @file_get_contents($apiUrl);

    // Check if the request was successful
    if ($result !== false) {
        // Now $result contains the response from the API
        $response = json_decode($result, true);
        $slug = $response['result']['file_code'];
        return $slug;
    } else {
        // If an error occurred or the request failed, return null
        return null;
    }
}




function filemoon($id, $name) {

    $gurl = 'https://pt-dream-7d7b.codephp5677.workers.dev/?id=' . $id . '/' .$name;
    $apiUrl = "https://filemoonapi.com/api/remote/add?key=44535mt2kldy8dozzrb3i&url=$gurl";
    $result = file_get_contents($apiUrl);

    // Check if the request was successful
    if ($result !== false) {
        // Now $result contains the response from the API
        $response = json_decode($result, true);
        $slug = $response['result']['filecode'];
        return $slug;
    
    } else {
        // Handle the case when the request fails
        return "Error accessing the API.";
    }
}




function streamwish($id, $name) {

    $gurl = 'https://pt-dream-7d7b.codephp5677.workers.dev/?id=' . $id . '/' .$name;

    $apiUrl = "https://api.streamwish.com/api/upload/url?key=11321prdf4zkxxeansr7o&url=$gurl";
    $result = file_get_contents($apiUrl);

    if ($result !== false) {
        // Now $result contains the response from the API
        $response = json_decode($result, true);
        $slug = $response['result']['filecode'];
       return $slug;
    
    } 
}

function doodstream($id) {
    $gurl = 'https://drive.google.com/file/d/' . $id;
    $key1 = "387412bb0lmbmou1izx1ui";
    $key2 = "82220pdjeunxl3pawsj8v";
    $apiUrl = "https://doodapi.com/api/upload/url?key=$key1&url=$gurl";

    try {
        $result = file_get_contents($apiUrl);

        // Check if the request was successful
        if ($result !== false) {
            // Now $result contains the response from the API
            $response = json_decode($result, true);
            $slug = $response['result']['filecode'];
            return $slug;
        }
    } catch (Exception $e) {
        // If an error occurs with the first key, try uploading using the second key
        $apiUrl = "https://doodapi.com/api/upload/url?key=$key2&url=$gurl";
        $result = file_get_contents($apiUrl);
        
        if ($result !== false) {
            // Now $result contains the response from the API
            $response = json_decode($result, true);
            $slug = $response['result']['filecode'];
            return $slug;
        } else {
            // Handle the error appropriately, such as logging or returning a default value
            return null;
        }
    }
}




function filepress($id) {
$conn = new mysqli('localhost', 'fulltoon_anime', '6@7A8a9a', 'fulltoon_Anime');
$query = "SELECT `Domain` FROM `Server_info` WHERE `Name` = 'FilePress_GDrive'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$d = str_replace("/file", '', $row['Domain']);
$domain = 'https://'.$d.'api/v1/file/add';

$data = array(
    "key" => "xw0Ntj+YFk6Fi25EzgXYwIa9osX6Gv/fd3WFEMB9OFo=",
    "id" => $id,
    
    "isAutoUploadToStream" => true
);

// Convert the data array to JSON
$jsonData = json_encode($data);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $domain); // Replace {Domain} with your domain
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Execute cURL session and fetch the response
$response = curl_exec($ch);

// Check if there was any error
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    
    $filepress = json_decode($response, true);
    return $filepress['data']['_id'];
}

// Close cURL session
curl_close($ch);
}


function gdtot($id) {
    $url = "https://drive.google.com/file/d/{$id}/view?usp=drive_link";
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://new2.gdtot.dad/api/upload/link',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('email' => 'animesdubbed4u@gmail.com','api_token' => 'RYFojfdEkQWXQlKsKeE9wGjux4CE','url' => $url),
  CURLOPT_HTTPHEADER => array(
	'Cookie: PHPSESSID=inmagsnejqpcardjeergj8394i'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo "<pre>";
$response = json_decode($response, true);
// print_r($response);
if($response['status'] == "true") {
    return $response['data']['0']['id'];
} else {
    echo "GDTOT is Making Problem";
    exit;
}
}

function formatSize($size) {
    $bytes = (float)$size;
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, 2) . ' ' . $units[$i];
    
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
   
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
   
    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 
   
    return round($bytes, $precision) . $units[$pow]; 
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
/*














function sendcm($id, $name) {
    // $id = "hdhdh"; // Remove this line to use the parameter value

    $gurl = 'https://send.cm/api/upload/url?key=173108uc11b2454ijotcp1&url=https://pt-dream-7d7b.codephp5677.workers.dev/?id=1ECjFLq9Pkduo5lxLNX5doRSsZeKmn-e-/okay%20.mkv' ;/*. $id . '/' .$name;
    $apiUrl = "https://send.cm/api/upload/url?key=173108uc11b2454ijotcp1&url=$furl";

    // Set up a custom context with a timeout
    $context = stream_context_create(['http' => ['timeout' => 10]]);

    $result = file_get_contents($gurl, false, $context);

    // Check if the request was successful
    if ($result !== false) {
        // Now $result contains the response from the API
        $response = json_decode($result, true);
        $slug = $response['result']['filecode'];
        return $result;
    } else {
        // Handle the case when the request fails
        $lastError = error_get_last();
        return "Error accessing the API. Details: " . print_r($lastError, true);
    }
}





function vidhide($id) {
    // $id = "hdhdh"; // Remove this line to use the parameter value

    $gurl = 'https://drive.google.com/file/d/' .$id '/view?title=' .$name;

    $apiUrl = "https://vidhideapi.com/api/upload/url?key=26516vmu1q3pfcwvg6wwt&url=$gurl";
    $result = file_get_contents($apiUrl);

    // Check if the request was successful
    if ($result !== false) {
        // Now $result contains the response from the API
        $response = json_decode($result, true);
        $slug = $response['result']['filecode'];
        return $slug;
        /* return $slug;
    } else {
        // Handle the case when the request fails
        return "Error accessing the API.";
    }
}




*/






?>