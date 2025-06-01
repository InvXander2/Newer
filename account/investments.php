<?php
include('../inc/config.php');
include '../admin/session.php';

$page_name = 'Investments';
$page_parent = '';
$page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
$page_description = 'Manage Investment provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

include('inc/head.php');

$id = $_SESSION['user'];

if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
}

$conn = $pdo->open();

$investment_planQuery = $conn->query("SELECT * FROM investment_plans ORDER BY 1 ASC");
if ($investment_planQuery->rowCount()) {
    $investment_plans = $investment_planQuery->fetchAll(PDO::FETCH_OBJ);
}

$new_investment_planQuery = $conn->query("SELECT *, investment.status AS invest_status FROM investment LEFT JOIN investment_plans ON investment_plans.id=investment.invest_plan_id LEFT JOIN users ON users.id=investment.user_id WHERE user_id=$id ORDER BY 1 DESC");
if ($new_investment_planQuery->rowCount()) {
    $new_investment_plans = $new_investment_planQuery->fetchAll(PDO::FETCH_OBJ);
}

$now = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Add custom CSS for card spacing -->
    <style>
        .col-lg-4 {
            padding-left: 15px;
            padding-right: 15px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

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
                                    <h4 class="page-title">Investments</h4>
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
                                <h4 class="card-title">Plans</h4>
                                <p class="text-muted mb-0">Select from the plans below to invest now.</p>
                            </div><!--end card-header-->
                            <div class="row mt-3">
                                <?php
                                foreach ($investment_plans as $investment_plan) :
                                    if ($investment_plan->max_invest >= 100000000) {
                                        $max_invest = "Unlimited";
                                    } else {
                                        $max_invest = "$" . number_format($investment_plan->max_invest, 0);
                                    }

                                    if ($investment_plan->min_invest >= 1000 && $investment_plan->min_invest < 10000) {
                                        $span = '<span class="badge badge-pink a-animate-blink mt-0">POPULAR</span>';
                                    } else {
                                        $span = '';
                                    }
                                ?>
                                    <div class="col-lg-4 mb-4"> <!-- Added mb-4 for vertical spacing -->
                                        <div class="card h-100"> <!-- Added h-100 for equal card height -->
                                            <div class="card-body">
                                                <?= $span; ?>
                                                <div class="pricingTable1 text-center">
                                                    <h6 class="title1 pt-3 pb-2 m-0"><?= $investment_plan->name; ?></h6>
                                                    <div class="p-3">
                                                        <h3 class="amount amount-border d-inline-block"><?= $investment_plan->rate; ?>%</h3>
                                                    </div>
                                                    <hr class="hr-dashed">
                                                    <h6 class="amount pt-3 pb-2" style="font-size: 16px; color: #563d07; font-weight: 500">Duration: <?= $investment_plan->duration; ?> days</h6>
                                                    <h6 class="amount pt-3 pb-2" style="font-size: 16px">From $<?= number_format($investment_plan->min_invest, 0); ?> to <?= $max_invest; ?></h6>
                                                    <a href="invest_summary.php?plan_id=<?= $investment_plan->id; ?>" class="btn btn-dark py-2 px-5 font-16"><span>Invest</span></a>
                                                </div><!--end pricingTable-->
                                            </div><!--end card-body-->
                                        </div><!--end card-->
                                    </div><!--end col-->
                                <?php
                                endforeach;
                                ?>
                            </div><!--end row-->
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
