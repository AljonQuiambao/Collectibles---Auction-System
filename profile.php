<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $user_id = trim($_SESSION["id"]);
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($link, $sql);
    $currentUser = $result->fetch_array(MYSQLI_ASSOC);

    print_r($currentUser);
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
                    <?php
                            if (isset($_SESSION['success_status'])) {
                            ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $_SESSION['success_status']; ?>
                                </div>
                            <?php
                                unset($_SESSION['success_status']);
                            }
                        ?>

                        <?php
                            if (isset($_SESSION['error_status'])) {
                            ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $_SESSION['error_status']; ?>
                                </div>
                            <?php
                                unset($_SESSION['error_status']);
                            }
                        ?>

                        <div class="row gutters">
                            <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="account-settings">
                                            <div class="user-profile">
                                                <div class="user-avatar">
                                                    <img src="assets/uploads/<?php echo $currentUser['avatar'] ?>" alt="<?php echo $user['name']; ?>">
                                                </div>
                                                <h4 class="user-name">
                                                    <!-- <?php print_r($user); ?> -->
                                                    <?php echo $currentUser['name']; ?>
                                                </h4>
                                                <h6 class="user-email">
                                                    @<?php echo ucwords($currentUser['username']); ?>
                                                </h6>
                                                <?php if ($currentUser['role'] == 1 || $currentUser['role'] == 2) { ?>
                                                    <form action="services.php" method="POST">
                                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4">
                                                            <div>
                                                                <input type="text" name="user_id" value="<?php echo $currentUser['id']; ?>">
                                                                <input type="submit" name="activate_multirole" class="btn btn-md btn-success" value="Activate Multirole">
                                                            </div>
                                                        </div>
                                                    </form>
                                                <?php  } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="services.php" method="POST">
                                            <div class="row gutters">
                                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                    <h4 class="mb-3 text-primary">Personal Details</h4>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="form-group">
                                                        <label>Full Name</label>
                                                        <input type="text" class="form-control" name="name" placeholder="Enter full name" value="<?php echo $currentUser['name']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <input type="text" class="form-control" name="address" placeholder="Enter address" value="<?php echo $currentUser['address']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="form-group">
                                                        <label>Username</label>
                                                        <input type="text" class="form-control" name="username" placeholder="Enter Username" value="<?php echo $currentUser['username']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="form-group">
                                                        <label>Date of Birth</label>
                                                        <input type="text" class="form-control" name="date_of_birth" placeholder="Enter date of birth" value="<?php echo date('m-d-Y', strtotime($currentUser['date_of_birth'])); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="form-group">
                                                        <label>Gender</label>
                                                        <input type="text" class="form-control" name="gender" placeholder="Enter gender" value="<?php echo $currentUser['gender']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <div class="form-group">
                                                        <label>Contact</label>
                                                        <input type="text" class="form-control" name="contact" placeholder="Enter contact number" value="<?php echo $currentUser['contact']; ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row gutters">
                                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                    <div class="text-right">
                                                        <input type="submit" name="update_user" class="btn btn-lg btn-primary" value="Save Changes">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>
    <?php include 'background.php';  ?>
    </div>
    <?php include 'script.php';  ?>
</body>

</html>