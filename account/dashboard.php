<?php
include('../inc/config.php');
include('../admin/includes/format.php');
include('../admin/session.php');

$page_name = 'Dashboard';
$page_parent = '';
$page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
$page_description = $settings->siteTitle . ' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

include('inc/head.php');

$id = $_SESSION['user'];

if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

// Ensure database connection is available
if (!isset($conn)) {
    error_log("Database connection not established for user $id.", 3, "errors.log");
    die("Error: Unable to connect to the database.");
}

// File-based locking to prevent concurrent execution for the same user
$lock_file = "locks/user_$id.lock";
$fp = fopen($lock_file, 'w');

if (flock($fp, LOCK_EX | LOCK_NB)) {
    try {
        // Get current timestamp
        $current_date = date('Y-m-d H:i:s');

        // Start a transaction for data consistency
        $conn->beginTransaction();

        // Find investments that are due for completion for the current user
        $stmt = $conn->prepare("SELECT i.*, ip.name 
                                FROM investment i 
                                JOIN investment_plans ip ON i.invest_plan_id = ip.id 
                                WHERE i.status = 'in progress' 
                                AND i.end_date <= ? 
                                AND i.user_id = ?");
        $stmt->execute(array($current_date, $id));

        // Process each due investment
        foreach ($stmt as $investment) {
            $returns = $investment['returns'];
            $plan_name = $investment['name'];
            $invest_id = $investment['invest_id'];

            // Update investment status to completed
            $update_stmt = $conn->prepare("UPDATE investment SET status = 'completed' WHERE invest_id = ?");
            $update_stmt->execute(array($invest_id));

            // Get the user's latest balance
            $balance_stmt = $conn->prepare("SELECT balance FROM transaction WHERE user_id = ? ORDER BY trans_id DESC LIMIT 1");
            $balance_stmt->execute(array($id));
            $last_trans = $balance_stmt->fetch(PDO::FETCH_ASSOC);
            $current_balance = $last_trans ? $last_trans['balance'] : 0;

            // Credit returns to balance
            $new_balance = $current_balance + $returns;
            $insert_trans = $conn->prepare("INSERT INTO transaction (user_id, trans_date, type, amount, remark, balance) 
                                            VALUES (?, ?, '1', ?, ?, ?)");
            $insert_trans->execute(array(
                $id,
                $current_date,
                $returns,
                "Investment returns from $plan_name",
                $new_balance
            ));

            // Log completion in activity table
            $insert_activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                              VALUES (?, ?, ?, ?)");
            $insert_activity->execute(array(
                $id,
                "Investment completed: $plan_name",
                "Investment",
                $current_date
            ));
        }

        // Handle cancelled investments (refund capital) for the current user
        $cancelled_stmt = $conn->prepare("SELECT * FROM investment 
                                          WHERE status = 'cancelled' 
                                          AND invest_id NOT IN (SELECT invest_id FROM transaction WHERE remark LIKE 'Refund%') 
                                          AND user_id = ?");
        $cancelled_stmt->execute(array($id));

        foreach ($cancelled_stmt as $investment) {
            $capital = $investment['capital'];
            $invest_id = $investment['invest_id'];

            // Get the user's latest balance
            $balance_stmt = $conn->prepare("SELECT balance FROM transaction WHERE user_id = ? ORDER BY trans_id DESC LIMIT 1");
            $balance_stmt->execute(array($id));
            $last_trans = $balance_stmt->fetch(PDO::FETCH_ASSOC);
            $current_balance = $last_trans ? $last_trans['balance'] : 0;

            // Refund capital to balance
            $new_balance = $current_balance + $capital;
            $insert_trans = $conn->prepare("INSERT INTO transaction (user_id, trans_date, type, amount, remark, balance) 
                                            VALUES (?, ?, '1', ?, ?, ?)");
            $insert_trans->execute(array(
                $id,
                $current_date,
                $capital,
                "Refund for cancelled investment #$invest_id",
                $new_balance
            ));

            // Log refund in activity table
            $insert_activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                              VALUES (?, ?, ?, ?)");
            $insert_activity->execute(array(
                $id,
                "Refunded capital for cancelled investment #$invest_id",
                "Investment",
                $current_date
            ));
        }

        // Commit the transaction
        $conn->commit();

        // Release the lock
        flock($fp, LOCK_UN);
        fclose($fp);
    } catch (PDOException $e) {
        // Roll back transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        // Log error to file
        error_log("Error processing investments for user $id: " . $e->getMessage(), 3, "errors.log");
        // Release the lock
        flock($fp, LOCK_UN);
        fclose($fp);
    }
} else {
    // Could not acquire lock, skip processing
    error_log("Could not acquire lock for user $id, skipping investment processing.", 3, "errors.log");
    fclose($fp);
}

// Existing dashboard queries (updated to use PDO for consistency)
$today = date('Y-m-d');
$year = date('Y');
if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

// Get latest transaction
$trans_stmt = $conn->prepare("SELECT * FROM transaction WHERE user_id = ? ORDER BY trans_id DESC LIMIT 1");
$trans_stmt->execute(array($id));
$row1 = $trans_stmt->fetch(PDO::FETCH_ASSOC);

if ($row1) {
    if ($row1["type"] == 1) {
        $transaction = $row1["amount"];
        $type = "credit";
    } else {
        $transaction = $row1["amount"];
        $type = "debit";
    }
    $time = strtotime($row1["trans_date"]);
    $sanitized_time = date("Y-m-d, g:i A", $time);
} else {
    $transaction = 0;
    $type = "none";
    $sanitized_time = "";
}

// Get second-to-last transaction for loss/gain calculation
$trans2_stmt = $conn->prepare("SELECT * FROM transaction WHERE user_id = ? ORDER BY trans_id DESC LIMIT 1,1");
$trans2_stmt->execute(array($id));
$row2 = $trans2_stmt->fetch(PDO::FETCH_ASSOC);

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

// Active investments
$inv_stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM investment WHERE user_id = ? AND status = 'in progress' ORDER BY invest_id DESC");
$inv_stmt->execute(array($id));
$row4 = $inv_stmt->fetch(PDO::FETCH_ASSOC);
$no_of_inv = $row4['numrows'];

// Active investment plans
$planQuery = $conn->prepare("SELECT * FROM investment LEFT JOIN investment_plans ON investment_plans.id = investment.invest_plan_id WHERE user_id = ? AND status = 'in progress' ORDER BY invest_id DESC");
$planQuery->execute(array($id));
$row5 = $planQuery->fetchAll(PDO::FETCH_OBJ);

// Completed investments
$comp_inv_stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM investment WHERE user_id = ? AND status = 'completed' ORDER BY invest_id DESC");
$comp_inv_stmt->execute(array($id));
$row6 = $comp_inv_stmt->fetch(PDO::FETCH_ASSOC);
$no_of_inv_comp = $row6['numrows'];

$total = 0;
$comp_stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = ? AND status = 'completed'");
$comp_stmt->execute(array($id));
foreach ($comp_stmt as $srow) {
    $amount = $srow['returns'];
    $total += $amount;
}

// All investment plans
$allplanQuery = $conn->query("SELECT * FROM investment_plans ORDER BY id ASC");
$stmt1 = $allplanQuery->fetchAll(PDO::FETCH_OBJ);

$total_plan1 = $total_plan2 = $total_plan3 = $percent_plan1 = $percent_plan2 = $percent_plan3 = 0;

// Plan 1 earnings
$stmt3 = $conn->prepare("SELECT * FROM investment WHERE user_id = ? AND status = 'completed' AND invest_plan_id = 1");
$stmt3->execute(array($id));
foreach ($stmt3 as $prow) {
    $amount_plan1 = $prow['returns'];
    $total_plan1 += $amount_plan1;
    $percent_plan1 = $total && $total_plan1 ? number_format($total_plan1 * 100 / $total, 0) : 0;
}

// Plan 2 earnings
$stmt3 = $conn->prepare("SELECT * FROM investment WHERE user_id = ? AND status = 'completed' AND invest_plan_id = 2");
$stmt3->execute(array($id));
foreach ($stmt3 as $prow) {
    $amount_plan2 = $prow['returns'];
    $total_plan2 += $amount_plan2;
    $percent_plan2 = $total && $total_plan2 ? number_format($total_plan2 * 100 / $total, 0) : 0;
}

// Plan 3 earnings
$stmt3 = $conn->prepare("SELECT * FROM investment WHERE user_id = ? AND status = 'completed' AND invest_plan_id = 3");
$stmt3->execute(array($id));
foreach ($stmt3 as $prow) {
    $amount_plan3 = $prow['returns'];
    $total_plan3 += $amount_plan3;
    $percent_plan3 = $total && $total_plan3 ? number_format($total_plan3 * 100 / $total, 0) : 0;
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

                                    <?php
                                    if (empty($row0['nationality'])) {
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
                                        <span class="" id="Select_date">Jan 01</span>
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
                    <div class="col-lg-9">
                        <div class="row justify-content-center">
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col">
                                                <p class="text-dark mb-0 font-weight-semibold">Wallet Balance</p>
                                                <h3 class="m-0">$ <?php echo number_format_short($row1["balance"], 2); ?></h3>
                                                <?php
                                                if ($loss_gain == "Increase") {
                                                    echo "
                                                        <p class='mb-0 text-truncate text-muted'><span class='text-success'><i class='mdi mdi-trending-up'></i>" . number_format($percent_loss_gain, 1, '.', '') . "%</span> " . $loss_gain . "</p>
                                                    ";
                                                } elseif ($loss_gain == "Decrease") {
                                                    echo "
                                                        <p class='mb-0 text-truncate text-muted'><span class='text-danger'><i class='mdi mdi-trending-down'></i>" . number_format($percent_loss_gain, 1, '.', '') . "%</span> " . $loss_gain . "</p>
                                                    ";
                                                } else {
                                                    echo "
                                                        <p class='mb-0 text-truncate text-muted'>Make a Deposit Now</p>";
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
                            </div> <!--end col--> 
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">                                                
                                            <div class="col">
                                                <p class="text-dark mb-0 font-weight-semibold">Last Session</p>
                                                <h3 class="m-0"><?= date('h:i:s A', strtotime($row0['date_view'])) ?></h3>
                                                <p class="mb-0 text-truncate text-muted"><?= date('D M j Y', strtotime($row0['date_view'])) ?></p>
                                            </div>
                                            <div class="col-auto align-self-center">
                                                <div class="report-main-icon bg-light-alt">
                                                    <i data-feather="clock" class="align-self-center text-blue icon-sm"></i>  
                                                </div>
                                            </div> 
                                        </div>
                                    </div><!--end card-body--> 
                                </div><!--end card--> 
                            </div> <!--end col-->
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <?php if ($no_of_inv > 0) : ?>
                                                <div class="col">
                                                    <p class="text-dark mb-0 font-weight-semibold">Active Plans</p>
                                                    <h3 class="m-0"><?= $no_of_inv; ?></h3>
                                                    <h5 class="mb-0 text-truncate text-muted">
                                                        <?php foreach ($row5 as $inv) : ?>
                                                            <div class="mb-2">
                                                                <strong><?= htmlspecialchars($inv->name); ?></strong><br>
                                                                Current Return: <span class="text-success"><?= number_format($inv->current, 2); ?></span><br>
                                                                Guaranteed Return: <span class="text-primary"><?= number_format($inv->returns, 2); ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </h5>
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
                            </div> <!--end col-->
                            <div class="col-md-6 col-lg-3">
                                <div class="card report-card">
                                    <div class="card-body">
                                        <div class="row d-flex justify-content-center">
                                            <?php if ($no_of_inv_comp > 0) : ?>
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
                            </div> <!--end col-->                               
                        </div><!--end row-->
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">                      
                                        <h4 class="card-title">Investment Overview</h4>                      
                                    </div><!--end col-->
                                    <div class="col-auto"> 
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Investment/ Yield
                                            </a>
                                        </div>               
                                    </div><!--end col-->
                                </div>  <!--end row-->                                  
                            </div><!--end card-header-->
                            <div class="card-body">
                                <div class="">
                                    <div id="ana_dash_1" class="apex-charts"></div>
                                </div> 
                            </div><!--end card-body--> 
                        </div><!--end card--> 
                    </div><!--end col-->
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">                      
                                        <h4 class="card-title">Earnings Summary</h4>                      
                                    </div><!--end col-->
                                    <div class="col-auto"> 
                                        <div class="dropdown">
                                            <a href="#" style="cursor: context-menu; width: 120%;" class="btn btn-sm btn-outline-light">
                                                All
                                            </a>
                                        </div>         
                                    </div><!--end col-->
                                </div>  <!--end row-->                                  
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
                                            <?php foreach ($stmt1 as $plan1) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($plan1->name) ?></td>
                                                    <?php 
                                                    $stmt2 = $conn->prepare("SELECT * FROM investment WHERE user_id = ? AND invest_plan_id = ? AND status = 'completed'");
                                                    $stmt2->execute(array($id, $plan1->id));
                                                    $total_invested = 0;
                                                    $total_earned = 0;
                                                    foreach ($stmt2 as $sinv) {
                                                        $amount_inv = $sinv['capital'];
                                                        $total_invested += $amount_inv;
                                                        $amount_got = $sinv['returns'];
                                                        $total_earned += $amount_got;
                                                    }
                                                    ?>
                                                    <td class="text-right"><?= number_format($total_invested, 2); ?></td>
                                                    <td class="text-right"><?= number_format($total_earned, 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table><!--end /table-->
                                </div><!--end /div-->                                 
                            </div><!--end card-body--> 
                        </div><!--end card--> 
                    </div> <!--end col--> 
                </div><!--end row-->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">                      
                                        <h4 class="card-title">What's New</h4>                      
                                    </div><!--end col-->
                                </div>  <!--end row-->                                  
                            </div><!--end card-header-->                   
                            <div class="card-body">
                                <ul class="list-group custom-list-group mb-n3">
                                    <?php
                                    $newQuery = $conn->query("SELECT * FROM news ORDER BY id DESC LIMIT 7");
                                    $news = $newQuery->fetchAll(PDO::FETCH_OBJ);
                                    $index = 1;
                                    foreach ($news as $new) : 
                                        if ($index == 1) {
                                            $tag1 = "Crypto News";
                                            $tag2 = "Apps";
                                        } elseif ($index == 2) {
                                            $tag1 = "Cryptocurrency";
                                            $tag2 = "Tech";
                                        } elseif ($index == 3) {
                                            $tag1 = "Bitcoin";
                                            $tag2 = "Tech";
                                        }
                                        ?>
                                        <li class="list-group-item align-items-center d-flex justify-content-between pt-0">
                                            <div class="media">
                                                <img src="../admin/images/<?= htmlspecialchars($new->photo); ?>" height="30" class="mr-3 align-self-center rounded" alt="...">
                                                <div class="media-body align-self-center"> 
                                                    <h6 class="m-0"><?= substrwords($new->short_title, 30); ?></h6>
                                                    <p class="mb-0 text-muted"><?= htmlspecialchars($tag1); ?>, <?= htmlspecialchars($tag2); ?></p>                                                                                           
                                                </div><!--end media body-->
                                            </div>
                                            <div class="align-self-center">
                                                <a target="_blank" href="../news-detail.php?id=<?= $new->id; ?>&title=<?= htmlspecialchars($new->slug); ?>" class="btn btn-sm btn-soft-primary">Read <i class="las la-external-link-alt font-15"></i></a>  
                                            </div>                                            
                                        </li>
                                        <?php
                                        $index++;
                                    endforeach; ?>
                                </ul>                                
                            </div><!--end card-body--> 
                        </div><!--end card--> 
                    </div> <!--end col--> 
                    <div class="col-lg-4"> 
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">                      
                                        <h4 class="card-title">Earnings By Channel</h4>                      
                                    </div><!--end col-->                                        
                                </div>  <!--end row-->                                  
                            </div><!--end card-header-->  
                            <div class="card-body">                                    
                                <div id="barchart" class="apex-charts ml-n4"></div>                                                               
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
                                </div>  <!--end row-->                                  
                            </div><!--end card-header-->                                              
                            <div class="card-body"> 
                                <div class="analytic-dash-activity" data-simplebar>
                                    <div class="activity">
                                        <?php
                                        $stmtact = $conn->prepare("SELECT COUNT(*) AS numrows FROM activity WHERE user_id = ?");
                                        $stmtact->execute(array($id));
                                        $drowact = $stmtact->fetch(PDO::FETCH_ASSOC);
                                        $no_of_act = $drowact['numrows'];
                                        $actQuery = $conn->prepare("SELECT * FROM activity WHERE user_id = ? ORDER BY id DESC LIMIT 6");
                                        $actQuery->execute(array($id));
                                        $actrow = $actQuery->fetchAll(PDO::FETCH_OBJ);
                                        if ($no_of_act > 0) {
                                            foreach ($actrow as $act) : ?>
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
                                            <?php
                                            endforeach;
                                        } else { ?>
                                            <div class="activity-info">
                                                <h5>No Activity Yet</h5>
                                            </div>
                                        <?php } ?>
                                    </div><!--end activity-->
                                </div><!--end analytics-dash-activity-->
                            </div>  <!--end card-body-->                                     
                        </div><!--end card--> 
                    </div><!--end col--> 
                </div><!--end row-->
                <div class="row">                        
                    <div class="col-lg-12">
                        <div class="card">
                            <script src="https://widgets.coingecko.com/coingecko-coin-market-ticker-list-widget.js"></script>
                            <coingecko-coin-market-ticker-list-widget coin-id="bitcoin" currency="usd" locale="en"></coingecko-coin-market-ticker-list-widget>
                        </div><!--end card--> 
                    </div> <!--end col-->  
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
            $stmt = $conn->prepare("SELECT * FROM investment WHERE user_id = ? AND status = 'completed' AND MONTH(end_date) = ? AND YEAR(end_date) = ?");
            $stmt->execute(array($id, $m, $year));
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
            error_log("Error in chart data query for user $id: " . $e->getMessage(), 3, "errors.log");
        }
    }
    $invests = json_encode($invests);
    $capital = json_encode($capital);
    ?>
    <?php include('inc/scripts.php'); ?>
</body>
</html>
