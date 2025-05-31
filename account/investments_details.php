<?php
include('../inc/config.php');
include '../admin/session.php';

$page_name = 'Active Investments';
$page_parent = 'Investments';
$page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
$page_description = 'View and manage your active and completed investments.';

include('inc/head.php');

$id = $_SESSION['user'];

if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
}

$conn = $pdo->open();

$new_investment_planQuery = $conn->query("SELECT *, investment.status AS invest_status FROM investment LEFT JOIN investment_plans ON investment_plans.id=investment.invest_plan_id LEFT JOIN users ON users.id=investment.user_id WHERE user_id=$id ORDER BY 1 DESC");
if ($new_investment_planQuery->rowCount()) {
    $new_investment_plans = $new_investment_planQuery->fetchAll(PDO::FETCH_OBJ);
}

$now = date('Y-m-d H:i:s');
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
                                    <h4 class="page-title">Active Investments</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="investments.php">Investments</a></li>
                                        <li class="breadcrumb-item active">Active Investments</li>
                                    </ol>
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
                                <h4 class="card-title">Your Investments</h4>
                                <p class="text-muted mb-0">All your active and completed investments in one place.</p>
                            </div><!--end card-header-->
                            <div class="card-body">
                                <?php
                                if (!empty($new_investment_plans)) {
                                    foreach ($new_investment_plans as $investment_plan) :
                                        $date_ivstart = strtotime($investment_plan->start_date);
                                        $date_ivend = strtotime($investment_plan->end_date);
                                        $date_now = strtotime($now);

                                        $invest_id = $investment_plan->invest_id;

                                        $secs = $date_ivend - $date_now;
                                        if ($secs < 0) {
                                            $time_left = "Elapsed";
                                        } elseif ($secs < 3600) {
                                            $time_left = "Some Minutes Left";
                                        } elseif ($secs <= 216000) {
                                            $time_left = round($secs / 3600, 0) . " Hours Left";
                                        } else {
                                            $time_left = round($secs / 86400, 0) . " Days Left";
                                        }

                                        if ($investment_plan->invest_status == 'completed') {
                                            $percent = 100;
                                        } else {
                                            $percent = round(($date_now - $date_ivstart) * 100 / ($date_ivend - $date_ivstart), 0);

                                            if ($date_now >= $date_ivend) {
                                                $stmt = $conn->prepare("UPDATE investment SET status=:c_status WHERE invest_id=:c_id");
                                                $stmt->execute(['c_status' => 'completed', 'c_id' => $invest_id]);
                                            }
                                        }

                                        if ($percent > 100) {
                                            $percent = 100;
                                        }
                                ?>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="task-box">
                                                    <div class="task-priority-icon"><i class="fas fa-circle text-<?php if ($investment_plan->invest_status == 'in progress') {
                                                                                                                echo 'info';
                                                                                                            } elseif ($investment_plan->invest_status == 'cancelled') {
                                                                                                                echo 'danger';
                                                                                                            } elseif ($investment_plan->invest_status == 'completed') {
                                                                                                                echo 'success';
                                                                                                            } ?>"></i></div>
                                                    <p class="text-muted float-right">
                                                        <span class="badge badge-<?php if ($investment_plan->invest_status == 'in progress') {
                                                                                        echo 'info';
                                                                                    } elseif ($investment_plan->invest_status == 'cancelled') {
                                                                                        echo 'danger';
                                                                                    } elseif ($investment_plan->invest_status == 'completed') {
                                                                                        echo 'success';
                                                                                    } ?> mr-2"><?php echo $investment_plan->invest_status; ?></span>
                                                        <span class="mx-1">·</span>
                                                        <span><i class="far fa-fw fa-clock"></i> <?php if ($investment_plan->invest_status == 'in progress') {
                                                                                                        echo $time_left;
                                                                                                    } else {
                                                                                                        echo "Elapsed";
                                                                                                    } ?></span>
                                                    </p>
                                                    <h5 class="mt-0"><?= $investment_plan->name; ?></h5>
                                                    <table class="table mb-0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Plan</th>
                                                                <th>Capital</th>
                                                                <th>ROI</th>
                                                                <th>Start Date</th>
                                                                <th>End Date</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><?= $investment_plan->name; ?></td>
                                                                <td>$ <?= number_format($investment_plan->capital, 2); ?></td>
                                                                <td>$ <?= number_format($investment_plan->returns, 2); ?></td>
                                                                <td><?= $investment_plan->start_date; ?></td>
                                                                <td><?= $investment_plan->end_date; ?></td>
                                                                <td><span class="badge badge-boxed badge-outline-<?php if ($investment_plan->invest_status == 'in progress') {
                                                                                                                        echo 'info';
                                                                                                                    } elseif ($investment_plan->invest_status == 'cancelled') {
                                                                                                                        echo 'danger';
                                                                                                                    } elseif ($investment_plan->invest_status == 'completed') {
                                                                                                                        echo 'success';
                                                                                                                    } ?>"><?php echo $investment_plan->invest_status; ?></span></td>
                                                            </tr>
                                                        </tbody>
                                                    </table><!--end /table-->
                                                    <p class="text-muted text-right mb-1"><?php if ($investment_plan->invest_status == 'in progress') {
                                                                                                echo $percent . "% Complete";
                                                                                            } elseif ($investment_plan->invest_status == 'completed') {
                                                                                                echo "Completed";
                                                                                            } else {
                                                                                                echo "On Hold";
                                                                                            } ?></p>
                                                    <div class="progress mb-4">
                                                        <div class="progress-bar bg-<?php if ($investment_plan->invest_status == 'in progress') {
                                                                                        echo 'info progress-bar-animated';
                                                                                    } elseif ($investment_plan->invest_status == 'cancelled') {
                                                                                        echo 'danger';
                                                                                    } elseif ($investment_plan->invest_status == 'completed') {
                                                                                        echo 'success';
                                                                                    } ?> progress-bar-striped" role="progressbar" style="width: <?= $percent; ?>%;" aria-valuenow="<?= $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div><!--end task-box-->
                                            </div>
                                        </div>
                                <?php
                                    endforeach;
                                } else {
                                    echo "<p>No Transaction Made</p>";
                                }
                                ?>
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
