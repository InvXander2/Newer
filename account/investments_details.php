<?php
// Start session and check authentication first
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit();
}

// Include configuration and database connection
include '../inc/config.php';
include '../admin/session.php';

// Page metadata
$page_name = 'Investments Details';
$page_title = 'Welcome to the Official Website of ' . ($settings->siteTitle ?? 'Manage Investment');
$page_description = 'Manage Investment provides quality infrastructure backed high-performance cloud computing services for cryptocurrency mining. Choose a plan to get started today! What are you waiting for? Together We Grow!...';

// Database connection
$conn = $pdo->open();

try {
    // Fetch investment plans
    $investment_planQuery = $conn->query("SELECT * FROM investment_plans ORDER BY id ASC");
    $investment_plans = $investment_planQuery->rowCount() ? $investment_planQuery->fetchAll(PDO::FETCH_OBJ) : [];

    // Fetch user investments (using prepared statement for security)
    $id = $_SESSION['user'];
    $stmt = $conn->prepare("
        SELECT i.*, ip.name AS plan_name, i.status AS invest_status 
        FROM investment i 
        LEFT JOIN investment_plans ip ON ip.id = i.invest_plan_id 
        LEFT JOIN users u ON u.id = i.user_id 
        WHERE i.user_id = :user_id 
        ORDER BY i.id DESC
    ");
    $stmt->execute(['user_id' => $id]);
    $new_investment_plans = $stmt->rowCount() ? $stmt->fetchAll(PDO::FETCH_OBJ) : [];
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
} finally {
    $pdo->close();
}

// Current date for display
$now = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'inc/head.php'; ?>
<body class="dark-topbar">
    <!-- Left Sidenav -->
    <?php include 'inc/sidebar.php'; ?>

    <div class="page-wrapper">
        <!-- Top Bar Start -->
        <?php include 'inc/header.php'; ?>
        <!-- Top Bar End -->

        <!-- Page Content -->
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">Investments</h4>
                                </div>
                                <div class="col-auto align-self-center">
                                    <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                        <span class="day-name" id="Day_Name">Today:</span>
                                        <span id="Select_date"><?php echo date('F d, Y'); ?></span>
                                        <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Page Title -->

                <!-- Display Session Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger border-0" role="alert">
                        <i class="la la-skull-crossbones alert-icon text-danger align-self-center font-30 mr-3"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="mdi mdi-close align-middle font-16"></i></span>
                        </button>
                        <strong>Error!</strong> <?php echo $_SESSION['error']; ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success border-0" role="alert">
                        <i class="mdi mdi-check-all alert-icon"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="mdi mdi-close align-middle font-16"></i></span>
                        </button>
                        <strong>Success!</strong> <?php echo $_SESSION['success']; ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Investments Section -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Plans and Investments</h4>
                                <p class="text-muted mb-0">All your investments in one place.</p>
                            </div>
                            <div class="card-body">
                                <!-- Investment Plans Table -->
                                <h5 class="mt-0">Available Plans</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Plan Name</th>
                                                <th>Description</th>
                                                <th>Minimum Investment</th>
                                                <th>Maximum Investment</th>
                                                <th>ROI (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($investment_plans)): ?>
                                                <?php foreach ($investment_plans as $plan): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($plan->name ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($plan->description ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($plan->min_investment ?? '0'); ?></td>
                                                        <td><?php echo htmlspecialchars($plan->max_investment ?? '0'); ?></td>
                                                        <td><?php echo htmlspecialchars($plan->roi ?? '0'); ?>%</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No plans available.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- User Investments Table -->
                                <h5 class="mt-4">Your Investments</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Plan</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($new_investment_plans)): ?>
                                                <?php foreach ($new_investment_plans as $investment): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($investment->plan_name ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($investment->amount ?? '0'); ?></td>
                                                        <td><?php echo htmlspecialchars($investment->invest_status ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($investment->created_at ?? 'N/A'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No investments found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Investments Section -->
            </div>
            <!-- End Container -->
        </div>
        <!-- End Page Content -->

        <!-- Footer -->
        <?php include 'inc/footer.php'; ?>
    </div>
    <!-- End Page Wrapper -->

    <!-- Scripts -->
    <?php include 'inc/scripts.php'; ?>
</body>
</html>
