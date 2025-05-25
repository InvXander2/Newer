<?php
include('../inc/config.php');
include 'session.php';

$conn = $pdo->open();

// Add new payment method
if(isset($_POST['add_payment'])){
    $method = $_POST['payment_method'];
    if(!empty($method)){
        $stmt = $conn->prepare("INSERT INTO payment_mode (name) VALUES (:name)");
        $stmt->execute(['name' => $method]);
        $_SESSION['success'] = 'Payment method added successfully';
    } else {
        $_SESSION['error'] = 'Payment method name is required';
    }
}

// Delete payment method
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM payment_mode WHERE id=:id");
    $stmt->execute(['id' => $id]);
    $_SESSION['success'] = 'Payment method deleted';
}

$methods = $conn->query("SELECT * FROM payment_mode ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);

?>

<?php include('inc/head.php'); ?>

<body>
<?php include('inc/sidebar.php'); ?>
<div class="content-wrapper">
  <?php include('inc/header.php'); ?>

  <section class="content">
    <div class="container">
      <h2>Manage Payment Methods</h2>

      <?php
      if(isset($_SESSION['error'])){
        echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
        unset($_SESSION['error']);
      }
      if(isset($_SESSION['success'])){
        echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
        unset($_SESSION['success']);
      }
      ?>

      <form method="POST" class="form-inline mb-3">
        <div class="form-group">
          <input type="text" name="payment_method" class="form-control" placeholder="Enter payment method">
        </div>
        <button type="submit" name="add_payment" class="btn btn-primary ml-2">Add Method</button>
      </form>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Payment Method</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($methods as $index => $method): ?>
          <tr>
            <td><?= $index + 1; ?></td>
            <td><?= $method->name; ?></td>
            <td>
              <a href="?delete=<?= $method->id; ?>" onclick="return confirm('Delete this method?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<?php include('inc/scripts.php'); ?>
</body>
</html>
