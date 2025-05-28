<?php
    include('../inc/config.php');
    include('../admin/includes/format.php');
    include '../admin/session.php';

    $page_name = 'Profile';
    $page_parent = '';
    $page_title = 'Welcome to the Official Website of '.$settings->siteTitle;
    $page_description = $settings->siteTitle.' provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

    include('inc/head.php');

    $id = $_SESSION['user'];

    if(!isset($_SESSION['user'])){
        header('location: ../login.php');
    }

    $sql0 = "SELECT * FROM users WHERE id=".$id;
    $result0 = $conne->query($sql0);
    $row0 = $result0->fetch_assoc();

    $referrals = $row0['uname'];

    $sql1 = "SELECT * FROM transaction WHERE user_id = $id ORDER BY trans_id DESC LIMIT 1";
    $result1 = $conne->query($sql1);
    $row1 = $result1->fetch_assoc();

    if ($row1["type"] == 1) {
        $transaction = $row1["amount"];
        $type = "credit";
    } else {
        $transaction = $row1["amount"];
        $type = "debit";
    }

    $time = strtotime($row1["trans_date"]);
    $sanitized_time = date("Y-m-d, g:i A", $time);

    $sql2 = "SELECT *, COUNT(*) AS numrows FROM users WHERE referral_code='$referrals' ";
    $result2 = $conne->query($sql2);
    $row2 = $result2->fetch_assoc();
    $no_of_referrals = $row2['numrows'];
?>

