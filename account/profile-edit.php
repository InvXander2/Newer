<?php
    include('../inc/config.php');
    include('../admin/includes/format.php');
    include '../admin/session.php';

    $page_name = 'Profile';
    $page_parent = '';
    $page_title = 'Welcome to the Official Website of '.$settings->siteTitle;
    $page_description = $settings->siteTitle.' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining.';

    include('inc/head.php');

    if(!isset($_SESSION['user'])){
        header('location: ../login.php');
    }

    $id = $_SESSION['user'];
    $sql0 = "SELECT * FROM users WHERE id=".$id;
    $result0 = $conne->query($sql0);
    $row0 = $result0->fetch_assoc();
?>

<body class="dark-topbar">
<?php include('inc/sidebar.php'); ?>

<div class="page-wrapper">
    <?php include('inc/header.php'); ?>

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="row">
                            <div class="col">
                                <h4 class="page-title">Profile Completion</h4>
                            </div>
                            <div class="col-auto align-self-center">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <span class="day-name">Today:</span>
                                    <span id="Select_date"><?php echo date("M d"); ?></span>
                                    <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                if(isset($_SESSION['error'])){
                    echo "<div class='alert alert-danger'>{$_SESSION['error']}</div>";
                    unset($_SESSION['error']);
                }
                if(isset($_SESSION['success'])){
                    echo "<div class='alert alert-success'>{$_SESSION['success']}</div>";
                    unset($_SESSION['success']);
                }
            ?>

            <div class="row">
                <div class="col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Update Profile Information</h4>
                        </div>
                        <form method="post" action="profile-edit-action.php" enctype="multipart/form-data">
                            <div class="card-body">

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 text-right align-self-center">Full Name</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input class="form-control" type="text" name="full_name" value="<?= $row0['full_name'] ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 text-right align-self-center">Username</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input class="form-control" type="text" name="uname" value="<?= $row0['uname'] ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 text-right align-self-center">Password</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input class="form-control" type="password" name="password" placeholder="Enter new password (leave blank to keep current)">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 text-right align-self-center">Date of Birth</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input class="form-control" type="date" name="dob" value="<?= $row0['dob'] ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 text-right align-self-center">Nationality</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <select name="nationality" class="form-control">
                                            <?php include('../inc/countries.php'); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 text-right align-self-center">Address</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input class="form-control" type="text" name="address" value="<?= $row0['address'] ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 text-right align-self-center">Phone Number</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input type="text" name="phone_no" class="form-control" value="<?= $row0['phone_no'] ?>" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 text-right align-self-center">Profile Picture</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input type="file" name="photo" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-9 col-xl-8 offset-lg-3">
                                        <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <?php include('inc/footer.php'); ?>
    </div>
</div>

<?php include('inc/scripts.php'); ?>
</body>
</html>
