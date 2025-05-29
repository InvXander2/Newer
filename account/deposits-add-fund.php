<?php
    include('../inc/config.php');
    include '../admin/session.php';

    $page_name = 'Deposits';
    $page_parent = '';
    $page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
    $page_description = 'Manage Investment provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

    include('inc/head.php');

    $id = $_SESSION['user'];

    if (!isset($_SESSION['user'])) {
        header('location: ../login.php');
        exit();
    }

    $conn = $pdo->open();

    $deposit_madeQuery = $conn->query("SELECT * FROM request WHERE user_id=$id AND type=1 ORDER BY id DESC");
    if ($deposit_madeQuery->rowCount()) {
        $deposit_made = $deposit_madeQuery->fetchAll(PDO::FETCH_OBJ);
    }

    $payment_methodQuery = $conn->query("SELECT * FROM payment_methods");
    if ($payment_methodQuery->rowCount()) {
        $payment_method = $payment_methodQuery->fetchAll(PDO::FETCH_OBJ);
    }

    $payment_completeQuery = $conn->query("SELECT * FROM payment_methods ORDER BY id ASC LIMIT 1");
    if ($payment_completeQuery->rowCount()) {
        $payment_complete = $payment_completeQuery->fetchAll(PDO::FETCH_OBJ);
    }

    $depositHistory = "SELECT * FROM transaction WHERE user_id = :id AND type = 1";
?>

<body>
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
                                    <h4 class="page-title">Deposits</h4>
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
                                <strong>Oh snap!</strong> " . htmlspecialchars($_SESSION['error']) . "
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
                                <strong>Well done!</strong> " . htmlspecialchars($_SESSION['success']) . "
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
                                <h4 class="card-title">Fund Account</h4>
                                <p class="text-muted mb-0">Add Fund to your Account</p>
                            </div><!--end card-header-->
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body">
                                        <form class="form-horizontal auth-form" method="post" action="deposits-payment-option" id="deposit-form">
                                            <div class="form-group mb-2">
                                                <label>Deposit Charge: 0%</label>
                                            </div><!--end form-group-->
                                            <div class="form-group mb-2">
                                                <label>Deposit Limit: ($100 - $10,000,000)</label>
                                            </div><!--end form-group-->
                                            <div class="form-group mb-2">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                                    <input type="number" name="deposit_amount" class="form-control" id="deposit-amount" placeholder="Enter Deposit Amount" aria-label="Amount (to the nearest dollar)" min="100" max="10000000" step="0.01" required />
                                                    <div class="input-group-append"><span class="input-group-text">.00</span></div>
                                                </div>
                                                <div id="deposit-error" class="invalid-feedback" style="display: none;"></div>
                                            </div><!--end form-group-->
                                            <div class="form-group mb-0 row">
                                                <div class="col-12">
                                                    <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="addFund">Fund <i class="fas fa-money-bill ml-1"></i></button>
                                                </div><!--end col-->
                                            </div><!--end form-group-->
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

    <!-- JavaScript for deposit form validation -->
    <script>
        $(document).ready(function() {
            $('#deposit-form').on('submit', function(e) {
                var amount = parseFloat($('#deposit-amount').val());
                var $error = $('#deposit-error');

                // Clear previous errors
                $error.hide().text('');
                $('#deposit-amount').removeClass('is-invalid');

                // Validate amount
                if (isNaN(amount) || amount < 100) {
                    e.preventDefault();
                    $error.text('Deposit amount must be at least $100.').show();
                    $('#deposit-amount').addClass('is-invalid');
                    return false;
                }
                if (amount > 10000000) {
                    e.preventDefault();
                    $error.text('Deposit amount cannot exceed $10,000,000.').show();
                    $('#deposit-amount').addClass('is-invalid');
                    return false;
                }

                return true;
            });

            // Prevent negative input
            $('#deposit-amount').on('input', function() {
                var value = $(this).val();
                if (value < 0) {
                    $(this).val('');
                    $('#deposit-error').text('Negative amounts are not allowed.').show();
                    $(this).addClass('is-invalid');
                } else {
                    $('#deposit-error').hide();
                    $(this).removeClass('is-invalid');
                }
            });
        });
    </script>

    <?php include('inc/scripts.php'); ?>
</body>
</html>
