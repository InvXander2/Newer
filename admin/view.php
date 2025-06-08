<?php
include 'includes/session.php';
include "../account/connect.php";

$id = $_GET['i_id'];

// Query for user details using prepared statement
$stmt0 = $conne->prepare("SELECT * FROM users WHERE id = ?");
$stmt0->bind_param("i", $id);
$stmt0->execute();
$result0 = $stmt0->get_result();
$row0 = $result0->fetch_assoc();
$stmt0->close();

// Query for latest transaction using prepared statement
$stmt1 = $conne->prepare("SELECT * FROM transaction WHERE user_id = ? ORDER BY trans_id DESC LIMIT 1");
$stmt1->bind_param("i", $id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row1 = $result1->fetch_assoc();
$stmt1->close();

// Convert trans_date from UTC+2 to UTC for Wallet Balance
$time = $row1 ? new DateTime($row1["trans_date"], new DateTimeZone('Europe/Paris')) : null;
if ($time) {
    $time->setTimezone(new DateTimeZone('UTC'));
    $sanitized_time = $time->format("Y-m-d, g:i A") . ' UTC';
} else {
    $sanitized_time = 'N/A';
}

// Query for all transactions using prepared statement
$sql0 = "SELECT * FROM transaction WHERE user_id = ? ORDER BY trans_id DESC";
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
         <div class="marbtm50 wdt-100">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
               <span class="portfolio-img-column image_hover">
               <img src="images/<?php echo htmlspecialchars($row0["photo"] ?? 'default.jpg'); ?>" class="img-responsive zoom_img_effect" style="height: 24rem" alt="worker-image">
               </span>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 project-desc">
               <ul class="profile_info list_none mb-4 pt-2 border-bottom">
                 <li>
                     <span class="title"><i class="fa fa-user"></i> Name:</span>
                     <p><?php echo htmlspecialchars($row0["full_name"] ?? 'N/A'); ?></p>
                 </li>
                 <li>
                     <span class="title"><i class="fa fa-envelope"></i> Username:</span>
                     <p><?php echo htmlspecialchars($row0["uname"] ?? 'N/A'); ?></p>
                 </li>
                 <li>
                     <span class="title"><i class="fa fa-hourglass-end"></i> Email:</span>
                     <p><?php echo htmlspecialchars($row0["email"] ?? 'N/A'); ?></p>
                 </li>
                 <li>
                     <span class="title"><i class="fa fa-flag"></i> Referral Code: </span>
                     <p><?php echo htmlspecialchars($row0["uname"] ?? 'N/A'); ?></p>
                 </li>
                 <li>
                     <span class="title"><i class="fa fa-map-marker"></i> Wallet Balance:</span>
                     <p>$<?php echo number_format($row1["balance"] ?? 0, 2); ?></p>
                 </li>
             </ul>
            </div>
         </div>
         <div class="col-md-12 marbtm50 wdt-100">
            <section class="content-header">
              <h1>
                All Transactions
              </h1>
            </section>
            
            <div class="box-body">
              <?php
                  $stmt = $conne->prepare($sql0);
                  $stmt->bind_param("i", $id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows > 0) {
              ?>
                      <div class="table-responsive">
                        <table id="example1" class="table table-bordered">
                          <thead>
                            <th>Trans. ID</th>
                            <th>Date & Time (UTC)</th>
                            <th>Type</th>
                            <th>Remarks</th>
                            <th>Amount ($)</th>
                          </thead>
                          <tbody>
                          <?php
                              // Output data of each row
                              while ($row = $result->fetch_assoc()) {
                                if ($row["type"] == 1) {
                                    $type = "credit";
                                } else {
                                    $type = "debit";
                                }
                          ?>
                              <tr>
                                  <td><?php echo htmlspecialchars($row["trans_id"]); ?></td>
                                  <td>
                                      <?php
                                          // Convert trans_date from UTC+2 to UTC
                                          $time = new DateTime($row["trans_date"], new DateTimeZone('Europe/Paris'));
                                          $time->setTimezone(new DateTimeZone('UTC'));
                                          $sanitized_time = $time->format("d/m/Y, g:i A") . ' UTC';
                                          echo htmlspecialchars($sanitized_time);
                                      ?>
                                  </td>
                                  <td><?php echo htmlspecialchars($type); ?></td>
                                  <td><?php echo htmlspecialchars($row["remark"]); ?></td>
                                  <td>$<?php echo number_format($row["amount"], 2); ?></td>
                              </tr>
                          <?php } ?>
                          </tbody>
                        </table>
                      </div>
                  <?php
                  } else { ?>
                      <section class="content-header">
                        <h1>
                          No transaction info
                        </h1>
                      </section>
                  <?php
                  }
                  $stmt->close();
                  $conne->close();
              ?>
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
    min-width: 800px;
  }
</style>
</body>
</html>
