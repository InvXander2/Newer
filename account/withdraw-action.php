<?php
include('../inc/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'inc/session.php';
include '../admin/includes/slugify.php';

$user_id = $_SESSION['user'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

$investor_email = $user['email'];
$investor_name = $user['full_name'];

if (isset($_POST['complete'])) {
    $withdrawal_amount = filter_input(INPUT_POST, 'withdrawal_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $payment_mode = filter_input(INPUT_POST, 'payment_mode', FILTER_SANITIZE_STRING);
    $status = 'pending';
    $trans_date = date('Y-m-d');
    $act_time = date('Y-m-d h:i A');

    try {
        $conn = $pdo->open();

        // Insert withdrawal request
        $stmt = $conn->prepare("INSERT INTO request (user_id, trans_date, type, amount, status) VALUES (:user_id, :trans_date, :type, :amount, :status)");
        $stmt->execute([
            'user_id' => $user_id,
            'trans_date' => $trans_date,
            'type' => 2,
            'amount' => $withdrawal_amount,
            'status' => $status
        ]);

        // Log activity
        $activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :date_sent)");
        $activity->execute([
            'user_id' => $user_id,
            'message' => 'You made a withdrawal request of $' . $withdrawal_amount,
            'category' => 'Withdrawal Request',
            'date_sent' => $act_time
        ]);

        // Compose email message
        $message = "
            <p><strong>Dear {$investor_name},</strong></p>
            <p>Your request to withdraw \${$withdrawal_amount} has been received. Funds will be withdrawn to your chosen payment option upon confirmation.</p>
            <p>Do note that Nexus Insights will not give you any other wallet address apart from the one shown on the website.</p>
            <p>To report fraudulent activities, contact <a href='mailto:fraud@primeassetslimited.com'>fraud@primeassetslimited.com</a></p>
            <p>Thank you for using our services.</p>
        ";

        // Notify admin via mail()
        $admin_msg = "{$investor_name} just requested a withdrawal. Login to Admin panel.";
        mail($settings->email, "New Withdrawal Request", wordwrap($admin_msg, 70));

        // Send confirmation email using PHPMailer
        require '../vendor/autoload.php';

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $sweet_url;
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@' . $sweet_url;
        $mail->Password = $noreply_password;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('noreply@' . $sweet_url, $settings->siteTitle);
        $mail->addAddress($investor_email);
        $mail->isHTML(true);
        $mail->Subject = $settings->siteTitle . " Withdrawal Request";
        $mail->Body = $message;

        $mail->send();

        $_SESSION['success'] = 'Your request has been sent and you will be contacted shortly.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Mailer Error: ' . $mail->ErrorInfo;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database Error: ' . $e->getMessage();
    } finally {
        $pdo->close();
    }
} else {
    $_SESSION['error'] = 'Make sure all fields are filled.';
}

header('location: withdrawals.php');
exit;
