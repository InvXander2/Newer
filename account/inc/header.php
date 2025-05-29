<?php
// inc/header.php
// Ensure $settings is available (loaded via config.php)
if (!isset($settings)) {
    include('../inc/config.php');
}

$sql0 = "SELECT * FROM users WHERE id=$id";
$result0 = $conne->query($sql0);
$row0 = $result0->fetch_assoc();

$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM direct_message WHERE user_id=0 || (user_id=$id && status=0)");
$stmt->execute();
$drow = $stmt->fetch();
$no_of_msg = $drow['numrows'];

$stmtQuery = $conn->query("SELECT * FROM direct_message WHERE user_id=$id || user_id=0 && status<2 order by 1 desc Limit 4");
if ($stmtQuery->rowCount()) {
    $dmrow = $stmtQuery->fetchAll(PDO::FETCH_OBJ);
}
?>

<div class="topbar">            
    <!-- Logo -->
    <div class="topbar-logo" style="display: flex; justify-content: center; align-items: center; padding: 10px 0;">
        <a href="/" title="<?php echo htmlspecialchars($settings->siteTitle); ?>">
            <img src="../assets/images/logo.png" alt="Logo" style="max-height: 50px;">
        </a>
    </div>

    <!-- Navbar -->
    <nav class="navbar-custom">    
        <ul class="list-unstyled topbar-nav float-right mb-0">  
            <li class="dropdown hide-phone">
                <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <i data-feather="search" class="topbar-icon"></i>
                </a>
                
                <div class="dropdown-menu dropdown-menu-right dropdown-lg p-0">
                    <!-- Top Search Bar -->
                    <div class="app-search-topbar">
                        <form action="#" method="get">
                            <input type="search" name="search" class="from-control top-search mb-0" placeholder="Type text...">
                            <button type="submit"><i class="ti-search"></i></button>
                        </form>
                    </div>
                </div>
            </li>                      

            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <i data-feather="bell" class="align-self-center topbar-icon"></i>
                    <?php if ($no_of_msg > 0) {
                        echo "<span class='badge badge-danger badge-pill noti-icon-badge'>".$no_of_msg."</span>";
                    } else { echo ""; } ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-lg pt-0">
                    <?php
                    if ($no_of_msg > 0) { ?>
                        <h6 class="dropdown-item-text font-15 m-0 py-3 border-bottom d-flex justify-content-between align-items-center">
                            Notifications <span class="badge badge-primary badge-pill"><?= $no_of_msg; ?></span>
                        </h6> 
                        <div class="notification-menu" data-simplebar>
                            <!-- item-->
                            <?php foreach ($dmrow as $dm) : ?>
                                <a href="message.php?id=<?= $dm->msg_id; ?>&readmessage" class="dropdown-item py-3">
                                    <div class="media">
                                        <div class="media-body align-self-center ml-2 text-truncate">
                                            <h6 class="my-0 font-weight-normal text-dark"><?= $dm->subject; ?></h6>
                                            <?php if ($dm->status == 0) { echo "<strong>"; } ?>
                                            <small class="text-muted mb-0"><?= substrwords($dm->message, 50); ?></small>
                                            <?php if ($dm->status == 0) { echo "</strong>"; } ?>
                                        </div>
                                    </div><!--end media-->
                                </a>
                            <?php endforeach; ?>
                            <!--end-item-->
                        </div>
                        <!-- All-->
                        <a href="messages" class="dropdown-item text-center text-primary">
                            View all <i class="fi-arrow-right"></i>
                        </a>
                    <?php } else {
                        echo "
                            <h6 class='dropdown-item-text font-15 m-0 py-3 border-bottom d-flex justify-content-between align-items-center'>
                                No Notifications Yet
                            </h6>
                        ";
                    } ?>
                </div>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span class="ml-1 nav-user-name hidden-sm"><?php echo $row0["full_name"] ?></span>
                    <img src="../admin/images/<?php if (!empty($row0["photo"])) { echo $row0["photo"]; } else { echo "profile.jpg"; } ?>" alt="profile-user" class="rounded-circle thumb-img" />
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="profile"><i> Profile</a>
                    <a class="dropdown-item" href="settings.php"><i> Settings</i></a>
                    <div class="divider"></div>
                    <a class="dropdown-item" href="logout.php"><i> Logout</i></a>
                </div>
            </li>
        </ul><!--end topbar-nav-->

        <ul class="list-unstyled topbar-nav mb-0">                        
            <li>
                <button class="nav-link button-menu-mobile">
                    <i data-feather="menu" class="align-self-center"></i>
                </button>
            </li>                            
        </ul>
    </nav>
    <!-- end navbar-->
</div>
