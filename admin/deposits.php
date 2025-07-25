<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Deposit Request
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Deposit</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <th class="hidden"></th>
                    <th>Username</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Change Status</th>
                  </thead>
                  <tbody>
                    <?php
                      $conn = $pdo->open();

                      try{
                        $stmt = $conn->prepare("SELECT *, users.uname AS username, request.status AS request_status, request.request_id AS id, users.id AS user_id FROM request LEFT JOIN users ON users.id=request.user_id WHERE request.type=1 ORDER BY request_id DESC");
                        $stmt->execute();
                        foreach($stmt as $row){
                          echo "
                            <tr>
                              <td class='hidden'></td>
                              <td>".$row['username']."</td>
                              <td>".$row['trans_date']."</td>
                              <td>$ ".number_format($row['amount'], 2)."</td>
                              <td>".$row['payment_mode']."</td>
                              <td>".$row['request_status']."</td>
                              <td>
                                <button class='btn btn-info btn-sm edit btn-flat' data-id='".$row['id']."'><i class='fa fa-edit'></i> Change Status</button>
                              </td>
                            </tr>
                          ";
                        }
                      }
                      catch(PDOException $e){
                        echo $e->getMessage();
                      }

                      $pdo->close();
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
     
  </div>
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/request_modal.php'; ?>

</div>
<!-- ./wrapper -->

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

  $(document).on('click', '.fund', function(e){
    e.preventDefault();
    $('#fund').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.msg', function(e){
    e.preventDefault();
    $('#msg').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.photo', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.status', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'request_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.reqid').val(response.request_id);
      $('#edit_status').val(response.status);
    }
  });
}
</script>
<style>
.table-responsive {
  overflow-x: auto;
  width: 100%;
}

.table-responsive table {
  min-width: 800px; /* Adjust based on your table's content width */
}
</style>
</body>
</html>
