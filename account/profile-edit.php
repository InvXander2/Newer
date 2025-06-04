<?php
include('../inc/config.php');
include('../admin/includes/format.php');
include('../../inc/session.php');

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit();
}

$id = $_SESSION['user'];

// Open database connection
$conn = $pdo->open();

try {
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql); // Use PDO's prepare() on $conn
    $stmt->execute(['id' => $id]);
    $row0 = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row0) {
        $_SESSION['error'] = "User not found.";
        header("location: dashboard.php");
        exit();
    }
} catch (PDOException $e) {
    // Log error and redirect
    error_log("Database error in profile-edit.php: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
    $_SESSION['error'] = "Database error occurred.";
    header("location: dashboard.php");
    exit();
} finally {
    // Close the database connection
    $pdo->close();
}

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
            if (isset($_SESSION['error'])) {
                echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success']) . "</div>";
                unset($_SESSION['success']);
            }
            ?>

            <form method="post" action="profile-edit-action.php" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-body">

                        <!-- Full Name -->
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($row0['full_name'] ?? '') ?>">
                        </div>

                        <!-- Username -->
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="uname" class="form-control" value="<?= htmlspecialchars($row0['uname'] ?? '') ?>">
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">Choose...</option>
                                <option value="Male" <?= ($row0['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($row0['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>

                        <!-- DOB -->
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($row0['dob'] ?? '') ?>">
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone_no" class="form-control" value="<?= htmlspecialchars($row0['phone_no'] ?? '') ?>">
                        </div>

                        <!-- Nationality -->
                        <div class="form-group">
                            <label>Nationality</label>
                            <select name="nationality" class="form-control">
                                <?php include('../inc/countries.php'); ?>
                                <script>
                                    document.querySelector('[name="nationality"]').value = <?= json_encode($row0['nationality'] ?? '') ?>;
                                </script>
                            </select>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($row0['address'] ?? '') ?>">
                        </div>

                        <!-- Profile Picture -->
                        <div class="form-group">
                            <label>Profile Picture</label><br>
                            <?php if (!empty($row0['photo'])): ?>
                                <img src="../images/users/<?= htmlspecialchars($row0['photo']) ?>" alt="Profile Picture" width="100" class="mb-2"><br>
                            <?php endif; ?>
                            <input type="file" name="photo" accept="image/*" class="form-control-file">
                        </div>

                        <!-- Submit -->
                        <div class="form-group mt-3">
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
