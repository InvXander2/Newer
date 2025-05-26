<?php  
include('../inc/config.php');  
include '../admin/session.php';  

$page_name = 'Withdrawals';  
$page_parent = '';  
$page_title = 'Welcome to the Official Website of '.$settings->siteTitle;  
$page_description = $settings->siteTitle.' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';  

include('inc/head.php');  

$id = $_SESSION['user'];  

if(!isset($_SESSION['user'])){  
    header('location: ../login.php');  
    exit();  
}  

if (!isset($_POST['withdrawNow'])) {  
    header("location: withdrawals.php");  
    exit();  
}

$withdrawal_amount = $_POST['withdrawal_amount'];  
$payment_mode = $_POST['payment_mode'];  

$conn = $pdo->open();  

$withdrawal_madeQuery = $conn->query("SELECT * FROM request WHERE user_id=$id && type=2 order by 1 desc ");  
if ($withdrawal_madeQuery->rowCount()) {  
   $withdrawal_made = $withdrawal_madeQuery->fetchAll(PDO::FETCH_OBJ);  
}  

$payment_methodQuery = $conn->query("SELECT * FROM payment_methods");  
if ($payment_methodQuery->rowCount()) {  
   $payment_method = $payment_methodQuery->fetchAll(PDO::FETCH_OBJ);  
}  

$payment_completeQuery = $conn->query("SELECT * FROM payment_methods order by 1 asc Limit 1");  
if ($payment_completeQuery->rowCount()) {  
   $payment_complete = $payment_completeQuery->fetchAll(PDO::FETCH_OBJ);  
}  

?>  
<body class="dark-topbar">  
<?php include('inc/sidebar.php'); ?>  
<div class="page-wrapper">  
<?php include('inc/header.php'); ?>  

<div class="page-content">  
<div class="container-fluid">  

<div class="row">  
<div class="col-lg-3"></div>  
<div class="col-lg-6">  
<div class="card">  
<div class="card-header">  
<h4 class="card-title">Complete Your Request</h4>  
</div>  

<div class="card-body">  
<div class="card">  
<div class="card-body">   
<form class="form-horizontal auth-form" method="post" action="">  
    <input type="number" name="withdrawal_amount" value="<?php echo $withdrawal_amount; ?>" hidden>  
    <input type="text" name="payment_mode" value="<?php echo $payment_mode; ?>" hidden>  

    <div class="form-group mb-2">  
        <label>Enter Payment Method Info</label>  
        <div class="form-input-group">  
            <input type="text" class="form-control" name="payment_info" required placeholder="This could be a wallet address, bank account info..." />  
        </div>  
    </div>  

    <div class="form-group mb-0 row">  
        <div class="col-12">  
            <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="complete">Complete Request <i class="fas fa-money-bill ml-1"></i></button>  
        </div>  
    </div>  
</form>  
</div>  
</div>  
</div>  
</div>  
</div>  
</div>  

<?php  
// Handle form submission  
if (isset($_POST['complete'])) {  
    $withdrawal_amount = $_POST['withdrawal_amount'];  
    $payment_mode = $_POST['payment_mode'];  
    $payment_info = $_POST['payment_info'];  

    try {  
        $stmt = $conn->prepare("INSERT INTO request (user_id, amount, type, method, method_info, status, created_at) VALUES (:user_id, :amount, :type, :method, :method_info, 0, NOW())");  
        $stmt->execute([  
            'user_id' => $id,  
            'amount' => $withdrawal_amount,  
            'type' => 2,  
            'method' => $payment_mode,  
            'method_info' => $payment_info  
        ]);  
  
        $_SESSION['success'] = 'Withdrawal request submitted successfully.';  
        header("Location: withdrawals.php");  
        exit();  
    } catch (PDOException $e) {  
        $_SESSION['error'] = 'Something went wrong: ' . $e->getMessage();  
        header("Location: withdrawals.php");  
        exit();  
    }  
}  
?>  

</div>  
<?php include('inc/footer.php'); ?>  
</div>  
<?php include('inc/scripts.php'); ?>  
</body>  
</html>
