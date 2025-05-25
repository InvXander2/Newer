<?php
include('../inc/config.php');
include 'includes/session.php';

$conn = $pdo->open();

// Fetch methods
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
      <h1>Payment Methods</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Payment Methods</li>
      </ol>
    </section>

    <section class="content">
      <?php
      if (isset($_SESSION['error'])) {
        echo "
          <div class='alert alert-danger alert-dismissible'>
            <button type='button' class='close' data-dismiss='alert'>&times;</button>
            <h4><i class='icon fa fa-warning'></i> Error!</h4>
            " . $_SESSION['error'] . "
          </div>
        ";
        unset($_SESSION['error']);
      }

      if (isset($_SESSION['success'])) {
        echo "
          <div class='alert alert-success alert-dismissible'>
            <button type='button' class='close' data-dismiss='alert'>&times;</button>
            <h4><i class='icon fa fa-check'></i> Success!</h4>
            " . $_SESSION['success'] . "
          </div>
        ";
        unset($_SESSION['success']);
      }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat">
                <i class="fa fa-plus"></i> New
              </a>
            </div>

            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>#</th>
                  <th>Payment Method</th>
                  <th>Wallet Address</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  <?php if (!empty($methods)): ?>
                    <?php foreach ($methods as $index => $method): ?>
                      <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($method->name); ?></td>
                        <td><?= htmlspecialchars($method->wallet_address); ?></td>
                        <td>
                          <button class="btn btn-success btn-sm edit btn-flat" data-id="<?= $method->id; ?>"><i class="fa fa-edit"></i> Edit</button>
                          <button class="btn btn-danger btn-sm delete btn-flat" data-id="<?= $method->id; ?>"><i class="fa fa-trash"></i> Delete</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="4">No payment methods found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/payment_methods_modal.php'; ?>
  <?php include 'includes/payment_methods_modal2.php'; ?>
  <?php include 'includes/footer.php'; ?>

</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  function getRow(id){
    $.ajax({
      type: 'POST',
      url: 'payment_methods_row.php',
      data: {id:id},
      dataType: 'json',
      success: function(response){
        $('.methodid').val(response.id);
        $('#edit_name').val(response.name);
        $('#edit_wallet').val(response.wallet_address);
        $('.name').html(response.name);
      }
    });
  }
});
</script>
</body>
</html>
