<?php
$servername = "127.0.0.1";
$port = "3306";
$username = "root";
$password = "wnrtks12!";
$database = "dbproject";

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>