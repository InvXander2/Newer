<?php
include('../inc/config.php');
include '../../inc/session.php';

$page_name = 'Investment Summary';
$page_parent = 'Investments';
$page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
$page_description = 'Review and confirm your investment plan details.';

include('inc/head.php');

$user_id = $_SESSION['user'];

if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
}

$conn = $pdo->open();

// Get the selected plan ID from the URL or form
$plan_id = isset($_GET['plan_id']) ? $_GET['plan_id'] : (isset($_POST['plan_id']) ? $_POST['plan_id'] : null);

if (!$plan_id) {
    $_SESSION['error'] = 'No investment plan selected.';
    header('location: investments.php');
    exit();
}

// Fetch the selected investment plan details
$stmt = $conn->prepare("SELECT * FROM investment_plans WHERE id = :id");
$stmt->execute(['id' => $plan_id]);
$investment_plan = $stmt->fetch(PDO::FETCH_OBJ);

if (!$investment_plan) {
    $_SESSION['error'] = 'Invalid investment plan selected.';
    header('location: investments.php');
    exit();
}

// Format max_invest
$max_invest = ($investment_plan->max_invest >= 100000000) ? "Unlimited" : "$" . number_format($investment_plan->max_invest, 0);

// Determine if the plan is popular
$span = ($investment_plan->min_invest >= 1000 && $investment_plan->min_invest < 10000) ? '<span class="badge badge-pink a-animate-blink mt-0">POPULAR</span>' : '';

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
                                    <h4 class="page-title">Investment Summary</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="investments.php">Investments</a></li>
                                        <li class="breadcrumb-item active">Investment Summary</li>
                                    </ol>
                                </div><!--end col-->
                                <div class="col-auto align-self-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                        <span class="day-name" id="Day_Name">Today:</span>&nbsp;
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
                                <h4 class="card-title">Investment Plan Summary</h4>
                                <p class="text-muted mb-0">Review the details of your selected investment plan and enter the amount to invest.</p>
                            </div><!--end card-header-->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <?php echo $span; ?>
                                                <div class="pricingTable1 text-center">
                                                    <h6 class="title1 pt-3 pb-2 m-0"><?php echo $investment_plan->name; ?></h6>
                                                    <div class="p-3">
                                                        <h3 class="amount amount-border d-inline-block"><?php echo $investment_plan->rate; ?>%</h3>
                                                    </div>
                                                    <hr class="hr-dashed">
                                                    <h6 class="amount pt-3 pb-2" style="font-size: 16px; color: #563d07; font-weight: 500">Duration: <?php echo $investment_plan->duration; ?> days</h6>
                                                    <h6 class="amount pt-3 pb-2" style="font-size: 16px">From $<?php echo number_format($investment_plan->min_invest, 0); ?> to <?php echo $max_invest; ?></h6>
                                                </div><!--end pricingTable-->
                                            </div><!--end card-body-->
                                        </div><!--end card-->
                                    </div><!--end col-->

                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Enter Investment Amount</h5>
                                                <form method="post" role="form" action="invest-now.php">
                                                    <input type="hidden" name="plan_id" value="<?php echo $investment_plan->id; ?>">
                                                    <div class="form-group">
                                                        <label>Principal Investment</label>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                                            <input type="number" name="invest_amount" class="form-control" placeholder="Amount to Invest" aria-label="Amount (to the nearest dollar)" required min="<?php echo $investment_plan->min_invest; ?>" max="<?php echo ($investment_plan->max_invest >= 100000000) ? '' : $investment_plan->max_invest; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <a href="investments.php" class="btn btn-secondary btn-sm">Cancel</a>
                                                        <button type="submit" name="invest" class="btn btn-dark btn-sm">Confirm Investment</button>
                                                    </div>
                                                </form>
                                            </div><!--end card-body-->
                                        </div><!--end card-->
                                    </div><!--end col-->
                                </div><!--end row-->
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

<?php
$pdo->close();
?>
