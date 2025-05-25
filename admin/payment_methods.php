<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Manage Payment Methods</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Payment Methods</li>
      </ol>
    </section>

    <section class="content">
      <div class="container">
        <?php
          if(isset($_SESSION['error'])){
            echo "
              <div class='alert alert-danger alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-warning'></i> Error!</h4>
                ".$_SESSION['error']."
              </div>
            ";
            unset($_SESSION['error']);
          }
          if(isset($_SESSION['success'])){
            echo "
              <div class='alert alert-success alert-dismissible'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-check'></i> Success!</h4>
                ".$_SESSION['success']."
              </div>
            ";
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
          <?php
            $conn = $pdo->open();

            $methods = $conn->query("SELECT * FROM payment_mode ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
            foreach($methods as $index => $method){
              echo "
                <tr>
                  <td>".($index+1)."</td>
                  <td>".$method->name."</td>
                  <td>
                    <a href='payment_methods.php?delete=".$method->id."' onclick=\"return confirm('Delete this method?')\" class='btn btn-danger btn-sm'>Delete</a>
                  </td>
                </tr>
              ";
            }

            $pdo->close();
          ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>

</div>
</body>
</html>
