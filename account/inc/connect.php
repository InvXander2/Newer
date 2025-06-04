<?php
$servername = "localhost"; // Correct host
$username = "nexuvmvy_nexusinsights";              // Correct user
$password = "Xander24427279";            // Correct password
$dbname = "nexuvmvy_nexusinsights";        // Correct database name

// Create connection
$conne = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conne->connect_error) {
    header("location:connection_error.php?error=" . $conne->connect_error);
    die($conne->connect_error);
}
?>
