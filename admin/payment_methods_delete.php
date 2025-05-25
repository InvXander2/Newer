<?php
include('../inc/config.php');
include 'includes/session.php';

if(isset($_POST['delete'])){
  $id = $_POST['id'];

  try {
    $conn = $pdo->open();

    $stmt = $conn->prepare("DELETE FROM payment_mode WHERE id=:id");
    $stmt->execute(['id'=>$id]);

    $_SESSION['success'] = 'Payment method deleted successfully';
  } catch (PDOException $e){
    $_SESSION['error'] = $e->getMessage();
  }

  $pdo->close();
}

header('location: payment_methods.php');
