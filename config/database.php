<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_store";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Fetch current configurations
$stmt = $conn->prepare("SELECT * FROM config");
$stmt->execute();
$configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Set file upload limit
ini_set('upload_max_filesize', $configs['file_upload_limit'] . 'M');
ini_set('post_max_size', $configs['file_upload_limit'] . 'M');
?>