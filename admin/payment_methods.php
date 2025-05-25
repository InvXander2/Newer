<?php
include('../inc/config.php');
include 'includes/session.php';

$conn = $pdo->open();

// Add new payment method
if(isset($_POST['add_payment'])){
    $method = trim($_POST['payment_method']);
    $wallet = trim($_POST['wallet_address']);

    if(!empty($method) && !empty($wallet)){
        $stmt = $conn->prepare("INSERT INTO payment_mode (name, wallet_address) VALUES (:name, :wallet)");
        $stmt->execute(['name' => $method, 'wallet' => $wallet]);
        $_SESSION['success'] = 'Payment method added successfully';
    } else {
        $_SESSION['error'] = 'Both payment method and wallet address are required';
    }
}

// Delete payment method
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM payment_mode WHERE id=:id");
    $stmt->execute(['id' => $id]);
    $_SESSION['success'] = 'Payment method deleted';
}

// Fetch methods safely
try {
    $stmt = $conn->prepare("SELECT * FROM payment_mode ORDER BY id DESC");
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
          if(isset($_SESSION['error'])){
            echo "
              <div class='alert alert-danger alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                <h4><i class='icon fa fa-warning'></i> Error!</h4>
                ".$_SESSION['error']."
              </div>
            ";
            unset($_SESSION['error']);
          }

          if(isset($_SESSION['success'])){
            echo "
              <div class='alert alert-success alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                <h4><i class='icon fa fa-check'></i> Success!</h4>
                ".$_SESSION['success']."
              </div>
            ";
            unset($_SESSION['success']);
          }
          ?>

          <form method="POST" class="form-inline mb-3" style="margin-bottom: 20px;">
            <div class="form-group mr-2">
              <input type="text" name="payment_method" class="form-control" placeholder="Enter payment method" required>
            </div>
            <div class="form-group mr-2">
              <input type="text" name="wallet_address" class="form-control" placeholder="Enter wallet address" required>
            </div>
            <button type="submit" name="add_payment" class="btn btn-primary">Add Method</button>
          </form>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Payment Method</th>
                <th>Wallet Address</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($methods)): ?>
                <?php foreach($methods as $index => $method): ?>
                  <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($method->name); ?></td>
                    <td><?= htmlspecialchars($method->wallet_address); ?></td>
                    <td>
                      <a href="?delete=<?= $method->id; ?>" onclick="return confirm('Delete this method?')" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4">No payment methods found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>

        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
