<?php
include('../inc/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'inc/session.php';
include '../admin/includes/slugify.php';

// Add $page_name to prevent errors if scripts.php is included
$page_name = 'Deposits';

$user_id = $_SESSION['user'];

$conn = $pdo->open();

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $row = $stmt->fetch();

    if (!$row) {
        $_SESSION['error'] = 'User not found';
        header('location: deposits.php');
        exit;
    }

    $investor_email = $row['email'];
    $investor_name = $row['full_name'];

    if (
        isset($_POST['complete']) &&
        !empty($_POST['deposit_amount']) &&
        !empty($_POST['payment_mode'])
    ) {
        $deposit_amount = floatval($_POST['deposit_amount']); // Ensure amount is a number
        $payment_mode = $_POST['payment_mode'];
        $status = 'pending';

        // Validate deposit amount
        if ($deposit_amount <= 0) {
            $_SESSION['error'] = 'Invalid deposit amount';
            header('location: deposits.php');
            exit;
        }

        $trans_date = date('Y-m-d h:i A'); // Consistent with other scripts
        $act_time = date('Y-m-d h:i A');

        try {
            // Insert deposit request
            $stmt = $conn->prepare("INSERT INTO request (user_id, trans_date, type, amount, status, payment_mode) VALUES (:user_id, :trans_date, :type, :amount, :status, :payment_mode)");
            $stmt->execute([
                'user_id' => $user_id,
                'trans_date' => $trans_date,
                'type' => 1, // Deposit type
                'amount' => $deposit_amount,
                'status' => $status,
                'payment_mode' => $payment_mode
            ]);

            // Log activity
            $activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :start_date)");
            $activity->execute([
                'user_id' => $user_id,
                'message' => 'You made a deposit request of $' . $deposit_amount,
                'category' => 'Deposit Request',
                'start_date' => $act_time
            ]);

            // Email template for user
            $wallet_address = 'bc1qpmhxkk9a3mt5caxtuqhc8ya2r3f082yc9gjyak'; // Consider storing in config.php or database
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
                                                                            Your request to deposit <strong>\${$deposit_amount}</strong> has been received. Please ensure to pay the requested amount to the wallet address provided below. Funds will be deposited to your Nexus Insights account upon confirmation.
                                                                            <br /><br />
                                                                            <strong>Nexus Insights Limited BTC Wallet Address:</strong><br />
                                                                            <span style='color: #000080; font-weight: bold;'>{$wallet_address}</span>
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
                                    <table style='font-size: 12px; color: #2d2d2d; line-height: 22px; margin: 0px auto; width: 100%;' border='0' width='100%' cellspacing='0' cellpadding='0' align='middle'>
                                        <tbody>
                                            <tr>
                                                <td lang='en' style='padding: 0px;' align='middle'>© {$year} Nexus Insights Limited.</td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 15px 0px 25px;' align='middle'>
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
                                                                            A new deposit request has been submitted with the following details:
                                                                            <br /><br />
                                                                            <strong>User:</strong> {$investor_name}<br />
                                                                            <strong>Email:</strong> {$investor_email}<br />
                                                                            <strong>Amount:</strong> \${$deposit_amount}<br />
                                                                            <strong>Payment Mode:</strong> {$payment_mode}<br /><br />
                                                                            Please log in to the admin panel to review this request.
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
                                    <table style='font-size: 12px; color: #2d2d2d; line-height: 22px; margin: 0px auto; width: 100%;' border='0' width='100%' cellspacing='0' cellpadding='0' align='middle'>
                                        <tbody>
                                            <tr>
                                                <td lang='en' style='padding: 0px;' align='middle'>© {$year} Nexus Insights Limited.</td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 15px 0px 25px;' align='middle'>
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
                $mail->Subject = "{$settings->siteTitle} Deposit Request";
                $mail->Body = $user_message;

                $mail->send();
                $_SESSION['success'] = 'Your request has been sent. Please proceed to pay and invest';
            } catch (Exception $e) {
                error_log("PHPMailer error in deposit request: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
                $_SESSION['success'] = 'Your request has been sent. Please proceed to pay and invest';
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
                $adminMail->Subject = "New Deposit Request - {$settings->siteTitle}";
                $adminMail->Body = $admin_message;

                $adminMail->send();
            } catch (Exception $e) {
                error_log("PHPMailer error in admin notification: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Database error occurred: ' . $e->getMessage();
            error_log("Database error in deposit request: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
        }
    } else {
        $_SESSION['error'] = 'Make sure all fields are filled';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error occurred: ' . $e->getMessage();
    error_log("Database error in user fetch: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
} finally {
    $pdo->close();
}

header('location: deposits.php');
exit;
?>
