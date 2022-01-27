<?php
// Include config file
require_once "config.php";

// Initialize the session
session_start();

$sql = "SELECT * FROM users WHERE role != 3";
$result = mysqli_query($link, $sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

$item_sql = "SELECT * FROM items WHERE is_deleted = false";
$item_result = mysqli_query($link, $item_sql);
$items = $item_result->fetch_all(MYSQLI_ASSOC);

$item_sold_sql = "SELECT * FROM items WHERE is_deleted = false && status == 3";
$item_sold_result = mysqli_query($link, $item_sql);
$items_sold = $item_sold_result->fetch_all(MYSQLI_ASSOC);
$items_sold_count = (count($items_sold) / 100) * 100;

function filterByItemStatus($items, $status)
{
    return array_filter($items, function ($item) use ($status) {
        if ($item['status'] == $status) {
            return true;
        }
    });
}

$item_pending = filterByItemStatus($items, 1);
$item_approved = filterByItemStatus($items, 2);
$item_reject = filterByItemStatus($items, 3);
$item_sold = filterByItemStatus($items, 5);

function filterByUserRole($users, $role)
{
    return array_filter($users, function ($user) use ($role) {
        if ($user['role'] == $role) {
            return true;
        }
    });
}

$bidders = filterByUserRole($users, 1);
$auctioneers = filterByUserRole($users, 2);

$user_id = trim($_SESSION["id"]);
$token_sql = "SELECT * FROM tokens WHERE user_id = $user_id";
$token_result = mysqli_query($link, $token_sql);
$token = $token_result->fetch_array(MYSQLI_ASSOC);
$display_token = $token['token'] ? $token['token'] : 0;

$fee_sql = "SELECT * FROM subscription_fee WHERE user_id = $user_id";
$fee_result = mysqli_query($link, $fee_sql);
$fee = $fee_result->fetch_array(MYSQLI_ASSOC);
$subscription_fee = $fee['subscription_fee'] ? $fee['subscription_fee'] : 0;
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body id="page-top">

    <div id="wrapper">

        <?php include 'sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <?php include 'navbar.php'; ?>

                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">
                            Admin Dashboard
                        </h1>
                    </div>

                    <div class="row">
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Commission
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                ₱ <?php echo number_format((float)$display_token, 2, '.', ''); ?> tokens
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-secondary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                Subscription Fee
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                ₱ <?php echo number_format((float)$subscription_fee, 2, '.', ''); ?> tokens
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Users
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo count($users); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Items
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo count($items); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bars fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Item Solds
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                        <?php echo $items_sold_count ?>%
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php $items_sold_count ?>%" aria-valuenow="<?php $items_sold_count ?>" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Users</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4">
                                        <canvas id="userChart"></canvas>
                                    </div>
                                    <hr>
                                    <div class="mt-4 text-center small">
                                        <span id="bidders" class="mr-2" data-value="<?php echo count($bidders); ?>">
                                            <i class="fas fa-circle text-primary"></i> Bidders
                                        </span>
                                        <span id="auctioneers" class="mr-2 auctioneers" data-value="<?php echo count($auctioneers); ?>">
                                            <i class="fas fa-circle text-success"></i> Auctioneers
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Item Status</h6>
                                </div>
                                <div class="card-body">
                                    <h4 class="small font-weight-bold">Pending <span class="float-right"><?php echo (count($item_pending) / 100) * 100; ?>%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo (count($item_pending) / 100) * 100; ?>%" aria-valuenow="<?php echo (count($item_pending) / 100) * 100; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Sold <span class="float-right"><?php echo (count($item_sold) / 100) * 100; ?>%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo (count($item_sold) / 100) * 100; ?>%" aria-valuenow="<?php echo (count($item_sold) / 100) * 100; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Reject <span class="float-right"><?php echo (count($item_reject) / 100) * 100; ?>%</span>
                                    </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo (count($item_reject) / 100) * 100; ?>%" aria-valuenow="<?php echo (count($item_reject) / 100) * 100; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Approved <span class="float-right"><?php echo (count($item_approved) / 100) * 100; ?>%</span>
                                    </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo (count($item_approved) / 100) * 100; ?>%" aria-valuenow="<?php echo (count($item_approved) / 100) * 100; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <?php include 'background.php'; ?>

    <?php include 'script.php'; ?>

</body>

</html>