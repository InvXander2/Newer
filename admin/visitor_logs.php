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
        Visitor Logs
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Visitor Logs</li>
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
            <div class="box-header with-border">
              <h3 class="box-title">Visitor Tracking History</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <th>ID</th>
                    <th>Page Name</th>
                    <th>Visit Time</th>
                    <th>Location</th>
                    <th>IP Address</th>
                    <th>User ID</th>
                    <th>Actions</th>
                  </thead>
                  <tbody>
                    <?php
                      $conn = $pdo->open();

                      try{
                        $stmt = $conn->prepare("SELECT * FROM visitor_logs ORDER BY visit_time DESC");
                        $stmt->execute();

                        foreach($stmt as $row){ ?>
                          <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['page_name']); ?></td>
                            <td><?php echo $row['visit_time']; ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                            <td><?php echo $row['user_id'] ? $row['user_id'] : 'N/A'; ?></td>
                            <td>
                              <button class="btn btn-danger btn-sm delete btn-flat" data-id="<?php echo $row['id']; ?>"><i class="fa fa-trash"></i> Delete</button>
                            </td>
                          </tr>
                        <?php  
                        }
                      } catch(PDOException $e){
                        echo "<tr><td colspan='7'>Error: " . $e->getMessage() . "</td></tr>";
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

</div>
<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  // Delete functionality
  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    if(confirm('Are you sure you want to delete this log?')) {
      $.ajax({
        type: 'POST',
        url: 'delete_visitor_log.php', // Create this file for deletion
        data: {id: id},
        dataType: 'json',
        success: function(response){
          if(response.success) {
            alert('Log deleted successfully');
            location.reload(); // Refresh page
          } else {
            alert('Error: ' . response.message);
          }
        }
      });
    }
  });
});
</script>
<style>
.table-responsive {
  overflow-x: auto;
  width: 100%;
}

.table-responsive table {
  min-width: 1000px; /* Adjust based on your table's content width */
}
</style>
</body>
</html>
