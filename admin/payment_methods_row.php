<?php
include('../inc/config.php');

$conn = $pdo->open();

$id = $_POST['id'];

$stmt = $conn->prepare("SELECT * FROM payment_mode WHERE id=:id");
$stmt->execute(['id'=>$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$pdo->close();

echo json_encode($row);
