<?php
include 'includes/session.php';
include 'includes/slugify.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Debugging: Log input values
    error_log("investments_edit.php: id=$id, status=$status, time=" . date('Y-m-d H:i:s'), 3, 'debug.log');

    $conn = $pdo->open();

    try {
        // Fetch investment details
        $stmt = $conn->prepare("
            SELECT i.*, i.status AS invest_status, COALESCE(ip.name, 'Unknown Plan') AS plan_name, u.id AS user_id, u.email, u.full_name 
            FROM investment i 
            LEFT JOIN investment_plans ip ON ip.id = i.invest_plan_id 
            LEFT JOIN users u ON u.id = i.user_id 
            WHERE i.invest_id = :id
        ");
        $stmt->execute(['id' => $id]);
        $investment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$investment) {
            throw new Exception('Investment not found.');
        }

        $user_id = $investment['user_id'];
        $capital = $investment['capital'];
        $returns = $investment['returns'];
        $plan_name = $investment['plan_name'];
        $investor_email = $investment['email'];
        $investor_name = $investment['full_name'];
        $current_status = $investment['invest_status'];
        $act_time = date('Y-m-d h:i A');
        $completion_date = date('Y-m-d');

        // Debugging: Log investment details
        error_log("investments_edit.php: user_id=$user_id, capital=$capital, returns=$returns, plan_name=$plan_name, email=$investor_email, current_status=$current_status", 3, 'debug.log');

        // Begin transaction
        $conn->beginTransaction();

        // Update investment status
        $stmt = $conn->prepare("UPDATE investment SET status = :status WHERE invest_id = :id");
        $stmt->execute(['status' => $status, 'id' => $id]);

        // Only process balance, remark, activity, and emails for 'completed' or 'cancelled'
        if ($status === 'completed' || $status === 'cancelled') {
            // Prevent duplicate credits
            if ($current_status === $status) {
                throw new Exception("Investment is already $status.");
            }

            // Get user's current balance (sum-based method)
            $stmt = $conn->prepare("
                SELECT (
                    COALESCE(SUM(CASE WHEN type = 1 THEN amount ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN type = 2 THEN amount ELSE 0 END), 0)
                ) AS balance
                FROM transaction
                WHERE user_id = :user_id
            ");
            $stmt->execute(['user_id' => $user_id]);
            $current_balance = floatval($stmt->fetchColumn() ?: 0);

            // Debugging: Log current balance
            error_log("investments_edit.php: current_balance=$current_balance", 3, 'debug.log');

            if ($status === 'completed') {
                // Credit returns
                $amount = $returns;
                $new_balance = $current_balance + $amount;
                $transaction_remark = "Received return for $plan_name";
                $activity_message = "Investment Completed";
                $activity_category = $plan_name;

                // Insert transaction with remark and status
                $stmt = $conn->prepare("
                    INSERT INTO transaction (user_id, amount, type, balance, trans_date, remark, status) 
                    VALUES (:user_id, :amount, :type, :balance, NOW(), :remark, 'completed')
                ");
                $stmt->execute([
                    'user_id' => $user_id,
                    'amount' => $amount,
                    'type' => 1, // Credit
                    'balance' => $new_balance,
                    'remark' => $transaction_remark
                ]);

                // Log activity
                $stmt = $conn->prepare("
                    INSERT INTO activity (user_id, message, category, date_sent) 
                    VALUES (:user_id, :message, :category, :date_sent)
                ");
                $stmt->execute([
                    'user_id' => $user_id,
                    'message' => $activity_message,
                    'category' => $activity_category,
                    'date_sent' => $act_time
                ]);

                // Prepare email content for user
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
                                                                                            <span style='font-size: 12pt; font-family: arial black, sans-serif; color: #000000;'><strong>Dear {$investor_name},</strong></span>
                                                                                        </p>
                                                                                        <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>&nbsp;</p>
                                                                                        <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>
                                                                                            <span style='color: #000000;'>
                                                                                                Your investment of $$capital for the $plan_name has been successfully completed on $completion_date. $transaction_remark, and you can view the details in your Nexus Insights account.
                                                                                            </span>
                                                                                        </p>
                                                                                        <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>&nbsp;</p>
                                                                                        <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>
                                                                                            <span style='color: #000000;'>
                                                                                                For any inquiries, please contact <strong><a style='color: #000000;' href='mailto:info@nexusinsights.eu'>info@nexusinsights.eu</a></strong>.
                                                                25 </span>
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

                // Notify admin
                $admin_msg = "{$investor_name} has completed an investment of $$capital for $plan_name. Login to Admin for details.";
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
                } catch (Exception $e) {
                    error_log("investments_edit.php: Email error - " . $e->getMessage(), 3, 'debug.log');
                }
            } elseif ($status === 'cancelled') {
                // Credit capital
                $amount = $capital;
                $new_balance = $current_balance + $amount;
                $transaction_remark = "Investment in $plan_name Cancelled";
                $activity_message = "Investment Cancelled";
                $activity_category = $plan_name;

                // Insert transaction with remark and status
                $stmt = $conn->prepare("
                    INSERT INTO transaction (user_id, amount, type, balance, trans_date, remark, status) 
                    VALUES (:user_id, :amount, :type, :balance, NOW(), :remark, 'completed')
                ");
                $stmt->execute([
                    'user_id' => $user_id,
                    'amount' => $amount,
                    'type' => 1, // Credit
                    'balance' => $new_balance,
                    'remark' => $transaction_remark
                ]);

                // Log activity
                $stmt = $conn->prepare("
                    INSERT INTO activity (user_id, message, category, date_sent) 
                    VALUES (:user_id, :message, :category, :date_sent)
                ");
                $stmt->execute([
                    'user_id' => $user_id,
                    'message' => $activity_message,
                    'category' => $activity_category,
                    'date_sent' => $act_time
                ]);

                // Prepare email content for user
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
                                                                                            <span style='font-size: 12pt; font-family: arial black, sans-serif; color: #000000;'><strong>Dear {$investor_name},</strong></span>
                                                                                        </p>
                                                                                        <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>&nbsp;</p>
                                                                                        <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>
                                                                                            <span style='color: #000000;'>
                                                                                                Your investment of $$capital for the $plan_name has been cancelled on $completion_date. $transaction_remark, and $$amount has been refunded to your account. You can view the details in your Nexus Insights account.
                                                                                            </span>
                                                                                        </p>
                                                                                        <p align='center' style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;'>&nbsp;</p>
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

                // Notify admin
                $admin_msg = "{$investor_name} has cancelled an investment of $$capital for $plan_name. Login to Admin for details.";
                $admin_msg = wordwrap($admin_msg, 70);
                mail($settings->email, "Investment Cancelled", $admin_msg);

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
                    $mail->Subject = $settings->siteTitle . " Investment Cancellation Confirmation";
                    $mail->Body = $email_message;
                    $mail->send();
                } catch (Exception $e) {
                    error_log("investments_edit.php: Email error - " . $e->getMessage(), 3, 'debug.log');
                }
            }

            // Debugging: Log transaction and activity details
            error_log("investments_edit.php: status=$status, amount=$amount, new_balance=$new_balance, transaction_remark=$transaction_remark, activity_message=$activity_message, activity_category=$activity_category", 3, 'debug.log');
        }

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = 'Investment status updated successfully';
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $_SESSION['error'] = 'Error updating investment: ' . $e->getMessage();
        error_log("investments_edit.php: Error - " . $e->getMessage(), 3, 'debug.log');
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Please select a status to update.';
}

// Debugging: Log session status
error_log("investments_edit.php: SESSION success=" . ($_SESSION['success'] ?? 'none') . ", error=" . ($_SESSION['error'] ?? 'none'), 3, 'debug.log');

header('location: investments.php');
?>
