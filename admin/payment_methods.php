<?php
include('../inc/config.php');
include 'includes/session.php';

$conn = $pdo->open();

// Add new payment method
if (isset($_POST['add_payment'])) {
    $method = trim($_POST['payment_method']);
    $wallet = trim($_POST['wallet_address']);

    if (!empty($method) && !empty($wallet)) {
        $stmt = $conn->prepare("INSERT INTO payment_mode (name, wallet_address) VALUES (:name, :wallet)");
        $stmt->execute(['name' => $method, 'wallet' => $wallet]);
        $_SESSION['success'] = 'Payment method added successfully';
    } else {
        $_SESSION['error'] = 'Both payment method and wallet address are required';
    }
}

// Delete payment method
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM payment_mode WHERE mode_id = :id");
    $stmt->execute(['id' => $id]);
    $_SESSION['success'] = 'Payment method deleted';
}

// Fetch methods safely
try {
    $stmt = $conn->prepare("SELECT * FROM payment_mode ORDER BY mode_id DESC");
    $stmt->execute();
    $methods = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching payment methods: " . $e->getMessage();
    $methods = [];
}

$pdo->close();
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Manage Payment Methods</h1>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <?php
          if (isset($_SESSION['error'])) {
            echo "
              <div class='alert alert-danger alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                <h4><i class='icon fa fa-warning'></
