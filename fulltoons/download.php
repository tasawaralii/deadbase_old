/*<?php
$file = 'https://www.googleapis.com/drive/v3/files/1_bzBBQCWBEZW7f8W2XmL7Qzp3yMm7pN9?alt=media&key=AIzaSyCH4I2OxQnocrzdiZLeLOSI6GOnkuUR-yI';

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="Name.mkv"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Expires: 0');
    readfile($file);
    exit;
} else {
    echo 'File not found.';
}
?>

<?php
// Assuming the custom link is passed as a query parameter named 'file'
if(isset($_GET['file'])) {
    $file = $_GET['file'];

    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Expires: 0');
        readfile($file);
        exit;
    } else {
        echo 'File not found.';
    }
} else {
    echo 'File link not provided.';
}
?>
*/
<?php
$url = 'https://www.googleapis.com/drive/v3/files/1_bzBBQCWBEZW7f8W2XmL7Qzp3yMm7pN9?alt=media&key=AIzaSyCH4I2OxQnocrzdiZLeLOSI6GOnkuUR-yI';
// Replace 'YOUR_API_KEY' with your actual API key

// Get the file name from the URL
$fileName = $_GET['name'];

$headers = get_headers($url, 1);
$fileSize = isset($headers['Content-Length']) ? $headers['Content-Length'] : 'unknown';
// Set the appropriate headers
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . $fileSize);

// Use readfile() to output the file contents
readfile($url);
?>