<?php
include('../inc/config.php');
include('../admin/includes/format.php');
include('../admin/session.php');

$id = $_SESSION['user'];

if(!isset($_SESSION['user'])){
    header('location: ../login.php');
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conne->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row0 = $result->fetch_assoc();

$page_title = 'Edit Profile';

include('inc/head.php');
?>

<body class="dark-topbar">
<?php include('inc/sidebar.php'); ?>
<div class="page-wrapper">
    <?php include('inc/header.php'); ?>

    <div class="page-content">
        <div class="container-fluid">
            <h4 class="mb-4">Edit Profile</h4>

            <?php
            if(isset($_SESSION['error'])){
                echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
                unset($_SESSION['success']);
            }
            ?>

            <form method="post" action="profile-edit-action.php" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-body">

                        <!-- Full Name -->
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($row0['full_name']); ?>">
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">Choose...</option>
                                <option value="Male" <?php if($row0['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if($row0['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                            </select>
                        </div>

                        <!-- DOB -->
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo $row0['dob']; ?>">
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone_no" class="form-control" value="<?php echo htmlspecialchars($row0['phone_no']); ?>">
                        </div>

                        <!-- Nationality -->
                        <div class="form-group">
                            <label>Nationality</label>
                            <select name="nationality" class="form-control">
                                <?php include('../inc/countries.php'); ?>
                            </select>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($row0['address']); ?>">
                        </div>

                        <!-- Username -->
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="uname" class="form-control" value="<?php echo htmlspecialchars($row0['uname']); ?>">
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                        </div>

                        <!-- Submit -->
                        <div class="form-group">
                            <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include('inc/footer.php'); ?>
</div>

<?php include('inc/scripts.php'); ?>
</body>
</html>
