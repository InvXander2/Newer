<?php
include('../inc/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'inc/session.php';
include '../admin/includes/slugify.php';

$user_id = $_SESSION['user'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=:user_id");
$stmt->execute(['user_id' => $user_id]);
$row = $stmt->fetch();
$investor_email = $row['email'];
$investor_name = $row['full_name'];

if (
    isset($_POST['withdrawNow']) &&
    !empty($_POST['withdrawal_amount']) &&
    !empty($_POST['payment_mode']) &&
    !empty($_POST['payment_info'])
) {
    $withdrawal_amount = $_POST['withdrawal_amount'];
    $payment_mode = $_POST['payment_mode'];
    $payment_info = $_POST['payment_info'];
    $status = 'pending';

    $conn = $pdo->open();
    $trans_date = date('Y-m-d');
    $act_time = date('Y-m-d h:i A');

    try {
        // Insert withdrawal request
        $stmt = $conn->prepare("INSERT INTO request (user_id, trans_date, type, amount, payment_mode, payment_info, status) VALUES (:user_id, :trans_date, :type, :amount, :payment_mode, :payment_info, :status)");
        $stmt->execute([
            'user_id' => $user_id,
            'trans_date' => $trans_date,
            'type' => 2,
            'amount' => $withdrawal_amount,
            'payment_mode' => $payment_mode,
            'payment_info' => $payment_info,
            'status' => $status
        ]);

        // Log activity
        $activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :start_date)");
        $activity->execute([
            'user_id' => $user_id,
            'message' => 'You made a withdrawal request of $' . $withdrawal_amount,
            'category' => 'Withdrawal Request',
            'start_date' => $act_time
        ]);

        // Email to user
        require '../vendor/autoload.php';
        $mail = new PHPMailer(true);
        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host = $smtpConfig['nexusinsights.it.com'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtpConfig['info@nexusinsights.it.com'];
            $mail->Password = $smtpConfig['Xander24427279'];
            $mail->SMTPSecure = $smtpConfig['ssl'];
            $mail->Port = $smtpConfig['465'];

            // Sender and recipient
            $mail->setFrom($smtpConfig['fromEmail'], $smtpConfig['fromName']);
            $mail->addAddress($investor_email, $investor_name);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = $settings->siteTitle . ' Withdrawal Request';
            $mail->Body = "
                <div>
                    <h2>Withdrawal Request Submitted</h2>
                    <p>Dear " . htmlspecialchars($investor_name) . ",</p>
                    <p>Your withdrawal request of <strong>$" . htmlspecialchars($withdrawal_amount) . "</strong> has been submitted and is currently being reviewed.</p>
                    <p>You will be contacted shortly regarding the next steps.</p>
                    <p>Thank you for choosing " . htmlspecialchars($settings->siteTitle) . "!</p>
                </div>";

            $mail->send();
            $_SESSION['success'] = 'Your request has been sent and you will be contacted on how to proceed shortly';
        } catch (Exception $e) {
            // Log PHPMailer error
            error_log("PHPMailer error in withdrawal request: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
            $_SESSION['success'] = 'Your request has been sent and will be processed shortly';
        }

        // Email to admin
        $adminMail = new PHPMailer(true);
        try {
            // SMTP settings (same as above)
            $adminMail->isSMTP();
            $adminMail->Host = $smtpConfig['nexusinsights.it.com'];
            $adminMail->SMTPAuth = true;
            $adminMail->Username = $smtpConfig['info@nexusinsights.it.com'];
            $adminMail->Password = $smtpConfig['Xander24427279'];
            $adminMail->SMTPSecure = $smtpConfig['ssl'];
            $adminMail->Port = $smtpConfig['465'];

            // Sender and recipient
            $adminMail->setFrom($smtpConfig['fromEmail'], $smtpConfig['fromName']);
            $adminMail->addAddress($settings->email, 'Admin');

            // Email content
            $adminMail->isHTML(true);
            $adminMail->Subject = 'New Withdrawal Request - ' . $settings->siteTitle;
            $adminMail->Body = "
                <div>
                    <h2>New Withdrawal Request</h2>
                    <p>User: " . htmlspecialchars($investor_name) . "</p>
                    <p>Email: " . htmlspecialchars($investor_email) . "</p>
                    <p>Amount: $" . htmlspecialchars($withdrawal_amount) . "</p>
                    <p>Payment Mode: " . htmlspecialchars($payment_mode) . "</p>
                    <p>Payment Info: " . htmlspecialchars($payment_info) . "</p>
                    <p>Please log in to the admin panel to review this request.</p>
                </div>";

            $adminMail->send();
        } catch (Exception $e) {
            error_log("PHPMailer error in admin notification: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
        error_log("Database error in withdrawal request: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Make sure all fields are filled';
}

header('location: withdrawals.php');
?>
