<?php

// Set up your request data
$data = array(
    "email" => "animesdubbed4u@gmail.com",
    "api_token" => "RYFojfdEkQWXQlKsKeE9wGjux4CE",
    "url" => "https://drive.google.com/file/d/1K0cO1EVwuhA3MnqFiaL2--ed3q4-hgrO/view?usp=drive_link"

);

// Convert the data array to JSON
$jsonData = json_encode($data);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, "https://new2.gdtot.dad/api/upload/link"); 
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
    echo $response;
}

// Close cURL session
curl_close($ch);

?>
