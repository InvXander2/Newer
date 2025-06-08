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
                  </thead>
                  <tbody>
                    <?php
                      $conn = $pdo->open();

                      try {
                        // Use DISTINCT to avoid duplicate IP addresses
                        $stmt = $conn->prepare("SELECT DISTINCT ip_address, location FROM visitor_logs ORDER BY ip_address");
                        $stmt->execute();

                        foreach ($stmt as $row) { ?>
                          <tr>
                            <td><a href="ip_logs.php?ip=<?php echo urlencode($row['ip_address']); ?>"><?php echo htmlspecialchars($row['ip_address']); ?></a></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                          </tr>
                        <?php }
                      } catch (PDOException $e) {
                        echo "<tr><td colspan='2'>Error: " . $e->getMessage() . "</td></tr>";
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
<style>
.table-responsive {
  overflow-x: auto;
  width: 100%;
}

.table-responsive table {
  min-width: 600px; /* Adjusted for fewer columns */
}
</style>
</body>
</html>
