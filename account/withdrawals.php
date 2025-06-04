<?php
    include('../inc/config.php');
    include('../inc/session.php');

    $page_name = 'Withdrawals';
    $page_parent = '';
    $page_title = 'Welcome to the Official Website of ' . htmlspecialchars($settings->siteTitle);
    $page_description = htmlspecialchars($settings->siteTitle) . ' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

    include('inc/head.php');

    $id = $_SESSION['user'];

    if (!isset($_SESSION['user'])) {
        header('location: ../login.php');
        exit();
    }

    $conn = $pdo->open();

    // Fetch withdrawal requests
    try {
        $withdrawal_madeQuery = $conn->prepare("SELECT * FROM request WHERE user_id = :user_id AND type = 2 ORDER BY request_id DESC");
        $withdrawal_madeQuery->execute(['user_id' => $id]);
        $withdrawal_made = $withdrawal_madeQuery->rowCount() ? $withdrawal_madeQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Error fetching withdrawal requests: " . $e->getMessage());
        $withdrawal_made = [];
    }

    // Fetch payment methods
    try {
        $payment_methodQuery = $conn->prepare("SELECT * FROM payment_methods");
        $payment_methodQuery->execute();
        $payment_method = $payment_methodQuery->rowCount() ? $payment_methodQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Error fetching payment methods: " . $e->getMessage());
        $payment_method = [];
    }

    // Fetch first payment method
    try {
        $payment_completeQuery = $conn->prepare("SELECT * FROM payment_methods ORDER BY id ASC LIMIT 1");
        $payment_completeQuery->execute();
        $payment_complete = $payment_completeQuery->rowCount() ? $payment_completeQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Error fetching payment complete method: " . $e->getMessage());
        $payment_complete = [];
    }

    // Fetch withdrawal history
    try {
        $withdrawalHistoryQuery = $conn->prepare("SELECT * FROM transaction WHERE user_id = :user_id AND type = 2 ORDER BY trans_id DESC");
        $withdrawalHistoryQuery->execute(['user_id' => $id]);
        $withdrawalHistory = $withdrawalHistoryQuery->rowCount() ? $withdrawalHistoryQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Error fetching withdrawal history: " . $e->getMessage());
        $withdrawalHistory = [];
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
                                    <h4 class="page-title">Withdrawals</h4>
                                </div><!--end col-->
                                <div class="col-auto align-self-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                        <span class="day-name" id="Day_Name">Today:</span> 
                                        <span class="" id="Select_date">
                                            <?php
                                                // Display today's date in UTC
                                                $today = new DateTime('now', new DateTimeZone('UTC'));
                                                echo $today->format('M d, Y');
                                            ?>
                                        </span>
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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Withdrawal Requests</h4>
                                <p class="text-muted mb-0">All your withdrawal requests in one place. To withdraw funds, click the <b>Make a Withdrawal</b> button below.</p>
                                <a href="withdrawals-remove-fund" style="width: 20%" type="button" class="btn btn-primary btn-square btn-outline-dashed waves-effect waves-light mt-3 mb-3">Withdraw Funds</a>
                            </div><!--end card-header--> 
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body"> 
                                        <?php if (!empty($withdrawal_made)) { ?>
                                            <table class="table mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Request ID</th>
                                                        <th>Date & Time (UTC)</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($withdrawal_made as $withdraw_now) : ?>
                                                        <tr>
                                                            <td>DIDTXCRT<?= htmlspecialchars($withdraw_now->request_id); ?></td>
                                                            <td>
                                                                <?php
                                                                    // Convert trans_date from UTC+2 to UTC
                                                                    $trans_date = new DateTime($withdraw_now->trans_date, new DateTimeZone('Europe/Paris'));
                                                                    $trans_date->setTimezone(new DateTimeZone('UTC'));
                                                                    echo htmlspecialchars($trans_date->format('Y-m-d g:i A'));
                                                                ?>
                                                            </td>
                                                            <td>$<?= number_format($withdraw_now->amount, 2); ?></td>
                                                            <td><span class="badge badge-boxed badge-outline-<?php echo $withdraw_now->status == 'pending' ? 'info' : ($withdraw_now->status == 'cancelled' ? 'danger' : 'success'); ?>"><?= htmlspecialchars($withdraw_now->status); ?></span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table><!--end /table-->
                                        <?php } else { ?>
                                            <p>No Request Made</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Withdrawal History</h4>
                                <p class="text-muted mb-0">All your withdrawals in one place.</p>
                            </div><!--end card-header--> 
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body"> 
                                        <?php if (!empty($withdrawalHistory)) { ?>
                                            <table class="table mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Request ID</th>
                                                        <th>Date & Time (UTC)</th>
                                                        <th>Amount</th>
                                                        <th>Remark</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($withdrawalHistory as $row) : ?>
                                                        <tr>
                                                            <td>DIDTXCRT<?= htmlspecialchars($row->trans_id); ?></td>
                                                            <td>
                                                                <?php
                                                                    // Convert trans_date from UTC+2 to UTC
                                                                    $trans_date = new DateTime($row->trans_date, new DateTimeZone('Europe/Paris'));
                                                                    $trans_date->setTimezone(new DateTimeZone('UTC'));
                                                                    echo htmlspecialchars($trans_date->format('Y-m-d g:i A'));
                                                                ?>
                                                            </td>
                                                            <td>$<?= number_format($row->amount, 2); ?></td>
                                                            <td><?= htmlspecialchars($row->remark); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table><!--end /table-->
                                        <?php } else { ?>
                                            <p>You have made no withdrawal</p>
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

    <?php 
        $pdo->close();
        include('inc/scripts.php'); 
    ?>
</body>
</html>
