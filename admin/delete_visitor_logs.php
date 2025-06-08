<?php
session_start();
include '../includes/session.php'; // Ensure admin authentication

if(isset($_POST['id']) && !empty($_POST['id'])) {
  try {
    $conn = $pdo->open();
    $stmt = $conn->prepare("DELETE FROM visitor_logs WHERE id = :id");
    $stmt->execute(['id' => $_POST['id']]);
    $pdo->close();

    echo json_encode(['success' => true, 'message' => 'Log deleted']);
  } catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid ID']);
}
?>
