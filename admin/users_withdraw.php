<?php
	include 'includes/session.php';

	if(isset($_POST['withdraw'])) {
		$id = $_POST['id'];
		$amt = $_POST['amount'];

		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE id=:id");
		$stmt->execute(['id'=>$id]);
		$row = $stmt->fetch();

		if($row['numrows'] > 0) {
			$stmt = $conn->prepare("SELECT balance FROM transaction WHERE user_id = :id ORDER BY trans_id DESC LIMIT 1");
			$stmt->execute(['id'=>$id]);
			$row2 = $stmt->fetch();
			$receiver_balance = $row2['balance'] ?? 0; // Default to 0 if no transactions exist

			if($receiver_balance >= $amt) {
				$trans_id = NULL;
				$trans_date = date('Y-m-d g:i A');
				$remarks = 'Amount of '.$amt.' was withdrawn successfully';
				$type = '2'; // Assuming '2' indicates withdrawal
				$balance = $receiver_balance - $amt;

				$act_time = date('Y-m-d h:i A');

				try {
					$stmt = $conn->prepare("INSERT INTO transaction (trans_id, user_id, trans_date, type, amount, remark, balance) VALUES (:trans_id, :user_id, :trans_date, :type, :amount, :remark, :balance)");
					$stmt->execute(['trans_id'=>$trans_id, 'user_id'=>$id, 'trans_date'=>$trans_date, 'type'=>$type, 'amount'=>$amt, 'remark'=>$remarks, 'balance'=>$balance]);

					$activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :start_date)");
					$activity->execute(['user_id'=>$id, 'message'=>$remarks, 'category'=>'Withdrawal', 'start_date'=>$act_time]);

					$_SESSION['success'] = 'Amount withdrawn successfully';
				}
				catch(PDOException $e) {
					$_SESSION['error'] = $e->getMessage();
				}
			}
			else {
				$_SESSION['error'] = 'Insufficient balance for withdrawal';
			}
		}
		else {
			$_SESSION['error'] = 'User not found';
		}

		$pdo->close();
	}
	else {
		$_SESSION['error'] = 'Fill up withdrawal form first';
	}

	header('location: users.php');
?>
