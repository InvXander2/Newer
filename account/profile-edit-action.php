<?php
	include 'inc/session.php';
	include '../admin/includes/slugify.php';

	$user_id = $_SESSION['user'];

	if (isset($_POST['update'])) {
		$full_name = $_POST['full_name'];
		$uname = $_POST['uname'];
		$dob = $_POST['dob'];
		$nationality = $_POST['nationality'];
		$phone_no = $_POST['phone_no'];
		$address = $_POST['address'];
		$password = $_POST['password'];
		$photo = $_FILES['photo']['name'];

		$conn = $pdo->open();
		$act_time = date('Y-m-d h:i A');

		// Fetch existing user data to get the current photo if no new one is uploaded
		try {
			$stmt = $conn->prepare("SELECT photo FROM users WHERE id=:id");
			$stmt->execute(['id' => $user_id]);
			$user = $stmt->fetch();
		} catch (PDOException $e) {
			$_SESSION['error'] = 'Error fetching user: ' . $e->getMessage();
			header('location: profile.php');
			exit();
		}

		// Handle photo upload
		if (!empty($photo)) {
			move_uploaded_file($_FILES['photo']['tmp_name'], '../admin/images/' . $photo);
			$filename = $photo;
		} else {
			$filename = $user['photo'];
		}

		// Handle password (only update if provided)
		$fields = [
			'full_name' => $full_name,
			'uname' => $uname,
			'dob' => $dob,
			'nationality' => $nationality,
			'phone_no' => $phone_no,
			'address' => $address,
			'photo' => $filename,
			'id' => $user_id
		];

		$sql = "UPDATE users SET full_name=:full_name, uname=:uname, dob=:dob, nationality=:nationality, phone_no=:phone_no, address=:address, photo=:photo";

		if (!empty($password)) {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$sql .= ", password=:password";
			$fields['password'] = $hashed_password;
		}

		$sql .= " WHERE id=:id";

		try {
			$stmt = $conn->prepare($sql);
			$stmt->execute($fields);

			// Log activity
			$activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) VALUES (:user_id, :message, :category, :date_sent)");
			$activity->execute([
				'user_id' => $user_id,
				'message' => 'You updated your profile',
				'category' => 'Info',
				'date_sent' => $act_time
			]);

			$_SESSION['success'] = 'Profile updated successfully';

		} catch (PDOException $e) {
			$_SESSION['error'] = 'Database error: ' . $e->getMessage();
		}

		$pdo->close();
	} else {
		$_SESSION['error'] = 'Invalid form submission';
	}

	header('location: profile.php');
?>
