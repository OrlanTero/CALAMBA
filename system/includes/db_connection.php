<?php
$servername = "localhost";
$username = "cmdc";  
$password = "115314450Oshe";      
$dbname = "cmdc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
