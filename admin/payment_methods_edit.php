<?php
	include '../inc/session.php';
	include 'includes/slugify.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$name = $_POST['name'];
		$wallet_address = $_POST['wallet_address'];
		$details = $_POST['details'];

		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("UPDATE payment_methods SET name=:name, wallet_address=:wallet_address, details=:details WHERE id=:id");
			$stmt->execute([
				'name' => $name, 
				'wallet_address' => $wallet_address, 
				'details' => $details, 
				'id' => $id
			]);
			$_SESSION['success'] = 'Payment method updated successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}
		
		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up edit payment method form first';
	}

	header('location: payment_methods.php');

?>