<body class="dark-topbar">
    <!-- Left Sidenav -->
    <?php include('inc/sidebar.php'); ?>

    <div class="page-wrapper">
        <!-- Top Bar Start -->
        <?php include('inc/header.php'); ?>

        <!-- Page Content-->
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">Profile</h4>
                                </div>
                                <div class="col-auto align-self-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                        <span class="day-name" id="Day_Name">Today:</span>&nbsp;
                                        <span class="" id="Select_date">Jan 11</span>
                                        <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php
                            if (empty($row0['nationality'])) {
                                echo '<div class="alert custom-alert custom-alert-primary icon-custom-alert alert-secondary-shadow fade show" role="alert">
                                        <i class="mdi mdi-alert-outline alert-icon text-primary align-self-center font-30 mr-3"></i>
                                        <div class="alert-text my-1">
                                            <span><a href="profile-edit" class="btn mb-1 btn-primary">Click Here</a> to Complete Your Profile Setup</span>
                                        </div>
                                        <div class="alert-close">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true"><i class="mdi mdi-close font-16"></i></span>
                                            </button>
                                        </div>
                                    </div>';
                            }
                        ?>
                    </div>
                </div>

                <?php
                    if(isset($_SESSION['error'])){
                        echo "<div class='alert alert-danger border-0' role='alert'>
                            <i class='la la-skull-crossbones alert-icon text-danger align-self-center font-30 mr-3'></i>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'><i class='mdi mdi-close align-middle font-16'></i></span>
                            </button>
                            <strong>Oh snap!</strong> ".$_SESSION['error']."</div>";
                        unset($_SESSION['error']);
                    }
                    if(isset($_SESSION['success'])){
                        echo "<div class='alert alert-success border-0' role='alert'>
                            <i class='mdi mdi-check-all alert-icon'></i>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'><i class='mdi mdi-close align-middle font-16'></i></span>
                            </button>
                            <strong>Well done!</strong> ".$_SESSION['success']."</div>";
                        unset($_SESSION['success']);
                    }
                ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">                              
                            <div class="card-body">
                                <div class="dastone-profile">
                                    <div class="row">
                                        <div class="col-lg-6 align-self-center mb-3 mb-lg-0">
                                            <div class="dastone-profile-main">
                                                <div class="dastone-profile-main-pic">
                                                    <img src="../admin/images/<?php echo $row0["photo"]; ?>" alt="" height="110" class="rounded-circle">
                                                    <span class="dastone-profile_main-pic-change">
                                                        <i class="fas fa-camera"></i>
                                                    </span>
                                                </div>                                                    
                                                <div class="dastone-profile_user-detail">
                                                    <h5 class="dastone-user-name"><b> Fullname :</b> <?php echo $row0["full_name"] ?></h5>                                                        
                                                    <p class="mb-0 dastone-user-name-post"><b> Username :</b> <?php echo $row0["uname"]; ?></p>                          
                                                </div>
                                            </div>                                                
                                        </div>
                                        
                                        <div class="col-lg-6 ml-auto align-self-center">
                                            <ul class="list-unstyled personal-detail mb-0">
                                                <li><i class="las la-phone mr-2 text-secondary font-22 align-middle"></i> <b> Phone </b> : <?php echo empty($row0["phone_no"]) ? 'xxx-xxx-xxx-xxxx' : $row0["phone_no"]; ?></li>
                                                <li class="mt-2"><i class="las la-envelope text-secondary font-22 align-middle mr-2"></i> <b> Email </b> : <?php echo $row0["email"]; ?></li>
                                                <li class="mt-2"><i class="las la-globe text-secondary font-22 align-middle mr-2"></i> <b> Nationality </b> : <?php echo empty($row0["nationality"]) ? 'xxxxxxxxxxxxx' : $row0["nationality"]; ?></li>
                                                <li class="mt-2"><i class="las la-map-marker text-secondary font-22 align-middle mr-2"></i> <b> Address </b> : <?php echo empty($row0["address"]) ? 'No address provided' : $row0["address"]; ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>                                                                                
                            </div>         
                        </div>     
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">                              
                            <div class="card-body">
                                <div class="dastone-profile">
                                    <div class="row">
                                        <div class="col-lg-4 ml-auto align-self-center">
                                            <ul class="list-unstyled personal-detail mb-0">
                                                <li><i class="las la-genderless mr-2 text-secondary font-22 align-middle"></i> <b> Gender </b> : <?php echo empty($row0["gender"]) ? 'xx-xxxx' : $row0["gender"]; ?></li>               
                                            </ul>
                                        </div>
                                        
                                        <div class="col-lg-4 ml-auto align-self-center">
                                            <ul class="list-unstyled personal-detail mb-0">
                                                <li class="mt-2"><i class="las la-calendar text-secondary font-22 align-middle mr-2"></i> <b> Date Of Birth </b> : <?php echo empty($row0["dob"]) ? 'DD-MM-YYYY' : $row0["dob"]; ?></li>                 
                                            </ul>
                                        </div>
                                        
                                        <div class="col-lg-4 ml-auto align-self-center">
                                            <ul class="list-unstyled personal-detail mb-0">
                                                <li class="mt-2"><i class="las la-calendar-check text-secondary font-22 align-middle mr-2"></i> <b> Date Joined </b> : <?php echo $row0["created_on"]; ?></li>                                                   
                                            </ul>
                                        </div>
                                    </div>                                                                                
                                </div>         
                            </div>     
                        </div>     
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">                                                        
                                    <div class="card-body  report-card">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col">
                                                <p class="text-dark mb-1 font-weight-semibold">Wallet Balance</p>
                                                <h3 class="my-2 font-24 font-weight-bold">&#36; <?php echo number_format_short($row1["balance"], 2); ?></h3>
                                            </div>
                                            <div class="col-auto align-self-center">
                                                <div class="report-main-icon bg-light-alt">
                                                    <i data-feather="briefcase" class="align-self-center text-muted icon-md"></i>  
                                                </div>
                                            </div>
                                        </div>
                                    </div>          
                                </div>     
                            </div>
                            <div class="col-lg-6">
                                <div class="card">                                                        
                                    <div class="card-body  report-card">
                                        <div class="row d-flex justify-content-center">
                                            <div class="col">
                                                <p class="text-dark mb-1 font-weight-semibold">Referral Code: <?php echo $row0["uname"]; ?></p>
                                                <h3 class="my-2 font-24 font-weight-bold"> <?php echo number_format_short($no_of_referrals, 0); ?> Referral(s)</h3>
                                            </div>
                                            <div class="col-auto align-self-center">
                                                <div class="report-main-icon bg-light-alt">
                                                    <i data-feather="git-merge" class="align-self-center text-muted icon-md"></i>  
                                                </div>
                                            </div>
                                        </div>
                                    </div>          
                                </div>     
                            </div>
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
