<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Manage Payment Methods</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Payment Methods</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container">

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
            <input type="text" name="payment_method" class="form-control" placeholder="Enter payment method" required>
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
              <td><?= htmlspecialchars($method->name); ?></td>
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

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
  $(function () {
    // Sidebar toggle handled by AdminLTE script included in scripts.php
    // You can add custom sidebar toggle logic here if needed
  });
</script>

</body>
</html>
