<?php 
  include 'includes/session.php';

  if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT *, mode_id AS pmid FROM payment_mode WHERE mode_id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();

    $pdo->close();

    echo json_encode($row);
  }
?>
