<?php
$servername = "sql201.infinityfree.com"; // Correct host
$username = "if0_39045086";              // Correct user
$password = "Xgyuc8McZpz8Rr";            // Correct password
$dbname = "if0_39045086_hyip_db";        // Correct database name

// Create connection
$conne = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conne->connect_error) {
    header("location:connection_error.php?error=" . $conne->connect_error);
    die($conne->connect_error);
}
?>
