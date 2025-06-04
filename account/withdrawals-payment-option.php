<?php
    include('../inc/config.php');
    include '../inc/session.php';

    $page_name = 'Withdrawals';
    $page_parent = '';
    $page_title = 'Welcome to the Official Website of '.$settings->siteTitle;
    $page_description = $settings->siteTitle.' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining.';

    include('inc/head.php');

    if (!isset($_SESSION['user'])) {
        header('location: ../login.php');
        exit();
    }

    $id = $_SESSION['user'];

    if (!isset($_POST['removeFund']) || !isset($_POST['withdrawal_amount'])) {
        $_SESSION['error'] = 'Invalid withdrawal attempt.';
        header('location: withdrawals.php');
        exit();
    }

    $withdrawal_amount = $_POST['withdrawal_amount'];

    $conn = $pdo->open();

    $payment_methodQuery = $conn->query("SELECT * FROM payment_methods");
    $payment_method = $payment_methodQuery->rowCount() ? $payment_methodQuery->fetchAll(PDO::FETCH_OBJ) : [];
?>

<body class="dark-topbar">
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
                                <h4 class="page-title">Withdrawals</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
                unset($_SESSION['success']);
            }
            ?>

            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Choose Payment Option</h4>
                            <p class="text-muted mb-0">Enter your payment information and select a payment method.</p>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal auth-form" method="post" action="withdraw-action.php">
                                <input type="hidden" name="withdrawal_amount" value="<?= htmlspecialchars($withdrawal_amount) ?>">

                                <div class="form-group">
                                    <label for="payment_mode">Payment Method</label>
                                    <select name="payment_mode" class="form-control" required>
                                        <option selected disabled>Choose Mode of Payment</option>
                                        <?php foreach ($payment_method as $payment): ?>
                                            <option value="<?= htmlspecialchars($payment->name) ?>"><?= htmlspecialchars($payment->name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="payment_info">Payment Info (e.g., Wallet Address or Bank Details)</label>
                                    <textarea name="payment_info" class="form-control" rows="3" required></textarea>
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" type="submit" name="withdrawNow">
                                        Proceed with Withdrawal
                                    </button>
                                </div>
                            </form>
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
