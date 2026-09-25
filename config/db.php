<?php
$host = "localhost";
$user = "root";
$password = ""; 
$dbname = "spirited_finds_db";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>