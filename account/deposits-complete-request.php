<?php
include('../inc/config.php');
include('../admin/session.php');

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header('location: ../index.php');
    exit();
}

$page_name = 'Deposits';
$page_parent = '';
$page_title = 'Welcome to the Official Website of ' . htmlspecialchars($settings->name);
$page_description = htmlspecialchars($settings->name) . ' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today!';

include('inc/head.php');

$id = $_SESSION['user'];

// Validate POST data
$deposit_amount = 0;
$payment_mode = '';
if (isset($_POST['pay_now'])) {
    $deposit_amount = filter_var($_POST['deposit_amount'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $payment_mode = filter_var($_POST['payment_mode'], FILTER_SANITIZE_STRING);
    error_log("Posted payment mode: $payment_mode", 3, __DIR__ . "/../inc/error_log.txt"); // Debug
    if ($deposit_amount === false || $deposit_amount <= 0 || empty($payment_mode)) {
        $_SESSION['error'] = 'Invalid deposit amount or payment mode.';
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
    // Verify payment mode exists
    $payment_check = $conn->prepare("SELECT name FROM payment_methods WHERE name = :payment_mode");
    $payment_check->execute(['payment_mode' => $payment_mode]);
    if (!$payment_check->rowCount()) {
        error_log("Invalid payment mode: $payment_mode", 3, __DIR__ . "/../inc/error_log.txt");
        $_SESSION['error'] = 'Selected payment method not found.';
        header('location: deposits.php');
        exit();
    }

    // Fetch deposit requests
    try {
        $deposit_madeQuery = $conn->prepare("SELECT * FROM request WHERE user_id = :user_id AND type = 1 ORDER BY request_id DESC");
        $deposit_madeQuery->execute(['user_id' => $id]);
        $deposit_made = $deposit_madeQuery->rowCount() ? $deposit_madeQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Request query error: " . $e->getMessage(), 3, __DIR__ . "/../inc/error_log.txt");
        throw new PDOException("Failed to fetch deposit requests: " . $e->getMessage());
    }

    // Fetch payment methods
    try {
        $payment_methodQuery = $conn->prepare("SELECT * FROM payment_methods");
        $payment_methodQuery->execute();
        $payment_method = $payment_methodQuery->rowCount() ? $payment_methodQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Payment methods query error: " . $e->getMessage(), 3, __DIR__ . "/../inc/error_log.txt");
        throw new PDOException("Failed to fetch payment methods: " . $e->getMessage());
    }

    // Fetch selected payment method
    try {
        $payment_completeQuery = $conn->prepare("SELECT * FROM payment_methods WHERE name = :payment_mode LIMIT 1");
        $payment_completeQuery->execute(['payment_mode' => $payment_mode]);
        $payment_complete = $payment_completeQuery->rowCount() ? $payment_completeQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Selected payment method query error: " . $e->getMessage(), 3, __DIR__ . "/../inc/error_log.txt");
        throw new PDOException("Failed to fetch selected payment method: " . $e->getMessage());
    }

    // Fetch deposit history
    try {
        $depositHistoryQuery = $conn->prepare("SELECT * FROM transaction WHERE user_id = :user_id AND type = 1");
        $depositHistoryQuery->execute(['user_id' => $id]);
        $deposit_history = $depositHistoryQuery->rowCount() ? $depositHistoryQuery->fetchAll(PDO::FETCH_OBJ) : [];
    } catch (PDOException $e) {
        error_log("Transaction query error: " . $e->getMessage(), 3, __DIR__ . "/../inc/error_log.txt");
        throw new PDOException("Failed to fetch deposit history: " . $e->getMessage());
    }
} catch (PDOException $e) {
    error_log("Database error in deposits.php: " . $e->getMessage(), 3, __DIR__ . "/../inc/error_log.txt");
    $_SESSION['error'] = 'Database error occurred: ' . htmlspecialchars($e->getMessage()); // Temporary for debugging
    header('location: deposits.php');
    exit();
} finally {
    $pdo->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
</head>
<body>
    <?php include('inc/sidebar.php'); ?>

    <div class="page-content-wrapper">
        <?php include('inc/header.php'); ?>

        <div class="main-content">
            <div class="page-header">
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
                                            <span id="Select_date"><?= date('M d') ?></span>
                                            <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                                        </a>
                                    </div>
                                </div>                                                              
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

            <div class="container-fluid">
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
                                                            <!-- Debug photo value -->
                                                            <p class="text-muted">Photo: <?= htmlspecialchars($complete->photo ?: 'Empty') ?></p>
                                                            <?php if (!empty($complete->photo) && file_exists(__DIR__ . "/../admin/images/" . $complete->photo)): ?>
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
