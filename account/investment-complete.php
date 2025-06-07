<?php
include('../inc/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'inc/session.php';
include '../admin/includes/slugify.php';

// Add $page_name to prevent errors if scripts.php is included
$page_name = 'Investment Completion';

$user_id = $_SESSION['user'];

$conn = $pdo->open();

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $row = $stmt->fetch();

    if (!$row) {
        $_SESSION['error'] = 'User not found. Please log in again.';
        header('location: dashboard.php');
        exit;
    }

    $investor_email = $row['email'];
    $investor_name = $row['full_name'];

    if (isset($_POST['complete'])) {
        $invest_id = $_POST['invest_id'];

        if (empty($invest_id)) {
            $_SESSION['error'] = 'Investment ID is missing.';
            header('location: dashboard.php');
            exit;
        }

        // Validate investment exists and belongs to the user
        $stmt = $conn->prepare("SELECT * FROM investment WHERE invest_id = :invest_id AND user_id = :user_id");
        $stmt->execute(['invest_id' => $invest_id, 'user_id' => $user_id]);
        $investment = $stmt->fetch();

        if (!$investment) {
            $_SESSION['error'] = 'Investment not found or does not belong to you.';
            header('location: dashboard.php');
            exit;
        }

        // Check if investment is already completed
        if ($investment['status'] === 'completed') {
            $_SESSION['error'] = 'This investment is already completed.';
            header('location: dashboard.php');
            exit;
        }

        $status = 'completed';
        $completion_date = date('Y-m-d');
        $act_time = date('Y-m-d h:i A');

        try {
            // Begin transaction
            $conn->beginTransaction();

            // Update investment status to completed
            $stmt = $conn->prepare("UPDATE investment SET status = :status, end_date = :completion_date WHERE invest_id = :invest_id AND user_id = :user_id");
            $stmt->execute(['status' => $status, 'completion_date' => $completion_date, 'invest_id' => $invest_id, 'user_id' => $user_id]);

            // Fetch investment details
            $capital = $investment['capital'];
            $returns = $investment['returns'];
            $invest_plan_id = $investment['invest_plan_id'];

            // Fetch plan name
            $stmt = $conn->prepare("SELECT name FROM investment_plans WHERE id = :invest_plan_id");
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
            $transaction_remark = "Received return of $$amount for $plan_name";

            // Insert transaction
            $stmt = $conn->prepare("INSERT INTO transaction (user_id, amount, type, balance, trans_date, remark) 
                                    VALUES (:user_id, :amount, :type, :balance, :trans_date, :remark)");
            $stmt->execute([
                'user_id' => $user_id,
                'amount' => $amount,
                'type' => 1, // Credit
                'balance' => $new_balance,
                'trans_date' => $act_time,
                'remark' => $transaction_remark
            ]);

            // Log activity
            $activity_message = "Completed investment of $$capital for $plan_name";
            $stmt = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                    VALUES (:user_id, :message, :category, :date_sent)");
            $stmt->execute([
                'user_id' => $user_id,
                'message' => $activity_message,
                'category' => 'Investment Completion',
                'date_sent' => $act_time
            ]);

            // Commit transaction
            $conn->commit();

            // Debugging: Log details
            error_log("investment-complete.php: invest_id=$invest_id, user_id=$user_id, status=$status, amount=$amount, new_balance=$new_balance, transaction_remark=$transaction_remark, activity_message=$activity_message, activity_category=Investment Completion, time=" . date('Y-m-d H:i:s'), 3, '../debug.log');

            // Email template for user
            $user_message = <<<HTML
<div style='font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; direction: ltr; background-color: #f3f2f1; margin: 0; padding: 0;'>
    <table class='main' border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#F3F2F1'>
        <tbody>
            <tr>
                <td class='outer-box' style='padding: 0 8px;' align='center' bgcolor='#F3F2F1'>
                    <table style='max-width: 600px; padding: 0 0 15px 0;' border='0' width='100%' cellspacing='0' cellpadding='0'>
                        <tbody>
                            <tr>
                                <td style='padding: 10px 0 13px 0;' align='left'>
                                    <a href='https://{$sweet_url}'>
                                        <img
                                            style='display: block;'
                                            src='https://{$sweet_url}/assets/images/logo-dark.png'
                                            alt='nexus-logo'
                                            width='300'
                                            height='60'
                                            border='0'
                                        />
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class='width-600' style='max-width: 600px;' border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF'>
                        <tbody>
                            <tr>
                                <td class='content-box' style='padding-bottom: 24px !important;'>
                                    <table border='0' width='100%' cellspacing='0' cellpadding='0'>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table border='0' width='100%' cellspacing='0' cellpadding='0'>
                                                        <tbody>
                                                            <tr>
                                                                <td style='padding: 16px 10px 0;'>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                        <span style='font-size: 12pt; font-family: arial black, sans-serif; color: #000000;'>
                                                                            <strong>Dear {$investor_name},</strong>
                                                                        </span>
                                                                    </p>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'> </p>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                        <span style='color: #000000;'>
                                                                            Your investment of <strong>\${$capital}</strong> for the <strong>{$plan_name}</strong> has been successfully completed on {$completion_date}, and <strong>\${$amount}</strong> has been credited to your account. You can view the details in your Nexus Insights account.
                                                                        </span>
                                                                    </p>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'> </p>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                        <span style='color: #000000;'>
                                                                            Do note that Nexus Insights will not give you any other wallet address apart from the one shown on the website.
                                                                        </span>
                                                                        <br /><br />
                                                                        <span style='color: #000000;'>
                                                                            To report fraudulent activities, contact
                                                                            <strong><a style='color: #000000;' href='mailto:{$settings->email2}'>fraud@nexusinsights.it.com</a></strong>
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
                            <tr>
                                <td> </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style='max-width: 550px; width: 100%;' border='0' cellspacing='0' cellpadding='0' bgcolor='#F2F2F2'>
                        <tbody>
                            <tr>
                                <td style='padding: 24px 4px; width: 100%;'>
                                    <table style='max-width: 424px;' border='0' cellspacing='0' cellpadding='0' align='center'>
                                        <tbody>
                                            <tr>
                                                <td style='font-size: 12px; line-height: 16px; color: #4b4b4b; padding: 20px 0; margin: 0 auto;' align='center'>
                                                    *This email account is not monitored. Reply to <a href='mailto:{$settings->email}'>{$settings->email}</a> if you have any query.
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/investment'> View Our Available Plans </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table style='font-size: 12px; color: #2d2d2d; line-height: 22px; margin: 0px auto; width: 100%;' border='0' width='100%' cellspacing='0' cellpadding='0' align='center'>
                                        <tbody>
                                            <tr>
                                                <td lang='en' style='padding: 0px;' align='center'>© {$year} Nexus Insights Limited.</td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 15px 0px 25px;' align='center'>
                                                    <span><a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}'>Home</a>|</span>
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/about'>About</a>
                                                    <span>|</span>
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/investment'>Plans</a>
                                                    <br />
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/news'>News</a>
                                                    <span>|</span>
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/contact'>Contact</a>
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
HTML;

            // Email template for admin
            $admin_message = <<<HTML
<div style='font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; direction: ltr; background-color: #f3f2f1; margin: 0; padding: 0;'>
    <table class='main' border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#F3F2F1'>
        <tbody>
            <tr>
                <td class='outer-box' style='padding: 0 8px;' align='center' bgcolor='#F3F2F1'>
                    <table style='max-width: 600px; padding: 0 0 15px 0;' border='0' width='100%' cellspacing='0' cellpadding='0'>
                        <tbody>
                            <tr>
                                <td style='padding: 10px 0 13px 0;' align='left'>
                                    <a href='https://{$sweet_url}'>
                                        <img
                                            style='display: block;'
                                            src='https://{$sweet_url}/assets/images/logo-dark.png'
                                            alt='nexus-logo'
                                            width='300'
                                            height='60'
                                            border='0'
                                        />
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class='width-600' style='max-width: 600px;' border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF'>
                        <tbody>
                            <tr>
                                <td class='content-box' style='padding-bottom: 24px !important;'>
                                    <table border='0' width='100%' cellspacing='0' cellpadding='0'>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table border='0' width='100%' cellspacing='0' cellpadding='0'>
                                                        <tbody>
                                                            <tr>
                                                                <td style='padding: 16px 10px 0;'>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                        <span style='font-size: 12pt; font-family: arial black, sans-serif; color: #000000;'>
                                                                            <strong>Dear Admin,</strong>
                                                                        </span>
                                                                    </p>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'> </p>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                        <span style='color: #000000;'>
                                                                            An investment has been completed with the following details:
                                                                            <br /><br />
                                                                            <strong>User:</strong> {$investor_name}<br />
                                                                            <strong>Email:</strong> {$investor_email}<br />
                                                                            <strong>Plan:</strong> {$plan_name}<br />
                                                                            <strong>Capital:</strong> \${$capital}<br />
                                                                            <strong>Returns Credited:</strong> \${$amount}<br /><br />
                                                                            Please log in to the admin panel to review this investment.
                                                                        </span>
                                                                    </p>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'> </p>
                                                                    <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                        <span style='color: #000000;'>
                                                                            Do note that Nexus Insights will not give you any other wallet address apart from the one shown on the website.
                                                                        </span>
                                                                        <br /><br />
                                                                        <span style='color: #000000;'>
                                                                            To report fraudulent activities, contact
                                                                            <strong><a style='color: #000000;' href='mailto:{$settings->email2}'>support@nexusinsights.it.com</a></strong>
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
                            <tr>
                                <td> </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style='max-width: 550px; width: 100%;' border='0' cellspacing='0' cellpadding='0' bgcolor='#F2F2F2'>
                        <tbody>
                            <tr>
                                <td style='padding: 24px 4px; width: 100%;'>
                                    <table style='max-width: 424px;' border='0' cellspacing='0' cellpadding='0' align='center'>
                                        <tbody>
                                            <tr>
                                                <td style='font-size: 12px; line-height: 16px; color: #4b4b4b; padding: 20px 0; margin: 0 auto;' align='center'>
                                                    *This email account is not monitored. Reply to <a href='mailto:{$settings->email2}'>{$settings->email2}</a> if you have any query.
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/investment'> View Our Available Plans </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table style='font-size: 12px; color: #2d2d2d; line-height: 22px; margin: 0px auto; width: 100%;' border='0' width='100%' cellspacing='0' cellpadding='0' align='center'>
                                        <tbody>
                                            <tr>
                                                <td lang='en' style='padding: 0px;' align='center'>© {$year} Nexus Insights.</td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 15px 0px 25px;' align='center'>
                                                    <span><a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}'>Home</a>|</span>
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/about'>About</a>
                                                    <span>|</span>
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/investment'>Plans</a>
                                                    <br />
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/news'>News</a>
                                                    <span>|</span>
                                                    <a style='text-decoration: underline; color: #085ff7;' href='https://{$sweet_url}/contact'>Contact</a>
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
HTML;

            // Send email to user
            require '../vendor/autoload.php';
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = $smtpConfig['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtpConfig['username'];
                $mail->Password = $smtpConfig['password'];
                $mail->SMTPSecure = $smtpConfig['secure'];
                $mail->Port = $smtpConfig['port'];

                // Recipients
                $mail->setFrom($smtpConfig['fromEmail'], $smtpConfig['fromName']);
                $mail->addAddress($investor_email, $investor_name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Investment Completion Confirmation";
                $mail->Body = $user_message;

                $mail->send();
                $_SESSION['success'] = 'Your investment has been successfully completed. Check your email for confirmation.';
            } catch (Exception $e) {
                error_log("PHPMailer error in investment completion: " . $e->getMessage(), 3, '../debug.log');
                $_SESSION['success'] = 'Your investment has been completed. A confirmation email could not be sent.';
            }

            // Send email to admin
            $adminMail = new PHPMailer(true);
            try {
                // Server settings
                $adminMail->isSMTP();
                $adminMail->Host = $smtpConfig['host'];
                $adminMail->SMTPAuth = true;
                $adminMail->Username = $smtpConfig['username'];
                $adminMail->Password = $smtpConfig['password'];
                $adminMail->SMTPSecure = $smtpConfig['secure'];
                $adminMail->Port = $smtpConfig['port'];

                // Recipients
                $adminMail->setFrom($smtpConfig['fromEmail'], $smtpConfig['fromName']);
                $adminMail->addAddress($settings->email2, 'Admin'); // Using email2 for admin notifications

                // Content
                $adminMail->isHTML(true);
                $adminMail->Subject = "Investment Completed - {$settings->siteTitle}";
                $adminMail->Body = $admin_message;

                $adminMail->send();
            } catch (Exception $e) {
                error_log("PHPMailer error in admin notification: " . $e->getMessage(), 3, '../debug.log');
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            $_SESSION['error'] = 'Database error occurred: ' . $e->getMessage();
            error_log("investment-complete.php: Database error - " . $e->getMessage(), 3, '../debug.log');
        }
    } else {
        $_SESSION['error'] = 'Invalid request. Please ensure all fields are filled.';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error occurred: ' . $e->getMessage();
    error_log("investment-complete.php: User fetch error - " . $e->getMessage(), 3, '../debug.log');
} finally {
    $pdo->close();
}

header('location: dashboard.php');
exit;
?>
