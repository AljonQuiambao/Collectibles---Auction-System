<?php
// Include config file
require_once "config.php";

$category_sql = "SELECT * FROM item_category";
$result = mysqli_query($link, $category_sql);
$categories = $result->fetch_all(MYSQLI_ASSOC);

$date_now = date("Y-m-d H:i:s");
$items_sql = "SELECT * FROM bidding_sessions
    JOIN items ON bidding_sessions.item_id = items.id";

$results = mysqli_query($link, $items_sql);
$items = $results->fetch_all(MYSQLI_ASSOC);

function filterByDate($items, $dateNow)
{
    return array_filter($items, function ($item) use ($dateNow) {
        if ($item['end_time'] >= $dateNow) {
            return true;
        }
    });
}

$items = filterByDate($items, $date_now);

$image_sql = "SELECT * FROM images ORDER BY id DESC";
$item_result = mysqli_query($link, $image_sql);
$images = $item_result->fetch_all(MYSQLI_ASSOC);

// print_r($items);
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'header.php' ?>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'navbar.php' ?>

                <div class="container-fluid">
                    <div class="jumbotron text-white jumbotron-image shadow" style="background-image: linear-gradient(rgba(0, 0, 0, 0.527), rgba(0, 0, 0, 0.5)),
                            url(img/background-template.jpg);
                            background-position: center; 
                            background-repeat: no-repeat; 
                            padding: 1rem;">
                        <h2 class="mb-4 text-uppercase">
                            Online Auction for the whole family
                        </h2>
                        <p class="mb-4">
                            Online Auction is where everyone goes to shop, sell, and give, while discovering variety and affordability.
                        </p>
                        <a href="landing.php" class="btn btn-secondary">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                            Back to Homepage
                        </a>
                        <a href="register.php" class="btn btn-primary">Register Now</a>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-9 col-sm-9">
                            <h1 class="h3 mb-0 text-gray-800">
                                Product Catalog
                            </h1>
                        </div>
                        <?php if (array_filter($items) !== []) { ?>
                            <div class="col-md-3 col-sm-3 mt-2">
                                <div class="input-group">
                                    <input type="text" id="search-filter" class="form-control" onkeyup="searchTitle()" placeholder="Search for title..">
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div id="products" class="row view-group">
                        <?php foreach ($items as $item) { ?>
                            <div class="col-lg-3">
                                <div class="card shadow mb-4">
                                    <div class="ml-2">
                                        <span class="badge badge-danger badge-counter">
                                            Ends in <span class="counter" data-bid-time="<?php echo $item['bidding_time']; ?>" data-end-time="<?php echo $item['end_time']; ?>"></span>
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
                                        <?php echo $item['details']; ?>
                                    </div>
                                    <div class="card-footer text-center">
                                        <span class="badge badge-primary badge-counter">
                                            Current bid ₱ <?php echo number_format($item['current_bid'], 2, '.', ''); ?>
                                        </span>
                                        <div class="mt-2">
                                            <a href="item-display-details.php?item_id=<?php echo $item['item_id']; ?>" class="btn btn-secondary">
                                                <span class="text">
                                                    View Details
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php include 'footer.php' ?>
            </div>
        </div>
        <?php include 'background.php' ?>
        <?php include 'script.php' ?>
        <script src="js/counter.js"></script>
        <script src="js/string-trim.js"></script>
        <script>
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

            $(function() {
                $(".feedback").addClass("hidden");
            });
        </script>
</body>

</html>