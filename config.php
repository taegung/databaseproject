<?php
$servername = "192.168.56.101";
$port = "4567";
$username = "taegung";
$password = "1234";
$database = "db";

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>