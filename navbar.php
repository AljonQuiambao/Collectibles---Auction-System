<?php
    // Include config file
    require_once "config.php";

    $not_loggedin = false;
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        $not_loggedin = true;
    }
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <?php
       if ($not_loggedin) {
           echo '<div class="sidebar-brand d-flex align-items-center justify-content-center" 
                style="font-size: 1.5rem; color: #4E73DF; letter-spacing: 2px;">
                <div class="sidebar-brand-icon rotate-n-15 mr-1">
                    <i class="fas fa-gavel"></i>
                </div>
                <div class="sidebar-brand-text mr-3">
                    <a href="landing.php" style="text-decoration: none;">Collectibles</a>
                </div>
            </div>';
       }
    ?>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <?php
            if (!$not_loggedin) {
                include 'alerts.php';
                echo '<div class="topbar-divider d-none d-sm-block"></div>';
            }
        ?>

        <div class="mt-1">
            <?php if ($not_loggedin) {
                echo '<a href="login.php" class="btn btn-primary">
                <span class="text">
                    Login
                </span>
             </a>';
            } ?>

            <?php if (!$not_loggedin) { ?>
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="user" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            <?php echo htmlspecialchars($_SESSION["username"]); ?>
                        </span>

                        <?php if ($_SESSION['login_avatar']) { ?>
                            <img class="img-profile rounded-circle" src="assets/uploads/<?php echo $_SESSION['login_avatar']; ?>" alt="">
                        <?php } else { ?>
                            <img class="img-profile rounded-circle" src="img/admin_avatar.svg" alt=""> 
                        <?php } ?>
                        
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="user">
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item" href="my-tokens.php">
                            <i class="fas fa-coins fa-sm fa-fw mr-2 text-gray-400"></i>
                            My Tokens
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>
            <?php } ?>
        </div>
    </ul>
</nav>
