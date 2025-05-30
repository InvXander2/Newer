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

if (!$row) {
    $_SESSION['error'] = 'User not found. Please log in again.';
    header('location: dashboard.php');
    exit();
}

$investor_email = $row['email'];
$investor_name = $row['full_name'];

if (isset($_POST['complete'])) {
    $invest_id = $_POST['invest_id'];

    if (empty($invest_id)) {
        $_SESSION['error'] = 'Investment ID is missing.';
        header('location: dashboard.php');
        exit();
    }

    $conn = $pdo->open();

    // Validate investment exists and belongs to the user
    $stmt = $conn->prepare("SELECT * FROM investment WHERE invest_id=:invest_id AND user_id=:user_id");
    $stmt->execute(['invest_id' => $invest_id, 'user_id' => $user_id]);
    $investment = $stmt->fetch();

    if (!$investment) {
        $_SESSION['error'] = 'Investment not found or does not belong to you.';
        $pdo->close();
        header('location: dashboard.php');
        exit();
    }

    // Check if investment is already completed
    if ($investment['status'] === 'completed') {
        $_SESSION['error'] = 'This investment is already completed.';
        $pdo->close();
        header('location: dashboard.php');
        exit();
    }

    $status = 'completed';
    $completion_date = date('Y-m-d');
    $act_time = date('Y-m-d h:i A');

    try {
        // Begin transaction
        $conn->beginTransaction();

        // Update investment status to completed
        $stmt = $conn->prepare("UPDATE investment SET status=:status, end_date=:completion_date WHERE invest_id=:invest_id AND user_id=:user_id");
        $stmt->execute(['status' => $status, 'completion_date' => $completion_date, 'invest_id' => $invest_id, 'user_id' => $user_id]);

        // Fetch investment details
        $capital = $investment['capital'];
        $returns = $investment['returns'];
        $invest_plan_id = $investment['invest_plan_id'];

        // Fetch plan name
        $stmt = $conn->prepare("SELECT name FROM investment_plans WHERE id=:invest_plan_id");
        $stmt->execute(['invest_plan_id' => $invest_plan_id]);
        $plan = $stmt->fetch();
        $plan_name = $plan['name'] ?? 'Investment Plan';

        // Get current balance
        $stmt = $conn->prepare("SELECT balance FROM transaction WHERE user_id = :user_id ORDER BY trans_id DESC LIMIT 1");
        $stmt->execute(['user_id' => $user_id]);
        $current_balance = $stmt->fetchColumn() ?: 0;

        // Credit returns
        $amount = $returns;
        $new_balance = $current_balance + $amount;
        $transaction_remark = "Received return for $plan_name";

        // Insert transaction with updated remark
        $stmt = $conn->prepare("INSERT INTO transaction (user_id, amount, type, balance, trans_date, remark) 
                                VALUES (:user_id, :amount, :type, :balance, NOW(), :remark)");
        $stmt->execute([
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => 1, // Credit
            'balance' => $new_balance,
            'remark' => $transaction_remark
        ]);

        // Log activity with updated category and message
        $activity_message = "Investment Completed";
        $stmt = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                VALUES (:user_id, :message, :category, :date_sent)");
        $stmt->execute([
            'user_id' => $user_id,
            'message' => $activity_message,
            'category' => $plan_name,
            'date_sent' => $act_time
        ]);

        // Commit transaction
        $conn->commit();

        // Debugging: Log details
        error_log("investment-complete.php: invest_id=$invest_id, user_id=$user_id, status=$status, amount=$amount, new_balance=$new_balance, transaction_remark=$transaction_remark, activity_message=$activity_message, activity_category=$plan_name, time=" . date('Y-m-d H:i:s'), 3, '../debug.log');

        // Prepare email content
        $email_message = "
            <div style='font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; direction: ltr;'>
                <table border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#F3F2F1'>
                    <tbody>
                        <tr>
                            <td align='center' bgcolor='#F3F2F1' style='padding: 0 8px;'>
                                <table border='0' width='100%' cellspacing='0' cellpadding='0' style='max-width: 600px; padding: 0 0 15px 0;'>
                                    <tbody>
                                        <tr>
                                            <td align='left' style='padding: 10px 0 13px 0;'>
                                                <a href='https://www.nexusinsights.eu'>
                                                    <img style='display: block;' src='https://www.nexusinsights.eu/assets/images/logo-dark.png' alt='prime-logo' width='300' height='60' border='0' />
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' style='max-width: 600px;'>
                                    <tbody>
                                        <tr>
                                            <td style='padding-bottom: 24px !important;'>
                                                <table border='0' width='100%' cellspacing='0' cellpadding='0'>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <table border='0' width='100%' cellspacing='0' cellpadding='0'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style='padding: 16px 10px 0;'>
                                                                                <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>
                                                                                    <span style='font-size: 12pt; font-family: arial black, sans-serif; color: #000000;'><strong>Dear $investor_name,</strong></span>
                                                                                </p>
                                                                                <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'> </p>
                                                                                <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>
                                                                                    <span style='color: #000000;'>
                                                                                        Your investment of $$capital for the $plan_name has been successfully completed on $completion_date, and $$amount has been credited to your account. You can view the details in your Nexus Insights account.
                                                                                    </span>
                                                                                </p>
                                                                                <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'> </p>
                                                                                <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>
                                                                                    <span style='color: #000000;'>
                                                                                        For any inquiries, please contact <strong><a style='color: #000000;' href='mailto:info@nexusinsights.eu'>info@nexusinsights.eu</a></strong>.
                                                                                    </span>
                                                                                </p>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table border='0' width='573' cellspacing='0' cellpadding='0' bgcolor='#F2F2F2' style='max-width: 550px; height: 264px; width: 114.979%;'>
                                    <tbody>
                                        <tr>
                                            <td style='padding: 24px 4px; width: 100%;'>
                                                <table border='0' cellspacing='0' cellpadding='0' align='center' style='max-width: 424px;'>
                                                    <tbody>
                                                        <tr>
                                                            <td align='center' style='font-size: 12px; line-height: 16px; color: #4b4b4b; padding: 20px 0; margin: 0 auto;'>
                                                                *This email account is not monitored. Reply to <a href='mailto:info@nexusinsights.eu'>info@nexusinsights.eu</a> if you have any query.
                                                                <a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/investment'> View Our Available Plans </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table border='0' width='100%' cellspacing='0' cellpadding='0' align='center' style='font-size: 12px; color: #2d2d2d; line-height: 22px; margin: 0px auto; height: 63px; width: 69.7305%;'>
                                                    <tbody>
                                                        <tr style='height: 43px;'>
                                                            <td align='center' lang='en' style='padding: 0px; height: 43px;'>© 2021 Nexus Insights Limited.</td>
                                                        </tr>
                                                        <tr style='height: 10px;'>
                                                            <td align='center' style='padding: 15px 0px 25px; height: 10px;'>
                                                                <span><a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu'>Home</a>|</span>
                                                                <a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/about'>About</a>
                                                                <span>|</span>
                                                                <a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/investment'>Plans</a> <br />
                                                                <a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/news'> News </a>
                                                                <span>|</span>
                                                                <a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/contact'>Contact</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ";

        // Notify Admin
        $admin_msg = "$investor_name has completed an investment of $$capital for $plan_name. Login to Admin for details.";
        $admin_msg = wordwrap($admin_msg, 70);
        mail($settings->email, "Investment Completed", $admin_msg);

        // Send email to user
        require '../vendor/autoload.php';

        $mail = new PHPMailer(true);
        try {
            $mail->IsSMTP();
            $mail->Host = $sweet_url;
            $mail->Port = '465';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@' . $sweet_url;
            $mail->Password = $noreply_password;
            $mail->SMTPSecure = 'ssl';
            $mail->From = 'noreply@' . $sweet_url;
            $mail->FromName = $settings->siteTitle;
            $mail->AddAddress($investor_email);
            $mail->IsHTML(true);

            $mail->Subject = $settings->siteTitle . " Investment Completion Confirmation";
            $mail->Body = $email_message;

            $mail->send();

            $_SESSION['success'] = 'Your investment has been successfully completed. Check your email for confirmation.';
        } catch (Exception $e) {
            $_SESSION['success'] = 'Your investment has been completed. A confirmation email could not be sent.';
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        error_log("investment-complete.php: Error - " . $e->getMessage(), 3, '../debug.log');
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Invalid request. Please ensure all fields are filled.';
}

header('location: dashboard.php');
?>
