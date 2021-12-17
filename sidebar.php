<?php
class Roles
{
    const Bidder = 1;
    const Auctioneer = 2;
    const Admin = 3;
    const MultiRole = 4;
}

// Get current user
$param_id = trim($_SESSION["id"]);
$sql = "SELECT * FROM users WHERE id = $param_id";
$result = mysqli_query($link, $sql);
$user = $result->fetch_array(MYSQLI_ASSOC);
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="welcome.php">
        <div class="sidebar-brand-icon rotate-n-15 mr-2">
            <i class="fas fa-gavel"></i>
        </div>
        <div class="sidebar-brand-text mr-3">
            Collectibles
        </div>
    </a>

    <?php if ($user['role'] == 1) { ?>
        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Auctions -->
        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/welcome.php') { ?>active <?php } ?>">
            <a class="nav-link" href="welcome.php">
                <i class="fas fa-fw fa-clock"></i>
                <span>Auctions</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Bidder Section
        </div>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/my-bidding.php') { ?>active <?php } ?>">
            <a class="nav-link" href="my-bidding.php">
                <i class="fas fa-user-tag"></i>
                <span> My Bidding</span>
            </a>
        </li>

        <!-- Nav Item - My bids -->
        <!-- <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/my-bids.php') { ?>active <?php } ?>">
            <a class="nav-link" href="my-bids.php">
                <i class="fas fa-fw fa-search-dollar"></i>
                <span> My Bids</span>
            </a>
        </li> -->

        <!-- Nav Item - Bidding History -->
        <!-- <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/bid-history.php') { ?>active <?php } ?>">
            <a class="nav-link" href="bid-history.php">
                <i class="fas fa-fw fa-user-clock"></i>
                <span> Bidding History</span>
            </a>
        </li> -->

        <!-- Nav Item - Won Items -->
        <!-- <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/won-items.php') { ?>active <?php } ?>">
            <a class="nav-link" href="won-items.php">
                <i class="fas fa-fw fa-tags"></i>
                <span> Won Items</span>
            </a>
        </li> -->
    <?php } ?>

    <?php if ($user['role'] == 2) {  ?>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Auctioneer Section
        </div>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/my-auctions.php') { ?>active <?php } ?>">
            <a class="nav-link" href="my-auctions.php">
                <i class="fas fa-search"></i>
                <span> My Auctions</span></a>
        </li>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/sold-items.php') { ?>active <?php } ?>">
            <a class="nav-link" href="sold-items.php">
                <i class="fas fa-tag"></i>
                <span> Sold Items</span></a>
        </li>
    <?php } ?>

    <?php if ($user['role'] == 3) {  ?>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Admin Section
        </div>

        <!-- Nav Item - Admin -->
        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/admin-dashboard.php') { ?>active <?php } ?>">
            <a class="nav-link" href="admin-dashboard.php">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Admin Dashboard</span></a>
        </li>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/users.php') { ?>active <?php } ?>">
            <a class="nav-link" href="users.php">
                <i class="fas fa-fw fa-users"></i>
                <span> User List</span>
            </a>
        </li>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/admin-request.php') { ?>active <?php } ?>">
            <a class="nav-link" href="admin-request.php">
                <i class="fas fa-fw fa-bars"></i>
                <span>Request Items</span></a>
        </li>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/payment-confirmation.php') { ?>active <?php } ?>">
            <a class="nav-link" href="payment-confirmation.php">
                <i class="fas fa-fw fa-check"></i>
                <span>Confirm Payment</span></a>
        </li>
    <?php } ?>

    <?php if ($user['role'] == 4) { ?>
        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Auctions -->
        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/welcome.php') { ?>active <?php } ?>">
            <a class="nav-link" href="welcome.php">
                <i class="fas fa-fw fa-clock"></i>
                <span>Auctions</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Bidder Section
        </div>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/my-bidding.php') { ?>active <?php } ?>">
            <a class="nav-link" href="my-bidding.php">
                <i class="fas fa-user-tag"></i>
                <span> My Bidding</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Auctioneer Section
        </div>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/my-auctions.php') { ?>active <?php } ?>">
            <a class="nav-link" href="my-auctions.php">
                <i class="fas fa-search"></i>
                <span> My Auctions</span></a>
        </li>

        <li class="nav-item <?php if ($_SERVER['PHP_SELF'] == '/collectibles/sold-items.php') { ?>active <?php } ?>">
            <a class="nav-link" href="sold-items.php">
                <i class="fas fa-tag"></i>
                <span> Sold Items</span></a>
        </li>
    <?php } ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>