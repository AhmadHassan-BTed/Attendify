<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_portal_beenishtasaffar";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully to database: " . $dbname;
?>