<?php
// Make $conn a global variable
global $conn;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nhs_portal_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
$db_connection_successful = true;
if ($conn->connect_error) {
    $db_connection_successful = false;
    die("Connection failed: " . $conn->connect_error);
}
?>
