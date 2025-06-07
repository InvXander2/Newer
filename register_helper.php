<?php
include('init.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'inc/session.php';

if (isset($_POST['signup'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $referral = empty($_POST['referral']) ? 'nexusinsights' : $_POST['referral'];
    $type = 0;
    $status = 0;

    $_SESSION['full_name'] = $full_name;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;

    if ($password !== $repassword) {
        $_SESSION['error'] = 'Passwords did not match';
        header('location: register.php');
        exit;
    }

    $conn = $pdo->open();

    $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    if ($row['numrows'] > 0) {
        $_SESSION['error'] = 'Email already taken';
        header('location: register.php');
        $pdo->close();
        exit;
    }

    $now = date('Y-m-d');
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Generate activation code
    $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = substr(str_shuffle($set), 0, 12);

    try {
        $stmt = $conn->prepare("INSERT INTO users (email, password, full_name, uname, referral_code, activate_code, created_on, type, status) VALUES (:email, :password, :full_name, :username, :referral, :code, :now, :type, :status)");
        $stmt->execute([
            'email' => $email,
            'password' => $password_hashed,
            'full_name' => $full_name,
            'username' => $username,
            'referral' => $referral,
            'code' => $code,
            'now' => $now,
            'type' => $type,
            'status' => $status
        ]);
        $userid = $conn->lastInsertId();

        // Email template
        $message = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>{$settings->siteTitle}</title>
    <link rel='shortcut icon' href='favicon.ico'>
    <style type='text/css'>
        table[name='blk_permission'], table[name='blk_footer'] {display:none;}
    </style>
    <meta name='googlebot' content='noindex' />
    <meta name='robots' content='noindex, nofollow' />
    <link rel='stylesheet' href='/style/dhtmlwindow.css' type='text/css' />
    <script type='text/javascript' src='/script/dhtmlwindow.js'></script>
    <link rel='stylesheet' href='/style/modal.css' type='text/css' />
    <script type='text/javascript' src='/script/modal.js'></script>
    <script type='text/javascript'>
        function show_popup(popup_name, popup_url, popup_title, width, height) {
            var widthpx = width + 'px';
            var heightpx = height + 'px';
            emailwindow = dhtmlmodal.open(popup_name, 'iframe', popup_url, popup_title, 'width=' + widthpx + ',height=' + heightpx + ',center=1,resize=0,scrolling=1');
        }
        function show_modal(popup_name, popup_url, popup_title, width, height) {
            var widthpx = width + 'px';
            var heightpx = height + 'px';
            emailwindow = dhtmlmodal.open(popup_name, 'iframe', popup_url, popup_title, 'width=' + widthpx + ',height=' + heightpx + ',modal=1,center=1,resize=0,scrolling=1');
        }
        var popUpWin = 0;
        function popUpWindow(URLStr, PopUpName, width, height) {
            if (popUpWin) {
                if (!popUpWin.closed) popUpWin.close();
            }
            var left = (screen.width - width) / 2;
            var top = (screen.height - height) / 2;
            popUpWin = open(URLStr, PopUpName, 'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbar=0,resizable=0,copyhistory=yes,width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + '');
        }
    </script>
    <meta content='width=device-width, initial-scale=1.0' name='viewport'>
    <style type='text/css'>
        /* Add your existing CSS styles here (omitted for brevity) */
    </style>
</head>
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" style='height: 100% !important; margin: 0; padding: 0; width: 100% !important; min-width: 100%;'>
    <table width='100%' cellspacing='0' cellpadding='0' border='0' name='bmeMainBody' style='background-color: rgb(0, 0, 0);' bgcolor='#000000'>
        <tbody>
            <tr>
                <td width='100%' valign='top' align='center'>
                    <table cellspacing='0' cellpadding='0' border='0' name='bmeMainColumnParentTable'>
                        <tbody>
                            <tr>
                                <td name='bmeMainColumnParent' style='border: 0px none transparent; border-radius: 0px; border-collapse: separate;'>
                                    <table name='bmeMainColumn' class='bmeHolder bmeMainColumn' style='max-width: 600px; overflow: visible; border-radius: 0px; border-collapse: separate; border-spacing: 0px;' cellspacing='0' cellpadding='0' border='0' align='center'>
                                        <tbody>
                                            <tr>
                                                <td width='100%' class='blk_container bmeHolder' name='bmePreHeader' valign='top' align='center' style='color: rgb(102, 102, 102); border: 0px none transparent;' bgcolor=''></td>
                                            </tr>
                                            <tr>
                                                <td width='100%' class='bmeHolder' valign='top' align='center' name='bmeMainContentParent' style='border: 0px none rgb(102, 102, 102); border-radius: 0px; border-collapse: separate; border-spacing: 0px; overflow: hidden;'>
                                                    <table name='bmeMainContent' style='border-radius: 0px; border-collapse: separate; border-spacing: 0px; border: 0px none transparent;' width='100%' cellspacing='0' cellpadding='0' border='0' align='center'>
                                                        <tbody>
                                                            <tr>
                                                                <td width='100%' class='blk_container bmeHolder' name='bmeHeader' valign='top' align='center' style='color: rgb(56, 56, 56); border: 0px none transparent; background-color: rgb(0, 0, 0);' bgcolor='#000000'>
                                                                    <div id='dv_1' class='blk_wrapper'>
                                                                        <table width='600' cellspacing='0' cellpadding='0' border='0' class='blk' name='blk_text'>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>
                                                                                        <table cellpadding='0' cellspacing='0' border='0' width='100%' class='bmeContainerRow'>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td class='tdPart' valign='top' align='center'>
                                                                                                        <table cellspacing='0' cellpadding='0' border='0' width='600' name='tblText' style='float:left; background-color:transparent;' align='left' class='tblText'>
                                                                                                            <tbody>
                                                                                                                <tr>
                                                                                                                    <td valign='top' align='left' name='tblCell' style='padding: 20px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 400; color: rgb(56, 56, 56); text-align: left;' class='tblCell'>
                                                                                                                        <div style='line-height: 150%; text-align: center;'>
                                                                                                                            <span style='font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #939393; line-height: 150%;'></span>
                                                                                                                        </div>
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
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width='100%' class='blk_container bmeHolder bmeBody' name='bmeBody' valign='top' align='center' style='color: rgb(56, 56, 56); border: 0px none transparent; background-color: rgb(255, 255, 255);' bgcolor='#ffffff'>
                                                                    <div id='dv_11' class='blk_wrapper'>
                                                                        <table width='600' cellspacing='0' cellpadding='0' border='0' class='blk' name='blk_divider'>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class='tblCellMain' style='padding: 10px 20px;'>
                                                                                        <table class='tblLine' cellspacing='0' cellpadding='0' border='0' width='100%' style='border-top-width: 0px; border-top-style: none; min-width: 1px;'>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td><span></span></td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div id='dv_3' class='blk_wrapper'>
                                                                        <table width='600' cellspacing='0' cellpadding='0' border='0' class='blk' name='blk_image'>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>
                                                                                        <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td align='center' class='bmeImage' style='border-collapse: collapse; padding: 20px;'>
                                                                                                        <img src='https://{$sweet_url}/assets/images/logo/logo.png' style='display: block;' alt='' border='0'>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div id='dv_9' class='blk_wrapper'>
                                                                        <table width='600' cellspacing='0' cellpadding='0' border='0' class='blk' name='blk_divider'>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class='tblCellMain' style='padding: 20px 0px;'>
                                                                                        <table class='tblLine' cellspacing='0' cellpadding='0' border='0' width='100%' style='border-top-width: 1px; border-top-color: rgb(223, 223, 223); border-top-style: solid; min-width: 1px;'>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td><span></span></td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div id='dv_12' class='blk_wrapper'>
                                                                        <table width='600' cellspacing='0' cellpadding='0' border='0' class='blk' name='blk_card'>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class='bmeImageCard' align='center' style='padding-left:20px; padding-right:20px; padding-top:0px; padding-bottom:0px;'>
                                                                                        <table width='100%' cellspacing='0' cellpadding='0' border='0'>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td valign='top' class='bmeImageContainer' style='border-collapse: collapse; background-color: rgba(0, 0, 0, 0);' width='560'>
                                                                                                        <table cellspacing='0' cellpadding='0' border='0' width='100%'>
                                                                                                            <tbody>
                                                                                                                <tr>
                                                                                                                    <td valign='top' align='center' class='tdPart'>
                                                                                                                        <table cellspacing='0' cellpadding='0' border='0' class='bmeCaptionTable' style='float: left;' width='373' align='left'>
                                                                                                                            <tbody>
                                                                                                                                <tr>
                                                                                                                                    <td style='padding: 20px 0px 20px 20px; font-family: Arial, Helvetica, sans-serif; font-weight: normal; font-size: 14px; color: rgb(56, 56, 56); text-align: left;' name='tblCell' valign='top' align='left' class='tblCell'>
                                                                                                                                        <div style='line-height: 150%;'>
                                                                                                                                            <span style='font-size: 18px; font-family: Helvetica, Arial, sans-serif; color: #1e1e1e; line-height: 150%;'>
                                                                                                                                                <strong>Dear {$full_name} ({$username}),</strong>
                                                                                                                                            </span>
                                                                                                                                            <br><br>
                                                                                                                                            <span style='font-size: 14px; font-family: Helvetica, Arial, sans-serif; color: #4a4949; line-height: 150%;'>
                                                                                                                                                Thank you for signing up with us. Your new account is being provisioned and can be accessed once activated.
                                                                                                                                            </span>
                                                                                                                                            <span style='font-size: 18px; font-family: Helvetica, Arial, sans-serif; color: #d60000; line-height: 150%;'>
                                                                                                                                                <em><strong>Your account details:</strong></em>
                                                                                                                                            </span>
                                                                                                                                            <br>
                                                                                                                                            <span style='font-size: 14px; font-family: Helvetica, Arial, sans-serif; color: #929292; line-height: 150%;'>
                                                                                                                                                <strong>Email Address:</strong> {$email}
                                                                                                                                            </span>
                                                                                                                                            <br><br>
                                                                                                                                            <span style='font-size: 14px; font-family: Helvetica, Arial, sans-serif; color: #929292; line-height: 150%;'>
                                                                                                                                                Please click the link below to activate your account.
                                                                                                                                            </span>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                            </tbody>
                                                                                                                        </table>
                                                                                                                    </td>
                                                                                                                    <td valign='top' align='center' class='tdPart'>
                                                                                                                        <table cellspacing='0' cellpadding='0' border='0' class='bmeImageTable' style='float: right; height: 222px;' align='right' dimension='30%' width='187' height='222'>
                                                                                                                            <tbody>
                                                                                                                                <tr>
                                                                                                                                    <td name='bmeImgHolder' style='padding:20px;' align='left' valign='top' height='182'>
                                                                                                                                        <img src='https://{$sweet_url}/assets/images/gallery/6.jpg' width='147' style='max-width: 250px; display: block;' alt='' border='0'>
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
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div id='dv_13' class='blk_wrapper'>
                                                                        <table width='600' cellspacing='0' cellpadding='0' border='0' class='blk' name='blk_button'>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td width='40'></td>
                                                                                    <td align='center'>
                                                                                        <table class='tblContainer' cellspacing='0' cellpadding='0' border='0' width='100%'>
                                                                                            <tr><td height='0'></td></tr>
                                                                                            <tr>
                                                                                                <td align='left'>
                                                                                                    <table cellspacing='0' cellpadding='0' border='0' class='bmeButton' align='left'>
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <td style='border-radius: 20px; border: 0px none transparent; text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: 14px; padding: 10px 20px; font-weight: bold; background-color: rgb(214, 60, 30);'>
                                                                                                                    <span style='font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: rgb(255, 255, 255);'>
                                                                                                                        <a style='color:#FFFFFF; text-decoration:none;' href='https://{$sweet_url}/activate.php?code={$code}&user={$userid}'>Activate Account</a>
                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr><td height='0'></td></tr>
                                                                                        </table>
                                                                                    </td>
                                                                                    <td width='40'></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div style='text-align: left'>
                                                                        <br><br>
                                                                        <span style='font-size: 18px; font-family: Helvetica, Arial, sans-serif; color: #1e1e1e; line-height: 150%;'><strong></strong></span>
                                                                    </div>
                                                                    <div id='dv_14' class='blk_wrapper'>
                                                                        <table width='600' cellspacing='0' cellpadding='0' border='0' class='blk' name='blk_divider'>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class='tblCellMain' style='padding: 10px;'>
                                                                                        <table class='tblLine' cellspacing='0' cellpadding='0' border='0' width='100%'>
                                                                                            <tbody>
                                                                                                <tr><td><span></span></td></tr>
                                                                                            </tbody>
 </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width='100%' class='blk_container bmeHolder' name='bmePreFooter' valign='top' align='center' style='border: 0px none transparent; background-color: rgb(255, 255, 255);' bgcolor='#ffffff'></td>
                                                            </tr>
                                                            <tr>
                                                                <td width='100%' class='blk_container bmeHolder' name='bmeFooter' style='color: rgb(102, 102, 102); border: none; padding: 20px;'>
                                                                    <div id='dv_5' class='blk_wrapper'>
                                                                        <table width='600' cellspacing='0' cellpadding='0' border='0' class='blk' name='blk_permission'>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td name='tblCell' class='tblCell' style='padding:20px;'></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
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
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
HTML;

        // Notify Admin
        $msg = "New User Registered: {$email}, Login Admin";
        $msg = wordwrap($msg, 70);
        mail($settings->email2, "New User Alert", $msg);

        // Send email using PHPMailer
        require 'vendor/autoload.php';

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
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "{$settings->siteTitle} Sign Up";
            $mail->Body = $message;

            $mail->send();

            unset($_SESSION['full_name']);
            unset($_SESSION['username']);
            unset($_SESSION['email']);

            $_SESSION['success'] = 'Account created. Check your email to activate. To continue to navigate site, <a href="index.php">Click Here</a>';
            header('location: register.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['success'] = 'Account has been set up and will be activated shortly. An email will be sent to you with your login details. To continue to navigate site, <a href="index.php">Click Here</a>';
            header('location: register.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['success'] = $e->getMessage();
        header('location: register.php');
        exit();
    } finally {
        $pdo->close();
    }
} else {
    $_SESSION['error'] = 'Fill up signup form first';
    header('location: register.php');
    exit();
}
?>
