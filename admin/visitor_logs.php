<?php
include 'includes/session.php';
include '../account/connect.php'; // MySQLi connection
?>

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
        if (isset($_SESSION['error'])) {
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
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
              <p><i class="fa fa-eye"></i> Click on the visitor's IP Address to view the tracking details</p>
              <div class="table-responsive">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <th>IP Address</th>
                    <th>Location</th>
                    <th>Actions</th>
                  </thead>
                  <tbody>
                    <?php
                      try {
                        // Query logs with id for deletion
                        $stmt = $conne->prepare("SELECT DISTINCT id, ip_address, location FROM visitor_logs ORDER BY ip_address");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                              <td><a href="ip_logs.php?ip=<?php echo urlencode($row['ip_address']); ?>" title="View details for IP <?php echo htmlspecialchars($row['ip_address']); ?>"><?php echo htmlspecialchars($row['ip_address']); ?></a></td>
                              <td><?php echo htmlspecialchars($row['location']); ?></td>
                              <td>
                                <button class="btn btn-danger btn-sm delete btn-flat" data-id="<?php echo $row['id']; ?>"><i class="fa fa-trash"></i> Delete</button>
                              </td>
                            </tr>
                          <?php }
                        } else {
                          echo "<tr><td colspan='3'>No visitor logs found.</td></tr>";
                        }
                        $stmt->close();
                      } catch (Exception $e) {
                        echo "<tr><td colspan='3'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                      }
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
        url: 'delete_visitor_log.php',
        data: {id: id},
        dataType: 'json',
        success: function(response){
          if(response.success) {
            alert('Log deleted successfully');
            location.reload(); // Refresh page
          } else {
            alert('Error: ' + response.message);
          }
        },
        error: function(xhr, status, error) {
          alert('Error deleting log: ' + error);
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
  min-width: 600px; /* Adjusted for columns */
}

.box-body p {
  font-weight: bold;
  margin-bottom: 15px;
}
</style>
</body>
</html>
