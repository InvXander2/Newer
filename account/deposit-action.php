<?php
include('../inc/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'inc/session.php';
include '../admin/includes/slugify.php';

$user_id = $_SESSION['user'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$row = $stmt->fetch();
$investor_email = $row['email'];
$investor_name = $row['full_name'];

if (isset($_POST['complete']) && !empty($_POST['deposit_amount']) && !empty($_POST['payment_mode'])) {
    $deposit_amount = htmlspecialchars(trim($_POST['deposit_amount']));
    $payment_mode = htmlspecialchars(trim($_POST['payment_mode']));
    $status = 'pending';

    $conn = $pdo->open();
    $trans_date = date('Y-m-d');
    $act_time = date('Y-m-d h:i A');

    try {
        // Insert deposit request
        $stmt = $conn->prepare("INSERT INTO request (user_id, trans_date, type, amount, status) 
                                VALUES (:user_id, :trans_date, :type, :amount, :status)");
        $stmt->execute([
            'user_id' => $user_id,
            'trans_date' => $trans_date,
            'type' => 1,
            'amount' => $deposit_amount,
            'status' => $status
        ]);

        // Log user activity
        $activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                    VALUES (:user_id, :message, :category, :date_sent)");
        $activity->execute([
            'user_id' => $user_id,
            'message' => "You made a deposit request of $$deposit_amount",
            'category' => 'Deposit Request',
            'date_sent' => $act_time
        ]);

        // Email content
        $message = <<<EOT
<!-- Email HTML content here (same as your current HTML email block) -->
EOT;

        // Notify admin
        $msg = wordwrap("$investor_name just requested a deposit. Login Admin", 70);
        mail($settings->email, "New Deposit Request", $msg);

        // Send confirmation email
        require '../vendor/autoload.php';
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $sweet_url;
            $mail->Port = 465;
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@' . $sweet_url;
            $mail->Password = $noreply_password;
            $mail->SMTPSecure = 'ssl';
            $mail->setFrom('noreply@' . $sweet_url, $settings->siteTitle);
            $mail->addAddress($investor_email);
            $mail->isHTML(true);
            $mail->Subject = $settings->siteTitle . " Deposit Request";
            $mail->Body = $message;

            $mail->send();

            $_SESSION['success'] = 'Your request has been sent and you will be contacted on how to proceed shortly';
        } catch (Exception $e) {
            $_SESSION['success'] = 'Your request has been sent. Please proceed to pay and invest';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Make sure all fields are filled.';
}

header('location: deposits.php');
exit();
?>
