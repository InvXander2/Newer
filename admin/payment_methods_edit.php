<?php
include('../inc/config.php');
include 'includes/session.php';

if(isset($_POST['edit'])){
  $id = $_POST['id'];
  $name = trim($_POST['name']);
  $wallet = trim($_POST['wallet_address']);

  try {
    $conn = $pdo->open();

    $stmt = $conn->prepare("UPDATE payment_mode SET name=:name, wallet_address=:wallet WHERE id=:id");
    $stmt->execute(['name'=>$name, 'wallet'=>$wallet, 'id'=>$id]);

    $_SESSION['success'] = 'Payment method updated successfully';
  } catch (PDOException $e){
    $_SESSION['error'] = $e->getMessage();
  }

  $pdo->close();
}

header('location: payment_methods.php');
