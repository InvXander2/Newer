<?php
include('../inc/config.php');
include('../admin/includes/format.php');

include '../admin/session.php';

$page_name = 'Dashboard';
$page_parent = '';
$page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
$page_description = '';

include('inc-top');

$id = $_SESSION['id'];

if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    exit();
}

$conn = $pdo->open();

// Fetch user data using PDO
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$today = date('Y-m-d');
$year = date('Y');
if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

// Last transaction
$stmt = $conn->prepare("SELECT * FROM transaction WHERE user_id = :id ORDER BY trans_id DESC LIMIT 1");
$stmt->execute(['id' => $id]);
$row1 = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row1) {
    $transaction = $row1["amount"];
    $type = ($row1["type"] == 1) ? "Credit" : "Debit";
    $time = strtotime($row1["trans_date"]);
    $sanitized_time = date("Y-m-d, g:i A", $time);
} else {
    $transaction = $type = $sanitized_time = "N/A";
}

// Second-to-last transaction
$stmt = $conn->prepare("SELECT * FROM transaction WHERE user_id = :id ORDER BY trans_id DESC LIMIT 1,1");
$stmt->execute(['id' => $id]);
$row2 = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($row2)) {
    $loss = "";
    $percent_loss_gain = "";
} elseif ($row1["amount"] > $row2["amount"]) {
    $loss = "Increase";
    $percent_loss_gain = ($row1["amount"] - $row2["amount"]) * 100 / $row2["amount"];
} elseif ($row2["amount"] > $row1["amount"]) {
    $loss = "Decrease";
    $percent_loss_gain = ($row2["amount"] - $row1["amount"]) * 100 / $row2["amount"];
} else {
    $loss_gain = "";
    $percent_loss_gain = "";
}

// Active investments
$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM investment WHERE user_id = :id AND status = 'in progress'");
$stmt->execute(['id' => $id]);
$row4 = $stmt->fetch(PDO::FETCH_ASSOC);
$no_of_inv = $row4['numrows'];

$stmt = $conn->prepare("SELECT i.*, ip.name FROM investment i LEFT JOIN investment_plans ip ON ip.id = i.invest_plan_id WHERE i.user_id = :id AND i.status = 'in progress' ORDER BY i.invest_id DESC");
$stmt->execute(['id' => $id]);
$row5 = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Completed investments
$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM investment WHERE user_id = :id AND status = 'completed'");
$stmt->execute(['id' => $id]);
$row6 = $stmt->fetch(PDO::FETCH_ASSOC);
$no_of_inv_comp = $row6['numrows'];

$stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = :id AND status = 'completed'");
$stmt->execute(['id' => $id]);
$total = 0;
foreach ($stmt as $srow) {
    $amount = $srow['returns'];
    $total += $amount;
}

// Investment plans
$stmt = $conn->prepare("SELECT * FROM investment_plans ORDER BY id ASC");
$stmt->execute();
$stmt1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_plan1 = $total_plan2 = $total_plan3 = $percent_plan1 = $percent_plan2 = $percent_plan3 = 0;

$stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = :id AND status = 'completed' AND invest_plan_id = 1");
$stmt->execute(['id' => $id]);
foreach ($stmt as $prow) {
    $amount_plan1 = $prow['returns'];
    $total_plan1 += $amount_plan1;
    $percent_plan1 = $total ? number_format($total_plan1 * 100 / $total, 0) : 0;
}

$stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = :id AND status = 'completed' AND invest_plan_id = 2");
$stmt->execute(['id' => $id]);
foreach ($stmt as $prow) {
    $amount_plan2 = $prow['returns'];
    $total_plan2 += $amount_plan2;
    $percent_plan2 = $total ? number_format($total_plan2 * 100 / $total, 0) : 0;
}

$stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = :id AND status = 'completed' AND invest_plan_id = 3");
$stmt->execute(['id' => $id]);
foreach ($stmt as $prow) {
    $amount_plan3 = $prow['returns'];
    $total_plan3 += $amount_plan3;
    $percent_plan3 = $total ? number_format($total_plan3 * 100 / $total, 0) : 0;
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
                                        <li class="breadcrumb-item"><a href="profile"><?= htmlspecialchars($user['full_name']) ?></a></li>
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>

                                    <?php
                                    if (empty($user['nationality'])) {
                                        echo '
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
                                        ';
                                    }
                                    ?>
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

                <?php
                if (isset($_SESSION['error'])) {
                    echo '
                        <div class="alert alert-danger border-0" role="alert">
                            <i class="la la-skull-crossbones alert-icon text-danger align-self-center font-30 mr-3"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="mdi mdi-close align-middle font-16"></i></span>
                            </button>
                            <strong>Oh snap!</strong> ' . htmlspecialchars($_SESSION['error']) . '
                        </div>
                    ';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo '
                        <div class="alert alert-success border-0" role="alert">
                            <i class="mdi mdi-check-all alert-icon"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="mdi mdi-close align-middle font-16"></i></span>
                            </button>
                            <strong>Well done!</strong> ' . htmlspecialchars($_SESSION['success']) . '
                        </div>
                    ';
                    unset($_SESSION['success']);
                }
                ?>

                <div class="row">
                    <div class="col-lg-9">
                        <div class="row justify-content-center">
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col">
                                                <p class="text-dark mb-0 font-weight-semibold">Wallet Balance</p>
                                                <h3 class="m-0">$<?php echo number_format($row1['balance'] ?? 0, 2); ?></h3>
                                                <?php
                                                if ($loss_gain == "Increase") {
                                                    echo '
                                                        <p class="mb-0 text-truncate text-muted"><span class="text-success"><i class="mdi mdi-trending-up"></i>' . number_format($percent_loss_gain, 1) . '%</span> ' . $loss_gain . '</p>
                                                    ';
                                                } elseif ($loss_gain == "Decrease") {
                                                    echo '
                                                        <p class="mb-0 text-truncate text-muted"><span class="text-danger"><i class="mdi mdi-trending-down"></i>' . number_format($percent_loss_gain, 1) . '%</span> ' . $loss_gain . '</p>
                                                    ';
                                                } else {
                                                    echo '
                                                        <p class="mb-0 text-truncate text-muted">Make a Deposit Now</p>
                                                    ';
                                                }
                                                ?>
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
                                        <div class="row d-flex justify-content-center">
                                            <div class="col">
                                                <p class="text-dark mb-0 font-weight-semibold">Last Session</p>
                                                <h3 class="m-0"><?php echo date('h:i:s A', strtotime($user['date_view'])); ?></h3>
                                                <p class="mb-0 text-truncate text-muted"><?php echo date('D M j Y', strtotime($user['date_view'])); ?></p>
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
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <?php if ($no_of_inv > 0) : ?>
                                                <div class="col">
                                                    <p class="text-dark mb-0 font-weight-semibold">Active Plans</p>
                                                    <h3 class="m-0"><?= $no_of_inv; ?></h3>
                                                    <div class="mt-3">
                                                        <?php
                                                        $current_date = new DateTime();
                                                        $index = 0;
                                                        foreach ($row5 as $inv) :
                                                            $end_date = new DateTime($inv['end_date']);
                                                            $is_completed = $inv['status'] === 'completed';
                                                            $is_running = $end_date > $current_date && !$is_completed;
                                                        ?>
                                                            <div class="mb-3 pb-2 <?= $index < count($row5) - 1 ? 'border-bottom' : '' ?>">
                                                                <strong><?= htmlspecialchars($inv['name']); ?></strong><br>
                                                                Current Return: <span class="text-success">$<?= number_format($inv['current'], 2); ?></span><br>
                                                                Guaranteed Return: <span class="text-primary">$<?= number_format($inv['returns'], 2); ?></span>
                                                                <form action="investment-complete.php" method="POST" class="d-inline">
                                                                    <input type="hidden" name="invest_id" value="<?= htmlspecialchars($inv['invest_id']); ?>">
                                                                    <button type="submit" name="complete" class="btn btn-sm btn-success mt-2"
                                                                            <?= $is_running || $is_completed ? 'disabled style="opacity: 0.5;"' : '' ?>
                                                                            onclick="debugClick(<?= $inv['invest_id']; ?>)">
                                                                        Complete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        <?php
                                                            $index++;
                                                        endforeach;
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto align-self-center">
                                                    <div class="report-main-icon bg-light-alt">
                                                        <i data-feather="activity" class="align-self-center text-blue icon-sm"></i>
                                                    </div>
                                                </div>
                                            <?php else : ?>
                                                <div class="col">
                                                    <p class="text-dark mb-0 font-weight-semibold">Active Plans</p>
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
                                            <?php if ($no_of_inv_comp > 0) : ?>
                                                <div class="col">
                                                    <p class="text-dark mb-0 font-weight-semibold">Completed Investments</p>
                                                    <h3 class="m-0"><?= $no_of_inv_comp; ?></h3>
                                                    <h5 class="m-0">$<?= number_format($total, 2); ?></h5>
                                                    <p class="mb-0 text-truncate text-muted"><span class="text-success"><i class="mdi mdi-trending-up"></i></span> Total Amount Earned</p>
                                                </div>
                                                <div class="col-auto align-self-center">
                                                    <div class="report-main-icon bg-light-alt">
                                                        <i data-feather="briefcase" class="align-self-center text-blue icon-sm"></i>
                                                    </div>
                                                </div>
                                            <?php else : ?>
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
                                    <div class="col">
                                        <h4 class="card-title">Investment Overview</h4>
                                    </div>
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Investment/Yield
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="ana_dash_1" class="apex-charts"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Earnings Summary</h4>
                                    </div>
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" style="cursor: context-menu; width: 120%;" class="btn btn-sm btn-outline-light">
                                                All
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                            <?php foreach ($stmt1 as $plan1) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($plan1['name']) ?></td>
                                                    <?php
                                                    $stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = :id AND invest_plan_id = :plan_id AND status = 'completed'");
                                                    $stmt->execute(['id' => $id, 'plan_id' => $plan1['id']]);
                                                    $total_invested = $total_earned = 0;
                                                    foreach ($stmt as $sinv) {
                                                        $total_invested += $sinv['capital'];
                                                        $total_earned += $sinv['returns'];
                                                    }
                                                    ?>
                                                    <td class="text-right"><?= number_format($total_invested, 2) ?></td>
                                                    <td class="text-right"><?= number_format($total_earned, 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">What's New</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="list-group custom-list-group mb-n3">
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM news ORDER BY id DESC LIMIT 7");
                                    $stmt->execute();
                                    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    $index = 1;
                                    foreach ($news as $new) :
                                        $tag1 = $index == 1 ? "Crypto News" : ($index == 2 ? "Cryptocurrency" : "Bitcoin");
                                        $tag2 = $index == 1 ? "Apps" : "Tech";
                                    ?>
                                        <li class="list-group-item align-items-center d-flex justify-content-between pt-0">
                                            <div class="media">
                                                <img src="../admin/images/<?= htmlspecialchars($new['photo']); ?>" height="30" class="mr-3 align-self-center rounded" alt="...">
                                                <div class="media-body align-self-center">
                                                    <h6 class="m-0"><?= substrwords($new['short_title'], 30); ?></h6>
                                                    <p class="mb-0 text-muted"><?= $tag1; ?>, <?= $tag2; ?></p>
                                                </div>
                                            </div>
                                            <div class="align-self-center">
                                                <a target="_blank" href="../news-detail.php?id=<?= $new['id']; ?>&title=<?= htmlspecialchars($new['slug']); ?>" class="btn btn-sm btn-soft-primary">Read <i class="las la-external-link-alt font-15"></i></a>
                                            </div>
                                        </li>
                                    <?php
                                        $index++;
                                    endforeach;
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Earnings By Channel</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="barchart" class="apex-charts ml-n4"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Activity</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="analytic-dash-activity" data-simplebar>
                                    <div class="activity">
                                        <?php
                                        $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM activity WHERE user_id = :id");
                                        $stmt->execute(['id' => $id]);
                                        $no_of_act = $stmt->fetch(PDO::FETCH_ASSOC)['numrows'];

                                        $stmt = $conn->prepare("SELECT * FROM activity WHERE user_id = :id ORDER BY act_id DESC LIMIT 6");
                                        $stmt->execute(['id' => $id]);
                                        $actrow = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        if ($no_of_act > 0) {
                                            foreach ($actrow as $act) : ?>
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
                                            <?php endforeach; ?>
                                        <?php } else { ?>
                                            <div class="activity-info">
                                                <h5>No Activity Yet</h5>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <script src="https://widgets.coingecko.com/coingecko-coin-market-ticker-list-widget.js"></script>
                            <coingecko-coin-market-ticker-list-widget coin-id="bitcoin" currency="usd" locale="en"></coingecko-coin-market-ticker-list-widget>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('inc-footer'); ?>
        </div>
    </div>

    <!-- Chart Data -->
    <?php
    $invests = array();
    $capital = array();
    for ($m = 1; $m <= 12; $m++) {
        $stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = :id AND status = 'completed' AND MONTH(end_date) = :month AND YEAR(end_date) = :year");
        $stmt->execute(['id' => $id, 'month' => $m, 'year' => $year]);
        $total = $total2 = 0;
        foreach ($stmt as $srow) {
            $amount = $srow['returns'] - $srow['capital'];
            $total += $amount;
            $amount2 = $srow['capital'];
            $total2 += $amount2;
        }
        array_push($invests, round($total, 2));
        array_push($capital, round($total2));
    }
    $invests = implode(',', $invests);
    $capital = implode(',', $capital);
    ?>

    <?php include('inc-bottom'); ?>

    <script>
        function debugClick(invest_id) {
            console.log('Complete button clicked for invest_id: ' + invest_id);
            // Optionally send to server-side log
            fetch('debug_log.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ invest_id: invest_id, action: 'complete_button_clicked', time: new Date().toISOString() })
            });
        }
    </script>
</body>
</html>
