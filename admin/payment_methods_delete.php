<?php
	include 'includes/session.php';

	if(isset($_POST['delete'])){
		$id = $_POST['id'];
		
		$conn = $pdo->open();

		try{
			$stmt = $conn->prepare("DELETE FROM payment_methods WHERE id=:id");
			$stmt->execute(['id'=>$id]);

			$_SESSION['success'] = 'Payment method deleted successfully';
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		}

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Select payment method to delete first';
	}

	header('location: payment_methods.php');
?>
