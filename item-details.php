<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $item_id = $_GET['item_id'];
    //print($item_id);

    $sql = "SELECT * FROM items 
            JOIN item_status ON items.id = item_status.item_id
            JOIN users ON items.user_id = users.id
            JOIN item_category ON items.category = item_category.category_id
            WHERE item_id = $item_id";

    $item_result = mysqli_query($link, $sql);
    $item = $item_result->fetch_array(MYSQLI_ASSOC);

    //print_r($item);

    if (array_filter($item) !== []) {
        $bid_session_sql = "SELECT * FROM bidding_sessions WHERE item_id = $item_id";
        $bid_session_result = mysqli_query($link, $bid_session_sql);
        $bid_session = $bid_session_result->fetch_array(MYSQLI_ASSOC);

        //print_r($bid_session);

        $bidding_session_id = $bid_session['id'];
        $bid_history_count = 0;
        
        $history_count_sql = "SELECT count(*) FROM bidding_history";
        $history_count_result = mysqli_query($link, $history_count_sql);
        $bid_history_count_sql = $history_count_result->fetch_all(MYSQLI_ASSOC);
        $top_bidders = [];

        if (array_filter($bid_history_count_sql) !== []) {  
            $query = "SELECT * FROM bidding_history
                    JOIN users ON bidding_history.bidder_id = users.id
                    WHERE bidding_session_id = $bidding_session_id ORDER BY bid_token desc";

            $query_result = mysqli_query($link, $query);
            $bid_history = $query_result->fetch_all(MYSQLI_ASSOC);
            $top_bidders = array_slice($bid_history, 0, 5);
        }

        $auctioneer_id = $bid_session['auctioneer_id'];
        $user_sql = "SELECT * FROM users WHERE id = $auctioneer_id";
        $user_result = mysqli_query($link, $user_sql);
        $auctioneer = $user_result->fetch_array(MYSQLI_ASSOC);

        $bid_err = "";
        $current_bid = $bid_session['current_bid'];

        if (isset($_POST['put_bid'])) {
            $current_user_id = trim($_SESSION["id"]);
            $bid_token = $_POST['bid_token'];
            $item_title = $item[0]['title'];

            $query_sql = "INSERT INTO bidding_history(bidding_session_id, item_id, auctioneer_id, bidder_id, bid_token)
                             VALUES ('$bidding_session_id', '$item_id', '$auctioneer_id', '$current_user_id', '$bid_token')"; 
            $query_sql_run = mysqli_query($link, $query_sql);

            if ($query_sql_run) {
                $query_update = "UPDATE bidding_sessions SET current_bid = $bid_token
                    WHERE auctioneer_id = $auctioneer_id && 
                    item_id = $item_id"; 

                $query_update_run = mysqli_query($link, $query_update);

                //for auctioneer
                $auc_notif_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                                VALUES ('$auctioneer_id', '$item_id', 2, 'Your item ($item_title) has a current bid of $bid_token.', 0, NOW())"; 
                $auc_notif_run = mysqli_query($link, $auc_notif_sql);

                //for bidder
                $bid_notif_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                                VALUES ('$current_user_id', '$item_id', 2, 'Congratulations! Your are the highest bidder for the item ($item_title).', 0, NOW())"; 
                $bid_notif_run = mysqli_query($link, $bid_notif_sql);
            }
        }
    }

    $param_id = trim($_SESSION["id"]);
    $token_sql = "SELECT * FROM tokens WHERE id = $param_id";
    $token_result = mysqli_query($link, $token_sql);
    $token = $token_result->fetch_array(MYSQLI_ASSOC);
    $display_token = $token['token'];

    $sql = "SELECT * FROM images WHERE item_id = $item_id ORDER BY id DESC";
    $item_result = mysqli_query($link, $sql);
    $images = $item_result->fetch_all(MYSQLI_ASSOC);
    
    //for comment section
    $comment = "";
    $comment_err = "";
    $sql = "SELECT * FROM comments
                JOIN users ON comments.user_id = users.id
                WHERE item_id = $item_id and user_id = $param_id ORDER BY date_posted desc";

    $result = mysqli_query($link, $sql);
    $comments = $result->fetch_all(MYSQLI_ASSOC);
    $comment_count =  count($comments);
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
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <div>
                                        <h3>
                                            <form id="counter" action="services.php" method="POST">
                                                <input type="hidden" class="indicator-status" value="<?php echo $item['status']; ?>">
                                                <input name="auctioneer_id" value="<?php echo $auctioneer['id']; ?>" type="hidden">
                                                <input name="bidder_id" value="<?php echo $top_bidders[0]['id']; ?>" type="hidden">
                                                <input name="item_id" value="<?php echo $item_id; ?>" type="hidden">
                                                <input name="category" value="<?php echo $item['category_id']; ?>" type="hidden">
                                                <input class="hidden" id="counter_submit" name="counter_submit" type="submit">
                                            </form>
                                            Auction Started : Time Remaining <strong> <span class="counter" 
                                            data-bid-time="<?php echo $bid_session['bidding_time']; ?>" data-end-time="<?php echo $bid_session['end_time']; ?>"></span></strong>
                                        </h3>
                                        <h5>
                                            Available Balance:
                                            <span id="available-token">
                                                <strong>₱ <?php echo $display_token;?></strong>
                                            </span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-3 text-center">
                                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                                <?php
                                                    $result = array();
                                                    foreach ($images as $element) {
                                                        $result[$element['item_id']][] = $element;
                                                    }               

                                                    foreach ($result as $key => $image) { ?>
                                                    <div style="width: 100%;" id="<?php echo $key; ?>" class="carousel slide" data-ride="carousel">
                                                        <div class="carousel-inner">
                                                            <?php
                                                            foreach ($image as $id => $data) {
                                                                $imageURL = 'uploads/' . $data["file_name"];
                                                                ?>
                                                                <div class="carousel-item <?php if ($id === 0) { echo "active";} ?>">
                                                                    <img class="d-block item-slider w-100 h-100" src="<?php echo $imageURL; ?>">
                                                                </div>
                                                            <?php 
                                                            } ?>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                } ?>
                                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <h4 class="font-weight-bold text-primary">
                                                <?php echo $item['title']; ?>
                                                <sup>
                                                    <span class="badge badge-primary badge-counter">
                                                        <?php echo $item['category']; ?>
                                                    </span>
                                                </sup>
                                            </h4>
                                            <div class="item-details" class="pr-2">
                                                <?php echo $item['details']; ?>
                                            </div>
                                            <div class="mt-3">
                                                <span style="font-size: small;">
                                                    Date Posted: <?php echo $item['date_added']; ?>
                                                </span>
                                            </div>
                                            <div class="mt-4">
                                                Bid History
                                                <span class="badge badge-info badge-counter">
                                                    <span id="bid-counter">
                                                        <?php echo count($bid_history);?>
                                                    </span> 
                                                    <?php echo count($bid_history) > 1 ? 'bids': 'bid';?>
                                                </span>
                                            </div>
                                            <div class="mt-2">
                                                Current Bid
                                                <h4 id="bid-payment">
                                                    <span id="current-bid">
                                                        <strong> ₱ <?php echo number_format((float)$bid_session['current_bid'], 2, '.', ''); ?>
                                                        </strong>
                                                    </span>
                                                </h4>
                                            </div>

                                            <form action="<?php echo 'item-details.php?item_id='.$item_id; ?>" method="post">
                                                <span id="success" class="alert-success hidden">
                                                    <strong>Congratulations!</strong> You are the highest bidder.
                                                </span>
                                                <span id="greater" class="alert-danger hidden">
                                                    <strong>Opps!</strong> You must input greater than the current bid.
                                                </span>
                                                <span id="no-input" class="alert-danger hidden">
                                                    Kindly place your bid.
                                                </span>
                                                <span id="must-100-input" class="alert-warning hidden">
                                                    Please input your bid greater than 100 to the current bid.
                                                </span>
                                                <div class="form-group row mb-2 mt-2">
                                                    <div class="col-sm-6 mb-3">
                                                        <input name="bid_token" type="text" class="form-control form-control-user" id="bid-textbox" placeholder="Your Max Bid">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input id="place-bid" name="put_bid" type="submit" class="btn btn-primary" value="Place Bid">
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <span class="random"></span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="panel panel-default card shadow p-4">
                                                <div class="panel-heading">
                                                    <h4 class="text-center">
                                                        <i class="fas fa-crown"></i>
                                                        Top Bidders
                                                    </h4>
                                                </div>
                                                <ul class="list-group list-group-flush">
                                                    <?php
                                                         if (array_filter($top_bidders) != []) {
                                                             foreach ($top_bidders as $index => $top_bidder) { ?>
                                                        <li class="list-group-item">
                                                            <span class="badge badge-success badge-counter p-4">
                                                                <?php echo $index + 1;?>                                                  
                                                            </span>
                                                            <span class="ml-4 mr-4">
                                                                <?php echo $top_bidder['name'];?>                                                  
                                                            </span>
                                                            <span class="ml-4">
                                                                <?php echo intval($top_bidder['bid_token']);?>                                                  
                                                            </span>
                                                        </li>
                                                    <?php  } 
                                                    } ?>
                                                    <div class="row mt-2 no-data-available <?php echo array_filter($top_bidders) === [] ? "" : "hidden";?>">
                                                        <div class="col-lg-12">
                                                            <div class="alert alert-primary" role="alert">
                                                                No bidders yet.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row" style="margin-top: 3rem;">
                                        <?php include 'comments.php'; ?>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="mt-2">
                                        <a href="welcome.php" class="btn btn-secondary">
                                            <span class="text">
                                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                                Back to Auctions
                                            </span>
                                        </a>
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
    <script src="js/counter.js"></script>
    <script src="js/time-ago.js"> </script>
    <script src="js/main.js"> </script>
    <script src="js/string-trim.js"></script>
    <script>
        var words = [
            '',
            'The more difficult the victory, the greater the happiness in winning!',
            'You are not the highest bidder!',
            'The will to win is important, but you need to bid higher!',
            'Champions keep playing until they get it right!',
            'You can do better than that, come on and win this thing!',
            'It aint over till its over, bid higher!',
            'You are not the highest bidder!',
        ];

        var getRandomWord = function() {
            return words[Math.floor(Math.random() * words.length)];
        };

        $(function() { // after page load
            setInterval(function() {
                $('.random').fadeOut(500, function() {
                    $(this).html(getRandomWord()).fadeIn(500);
                });
            }, 5000);
        });
    </script>
</body>

</html>