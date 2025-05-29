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
    $invest_id = $_POST['invest_id']; // Investment ID passed from form

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
        // Update investment status to completed
        $stmt = $conn->prepare("UPDATE investment SET status=:status, end_date=:completion_date WHERE invest_id=:invest_id AND user_id=:user_id");
        $stmt->execute(['status' => $status, 'completion_date' => $completion_date, 'invest_id' => $invest_id, 'user_id' => $user_id]);

        // Fetch investment details for email and activity log
        $capital = $investment['capital'];
        $invest_plan_id = $investment['invest_plan_id'];

        // Fetch plan name from investment_plans table
        $stmt = $conn->prepare("SELECT name FROM investment_plans WHERE id=:invest_plan_id");
        $stmt->execute(['invest_plan_id' => $invest_plan_id]);
        $plan = $stmt->fetch();
        $plan_name = $plan['name'] ?? 'Investment Plan'; // Fallback if plan name not found

        // Log activity
        $activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :start_date)");
        $activity->execute([
            'user_id' => $user_id,
            'message' => 'Your investment of $' . $capital . ' for ' . $plan_name . ' has been completed.',
            'category' => 'Investment Completion',
            'start_date' => $act_time
        ]);

        // Prepare email content
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
                                                                <a href='https://www.nexusinsights.eu'>
                                                                    <img
                                                                        class='m_4873022159957722792jecl-Icon-img CToWUd'
                                                                        style='display: block;'
                                                                        src='https://www.nexusinsights.eu/assets/images/logo-dark.png'
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
                                                                                                    <span style='font-size: 12pt; font-family: arial black, sans-serif; color: #000000;'> <strong>Dear " . $investor_name . ",</strong> </span>
                                                                                                </p>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'> </p>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                                                    <span style='color: #000000;'>
                                                                                                        Your investment of $" . $capital . " for the " . $plan_name . " has been successfully completed on " . $completion_date . ". You can view the details in your Nexus Insights account.
                                                                                                    </span>
                                                                                                </p>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'> </p>
                                                                                                <p style='font-size: 13px; line-height: 20px; color: #666666; margin: 0px; text-align: left;' align='center'>
                                                                                                    <span style='color: #000000;'>
                                                                                                        For any inquiries, please contact
                                                                                                        <strong><a style='color: #000000;' href='mailto:info@nexusinsights.eu'>info@nexusinsights.eu</a></strong>.
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
                                                <table style='max-width: 550px; height: 264px; width: 114.979%;' border='0' width='573' cellspacing='0' cellpadding='0' bgcolor='#F2F2F2'>
                                                    <tbody>
                                                        <tr>
                                                            <td style='padding: 24px 4px; width: 100%;'>
                                                                <table style='max-width: 424px;' border='0' cellspacing='0' cellpadding='0' align='center'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style='font-size: 12px; line-height: 16px; color: #4b4b4b; padding: 20px 0; margin: 0 auto;' align='center'>
                                                                                *This email account is not monitored. Reply to <a href='mailto:info@nexusinsights.eu'>info@nexusinsights.eu</a> if you have any query.
                                                                                <a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/investment'> View Our Available Plans </a>
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
                                                                                <span class='m_4873022159957722792j-5'><a style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu'>Home</a>|</span>
                                                                                <a class='m_4873022159957722792j-1' style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/about'>About</a>
                                                                                <span class='m_4873022159957722792j-5'>|</span>
                                                                                <a class='m_4873022159957722792j-2' style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/investment'>Plans</a> <br />
                                                                                <a class='m_4873022159957722792j-3' style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/news'> News </a>
                                                                                <span class='m_4873022159957722792j-5'>|</span>
                                                                                <a class='m_4873022159957722792j-4' style='text-decoration: underline; color: #085ff7;' href='https://www.nexusinsights.eu/contact'>Contact</a>
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
            </div>
        ";

        // Notify Admin
        $msg = $investor_name . " has completed an investment of $" . $capital . " for " . $plan_name . ". Login to Admin for details.";
        $msg = wordwrap($msg, 70);
        mail($settings->email, "Investment Completed", $msg);

        // Load PHPMailer
        require '../vendor/autoload.php';

        $mail = new PHPMailer(true);
        try {
            // Server settings
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
            $mail->Body = $message;

            $mail->send();

            unset($_SESSION['full_name']);
            unset($_SESSION['username']);
            unset($_SESSION['email']);

            $_SESSION['success'] = 'Your investment has been successfully completed. Check your email for confirmation.';
        } catch (Exception $e) {
            $_SESSION['success'] = 'Your investment has been completed. A confirmation email could not be sent.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Invalid request. Please ensure all fields are filled.';
}

header('location: dashboard.php');
?>
