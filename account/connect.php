<?php
$servername = "sql201.infinityfree.com";
// Enter your MySQL username below(default=root)
$username = "if0_39045086";
// Enter your MySQL password below
$password = "Xgyuc8McZpz8Rr";
$dbname = "if0_39045086_hyip_db";

// Create connection
$conne = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conne->connect_error) {
    header("location:connection_error.php?error=$conn->connect_error");
    die($conne->connect_error);
}
?>
