<?php
// session_start() removed to avoid duplicate call, as session.php starts the session
include('../inc/config.php');
include('../inc/session.php');

$page_name = 'Transaction History';
$page_parent = '';
$page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
$page_description = $settings->siteTitle . ' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

include('inc/head.php');

$id = $_SESSION['user'];

if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

$conn = $pdo->open();

try {
    // Query all transactions for the user, sorted by trans_id DESC
    $stmt = $conn->prepare("SELECT trans_id, trans_date, type, amount, remark, balance 
                            FROM transaction 
                            WHERE user_id = ? 
                            ORDER BY trans_id DESC");
    $stmt->execute([$id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    error_log("Error fetching transactions for user $id: " . $e->getMessage(), 3, "errors.log");
    $_SESSION['error'] = "Unable to fetch transaction history.";
}

$pdo->close();
?>

<body class="dark-topbar">
    <!-- Left Sidenav -->
    <?php include('inc/sidebar.php'); ?>
    <!-- end left-sidenav-->

    <div class="page-wrapper">
        <!-- Top Bar Start -->
        <?php include('inc/header.php'); ?>
        <!-- Top Bar End -->

        <!-- Page Content-->
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">History</h4>
                                </div><!--end col-->
                                <div class="col-auto align-self-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                        <span class="day-name" id="Day_Name">Today:</span> 
                                        <span class="" id="Select_date"><?php echo date('M d'); ?></span>
                                        <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                                    </a>
                                </div><!--end col-->
                            </div><!--end row-->
                        </div><!--end page-title-box-->
                    </div><!--end col-->
                </div><!--end row-->
                <!-- end page title end breadcrumb -->

                <?php
                if (isset($_SESSION['error'])) {
                    echo "
                        <div class='alert alert-danger border-0' role='alert'>
                            <i class='la la-skull-crossbones alert-icon text-danger align-self-center font-30 mr-3'></i>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'><i class='mdi mdi-close align-middle font-16'></i></span>
                            </button>
                            <strong>Oh snap!</strong> " . $_SESSION['error'] . "
                        </div>
                    ";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo "
                        <div class='alert alert-success border-0' role='alert'>
                            <i class='mdi mdi-check-all alert-icon'></i>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'><i class='mdi mdi-close align-middle font-16'></i></span>
                            </button>
                            <strong>Well done!</strong> " . $_SESSION['success'] . "
                        </div>
                    ";
                    unset($_SESSION['success']);
                }
                ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Transaction History</h4>
                                <p class="text-muted mb-0">All your transactions in one place.</p>
                            </div><!--end card-header-->
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body">
                                        <?php if (!empty($transactions)) { ?>
                                            <div style="overflow-x: auto;">
                                                <table class="table mb-0" style="min-width: 800px;">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Transaction ID</th>
                                                            <th>Date & Time (UTC)</th>
                                                            <th>Type</th>
                                                            <th>Amount</th>
                                                            <th>Remark</th>
                                                            <th>Balance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($transactions as $transaction) : ?>
                                                            <tr>
                                                                <td>TXID<?= $transaction->trans_id; ?></td>
                                                                <td><?php 
                                                                    // Display date in UTC
                                                                    $date = new DateTime($transaction->trans_date, new DateTimeZone('UTC'));
                                                                    echo htmlspecialchars($date->format('Y-m-d h:i:s A')) . ' UTC';
                                                                ?></td>
                                                                <td><span class="badge badge-boxed badge-outline-<?php echo $transaction->type == '1' ? 'success' : 'danger'; ?>">
                                                                    <?php echo $transaction->type == '1' ? 'Deposit' : 'Withdrawal/Investment'; ?>
                                                                </span></td>
                                                                <td>$<?php echo number_format($transaction->amount, 2); ?></td>
                                                                <td><?php echo htmlspecialchars($transaction->remark); ?></td>
                                                                <td>$<?php echo number_format($transaction->balance, 2); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table><!--end /table-->
                                            </div>
                                        <?php } else { ?>
                                            <p>No transactions found.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!-- container -->

            <?php include('inc/footer.php'); ?><!--end footer-->
        </div>
        <!-- end page content -->
    </div>
    <!-- end page-wrapper -->

    <?php include('inc/scripts.php'); ?>
</body>
</html>
