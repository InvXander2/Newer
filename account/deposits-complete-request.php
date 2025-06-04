<?php
    include('../inc/config.php');
    include '../../inc/session.php';

    $page_name = 'Deposits';
    $page_parent = '';
    $page_title = 'Welcome to the Official Website of ' . $settings->siteTitle;
    $page_description = 'Manage Investment provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

    include('inc/head.php');

    $id = $_SESSION['user'];

    if (!isset($_SESSION['user'])) {
        header('location: ../login.php');
    }

    if (isset($_POST['payNow'])) {
        $deposit_amount = $_POST['deposit_amount'];
        $payment_mode = $_POST['payment_mode'];
    } else {
        header("location: deposits.php");
    }

    $conn = $pdo->open();

    $deposit_madeQuery = $conn->query("SELECT * FROM request WHERE user_id=$id && type=1 ORDER BY 1 DESC");
    if ($deposit_madeQuery->rowCount()) {
        $deposit_made = $deposit_madeQuery->fetchAll(PDO::FETCH_OBJ);
    }

    $payment_methodQuery = $conn->query("SELECT * FROM payment_methods");
    if ($payment_methodQuery->rowCount()) {
        $payment_method = $payment_methodQuery->fetchAll(PDO::FETCH_OBJ);
    }

    // Fetch the selected payment method
    $payment_completeQuery = $conn->prepare("SELECT * FROM payment_methods WHERE name = :payment_mode LIMIT 1");
    $payment_completeQuery->execute(['payment_mode' => $payment_mode]);
    if ($payment_completeQuery->rowCount()) {
        $payment_complete = $payment_completeQuery->fetchAll(PDO::FETCH_OBJ);
    }

    $depositHistory = "SELECT * FROM transaction WHERE user_id = $id && type = 1";
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
                                        <span class="" id="Select_date">Jan 11</span>
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
                    <div class="col-lg-3"></div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Complete Your Request</h4>
                                <p class="text-muted mt-2">
                                    Log Request and send <strong>$<?php echo number_format($deposit_amount); ?></strong> worth of <strong><?php echo htmlspecialchars($payment_mode); ?></strong> to the displayed Wallet Address. Your account will be credited once payment is confirmed.
                                </p>
                            </div>
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-body"> 
                                        <form class="form-horizontal auth-form" method="post" action="deposit-action">
                                            <input type="number" name="deposit_amount" value="<?php echo $deposit_amount; ?>" hidden>
                                            <input type="text" name="payment_mode" value="<?php echo $payment_mode; ?>" hidden>
                        
                                            <div class="form-group mb-2">
                                                <?php foreach ($payment_complete as $complete) : ?>
                                                    <div class="form-group">
                                                        <label><strong>Payment Method:</strong> <?php echo htmlspecialchars($complete->name); ?></label>
                                                    </div>
                                                    <div class="form-group text-center">
                                                        <img src="../admin/images/<?php echo basename($complete->photo); ?>" alt="<?php echo htmlspecialchars($complete->name); ?>" style="max-width: 200px; display: block; margin: 0 auto;">
                                                    </div>
                                                    <div class="form-group">
                                                        <label><strong>Wallet Address:</strong> <?php echo htmlspecialchars($complete->wallet_address); ?></label>
                                                    </div>
                                                <?php endforeach; ?>
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
