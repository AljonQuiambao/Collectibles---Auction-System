<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    // Check if the user is logged in, if not then redirect him to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }

    $date_now = date("Y-m-d H:i:s");
    $sql = "SELECT * FROM bidding_sessions
                JOIN items ON bidding_sessions.item_id = items.id";

    $item_result = mysqli_query($link, $sql);
    $raw_items = $item_result->fetch_all(MYSQLI_ASSOC);

    function filterByDate($items, $dateNow)
    {
        return array_filter($items, function ($item) use ($dateNow) {
            if ($item['end_time'] >= $dateNow) {
                return true;
            }
        });
    }

    $items = filterByDate($raw_items, $date_now);

    $user_id = trim($_SESSION["id"]);
    $token_sql = "SELECT * FROM tokens WHERE user_id = $user_id";
    $token_result = mysqli_query($link, $token_sql);
    $token = $token_result->fetch_array(MYSQLI_ASSOC);
    $display_token = !empty($token) || $token['token'] ? $token['token'] : 0;

    $image_sql = "SELECT * FROM images ORDER BY id DESC";
    $item_result = mysqli_query($link, $image_sql);
    $images = $item_result->fetch_all(MYSQLI_ASSOC);
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
                        <div class="col-md-9 col-sm-9">
                            <h1 class="h3 mb-0 text-gray-800">
                                <span class="fas fa-fw fa-clock"></span>
                                Auctions
                            </h1>
                        </div>
                        <?php if (array_filter($items) !== []) { ?>
                            <div class="col-md-2 col-sm-2 mt-2">
                                <div class="input-group">
                                    <input type="text" id="search-filter" class="form-control" 
                                    onkeyup="searchTitle()" placeholder="Search for title..">
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1 mt-2">
                                <div class="btn-group">
                                    <button class="btn btn-info" id="list">
                                        <i class="fas fa-list"></i>
                                    </button>
                                    <button class="btn btn-danger" id="grid">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="mb-0 mt-2">
                                Available Balance: <strong>₱ <?php echo number_format((float)$display_token, 2, '.', ''); ?></strong>
                            </h6>
                        </div>
                    </div>

                    <div id="products" class="row view-group">
                        <?php
                        if (array_filter($items) != []) {
                            foreach ($items as $item) { ?>
                             <div class="item col-lg-4">
                                    <div class="thumbnail card shadow mb-4">
                                        <div class="ml-4">
                                            <span class="badge badge-danger badge-counter">
                                                 Ends in 
                                                 <span class="counter" 
                                                    data-bidder-id="<?php echo $user_id; ?>"
                                                    data-auctioneer-id="<?php echo $item['auctioneer_id']; ?>"
                                                    data-item-id="<?php echo $item['item_id']; ?>"
                                                    data-bid-time="<?php echo $item['bidding_time']; ?>" 
                                                    data-end-time="<?php echo $item['end_time']; ?>">
                                                </span>
                                            </span>
                                        </div>
                                        <div class="card-header py-3 text-center">
                                            <div class="img-event">
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
                                                                    if ($data['item_id'] === $item['item_id']) {
                                                                        $imageURL = 'uploads/' . $data["file_name"];
                                                                ?>
                                                                        <div class="carousel-item <?php if ($id === 0) {
                                                                                                        echo "active";
                                                                                                    } ?>">
                                                                            <img class="d-block item-slider w-100 h-100 img-welcome" src="<?php echo $imageURL; ?>">
                                                                        </div>
                                                                <?php
                                                                    }
                                                                } ?>
                                                            </div>
                                                        </div>
                                                    <?php
                                            } ?>
                                            </div>
                                        </div>
                                        <div class="caption card-body card-body-display">
                                            <h5 class="group text-center card-title inner list-group-item-heading">
                                                <strong>
                                                    <?php echo $item['title']; ?>
                                                </strong>
                                            </h5>
                                            <p class="item-details group inner list-group-item-text">
                                                <?php echo $item['details']; ?>
                                            </p>
                                        </div>
                                        <div class="card-footer text-center">
                                            <div class="details">
                                                <div class="mb-2">
                                                    <?php  
                                                        $item_id = $item['item_id']; 
                                                        $result = mysqli_query($link, "SELECT MAX(current_bid) 
                                                            FROM bidding_sessions WHERE item_id =  $item_id");
                                                        $row = mysqli_fetch_array($result);
                                                    ?>
                                                    <span class="badge badge-primary badge-counter">
                                                        Current bid ₱ <?php echo number_format((float)$row[0], 2, '.', ''); ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <a class="btn btn-secondary" href="item-details.php?item_id=<?php echo $item['item_id']; ?>">
                                                        View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php }
                        }
                        ?>
                    </div>
                    <div class="row no-data-available <?php echo array_filter($items) === [] ? "" : "hidden";?>">
                        <div class="col-lg-12">
                            <div class="alert alert-primary" role="alert">
                                No data available
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
    <script src="js/counter.js"></script>
    <script src="js/string-trim.js"></script>
    <script>
        $(document).ready(function() {
            $('#list').click(function(event) {
                event.preventDefault();
                $('#products .item').addClass('list-group-item');
            });
            $('#grid').click(function(event) {
                event.preventDefault();
                $('#products .item').removeClass('list-group-item');
                $('#products .item').addClass('grid-group-item');
            });
        });

        function searchTitle() {
            var $input = $('#search-filter');
            var $filter = $input.val();
            var $products = $('#products');
            for (i = 0; i < $products.length; i++) {
                title = $products[i].querySelector(".card-body h5.card-title");
                if (title.innerText.indexOf($filter) > -1) {
                    $('.no-data-available').addClass('hidden');
                    $products[i].style.display = "";
                } else {
                    $('.no-data-available').removeClass('hidden');
                    $products[i].style.display = "none";
                }
            }
        }
    </script>
</body>

</html>