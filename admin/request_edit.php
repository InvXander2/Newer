<?php
include('../inc/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'includes/session.php';
include 'includes/slugify.php';

$errors = [];
$successes = [];

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $request = $conn->prepare("SELECT * FROM request WHERE request_id=:id");
    $request->execute(['id' => $id]);
    $request_info = $request->fetch();
    $user_id = $request_info['user_id'];
    $request_type = $request_info['type'];
    $amount = $request_info['amount'];

    $user = $conn->prepare("SELECT * FROM users WHERE id=:user_id");
    $user->execute(['user_id' => $user_id]);
    $user_info = $user->fetch();
    $investor_name = $user_info['full_name'];
    $investor_email = $user_info['email'];

    if ($request_type == 1) {
        $trans_type = 'deposit';
    } else {
        $trans_type = 'withdraw';
    }

    $conn = $pdo->open();

    $trans_date = date('Y-m-d g:i A');
    $act_time = date('Y-m-d h:i A');

    try {
        $update_status = $conn->prepare("UPDATE request SET status=:status WHERE request_id=:id");
        $update_status->execute(['status' => $status, 'id' => $id]);

        if ($status == 'approved') {
            $get_balance = $conn->prepare("SELECT balance FROM transaction WHERE user_id = :user_id ORDER BY trans_id DESC LIMIT 1");
            $get_balance->execute(['user_id' => $user_id]);
            $wallet_balance = $get_balance->fetch();
            $receiver_balance = $wallet_balance["balance"];
            $trans_id = NULL;

            if ($request_type == 1) {
                $remarks = 'Amount of $' . $amount . ' was deposited successfully';
                $balance = $receiver_balance + $amount;
                $statement = 'Your request to deposit $' . $amount . ' was approved. Funds have been deposited to your Nexus Insights account.<br/> Thank you for investing with us';

                try {
                    $stmt = $conn->prepare("INSERT INTO transaction (trans_id, user_id, trans_date, type, amount, remark, balance) VALUES (:trans_id, :user_id, :trans_date, :type, :amount, :remark, :balance)");
                    $stmt->execute(['trans_id' => $trans_id, 'user_id' => $user_id, 'trans_date' => $trans_date, 'type' => $request_type, 'amount' => $amount, 'remark' => $remarks, 'balance' => $balance]);

                    $activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :start_date)");
                    $activity->execute(['user_id' => $user_id, 'message' => $remarks, 'category' => 'Deposit', 'start_date' => $act_time]);
                } catch (PDOException $e) {
                    $errors[] = 'Database error (transaction): ' . $e->getMessage();
                    error_log("Database error in transaction: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
                }
            }

            if ($request_type == 2) {
                $remarks = 'Amount of $' . $amount . ' was withdrawn successfully';
                $balance = $receiver_balance - $amount;
                $statement = 'Your request to withdraw $' . $amount . ' out of your Nexus Insights account was approved. Funds have been deposited to your chosen payment option.<br/> Thank you for investing with us';

                try {
                    $stmt = $conn->prepare("INSERT INTO transaction (trans_id, user_id, trans_date, type, amount, remark, balance) VALUES (:trans_id, :user_id, :trans_date, :type, :amount, :remark, :balance)");
                    $stmt->execute(['trans_id' => $trans_id, 'user_id' => $user_id, 'trans_date' => $trans_date, 'type' => $request_type, 'amount' => $amount, 'remark' => $remarks, 'balance' => $balance]);

                    $activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :start_date)");
                    $activity->execute(['user_id' => $user_id, 'message' => $remarks, 'category' => 'Withdrawal', 'start_date' => $act_time]);
                } catch (PDOException $e) {
                    $errors[] = 'Database error (transaction): ' . $e->getMessage();
                    error_log("Database error in transaction: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
                }
            }
        } elseif ($status == 'cancelled') {
            $statement = 'Your request to ' . $trans_type . ' $' . $amount . ' was denied.<br/> If this was done in error, please contact support.<br/> Thank you for investing with us';
        }

        // Email template (unchanged, but update domain if needed)
        $message = "
            <div id='_rc_sig'>
                <div id=':or' class='ii gt'>
                    <div id=':oq' class='a3s aiL msg4873022159957722792'>
                        <div id='m_4873022159957722792body' class='m_4873022159957722792body' style='background-color: #f3f2f1; margin: 0; padding: 0;'>
                            <div style='font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; direction: ltr;'>
                                <table class='m_4873022159957722792main' border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#F3F2F1'>
                                    <tbody>
                                        <tr>
                                            <td class='m_4873022159957722792outer-box' style='padding: 0 8px;' align='center' bgcolor='#F3F2F1'>
                                                <table style='max-width: 600px; padding: 0 0 15px 0;' border='0' width='100%' cellspacing='0' cellpadding='0'>
                                                    <tbody>
                                                        <tr>
                                                            <td style='padding: 10px 0 13px 0;' align='left'>
                                                                <a href='https://www.nexusinsights.it.com'>
                                                                    <img
                                                                        class='m_4873022159957722792jecl-Icon-img CToWUd'
                                                                        style='display: block;'
                                                                        src='https://www.nexusinsights.it.com/assets/images/logo-dark.png'
                                                                        alt='prime-logo'
                                                                        width='300'
                                                                        height='60'
                                                                        border='0'
                                                                    />
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table class='m_4873022159957722792width-600' style='max-width: 600px;' border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF'>
                                                    <tbody>
                                                        <tr>
                                                            <td class='m_4873022159957722792content-box' style='padding-bottom: 24px !important;'>
                                                                <table border='0' width='100%' cellspacing='0' cellpadding='0'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <table border='0' width='100%' cellspacing='0' cellpadding='0'>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td style='padding: 16px 10px 0;'>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                                                    <span style='font-size: 12pt; font-family: arial black, sans-serif; color: #000000;'> <strong>Dear " . htmlspecialchars($investor_name) . ",</strong> </span>
                                                                                                </p>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>&nbsp;</p>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                                                    <span style='color: #000000;'>
                                                                                                        " . $statement . "
                                                                                                    </span>
                                                                                                </p>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>&nbsp;</p>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                                                    <span style='color: #000000;'> Do note that Nexus Insights will not give you any other wallet address apart from the one shown on the website. </span>
                                                                                                    <br />
                                                                                                    <br />
                                                                                                    <span style='color: #000000;'>
                                                                                                        To report fraudulent activities, contact
                                                                                                        <strong><a style='color: #000000;' href='mailto:support@nexusinsights.it.com'>fraud@nexusinsights.it.com</a></strong>
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
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table style='max-width: 550px; height: 264px; width: 114.979%;' border='0' width='573' cellspacing='0' cellpadding='0' bgcolor='#F2F2F2'>
                                                    <tbody>
                                                        <tr>
                                                            <td style='padding: 24px 4px; width: 100%;'>
                                                                <table style='max-width: 424px;' border='0' cellspacing='0' cellpadding='0' align='center'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style='font-size: 12px; line-height: 16px; color: #4b4b4b; padding: 20px 0; margin: 0 auto;' align='center'>
                                                                                *This email account is not monitored. Reply to <a href='mailto:info@nexusinsights.it.com'>info@nexusinsights.it.com</a> if you have any query.
                                                                                <a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.it.com/investment'> View Our Available Plans </a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table style='font-size: 12px; color: #2d2d2d; line-height: 22px; margin: 0px auto; height: 63px; width: 69.7305%;' border='0' width='100%' cellspacing='0' cellpadding='0' align='center'>
                                                                    <tbody>
                                                                        <tr style='height: 43px;'>
                                                                            <td lang='en' style='padding: 0px; height: 43px;' align='center'>© 2021 Nexus Insights Limited.</td>
                                                                        </tr>
                                                                        <tr style='height: 10px;'>
                                                                            <td class='m_4873022159957722792j-6' style='padding: 15px 0px 25px; height: 10px;' align='center'>
                                                                                <span class='m_4873022159957722792j-5'><a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.it.com'>Home</a>|</span>
                                                                                <a class='m_4873022159957722792j-1' style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.it.com/about'>About</a>
                                                                                <span class='m_4873022159957722792j-5'>|</span>
                                                                                <a class='m_4873022159957722792j-2' style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.it.com/investment'>Plans</a> <br />
                                                                                <a class='m_4873022159957722792j-3' style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.it.com/news'> News </a>
                                                                                <span class='m_4873022159957722792j-5'>|</span>
                                                                                <a class='m_4873022159957722792j-4' style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.it.com/contact'>Contact</a>
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
                        </div>
                    </div>
                </div>
            ";

        // Load PHPMailer
        require '../vendor/autoload.php';

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->Debugoutput = function ($str, $level) {
                error_log("PHPMailer Debug [$level]: $str", 3, __DIR__ . "/error_log.txt");
            };
            $mail->isSMTP();
            $mail->Host = $smtpConfig['host']; // Use config.php host (update to correct host, e.g., smtp.zoho.com)
            $mail->SMTPAuth = true;
            $mail->Username = $smtpConfig['username']; // info@nexusinsights.it.com
            $mail->Password = $smtpConfig['password']; // Update to real password
            $mail->SMTPSecure = $smtpConfig['secure']; // ssl
            $mail->Port = $smtpConfig['port']; // 465
            $mail->setFrom($smtpConfig['fromEmail'], $smtpConfig['fromName']);
            $mail->addAddress($investor_email, $investor_name);
            $mail->isHTML(true);
            $mail->Subject = $settings->siteTitle . ' Request Verdict';
            $mail->Body = $message;
            $mail->send();
            $successes[] = 'Status updated successfully and email sent to the user';
        } catch (Exception $e) {
            $errors[] = 'Status updated successfully, but failed to send email: ' . $e->getMessage();
            error_log("PHPMailer error in request verdict: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
        }

        resultBlock($errors, $successes);
        if (count($errors) > 0) {
            $_SESSION['error'] = implode('; ', $errors);
        } else {
            $_SESSION['success'] = implode('; ', $successes);
        }

        unset($_SESSION['full_name']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);

    } catch (PDOException $e) {
        $errors[] = 'Database error: ' . $e->getMessage();
        error_log("Database error in request update: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
        resultBlock($errors, $successes);
        $_SESSION['error'] = implode('; ', $errors);
    }

    $pdo->close();
} else {
    $errors[] = 'Make sure all fields are filled';
    resultBlock($errors, $successes);
    $_SESSION['error'] = implode('; ', $errors);
}

if ($request_type == 1) {
    header('location: deposits.php');
} else {
    header('location: withdrawals.php');
}
?>
