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
                               COALESCE(users.uname, 'Unknown User') AS username 
                        FROM investment 
                        LEFT JOIN investment_plans ON investment_plans.id = investment.invest_plan_id 
                        LEFT JOIN users ON users.id = investment.user_id 
                        ORDER BY invest_id DESC
                      ");
                      $stmt->execute();

                      // Debugging: Log number of rows
                      $row_count = $stmt->rowCount();
                      error_log("investments.php: Fetched $row_count rows, time=" . date('Y-m-d H:i:s'), 3, 'debug.log');

                      $now = date('Y-m-d H:i:s');
                      $row_index = 0;

                      foreach ($stmt as $row) {
                        // Debugging: Log row data
                        error_log("investments.php: Row $row_index, invest_id=" . ($row['invest_id'] ?? 'NULL') . ", username=" . ($row['username'] ?? 'NULL') . ", plan_name=" . ($row['plan_name'] ?? 'NULL'), 3, 'debug.log');
                        ?>
                          <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                            <td>$ <?php echo number_format($row['capital'] ?? 0, 2); ?></td>
                            <td>$ <?php echo number_format($row['returns'] ?? 0, 2); ?></td>
                            <td>
                              <?php
                                $date_ivstart = strtotime($row['start_date'] ?? $now);
                                $date_ivend = strtotime($row['end_date'] ?? $now);
                                $date_now = strtotime($now);

                                $secs = $date_now - $date_ivstart;
                                $day = $secs / 86400;

                                $total_days = ($date_ivend - $date_ivstart) / 86400;
                                $total_days = max($total_days, 1); // Prevent division by zero

                                $current_cpd = ($row['capital'] ?? 0) + (($row['capital'] ?? 0) * ($row['rate'] ?? 0) / $total_days * $day / 100);

                                if ($row['invest_status'] == 'in progress') {
                                  $query = $conn->prepare("UPDATE investment SET current = :current_cpd WHERE invest_id = :cpd_id");
                                  $query->execute(['current_cpd' => $current_cpd, 'cpd_id' => $row['invest_id']]);

                                  // Debugging: Log compound calculation
                                  error_log("investments.php: invest_id={$row['invest_id']}, current_cpd=$current_cpd, time=" . date('Y-m-d H:i:s'), 3, 'debug.log');

                                  echo number_format($current_cpd, 2);
                                } else {
                                  echo number_format($row['current'] ?? 0, 2);
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
                        <?php
                        $row_index++;
                      }
                      if ($row_count === 0) {
                        echo "<tr><td colspan='9'>No investments found.</td></tr>";
                      }
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
    console.log('Edit button clicked, data-id: ' + $(this).data('id')); // Debugging
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
        console.log('AJAX success:', response); // Debugging
        $('.invid').val(response.invest_id);
        $('#invest_status').val(response.invest_status);
      },
      error: function(xhr, status, error) {
        console.log('AJAX Error: ' + error, xhr.responseText); // Debugging
        // Log to server
        $.post('log_error.php', {error: 'AJAX Error in investments.php: ' + error + ', status: ' + status, xhr: xhr.responseText});
      }
    });
  }
});
</script>
</body>
</html>
