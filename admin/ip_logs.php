<?php
include 'includes/session.php';
include '../account/connect.php'; // MySQLi connection

// Get IP address from query parameter
$ip = $_GET['ip'] ?? null;

if (!$ip) {
    $_SESSION['error'] = "No IP address provided.";
    header('Location: visitor_logs.php');
    exit();
}

// Query for visitor logs using prepared statement
$stmt_logs = $conne->prepare("SELECT * FROM visitor_logs WHERE ip_address = ? ORDER BY visit_time DESC");
$stmt_logs->bind_param("s", $ip);
$stmt_logs->execute();
$result_logs = $stmt_logs->get_result();

// Check for associated user (get user_id from any log)
$user_id = null;
if ($result_logs->num_rows > 0) {
    $first_log = $result_logs->fetch_assoc();
    $user_id = $first_log['user_id'] ?? null;
    $result_logs->data_seek(0); // Reset result pointer for later use
}

// Query for user details if user_id exists
$row_user = null;
if ($user_id) {
    $stmt_user = $conne->prepare("SELECT full_name, uname, email, photo FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();
    $stmt_user->close();
}

$stmt_logs->close();
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <section class="content">
      <div class="row">
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
        <div class="marbtm50 wdt-100">
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <span class="portfolio-img-column image_hover">
              <img src="images/<?php echo htmlspecialchars($row_user['photo'] ?? 'default.jpg'); ?>" class="img-responsive zoom_img_effect" style="height: 24rem" alt="user-image">
            </span>
          </div>

          <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 project-desc">
            <ul class="profile_info list_none mb-4 pt-2 border-bottom">
              <li>
                <span class="title"><i class="fa fa-user"></i> Name:</span>
                <p><?php echo htmlspecialchars($row_user['full_name'] ?? 'N/A'); ?></p>
              </li>
              <li>
                <span class="title"><i class="fa fa-envelope"></i> Username:</span>
                <p><?php echo htmlspecialchars($row_user['uname'] ?? 'N/A'); ?></p>
              </li>
              <li>
                <span class="title"><i class="fa fa-hourglass-end"></i> Email:</span>
                <p><?php echo htmlspecialchars($row_user['email'] ?? 'N/A'); ?></p>
              </li>
              <li>
                <span class="title"><i class="fa fa-map-marker"></i> IP Address:</span>
                <p><?php echo htmlspecialchars($ip); ?></p>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-md-12 marbtm50 wdt-100">
          <section class="content-header">
            <h1>
              Tracking Details for IP: <?php echo htmlspecialchars($ip); ?>
            </h1>
          </section>
          
          <div class="box-body">
            <?php
              if ($result_logs->num_rows > 0) {
            ?>
              <div class="table-responsive">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <th>ID</th>
                    <th>Page Name</th>
                    <th>Visit Time (UTC)</th>
                    <th>Location</th>
                    <th>IP Address</th>
                    <th>User ID</th>
                  </thead>
                  <tbody>
                    <?php
                      while ($row = $result_logs->fetch_assoc()) {
                    ?>
                      <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['page_name']); ?></td>
                        <td>
                          <?php
                            try {
                              $time = new DateTime($row['visit_time'], new DateTimeZone('Europe/Paris'));
                              $time->setTimezone(new DateTimeZone('UTC'));
                              $sanitized_time = $time->format("d/m/Y, g:i A") . ' UTC';
                              echo htmlspecialchars($sanitized_time);
                            } catch (Exception $e) {
                              echo 'Invalid date';
                            }
                          ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                        <td><?php echo $row['user_id'] ? htmlspecialchars($row['user_id']) : 'N/A'; ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php
              } else {
            ?>
              <section class="content-header">
                <h1>
                  No tracking logs found for IP: <?php echo htmlspecialchars($ip); ?>
                </h1>
              </section>
            <?php
              }
              $conne->close();
            ?>
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
  min-width: 1000px; /* Adjusted for more columns */
}
</style>
</body>
</html>
