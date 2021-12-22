<?php
// Include config file
require_once "config.php";

// Initialize the session
session_start();

$user_id = trim($_SESSION["id"]);
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($link, $sql);
$user = $result->fetch_array(MYSQLI_ASSOC);

$token_sql = "SELECT * FROM tokens WHERE id = $user_id";
$token_result = mysqli_query($link, $token_sql);
$token = $token_result->fetch_array(MYSQLI_ASSOC);
$display_token = number_format($token['token']);
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
                    <div class="row col-12">
                        <div class="col-3">
                            <h1 class="h3 mb-2 text-gray-800 mb-3">
                                <img class="mt-4" width="100%" src="img/gcash.png"/>
                            </h1>
                        </div>
                        <div class="card col-9 shadow mb-4 p-4">
                            <div class="bg-white shadow-sm pt-4 pl-2 pr-2 pb-2">
                                <ul role="tablist" class="nav bg-light nav-pills rounded nav-fill mb-3">
                                    <li class="nav-item"> 
                                        <a data-toggle="pill" href="#credit-card" class="nav-link active "> 
                                            Gcash Payment
                                        </a> 
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div id="credit-card" class="tab-pane fade show active pt-3">
                                    <form role="form" action="services.php" method="POST">
                                        <div class="form-group">
                                            <input type="hidden" name="user_id"  class="form-control" value="<?php echo $user_id; ?>">
                                            <input type="text" name="username" placeholder="Account Name" required class="form-control ">
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group"> 
                                                <input type="number" name="gcashNumber" placeholder="Gcash number" class="form-control " required>
                                                <div class="input-group-append"> 
                                                    <span class="input-group-text text-muted">
                                                        <i class="fas fa-phone-square-alt mx-1"></i> 
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" name="amount" placeholder="Amount" required class="form-control ">
                                        </div>
                                        <div class="card-footer"> 
                                            <input type="submit" name="cashout" class="subscribe btn btn-secondary btn-md shadow-sm" value="Cash Out"> 
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