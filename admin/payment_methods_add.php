<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	if(isset($_POST['add'])){
		$name = $_POST['name'];
		$wallet_address = $_POST['wallet_address'];
		$details = $_POST['details'];
		$photo = '';

		if(isset($_FILES['photo']) && $_FILES['photo']['name'] != ''){
			$ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
			$filename = uniqid() . '.' . $ext;
			move_uploaded_file($_FILES['photo']['tmp_name'], 'images/'.$filename);
			$photo = 'images/'.$filename;
		}

		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM payment_methods WHERE name=:name");
		$stmt->execute(['name'=>$name]);
		$row = $stmt->fetch();

		if($row['numrows'] > 0){
			$_SESSION['error'] = 'Payment method already exists';
		}
		else{
			try{
				$stmt = $conn->prepare("INSERT INTO payment_methods (name, wallet_address, details, photo) VALUES (:name, :wallet_address, :details, :photo)");
				$stmt->execute([
					'name'=>$name, 
					'wallet_address'=>$wallet_address, 
					'details'=>$details, 
					'photo'=>$photo
				]);
				$_SESSION['success'] = 'Payment method added successfully';
			}
			catch(PDOException $e){
				$_SESSION['error'] = $e->getMessage();
			}
		}

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up payment method form first';
	}

	header('location: payment_methods.php');
?>
