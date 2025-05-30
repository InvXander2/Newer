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
        Ongoing Investments
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Ongoing Investments</li>
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
              " . htmlspecialchars($_SESSION['error']) . "
            </div>
          ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . htmlspecialchars($_SESSION['success']) . "
            </div>
          ";
          unset($_SESSION['success']);
        }

        // Generate CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>Username</th>
                  <th>Plan</th>
                  <th>Capital</th>
                  <th>Return</th>
                  <th>Current Compound</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Status</th>
                  <th>Edit Status</th>
                </thead>
                <tbody>
                  <?php
                    $conn = $pdo->open();

                    try {
                      $stmt = $conn->prepare("
                        SELECT investment.*, investment.status AS invest_status, 
                               COALESCE(investment_plans.name, 'Unknown Plan') AS plan_name, 
                               users.uname AS username 
                        FROM investment 
                        LEFT JOIN investment_plans ON investment_plans.id = investment.invest_plan_id 
                        LEFT JOIN users ON users.id = investment.user_id 
                        ORDER BY invest_id DESC
                      ");
                      $stmt->execute();

                      $now = date('Y-m-d H:i:s');

                      foreach ($stmt as $row) { ?>
                          <tr>
                            <td><?php echo htmlspecialchars($row['username'] ?? 'Unknown User'); ?></td>
                            <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                            <td>$ <?php echo number_format($row['capital'], 2); ?></td>
                            <td>$ <?php echo number_format($row['returns'], 2); ?></td>
                            <td>
                              <?php
                                $date_ivstart = strtotime($row['start_date']);
                                $date_ivend = strtotime($row['end_date']);
                                $date_now = strtotime($now);

                                $secs = $date_now - $date_ivstart;
                                $day = $secs / 86400;

                                $total_days = ($date_ivend - $date_ivstart) / 86400;

                                $current_cpd = $row['capital'] + ($row['capital'] * $row['rate'] / $total_days * $day / 100);

                                if ($row['invest_status'] == 'in progress') {
                                  $query = $conn->prepare("UPDATE investment SET current = :current_cpd WHERE invest_id = :cpd_id");
                                  $query->execute(['current_cpd' => $current_cpd, 'cpd_id' => $row['invest_id']]);

                                  // Debugging: Log compound calculation
                                  error_log("investments.php: invest_id={$row['invest_id']}, current_cpd=$current_cpd, time=" . date('Y-m-d H:i:s'), 3, 'debug.log');

                                  echo number_format($current_cpd, 2);
                                } else {
                                  echo number_format($row['current'], 2);
                                }
                              ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['start_date'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['end_date'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['invest_status'] ?? ''); ?></td>
                            <td>
                              <button class="btn btn-primary btn-sm edit btn-flat" data-id="<?php echo htmlspecialchars($row['invest_id'] ?? ''); ?>"><i class="fa fa-edit"></i> Status</button>
                            </td>
                          </tr>
                      <?php } 
                    } catch (PDOException $e) {
                      $_SESSION['error'] = 'Database error: ' . $e->getMessage();
                      error_log("investments.php: Error - " . $e->getMessage(), 3, 'debug.log');
                    }
                    $pdo->close(); 
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
     
  </div>
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/investments_modal.php'; ?>

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

  function getRow(id){
    $.ajax({
      type: 'POST',
      url: 'investments_row.php',
      data: {id: id, csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'},
      dataType: 'json',
      success: function(response){
        $('.invid').val(response.invest_id);
        $('#invest_status').val(response.invest_status);
      },
      error: function(xhr, status, error) {
        console.log('AJAX Error: ' + error);
        // Debugging: Log AJAX error
        $.post('log_error.php', {error: 'AJAX Error in investments.php: ' + error + ', status: ' + status, xhr: xhr.responseText});
      }
    });
  }
});
</script>
</body>
</html>
