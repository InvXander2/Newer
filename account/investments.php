<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
	<?php include 'includes/navbar.php'; ?>

	<div class="content-wrapper">
		<div class="container">
			<section class="content-header">
				<h1 class="text-center">Available Investment Plans</h1>
			</section>

			<section class="content">
				<?php
				if(isset($_SESSION['error'])){
					echo "
						<div class='alert alert-danger alert-dismissible'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
							<h4><i class='icon fa fa-warning'></i> Error!</h4>
							".$_SESSION['error']."
						</div>
					";
					unset($_SESSION['error']);
				}
				if(isset($_SESSION['success'])){
					echo "
						<div class='alert alert-success alert-dismissible'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
							<h4><i class='icon fa fa-check'></i> Success!</h4>
							".$_SESSION['success']."
						</div>
					";
					unset($_SESSION['success']);
				}
				?>

				<div class="row">
					<?php
					$conn = $pdo->open();
					try{
						$stmt = $conn->prepare("SELECT * FROM investment_plans");
						$stmt->execute();
						foreach($stmt as $row){
							echo "
							<div class='col-md-4'>
								<div class='box box-solid'>
									<div class='box-body'>
										<h4>".$row['name']."</h4>
										<p><strong>Duration:</strong> ".$row['duration']."</p>
										<p><strong>Rate:</strong> ".$row['rate']."%</p>
										<p><strong>Minimum:</strong> $".$row['min_invest']."</p>
										<p><strong>Maximum:</strong> $".$row['max_invest']."</p>
										<button class='btn btn-primary invest-btn' data-id='".$row['id']."' data-toggle='modal' data-target='#investmentModal'>Invest</button>
									</div>
								</div>
							</div>
							";
						}
					}
					catch(PDOException $e){
						echo "There was some problem: " . $e->getMessage();
					}
					$pdo->close();
					?>
				</div>
			</section>
		</div>
	</div>

	<?php include 'includes/footer.php'; ?>
	<?php include 'includes/investment_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(document).ready(function(){
	$('.invest-btn').click(function(e){
		e.preventDefault();
		var planId = $(this).data('id');
		$('#planSelect').val(planId);
	});
});
</script>
</body>
</html>
