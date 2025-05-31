<?php
include('../inc/config.php');
include('../admin/session.php');

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit();
}

$page_name = 'Deposits';
$page_parent = '';
$page_title = 'Welcome to the Official Website of ' . htmlspecialchars($settings->siteTitle);
$page_description = htmlspecialchars($settings->siteTitle) . ' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

include('inc/head.php');

$id = $_SESSION['user'];

// Validate POST data
$deposit_amount = 0;
$payment_mode = '';
if (isset($_POST['payNow'])) {
    $deposit_amount = filter_var($_POST['deposit_amount'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
    $payment_mode = filter_var($_POST['payment_mode'], FILTER_SANITIZE_STRING);
    if ($deposit_amount === false || $deposit_amount <= 0 || empty($payment_mode)) {
        $_SESSION['error'] = 'Invalid deposit amount or payment method.';
        header('location: deposits.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Please submit the deposit form.';
    header('location: deposits.php');
    exit();
}

// Open database connection
$conn = $pdo->open();

try {
    // Fetch deposit requests
    $deposit_madeQuery = $conn->prepare("SELECT * FROM request WHERE user_id = :user_id AND type = 1 ORDER BY id DESC");
    $deposit_madeQuery->execute(['user_id' => $id]);
    $deposit_made = $deposit_madeQuery->rowCount() ? $deposit_madeQuery->fetchAll(PDO::FETCH_OBJ) : [];

    // Fetch payment methods
    $payment_methodQuery = $conn->prepare("SELECT * FROM payment_methods");
    $payment_methodQuery->execute();
    $payment_method = $payment_methodQuery->rowCount() ? $payment_methodQuery->fetchAll(PDO::FETCH_OBJ) : [];

    // Fetch selected payment method
    $payment_completeQuery = $conn->prepare("SELECT * FROM payment_methods WHERE name = :payment_mode LIMIT 1");
    $payment_completeQuery->execute(['payment_mode' => $payment_mode]);
    $payment_complete = $payment_completeQuery->rowCount() ? $payment_completeQuery->fetchAll(PDO::FETCH_OBJ) : [];

    // Fetch deposit history (if needed; currently unused in output)
    $depositHistoryQuery = $conn->prepare("SELECT * FROM transaction WHERE user_id = :user_id AND type = 1");
    $depositHistoryQuery->execute(['user_id' => $id]);
    $depositHistory = $depositHistoryQuery->rowCount() ? $depositHistoryQuery->fetchAll(PDO::FETCH_OBJ) : [];
} catch (PDOException $e) {
    error_log("Database error in deposits.php: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
    $_SESSION['error'] = 'Database error occurred.';
    header('location: deposits.php');
    exit();
} finally {
    $pdo->close();
}
?>

<body>
    <?php include('inc/sidebar.php'); ?>

    <div class="page-wrapper">
        <?php include('inc/header.php'); ?>

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">Deposits</h4>
                                </div>
                                <div class="col-auto align-self-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                        <span class="day-name" id="Day_Name">Today:</span> 
                                        <span class="" id="Select_date"><?= date('M d') ?></span>
                                        <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                                    </a>
                                </div>
                            </div>                                                              
                        </div>
                    </div>
                </div>

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
                                <h4 class="card-title">Complete Your Request</h4>
                                <p class="text-muted mt-2">
                                    Log Request and send <strong>$<?= number_format($deposit_amount, 2) ?></strong> worth of <strong><?= htmlspecialchars($payment_mode) ?></strong> to the displayed Wallet Address. Your account will be credited once payment is confirmed.
                                </p>
                            </div>
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body"> 
                                        <form class="form-horizontal auth-form" method="post" action="deposit-action.php">
                                            <input type="hidden" name="deposit_amount" value="<?= htmlspecialchars($deposit_amount) ?>">
                                            <input type="hidden" name="payment_mode" value="<?= htmlspecialchars($payment_mode) ?>">
                        
                                            <div class="form-group mb-2">
                                                <?php if (!empty($payment_complete)): ?>
                                                    <?php foreach ($payment_complete as $complete): ?>
                                                        <div class="form-group text-center">
                                                            <?php if (!empty($complete->photo)): ?>
                                                                <img src="../admin/images/<?= htmlspecialchars($complete->photo) ?>" alt="<?= htmlspecialchars($complete->name) ?>" style="max-width: 100px; margin-bottom: 10px;">
                                                            <?php else: ?>
                                                                <p class="text-muted">No image available for this payment method.</p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <label><strong>Wallet Address:</strong> <?= htmlspecialchars($complete->wallet_address) ?></label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="form-group">
                                                        <p class="text-danger">Selected payment method not found.</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                                                
                                            <div class="form-group mb-0 row">
                                                <div class="col-12">
                                                    <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="complete">Log Request Now <i class="fas fa-money-bill ml-1"></i></button>
                                                </div>
                                            </div>                          
                                        </form>
                                    </div>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('inc/footer.php'); ?>
        </div>
    </div>

    <?php include('inc/scripts.php'); ?>
</body>
</html>
