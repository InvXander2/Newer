<?php
	include 'inc/session.php';
	include '../admin/includes/slugify.php';

	$user_id = $_SESSION['user'];

	if (isset($_POST['update'])) {
		$full_name = $_POST['full_name'];
		$uname = $_POST['uname'];
		$gender = $_POST['gender'];
		$dob = $_POST['dob'];
		$nationality = $_POST['nationality'];
		$phone_no = $_POST['phone_no'];
		$address = $_POST['address'];
		$password = $_POST['password'];
		$photo = $_FILES['photo']['name'];

		$conn = $pdo->open();
		$act_time = date('Y-m-d h:i A');

		// Fetch existing photo
		try {
			$stmt = $conn->prepare("SELECT photo FROM users WHERE id=:id");
			$stmt->execute(['id' => $user_id]);
			$user = $stmt->fetch();
		} catch (PDOException $e) {
			$_SESSION['error'] = 'Error fetching user: ' . $e->getMessage();
			header('location: profile.php');
			exit();
		}

		// Photo upload
		$filename = !empty($photo) ? $photo : $user['photo'];
		if (!empty($photo)) {
			move_uploaded_file($_FILES['photo']['tmp_name'], '../admin/images/' . $photo);
		}

		// Prepare fields
		$fields = [
			'full_name' => $full_name,
			'uname' => $uname,
			'gender' => $gender,
			'dob' => $dob,
			'nationality' => $nationality,
			'phone_no' => $phone_no,
			'address' => $address,
			'photo' => $filename,
			'id' => $user_id
		];

		$sql = "UPDATE users SET 
			full_name=:full_name, 
			uname=:uname, 
			gender=:gender, 
			dob=:dob, 
			nationality=:nationality, 
			phone_no=:phone_no, 
			address=:address, 
			photo=:photo";

		// Update password if provided
		if (!empty($password)) {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$sql .= ", password=:password";
			$fields['password'] = $hashed_password;
		}

		$sql .= " WHERE id=:id";

		try {
			$stmt = $conn->prepare($sql);
			$stmt->execute($fields);

			// Activity log
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
