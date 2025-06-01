<?php
    include('../inc/config.php');
    include('../admin/includes/format.php');
    include('../admin/session.php');

    $page_name = 'Dashboard';
    $page_parent = '';
    $page_title = 'Welcome to the Official Website of ' . htmlspecialchars($settings->siteTitle);
    $page_description = htmlspecialchars($settings->siteTitle) . ' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

    include('inc/head.php');

    // Ensure user is logged in
    if (!isset($_SESSION['user'])) {
        header('location: ../login.php');
        exit();
    }

    $id = $_SESSION['user'];

    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user profile data for $row0
    try {
        $stmt0 = $conn->prepare("SELECT full_name, nationality FROM users WHERE id = :user_id");
        $stmt0->execute(['user_id' => $id]);
        $row0 = $stmt0->fetch(PDO::FETCH_ASSOC);
        if (!$row0) {
            // Handle case where user doesn't exist
            header('location: ../login.php');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error fetching user data: " . $e->getMessage());
        header('location: ../login.php');
        exit();
    }

    $today = date('Y-m-d');
    $year = date('Y');
    if (isset($_GET['year'])) {
        $year = filter_var($_GET['year'], FILTER_VALIDATE_INT) ?: date('Y');
    }

    // Fetch latest transaction
    try {
        $stmt1 = $conn->prepare("SELECT * FROM transaction WHERE user_id = :user_id ORDER BY trans_id DESC LIMIT 1");
        $stmt1->execute(['user_id' => $id]);
        $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

        if ($row1) {
            $transaction = $row1["amount"];
            $type = ($row1["type"] == 1) ? "credit" : "debit";
            $time = strtotime($row1["trans_date"]);
            $sanitized_time = date("Y-m-d, g:i A", $time);
        } else {
            $transaction = 0;
            $type = "";
            $sanitized_time = "";
        }
    } catch (PDOException $e) {
        error_log("Error fetching latest transaction: " . $e->getMessage());
        $transaction = 0;
        $type = "";
        $sanitized_time = "";
    }

    // Fetch second latest transaction for loss/gain calculation
    try {
        $stmt2 = $conn->prepare("SELECT * FROM transaction WHERE user_id = :user_id ORDER BY trans_id DESC LIMIT 1 OFFSET 1");
        $stmt2->execute(['user_id' => $id]);
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        if (empty($row2)) {
            $loss_gain = "";
            $percent_loss_gain = "";
        } elseif ($row1 && $row1["balance"] > $row2["balance"]) {
            $loss_gain = "Increase";
            $percent_loss_gain = ($row1["balance"] - $row2["balance"]) * 100 / $row2["balance"];
        } elseif ($row1 && $row2["balance"] > $row1["balance"]) {
            $loss_gain = "Decrease";
            $percent_loss_gain = ($row2["balance"] - $row1["balance"]) * 100 / $row2["balance"];
        } else {
            $loss_gain = "";
            $percent_loss_gain = "";
        }
    } catch (PDOException $e) {
        error_log("Error fetching second transaction: " . $e->getMessage());
        $loss_gain = "";
        $percent_loss_gain = "";
    }

    // Fetch recent transactions (last 5)
    try {
        $stmt3 = $conn->prepare("SELECT * FROM transaction WHERE user_id = :user_id ORDER BY trans_id DESC LIMIT 5");
        $stmt3->execute(['user_id' => $id]);
        $recent_transactions = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching recent transactions: " . $e->getMessage());
        $recent_transactions = [];
    }

    // Fetch active investments
    try {
        $stmt4 = $conn->prepare("SELECT COUNT(*) AS numrows FROM investment WHERE user_id = :user_id AND status = 'in progress'");
        $stmt4->execute(['user_id' => $id]);
        $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $no_of_inv = $row4['numrows'];

        $planQuery = $conn->prepare("SELECT investment.*, investment_plans.name FROM investment 
                                    LEFT JOIN investment_plans ON investment_plans.id = investment.invest_plan_id 
                                    WHERE user_id = :user_id AND status = 'in progress' 
                                    ORDER BY invest_id DESC");
        $planQuery->execute(['user_id' => $id]);
        $row5 = $planQuery->rowCount() ? $planQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Error fetching active investments: " . $e->getMessage());
        $no_of_inv = 0;
        $row5 = [];
    }

    // Fetch completed investments
    try {
        $stmt6 = $conn->prepare("SELECT COUNT(*) AS numrows FROM investment WHERE user_id = :user_id AND status = 'completed'");
        $stmt6->execute(['user_id' => $id]);
        $row6 = $stmt6->fetch(PDO::FETCH_ASSOC);
        $no_of_inv_comp = $row6['numrows'];

        $stmt = $conn->prepare("SELECT returns FROM investment WHERE user_id = :user_id AND status = 'completed'");
        $stmt->execute(['user_id' => $id]);
        $total = 0;
        foreach ($stmt as $srow) {
            $total += $srow['returns'];
        }
    } catch (PDOException $e) {
        error_log("Error fetching completed investments: " . $e->getMessage());
        $no_of_inv_comp = 0;
        $total = 0;
    }

    // Fetch investment plans
    try {
        $allplanQuery = $conn->prepare("SELECT * FROM investment_plans ORDER BY id ASC");
        $allplanQuery->execute();
        $stmt1 = $allplanQuery->rowCount() ? $allplanQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Error fetching investment plans: " . $e->getMessage());
        $stmt1 = [];
    }

    // Calculate earnings per plan
    $total_plan1 = $total_plan2 = $total_plan3 = $percent_plan1 = $percent_plan2 = $percent_plan3 = 0;

    try {
        $stmt3 = $conn->prepare("SELECT returns FROM investment WHERE user_id = :user_id AND status = 'completed' AND invest_plan_id = 1");
        $stmt3->execute(['user_id' => $id]);
        foreach ($stmt3 as $prow) {
            $total_plan1 += $prow['returns'];
            $percent_plan1 = $total > 0 ? number_format($total_plan1 * 100 / $total, 0) : 0;
        }

        $stmt3 = $conn->prepare("SELECT returns FROM investment WHERE user_id = :user_id AND status = 'completed' AND invest_plan_id = 2");
        $stmt3->execute(['user_id' => $id]);
        foreach ($stmt3 as $prow) {
            $total_plan2 += $prow['returns'];
            $percent_plan2 = $total > 0 ? number_format($total_plan2 * 100 / $total, 0) : 0;
        }

        $stmt3 = $conn->prepare("SELECT returns FROM investment WHERE user_id = :user_id AND status = 'completed' AND invest_plan_id = 3");
        $stmt3->execute(['user_id' => $id]);
        foreach ($stmt3 as $prow) {
            $total_plan3 += $prow['returns'];
            $percent_plan3 = $total > 0 ? number_format($total_plan3 * 100 / $total, 0) : 0;
        }
    } catch (PDOException $e) {
        error_log("Error calculating earnings per plan: " . $e->getMessage());
    }
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
                                    <h4 class="page-title">Analytics</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="profile"><?= htmlspecialchars($row0["full_name"]) ?></a></li>
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>

                                    <?php if (empty($row0['nationality'])): ?>
                                        <div class="alert custom-alert custom-alert-primary icon-custom-alert alert-secondary-shadow fade show" role="alert">
                                            <i class="mdi mdi-alert-outline alert-icon text-primary align-self-center font-30 mr-3"></i>
                                            <div class="alert-text my-1">
                                                <span><a href="profile-edit" class="btn mb-1 btn-primary">Click Here</a> to Complete Your Profile Setup</span>
                                            </div>
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="mdi mdi-close font-16"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger border-0" role="alert">
                        <i class="la la-skull-crossbones alert-icon text-danger align-self-center font-30 mr-3"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="mdi mdi-close align-middle font-16"></i></span>
                        </button>
                        <strong>Oh snap!</strong> <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success border-0" role="alert">
                        <i class="mdi mdi-check-all alert-icon"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="mdi mdi-close align-middle font-16"></i></span>
                        </button>
                        <strong>Well done!</strong> <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-9">
                        <div class="row justify-content-center">
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col">
                                                <p class="text-dark mb-0 font-weight-semibold">Wallet Balance</p>
                                                <h3 class="m-0">$ <?php echo number_format_short($row1["balance"] ?? 0, 2); ?></h3>
                                                <?php if ($loss_gain == "Increase"): ?>
                                                    <p class="mb-0 text-truncate text-muted"><span class="text-success"><i class="mdi mdi-trending-up"></i><?= number_format($percent_loss_gain, 1, '.', '') ?>%</span> <?= $loss_gain ?></p>
                                                <?php elseif ($loss_gain == "Decrease"): ?>
                                                    <p class="mb-0 text-truncate text-muted"><span class="text-danger"><i class="mdi mdi-trending-down"></i><?= number_format($percent_loss_gain, 1, '.', '') ?>%</span> <?= $loss_gain ?></p>
                                                <?php else: ?>
                                                    <p class="mb-0 text-truncate text-muted">Make a Deposit Now</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-auto align-self-center">
                                                <div class="report-main-icon bg-light-alt">
                                                    <i style="font-size: 21px;" class="align-self-center text-blue icon-sm dripicons-wallet"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--end card-body-->
                                </div><!--end card-->
                            </div><!--end col-->
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-between">
                                            <?php if ($no_of_inv > 0): ?>
                                                <div class="col">
                                                    <p class="text-dark mb-0 font-weight-semibold d-inline">Active Plans</p>
                                                    <a href="investments_details.php" class="btn btn-sm btn-outline-primary ml-2">View All</a>
                                                    <h3 class="m-0"><?= $no_of_inv; ?></h3>
                                                    <div class="mt-3">
                                                        <?php
                                                        $current_date = new DateTime();
                                                        $index = 0;
                                                        foreach ($row5 as $inv):
                                                            $start_date = new DateTime($inv->start_date);
                                                            $end_date = new DateTime($inv->end_date);
                                                            $is_completed = $inv->status === 'completed';
                                                            $is_running = $end_date > $current_date && !$is_completed;

                                                            // Calculate progress
                                                            $total_duration = $end_date->getTimestamp() - $start_date->getTimestamp();
                                                            $elapsed = $current_date->getTimestamp() - $start_date->getTimestamp();
                                                            $progress_percentage = ($total_duration > 0) ? min(100, ($elapsed / $total_duration) * 100) : 0;
                                                            $days_remaining = $end_date->diff($current_date)->days;
                                                        ?>
                                                            <div class="mb-3 pb-2 <?= $index < count($row5) - 1 ? 'border-bottom' : '' ?>">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <strong><?= htmlspecialchars($inv->name); ?></strong>
                                                                        <div class="return-info">
                                                                            <p class="mb-1">Guaranteed Return: <span class="text-primary">$<?= number_format($inv->returns, 2); ?></span></p>
                                                                            <div class="progress mt-2" style="height: 8px;">
                                                                                <div class="progress-bar bg-success" role="progressbar" 
                                                                                     style="width: <?= $progress_percentage ?>%;" 
                                                                                     aria-valuenow="<?= $progress_percentage ?>" 
                                                                                     aria-valuemin="0" 
                                                                                     aria-valuemax="100">
                                                                                </div>
                                                                            </div>
                                                                            <p class="mb-0 mt-1 text-muted">
                                                                                <?= $is_completed ? 'Completed' : ($days_remaining . ' day' . ($days_remaining != 1 ? 's' : '') . ' remaining') ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <form action="investment-complete.php" method="POST" class="d-inline mr-2">
                                                                            <input type="hidden" name="invest_id" value="<?= htmlspecialchars($inv->invest_id); ?>">
                                                                            <button type="submit" name="complete" class="btn btn-sm btn-success"
                                                                                    <?= $is_running || $is_completed ? 'disabled style="opacity: 0.5;"' : '' ?>>
                                                                                Complete
                                                                            </button>
                                                                        </form>
                                                                        <div class="report-main-icon bg-light-alt">
                                                                            <i data-feather="activity" class="align-self-center text-blue icon-sm"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php $index++; endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="col">
                                                    <p class="text-dark mb-0 font-weight-semibold d-inline">Active Plans</p>
                                                    <a href="investments_details.php" class="btn btn-sm btn-outline-primary ml-2">View All</a>
                                                    <h5 class="mb-0 text-danger">
                                                        <i class="mdi mdi-alert-outline alert-icon text-danger align-self-center font-30 mr-3"></i>
                                                        You have no ongoing investment. Invest now to earn.
                                                    </h5>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div><!--end card-body-->
                                </div><!--end card-->
                            </div><!--end col-->
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <?php if ($no_of_inv_comp > 0): ?>
                                                <div class="col">
                                                    <p class="text-dark mb-0 font-weight-semibold">Completed Investments</p>
                                                    <h3 class="m-0"><?= $no_of_inv_comp; ?></h3>
                                                    <h5 class="m-0">$ <?= number_format_short($total, 2); ?></h5>
                                                    <p class="mb-0 text-truncate text-muted"><span class="text-success"><i class="mdi mdi-trending-up"></i></span> Total Amount Earned</p>
                                                </div>
                                                <div class="col-auto align-self-center">
                                                    <div class="report-main-icon bg-light-alt">
                                                        <i data-feather="briefcase" class="align-self-center text-blue icon-sm"></i>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="col">
                                                    <p class="text-dark mb-0 font-weight-semibold">Completed Investments</p>
                                                    <h5 class="mb-0 text-danger">
                                                        <i class="mdi mdi-alert-outline alert-icon text-danger align-self-center font-30 mr-3"></i>
                                                        You have not invested yet. Invest now to earn.
                                                    </h5>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div><!--end card-body-->
                                </div><!--end card-->
                            </div><!--end col-->
                        </div><!--end row-->
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="dropdown">
                                        </div>
                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-header-->
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h4 class="card-title">Earnings Summary</h4>
                                            </div><!--end col-->
                                            <div class="col-auto">
                                                <div class="dropdown">
                                                </div>
                                            </div><!--end col-->
                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div id="ana_device" class="apex-charts" style="min-height: 300px;"></div>
                                        </div>
                                    </div><!--end card-body-->
                                </div><!--end card-->
                            </div><!--end col-->
                        </div><!--end row-->
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h4 class="card-title">What's New</h4>
                                            </div><!--end col-->
                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body">
                                        <ul class="list-group custom-list-group mb-n3">
                                            <?php
                                            try {
                                                $newQuery = $conn->prepare("SELECT * FROM news ORDER BY id DESC LIMIT 7");
                                                $newQuery->execute();
                                                $news = $newQuery->rowCount() ? $newQuery->fetchAll(PDO::FETCH_OBJ) : [];
                                                $index = 1;
                                                foreach ($news as $new):
                                                    $tag1 = $index == 1 ? "Crypto News" : ($index == 2 ? "Cryptocurrency" : "Bitcoin");
                                                    $tag2 = $index == 1 ? "Apps" : "Tech";
                                            ?>
                                                <li class="list-group-item align-items-center d-flex justify-content-between pt-0">
                                                    <div class="media">
                                                        <img src="../admin/images/<?= htmlspecialchars($new->photo); ?>" height="30" class="mr-3 align-self-center rounded" alt="...">
                                                        <div class="media-body align-self-center">
                                                            <h6 class="m-0"><?= htmlspecialchars(substrwords($new->short_title, 30)); ?></h6>
                                                            <p class="mb-0 text-muted"><?= htmlspecialchars($tag1); ?>, <?= htmlspecialchars($tag2); ?></p>
                                                        </div><!--end media body-->
                                                    </div>
                                                    <div class="align-self-center">
                                                        <a target="_blank" href="../news-detail.php?id=<?= htmlspecialchars($new->id); ?>&title=<?= htmlspecialchars($new->slug); ?>" class="btn btn-sm btn-soft-primary">Read <i class="las la-external-link-alt font-15"></i></a>
                                                    </div>
                                                </li>
                                                <?php
                                                    $index++;
                                                endforeach;
                                            } catch (PDOException $e) {
                                                error_log("Error fetching news: " . $e->getMessage());
                                            }
                                            ?>
                                        </ul>
                                    </div><!--end card-body-->
                                </div><!--end card-->
                            </div><!--end col-->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h4 class="card-title">Activity</h4>
                                            </div><!--end col-->
                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body">
                                        <div class="analytic-dash-activity" data-simplebar>
                                            <div class="activity">
                                                <?php
                                                try {
                                                    $stmtact = $conn->prepare("SELECT COUNT(*) AS numrows FROM activity WHERE user_id = :user_id");
                                                    $stmtact->execute(['user_id' => $id]);
                                                    $drowact = $stmtact->fetch(PDO::FETCH_ASSOC);
                                                    $no_of_act = $drowact['numrows'];

                                                    $actQuery = $conn->prepare("SELECT * FROM activity WHERE user_id = :user_id ORDER BY act_id DESC LIMIT 6");
                                                    $actQuery->execute(['user_id' => $id]);
                                                    $actrow = $actQuery->rowCount() ? $actQuery->fetchAll(PDO::FETCH_OBJ) : [];

                                                    if ($no_of_act > 0) {
                                                        foreach ($actrow as $act): ?>
                                                            <div class="activity-info">
                                                                <div class="icon-info-activity">
                                                                    <i class="mdi mdi-clock-outline bg-soft-primary"></i>
                                                                </div>
                                                                <div class="activity-info-text">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <p class="text-muted mb-0 font-13 w-75"><span><?= htmlspecialchars($act->category); ?></span>
                                                                            <?= htmlspecialchars($act->message); ?>
                                                                        </p>
                                                                        <small class="text-muted"><?= htmlspecialchars($act->date_sent); ?></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach;
                                                    } else { ?>
                                                        <div class="activity-info">
                                                            <h5>No Activity Yet</h5>
                                                        </div>
                                                    <?php }
                                                } catch (PDOException $e) {
                                                    error_log("Error fetching activity: " . $e->getMessage());
                                                    ?>
                                                    <div class="activity-info">
                                                        <h5>Error Loading Activity</h5>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div><!--end activity-->
                                        </div><!--end analytics-dash-activity-->
                                    </div><!--end card-body-->
                                </div><!--end card-->
                            </div><!--end col-->
                        </div><!--end row-->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <script src="https://widgets.coingecko.com/coingecko-coin-market-ticker-list-widget.js"></script>
                                    <coingecko-coin-market-ticker-list-widget coin-id="bitcoin" currency="usd" locale="en"></coingecko-coin-market-ticker-list-widget>
                                </div><!--end card-->
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!-- container -->
                    <?php include('inc/footer.php'); ?><!--end footer-->
                </div>
                <!-- end page content -->
            </div>
            <!-- end page-wrapper -->

            <!-- Chart Data -->
            <?php
            $invests = array();
            $capital = array();
            for ($m = 1; $m <= 12; $m++) {
                try {
                    $stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = :user_id AND status = 'completed' AND MONTH(end_date) = :month AND YEAR(end_date) = :year");
                    $stmt->execute(['user_id' => $id, 'month' => $m, 'year' => $year]);
                    $total = $total2 = 0;
                    foreach ($stmt as $srow) {
                        $amount = $srow['returns'] - $srow['capital'];
                        $total += $amount;
                        $amount2 = $srow['capital'];
                        $total2 += $amount2;
                    }
                    array_push($invests, round($total, 2));
                    array_push($capital, round($total2));
                } catch (PDOException $e) {
                    error_log("Error calculating chart data for month $m: " . $e->getMessage());
                    array_push($invests, 0);
                    array_push($capital, 0);
                }
            }
            $invests = implode(',', $invests);
            $capital = implode(',', $capital);
            ?>
            <?php include('inc/scripts.php'); ?>
        </body>
    </html>
