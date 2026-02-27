<?php
$host = "localhost";
$user = "root";
$password = ""; 
$dbname = "credit"; 

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı xətası: " . $conn->connect_error);
}
?>