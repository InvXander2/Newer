<?php
include('../inc/config.php');
include '../admin/session.php';

$page_name = 'Withdrawals';
$page_parent = '';
$page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
$page_description = $settings->siteTitle . ' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

include('inc/head.php');

$id = $_SESSION['user'];

if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit();
}

$conn = $pdo->open();

// Fetch user balance
$stmt = $conn->prepare("SELECT balance FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch(PDO::FETCH_OBJ);
$balance = $user->balance ?? 0; // Default to 0 if balance is not found

$withdrawal_madeQuery = $conn->query("SELECT * FROM request WHERE user_id=$id && type=2 order by 1 desc");
if ($withdrawal_madeQuery->rowCount()) {
    $withdrawal_made = $withdrawal_madeQuery->fetchAll(PDO::FETCH_OBJ);
}

$payment_methodQuery = $conn->query("SELECT * FROM payment_methods");
if ($payment_methodQuery->rowCount()) {
    $payment_method = $payment_methodQuery->fetchAll(PDO::FETCH_OBJ);
}

$payment_completeQuery = $conn->query("SELECT * FROM payment_methods order by 1 asc Limit 1");
if ($payment_completeQuery->rowCount()) {
    $payment_complete = $payment_completeQuery->fetchAll(PDO::FETCH_OBJ);
}

$withdrawalHistory = "SELECT * FROM transaction WHERE user_id = $id && type = 2";

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
                                    <h4 class="page-title">Withdrawals</h4>
                                </div><!--end col-->
                                <div class="col-auto align-self-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                        <span class="day-name" id="Day_Name">Today:</span>&nbsp;
                                        <span class="" id="Select_date">Jan 11</span>
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
                    <div class="col-lg-3"></div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Withdraw Funds</h4>
                                <p class="text-muted mb-0">Withdraw funds from your wallet balance</p>
                            </div><!--end card-header-->
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body">
                                        <form class="form-horizontal auth-form" method="post" action="withdrawals-payment-option.php" onsubmit="return validateWithdrawal()">
                                            <div class="form-group mb-2">
                                                <label>Available Balance: $<?php echo number_format($balance, 2); ?></label>
                                            </div><!--end form-group-->
                                            <div class="form-group mb-2">
                                                <label>Charge: 0%</label>
                                            </div><!--end form-group-->
                                            <div class="form-group mb-2">
                                                <label>Withdrawal Limit: ($100 - $100,000)</label>
                                            </div><!--end form-group-->
                                            <div class="form-group mb-2">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                                    <input type="number" name="withdrawal_amount" id="withdrawal_amount" class="form-control" placeholder="Enter Amount to Withdraw" aria-label="Amount (to the nearest dollar)" min="100" max="100000" step="0.01" required />
                                                    <div class="input-group-append"></div>
                                                </div>
                                                <div id="error-message" class="text-danger" style="display: none;"></div>
                                            </div><!--end form-group-->
                                            <div class="form-group mb-0 row">
                                                <div class="col-12">
                                                    <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="removeFund">Withdraw <i class="fas fa-money-bill ml-1"></i></button>
                                                </div><!--end col-->
                                            </div> <!--end form-group-->
                                        </form><!--end form-->
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

    <script>
        function validateWithdrawal() {
            const withdrawalAmount = parseFloat(document.getElementById('withdrawal_amount').value);
            const balance = <?php echo json_encode($balance); ?>;
            const errorMessage = document.getElementById('error-message');

            // Reset error message
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';

            if (isNaN(withdrawalAmount) || withdrawalAmount <= 0) {
                errorMessage.textContent = 'Withdrawal amount must be greater than zero.';
                errorMessage.style.display = 'block';
                return false;
            }
            if (withdrawalAmount < 100) {
                errorMessage.textContent = 'Withdrawal amount must be at least $100.';
                errorMessage.style.display = 'block';
                return false;
            }
            if (withdrawalAmount > 100000) {
                errorMessage.textContent = 'Withdrawal amount cannot exceed $100,000 per transaction.';
                errorMessage.style.display = 'block';
                return false;
            }
            if (withdrawalAmount > balance) {
                errorMessage.textContent = 'Withdrawal amount cannot exceed your available balance ($' + balance.toFixed(2) + ').';
                errorMessage.style.display = 'block';
                return false;
            }

            return true; // Allow form submission
        }
    </script>
</body>
</html>
