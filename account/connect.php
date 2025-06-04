<?php
$servername = "localhost";
// Enter your MySQL username below(default=root)
$username = "nexuvmvy_nexusinsights";
// Enter your MySQL password below
$password = "Xander24427279";
$dbname = "nexuvmvy_nexusinsights";

// Create connection
$conne = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conne->connect_error) {
    header("location:connection_error.php?error=$conne->connect_error");
    die($conne->connect_error);
}
?>
