<?php
$page_name = 'Dashboard'; // Add this line
session_start();
include('inc/conn.php'); // Loads Database class and $pdo
// ... rest of your code ...
include('inc/checklogin.php');
check_login();
$aid = $_SESSION['user'];

// Helper functions (add here if not defined elsewhere)
if (!function_exists('number_format_short')) {
    function number_format_short($n, $precision = 1) {
        if ($n < 900) {
            return number_format($n, $precision);
        } elseif ($n < 900000) {
            return number_format($n / 1000, $precision) . 'K';
        } elseif ($n < 900000000) {
            return number_format($n / 1000000, $precision) . 'M';
        } else {
            return number_format($n / 1000000000, $precision) . 'B';
        }
    }
}

// Initialize database connection
try {
    $conn = $pdo->open();
} catch (Exception $e) {
    $_SESSION['error'] = "Database connection failed";
    header('location: ../login.php');
    exit();
}

// Fetch user profile data for $row0
try {
    $stmt0 = $conn->prepare("SELECT full_name, nationality, date_view FROM users WHERE id = :user_id");
    $stmt0->execute(['user_id' => $aid]);
    $row0 = $stmt0->fetch();
    if (!$row0) {
        $_SESSION['error'] = "User not found";
        $pdo->close();
        header('location: ../login.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $_SESSION['error'] = "Database error";
    $pdo->close();
    header('location: ../login.php');
    exit();
}

// Fetch wallet balance for $row1
try {
    $stmt = $conn->prepare("SELECT balance FROM wallet WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $aid]);
    $row1 = $stmt->fetch();
    if (!$row1) {
        $row1 = ['balance' => 0];
    }
} catch (PDOException $e) {
    error_log("Error fetching wallet balance: " . $e->getMessage());
    $row1 = ['balance' => 0];
}

// Calculate latest transaction for loss/gain
try {
    $stmt = $conn->prepare("SELECT amount FROM transaction WHERE user_id = :user_id AND type = 2 ORDER BY trans_id DESC LIMIT 1");
    $stmt->execute(['user_id' => $aid]);
    $latest_transaction = $stmt->fetch();
    $latest_transaction_amount = $latest_transaction ? $latest_transaction['amount'] : 0;

    $stmt = $conn->prepare("SELECT amount FROM transaction WHERE user_id = :user_id AND type = 2 ORDER BY trans_id DESC LIMIT 1 OFFSET 1");
    $stmt->execute(['user_id' => $aid]);
    $previous_transaction = $stmt->fetch();
    $previous_transaction_amount = $previous_transaction ? $previous_transaction['amount'] : 0;

    $loss_gain = ($latest_transaction_amount > $previous_transaction_amount) ? "Increase" : (($latest_transaction_amount < $previous_transaction_amount) ? "Decrease" : "No Change");
    $percent_loss_gain = ($previous_transaction_amount > 0) ? (($latest_transaction_amount - $previous_transaction_amount) / $previous_transaction_amount) * 100 : 0;
} catch (PDOException $e) {
    error_log("Error calculating loss/gain: " . $e->getMessage());
    $loss_gain = "No Change";
    $percent_loss_gain = 0;
}

// Fetch active investments for $row5
try {
    $stmt5 = $conn->prepare("SELECT i.*, p.name FROM investment i LEFT JOIN investment_plans p ON i.invest_plan_id = p.id WHERE i.user_id = :user_id AND i.status = 'running' ORDER BY i.start_date DESC");
    $stmt5->execute(['user_id' => $aid]);
    $row5 = $stmt5->fetchAll();
    $no_of_inv = count($row5);
} catch (PDOException $e) {
    error_log("Error fetching active investments: " . $e->getMessage());
    $row5 = [];
    $no_of_inv = 0;
}

// Fetch completed investments count and total earnings
try {
    $stmt = $conn->prepare("SELECT COUNT(*) AS numrows, SUM(returns) as total FROM investment WHERE user_id = :user_id AND status = 'completed'");
    $stmt->execute(['user_id' => $aid]);
    $drow = $stmt->fetch();
    $no_of_inv_comp = $drow['numrows'];
    $total = $drow['total'] ?? 0;
} catch (PDOException $e) {
    error_log("Error fetching completed investments: " . $e->getMessage());
    $no_of_inv_comp = 0;
    $total = 0;
}

// Fetch recent transactions
try {
    $stmt3 = $conn->prepare("SELECT * FROM transaction WHERE user_id = :user_id ORDER BY trans_id DESC LIMIT 5");
    $stmt3->execute(['user_id' => $aid]);
    $recent_transactions = $stmt3->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching recent transactions: " . $e->getMessage());
    $recent_transactions = [];
}

// Fetch investment plans for $stmt1
try {
    $allplanQuery = $conn->prepare("SELECT * FROM investment_plans ORDER BY id ASC");
    $allplanQuery->execute();
    $stmt1 = $allplanQuery->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching investment plans: " . $e->getMessage());
    $stmt1 = [];
}

// Calculate earnings per plan
$total_plan1 = $total_plan2 = $total_plan3 = $percent_plan1 = $percent_plan2 = $percent_plan3 = 0;
foreach ($stmt1 as $plan1) {
    $plan_id = $plan1['id'];
    try {
        $stmt3 = $conn->prepare("SELECT SUM(returns) as total_returns FROM investment WHERE user_id = :user_id AND status = 'completed' AND invest_plan_id = :plan_id");
        $stmt3->execute(['user_id' => $aid, 'plan_id' => $plan_id]);
        $row3 = $stmt3->fetch();
        ${"total_plan$plan_id"} = $row3['total_returns'] ?? 0;
        ${"percent_plan$plan_id"} = ($total > 0) ? number_format(${"total_plan$plan_id"} * 100 / $total, 0) : 0;
    } catch (PDOException $e) {
        error_log("Error calculating earnings for plan $plan_id: " . $e->getMessage());
        ${"total_plan$plan_id"} = 0;
        ${"percent_plan$plan_id"} = 0;
    }
}

// Chart data
$year = date('Y');
$invests = array();
$capital = array();
for ($m = 1; $m <= 12; $m++) {
    try {
        $stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = :user_id AND status = 'completed' AND MONTH(end_date) = :month AND YEAR(end_date) = :year");
        $stmt->execute(['user_id' => $aid, 'month' => $m, 'year' => $year]);
        $total = $total2 = 0;
        foreach ($stmt as $srow) {
            $amount = $srow['returns'] - $srow['capital'];
            $total += $amount;
            $amount2 = $srow['capital'];
            $total2 += $amount2;
        }
        array_push($invests, round($total, 2));
        array_push($capital, round($total2, 2));
    } catch (PDOException $e) {
        error_log("Error calculating chart data for month $m: " . $e->getMessage());
        array_push($invests, 0);
        array_push($capital, 0);
    }
}
$invests = json_encode($invests);
$capital = json_encode($capital);

// Close database connection
$pdo->close();
?>

<?php include('inc/head.php'); ?>
<body class="dark-topbar">
    <!-- Left Sidenav -->
    <?php include('inc/header.php'); ?>
    <!-- end left-sidenav-->

    <div class="page-wrapper">
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

                <!-- Key Metrics Row -->
                <div class="row justify-content-center">
                    <!-- Wallet Balance -->
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

                    <!-- Last Session -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card report-card">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <p class="text-dark mb-0 font-weight-semibold">Last Session</p>
                                        <h3 class="m-0"><?php echo $row0['date_view'] ? date('h:i:s A', strtotime($row0['date_view'])) : 'N/A'; ?></h3>
                                        <p class="mb-0 text-truncate text-muted"><?php echo $row0['date_view'] ? date('D M j Y', strtotime($row0['date_view'])) : 'No previous session'; ?></p>
                                    </div>
                                    <div class="col-auto align-self-center">
                                        <div class="report-main-icon bg-light-alt">
                                            <i data-feather="clock" class="align-self-center text-blue icon-sm"></i>
                                        </div>
                                    </div>
                                </div>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->

                    <!-- Active Plans -->
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
                                                    $start_date = new DateTime($inv['start_date']);
                                                    $end_date = new DateTime($inv['end_date']);
                                                    $is_completed = $inv['status'] === 'completed';
                                                    $is_running = $end_date > $current_date && !$is_completed;
                                                    $total_duration = $end_date->getTimestamp() - $start_date->getTimestamp();
                                                    $elapsed = $current_date->getTimestamp() - $start_date->getTimestamp();
                                                    $progress_percentage = ($total_duration > 0) ? min(100, ($elapsed / $total_duration) * 100) : 0;
                                                    $days_remaining = $end_date->diff($current_date)->days;
                                                ?>
                                                    <div class="mb-3 pb-2 <?= $index < count($row5) - 1 ? 'border-bottom' : '' ?>">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong><?= htmlspecialchars($inv['name']); ?></strong>
                                                                <div class="return-info">
                                                                    <p class="mb-1">Guaranteed Return: <span class="text-primary">$<?= number_format($inv['returns'], 2); ?></span></p>
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
                                                                    <input type="hidden" name="invest_id" value="<?= htmlspecialchars($inv['invest_id']); ?>">
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

                    <!-- Completed Investments -->
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

                <!-- Analytical Sections -->
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Investment Overview -->
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Investment Overview</h4>
                                    </div><!--end col-->
                                    <div class="col-auto">
                                        <div class="dropdown">
                                        </div>
                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-header-->
                            <div class="card-body">
                                <div class="">
                                    <div id="ana_dash_1" class="apex-charts"></div>
                                </div>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->

                <div class="row">
                    <!-- Earnings Summary -->
                    <div class="col-lg-6">
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
                                    <div id="ana_device" class="apex-charts"></div>
                                </div>
                                <div class="table-responsive mt-2">
                                    <table class="table border-dashed mb-0">
                                        <thead>
                                            <tr>
                                                <th>Plan</th>
                                                <th class="text-right">Invested</th>
                                                <th class="text-right">Earned</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stmt1 as $plan1): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($plan1['name']) ?></td>
                                                    <?php
                                                    try {
                                                        $stmt2 = $conn->prepare("SELECT capital, returns FROM investment WHERE user_id = :user_id AND invest_plan_id = :plan_id AND status = 'completed'");
                                                        $stmt2->execute(['user_id' => $aid, 'plan_id' => $plan1['id']]);
                                                        $total_invested = 0;
                                                        $total_earned = 0;
                                                        foreach ($stmt2 as $sinv) {
                                                            $total_invested += $sinv['capital'];
                                                            $total_earned += $sinv['returns'];
                                                        }
                                                    } catch (PDOException $e) {
                                                        error_log("Error fetching investment data for plan {$plan1['id']}: " . $e->getMessage());
                                                        $total_invested = 0;
                                                        $total_earned = 0;
                                                    }
                                                    ?>
                                                    <td class="text-right"><?= number_format($total_invested, 2) ?></td>
                                                    <td class="text-right"><?= number_format($total_earned, 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table><!--end /table-->
                                </div><!--end /div-->
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->

                    <!-- Earnings by Channel -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Earnings by Channel</h4>
                                    </div><!--end col-->
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" style="cursor: context-menu; width: 120%;" class="btn btn-sm btn-outline-light">All</a>
                                        </div>
                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-header-->
                            <div class="card-body">
                                <div id="barchart" class="apex-charts ml-n4"></div>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->

                <!-- Updates and Activity -->
                <div class="row">
                    <!-- What's New -->
                    <div class="col-lg-6">
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
                                        $conn = $pdo->open(); // Reopen connection for news
                                        $newQuery = $conn->prepare("SELECT * FROM news ORDER BY id DESC LIMIT 7");
                                        $newQuery->execute();
                                        $news = $newQuery->rowCount() ? $newQuery->fetchAll() : [];
                                        $index = 1;
                                        foreach ($news as $new):
                                            $tag1 = $index == 1 ? "Crypto News" : ($index == 2 ? "Cryptocurrency" : "Bitcoin");
                                            $tag2 = $index == 1 ? "Apps" : "Tech";
                                    ?>
                                        <li class="list-group-item align-items-center d-flex justify-content-between pt-0">
                                            <div class="media">
                                                <img src="../admin/images/<?= htmlspecialchars($new['photo']); ?>" height="30" class="mr-3 align-self-center rounded" alt="...">
                                                <div class="media-body align-self-center">
                                                    <h6 class="m-0"><?= htmlspecialchars(substrwords($new['short_title'], 30)); ?></h6>
                                                    <p class="mb-0 text-muted"><?= htmlspecialchars($tag1); ?>, <?= htmlspecialchars($tag2); ?></p>
                                                </div><!--end media body-->
                                            </div>
                                            <div class="align-self-center">
                                                <a target="_blank" href="../news-detail.php?id=<?= htmlspecialchars($new['id']); ?>&title=<?= htmlspecialchars($new['slug']); ?>" class="btn btn-sm btn-soft-primary">Read <i class="las la-external-link-alt font-15"></i></a>
                                            </div>
                                        </li>
                                    <?php
                                        $index++;
                                        endforeach;
                                        $pdo->close();
                                    } catch (PDOException $e) {
                                        error_log("Error fetching news: " . $e->getMessage());
                                    }
                                    ?>
                                </ul>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->

                    <!-- Activity -->
                    <div class="col-lg-6">
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
                                            $conn = $pdo->open(); // Reopen connection for activity
                                            $stmtact = $conn->prepare("SELECT COUNT(*) AS numrows FROM activity WHERE user_id = :user_id");
                                            $stmtact->execute(['user_id' => $aid]);
                                            $drowact = $stmtact->fetch();
                                            $no_of_act = $drowact['numrows'];

                                            $actQuery = $conn->prepare("SELECT * FROM activity WHERE user_id = :user_id ORDER BY act_id DESC LIMIT 6");
                                            $actQuery->execute(['user_id' => $aid]);
                                            $actrow = $actQuery->rowCount() ? $actQuery->fetchAll() : [];

                                            if ($no_of_act > 0) {
                                                foreach ($actrow as $act): ?>
                                                    <div class="activity-info">
                                                        <div class="icon-info-activity">
                                                            <i class="mdi mdi-clock-outline bg-soft-primary"></i>
                                                        </div>
                                                        <div class="activity-info-text">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <p class="text-muted mb-0 font-13 w-75"><span><?= htmlspecialchars($act['category']); ?></span>
                                                                    <?= htmlspecialchars($act['message']); ?>
                                                                </p>
                                                                <small class="text-muted"><?= htmlspecialchars($act['date_sent']); ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach;
                                            } else { ?>
                                                <div class="activity-info">
                                                    <h5>No Activity Yet</h5>
                                                </div>
                                            <?php }
                                            $pdo->close();
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

                <!-- CoinGecko Widget -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Cryptocurrency Prices (USD)</h4>
                            </div><!--end card-header-->
                            <div class="card-body">
                                <script src="https://widgets.coingecko.com/coingecko-coin-list-widget.js"></script>
                                <coingecko-coin-list-widget coin-ids="bitcoin,ethereum,tether,tron,solana" currency="usd" locale="en"></coingecko-coin-list-widget>
                                <div id="coingecko-fallback" style="display: none;">
                                    <p class="text-muted">Unable to load cryptocurrency prices. Please try again later or visit <a href="https://www.coingecko.com" target="_blank">CoinGecko</a> for live prices.</p>
                                </div>
                                <script>
                                    setTimeout(function() {
                                        if (!document.querySelector('coingecko-coin-list-widget').shadowRoot) {
                                            document.getElementById('coingecko-fallback').style.display = 'block';
                                        }
                                    }, 5000);
                                </script>
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

    <!-- jQuery  -->
    <?php include('inc/scripts.php'); ?>
</body>
</html>
