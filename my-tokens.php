<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $user_id = trim($_SESSION["id"]);
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($link, $sql);
    $currentUser = $result->fetch_array(MYSQLI_ASSOC);

    $token_sql = "SELECT * FROM tokens WHERE user_id = $user_id";
    $token_result = mysqli_query($link, $token_sql);
    $token = $token_result->fetch_array(MYSQLI_ASSOC);
    $display_token = !empty($token) || $token['token'] ? $token['token'] : 0;

    $date_now = date("Y-m-d H:i:s");
    $bid_history_sql = "SELECT * FROM bidding_history 
        JOIN items ON items.id = bidding_history.item_id 
        WHERE bidder_id = $user_id";

    $bid_history_result = mysqli_query($link, $bid_history_sql);
    $bid_history_items = $bid_history_result->fetch_all(MYSQLI_ASSOC);

    function filterByDate($bid_history_items, $dateNow)
    {
        return array_filter($bid_history_items, function ($item) use ($dateNow) {
            if ($item['date_bid'] > $dateNow) {
                return true;
            }
        });
    }

    $items = filterByDate($bid_history_items, $date_now);
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
                    <h1 class="h3 mb-2 text-gray-800 mb-3">
                        <i class="fas fa-coins"></i>
                        My Tokens
                    </h1>

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
                    <div class="row col-12">
                        <div class="card col-4 shadow mb-4 p-4 text-center">
                            <div class="row p-1">
                                <h4>
                                    Available Balance :
                                    <strong>
                                        ₱ <?php echo number_format((float)$display_token, 2, '.', ''); ?> tokens
                                    </strong>
                                </h4>
                            </div>
                            <div>
                                <img src="img/qr-code.png" width="50%;">
                            </div>
                            <div class="mt-2 mb-2">
                                <span class="mr-2">Just scan the QR code or</span>
                                <a href="gcash.php" class="btn btn-primary">
                                    <span class="text">
                                        Cash In
                                    </span>
                                </a>

                                <?php if (empty($items)) { ?>
                                    <a href="gcash-cashout.php" class="btn btn-secondary">
                                        <span class="text">
                                            Cash Out
                                        </span>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="card col-8 shadow mb-4 p-4">
                            <div class="row p-1 float-right">
                                <h3>
                                    Account Information:
                                </h3>
                            </div>
                            <div class="mt-2 mb-2">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th scope="row">NAME</th>
                                            <td><?php echo $currentUser['name']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">BIRTHDAY</th>
                                            <td><?php echo $currentUser['date_of_birth']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">USERNAME</th>
                                            <td><?php echo $currentUser['username']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">CURRENT ADDRESS</th>
                                            <td><?php echo $currentUser['address']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">GCASH NUMBER</th>
                                            <td colspan="2"><?php echo $currentUser['contact']; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
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