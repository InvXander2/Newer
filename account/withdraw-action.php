<?php
	include('../inc/config.php');

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	include 'inc/session.php';
	include '../admin/includes/slugify.php';

	$user_id = $_SESSION['user'];

	$stmt = $conn->prepare("SELECT * FROM users WHERE id=:user_id");
	$stmt->execute(['user_id'=>$user_id]);
	$row = $stmt->fetch();
	$investor_email = $row['email'];
	$investor_name = $row['full_name'];

	if(isset($_POST['complete'])){
		$withdrawal_amount = $_POST['withdrawal_amount'];
		$payment_mode = $_POST['payment_mode'];
		$payment_info = $_POST['payment_info'];
		$status = 'pending';

		$conn = $pdo->open();
		$trans_date = date('Y-m-d');
		$act_time = date('Y-m-d h:i A');

		try{
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
				'message' => 'You made a withdrawal request of $'.$withdrawal_amount,
				'category' => 'Withdrawal Request',
				'start_date' => $act_time
			]);

			// Email message to user
			$message = "<div id='_rc_sig'>
			... (unchanged HTML email content from your original code)
			</div>";

			// Notify Admin
			$msg = $investor_name." just requested a withdrawal , Login Admin";
			$msg = wordwrap($msg,70);
			mail($settings->email,"New Withdrawal Request",$msg);

			// Send email to user
			require '../vendor/autoload.php';

			$mail = new PHPMailer(true);                             
			try {
				$mail->IsSMTP();
				$mail->Host = $sweet_url;
				$mail->Port = '465';
				$mail->SMTPAuth = true;
				$mail->Username = 'noreply@'.$sweet_url;
				$mail->Password = $noreply_password;
				$mail->SMTPSecure = 'ssl';
				$mail->From = 'noreply@'.$sweet_url;
				$mail->FromName = $settings->siteTitle;
				$mail->AddAddress($investor_email);
				$mail->IsHTML(true);
				$mail->Subject = $settings->siteTitle." Withdrawal Request";
				$mail->Body = $message;

				$mail->send();

				unset($_SESSION['full_name']);
				unset($_SESSION['username']);
				unset($_SESSION['email']);

				$_SESSION['success'] = 'Your request has been sent and you will be contacted on how to proceed shortly';

			} catch (Exception $e) {
				$_SESSION['success'] = 'Your request has been sent. Please proceed to pay and invest';
			}

		} catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();
	} else {
		$_SESSION['error'] = 'Make Sure all Fields are filled';
	}

	header('location: withdrawals.php');
?>
