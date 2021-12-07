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

    //get items ready for bid
    $sql = "SELECT * FROM items 
            JOIN bidding_sessions ON items.id = bidding_sessions.item_id
            JOIN item_status ON items.id = item_status.item_id
            JOIN users ON items.user_id = users.id
            JOIN item_images ON items.id = item_images.item_id
            JOIN item_category ON items.category = item_category.category_id";

    $item_result = mysqli_query($link, $sql);
    $items = $item_result->fetch_all(MYSQLI_ASSOC);

    function filterByStatus($items, $status)
    {
        return array_filter($items, function ($item) use ($status) {
            if ($item['status'] == $status) {
                return true;
            }
        });
    }

    $items = filterByStatus($items, 4);

    // print_r($items);

    $param_id = trim($_SESSION["id"]);
    $token_sql = "SELECT * FROM tokens WHERE id = $param_id";
    $token_result = mysqli_query($link, $token_sql);
    $token = $token_result->fetch_array(MYSQLI_ASSOC);
    if ($token['token'] !== '') {
        $display_token = number_format($token['token']);
    }
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
                                Available Balance: <strong> <?php echo $display_token; ?></strong> token(s)
                            </h6>
                        </div>
                    </div>

                    <div id="products" class="row view-group">
                        <?php
                        if (array_filter($items) != []) {
                            foreach ($items as $item) { ?>
                             <div class="item col-lg-3">
                                    <div class="thumbnail card shadow mb-4">
                                        <div class="ml-4">
                                            <span class="badge badge-danger badge-counter">
                                                Ends in <span class="counter" data-date-time="<?php echo $item['bidding_time']; ?>"></span>
                                            </span>
                                        </div>
                                        <div class="card-header py-3 text-center">
                                            <div class="img-event">
                                            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 18rem;"
                                                         src="data:image/png;charset=utf8;base64,<?php echo base64_encode($item['image']); ?>" /> 
                                                <!-- <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 18rem;"
                                                    src="img/product_thumbnail.jpg" alt="..."> -->
                                            </div>
                                        </div>
                                        <div class="caption card-body">
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
                                                    <span class="badge badge-primary badge-counter">
                                                        Current bid <?php echo intval($item['current_bid']); ?>
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