<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $param_id = trim($_SESSION["id"]);
    $bid_history_sql = "SELECT * FROM bidding_history 
            JOIN items ON items.id = bidding_history.item_id
            JOIN item_status ON items.id = item_status.item_id
            JOIN item_category ON items.category = item_category.category_id
            WHERE bidder_id = $param_id ORDER BY date_bid DESC";

    $bid_history_result = mysqli_query($link, $bid_history_sql);
    $bid_history_items = $bid_history_result->fetch_all(MYSQLI_ASSOC);

    $payment_confirmation_sql = "SELECT * FROM bidding_history
                JOIN items ON items.id = bidding_history.item_id
                JOIN item_status ON items.id = item_status.item_id
                JOIN item_category ON items.category = item_category.category_id
                JOIN payment_confirmation ON items.id = payment_confirmation.item_id
                WHERE bidder_id = $param_id ORDER BY date_bid DESC"; 

    $payment_confirmation_result = mysqli_query($link, $payment_confirmation_sql);
    $payment_confirmation_items = $bid_history_result->fetch_all(MYSQLI_ASSOC);

    function filterByStatus($items, $status)
    {
        return array_filter($items, function ($item) use ($status) {
            if ($item['status'] == $status) {
                return true;
            }
        });
    }

    $date_now = date("Y-m-d H:i:s");
    function filterByDate($items, $dateNow)
    {
        return array_filter($items, function ($item) use ($dateNow) {
            if ($item['date_bid'] >= $dateNow) {
                return true;
            }
        });
    }

    $myBidItems = filterByDate($bid_history_items, $date_now);
    $bidHistoryItems = $bid_history_items;
    $wonItems = filterByStatus($bid_history_items, 5);
    $boughtItems = $payment_confirmation_items;

    $query_sql = "SELECT * FROM images ORDER BY id DESC";
    $item_result = mysqli_query($link, $query_sql);
    $images = $item_result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include 'sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <?php include 'navbar.php'; ?>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800 mb-3">
                        <i class="fas fa-user-tag"></i>
                        My Bidding
                    </h1>

                    <p>
                    <div class="alert alert-info">
                        <strong>'My Bids'</strong> section is the list of items that you engaged to bid that is active for bidding,
                        <strong>'Bidding History'</strong> are the items that the bidding session is ended while <strong>'Won Items'</strong> are items that you are won from the bidding.
                    </div>
                    </p>

                    <div class="row">
                        <div class="col-12">
                            <?php
                            if (isset($_SESSION['status'])) {
                            ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $_SESSION['status']; ?>
                                </div>
                            <?php
                                unset($_SESSION['status']);
                            }
                            ?>
                            <section id="tabs" class="project-tab">
                                <nav>
                                    <div class="nav nav-tabs nav-fill mb-4" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link active" style="text-align:left;" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
                                            <span class="fas fa-fw fa-search-dollar"></span>
                                            Ongoing Bidding
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
                                            <span class="fas fa-fw fa-user-clock"></span>
                                            Bidding History
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">
                                            <span class="fas fa-fw fa-tags"></span>
                                            Won Items
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-bought-tab" data-toggle="tab" href="#nav-bought" role="tab" aria-controls="nav-contact" aria-selected="false">
                                            <span class="fas fa-fw fa-tags"></span>
                                            Bought Items
                                        </a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="ongoing-bidding" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-4">Image</th>
                                                                <th class="col-3">Item</th>
                                                                <th class="col-2">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1">Bid Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($myBidItems) != []) {
                                                                foreach ($myBidItems as $item) { ?>
                                                                    <tr class="text-center">
                                                                        <td>
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
                                                                        </td>
                                                                        <td>
                                                                            <a href="item-details.php?item_id=<?php echo $item['item_id']; ?>" target="_blank">
                                                                                <?php echo $item['title']; ?>
                                                                            </a>
                                                                        </td>
                                                                        <td class="item-details" style="word-wrap: break-word;"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td>₱ <?php echo number_format((float)$item['bid_token'], 2, '.', ''); ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['date_bid'])); ?></td>
                                                                    </tr>

                                                            <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="bidding-history" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-1">Item</th>
                                                                <th class="col-3">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1">Bid Date</th>
                                                                <th class="col-1">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($bidHistoryItems) != []) {
                                                                foreach ($bidHistoryItems as $item) { ?>
                                                                    <tr class="text-center">
                                                                        <td>
                                                                            <a href="item-details.php?item_id=<?php echo $item['item_id']; ?>" target="_blank">
                                                                                <?php echo $item['title']; ?>
                                                                            </a>
                                                                        </td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td>₱ <?php echo number_format((float)$item['bid_token'], 2, '.', ''); ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['date_bid'])); ?></td>
                                                                        <td>
                                                                            <button class="btn btn-danger delete" data-id="<?php echo $item['item_id']; ?>" data-table-name="items" title="Delete">
                                                                                Delete
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                            <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="reject-items" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-1">Image</th>
                                                                <th class="col-1">Item</th>
                                                                <th class="col-1">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1">Bid date</th>
                                                                <th class="col-4">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($wonItems) != []) {
                                                                foreach ($wonItems as $item) { ?>
                                                                    <tr class="text-center">
                                                                        <td>
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
                                                                                                    <img class="table-slider w-100 h-100 img-welcome" src="<?php echo $imageURL; ?>">
                                                                                                </div>
                                                                                        <?php
                                                                                            }
                                                                                        } ?>
                                                                                    </div>
                                                                                </div>
                                                                            <?php
                                                                            } ?>
                                                                        </td>
                                                                        <td class="title">
                                                                            <input name="auctioneer_id" type="hidden" value="<?php echo $item['user_id']; ?>">
                                                                            <input name="bidder_id" type="hidden" value="<?php echo $param_id; ?>">
                                                                            <input name="item_id" type="hidden" value="<?php echo  $item['item_id']; ?>">
                                                                            <a href="item-details.php?item_id=<?php echo $item['item_id']; ?>" target="_blank">
                                                                                <?php echo $item['title']; ?>
                                                                            </a>
                                                                        </td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td>₱ <?php echo number_format((float)$item['bid_token'], 2, '.', ''); ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['date_bid'])); ?></td>
                                                                        <td>
                                                                            <button id="btn-submit" class="btn btn-success mb-2" data-toggle="modal" data-target="#submitProofModal" title="Ready to Bid">
                                                                                Submit Proof
                                                                            </button>
                                                                            <button class="btn btn-danger delete" data-id="<?php echo $item['item_id']; ?>" data-table-name="items" title="Delete">
                                                                                Delete
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                            <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="nav-bought" role="tabpanel" aria-labelledby="nav-bought-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="bought-items" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-1">Image</th>
                                                                <th class="col-1">Item</th>
                                                                <th class="col-1">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1">Bid date</th>
                                                                <th class="col-1">Seller Information</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($boughtItems) != []) {
                                                                foreach ($boughtItems as $item) { ?>
                                                                    <tr class="text-center">
                                                                        <td>
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
                                                                                                    <img class="table-slider w-100 h-100 img-welcome" src="<?php echo $imageURL; ?>">
                                                                                                </div>
                                                                                        <?php
                                                                                            }
                                                                                        } ?>
                                                                                    </div>
                                                                                </div>
                                                                            <?php
                                                                            } ?>
                                                                        </td>
                                                                        <td class="title">
                                                                            <input name="auctioneer_id" type="hidden" value="<?php echo $item['user_id']; ?>">
                                                                            <input name="bidder_id" type="hidden" value="<?php echo $param_id; ?>">
                                                                            <input name="item_id" type="hidden" value="<?php echo  $item['id']; ?>">
                                                                            <a href="item-details.php?item_id=<?php echo $item['id']; ?>" target="_blank">
                                                                                <?php echo $item['title']; ?>
                                                                            </a>
                                                                        </td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td>₱ <?php echo number_format((float)$item['bid_token'], 2, '.', ''); ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['date_bid'])); ?></td>
                                                                        <td>
                                                                            <?php
                                                                                $auctioneer_id = $item['user_id']; 
                                                                                $sql = "SELECT * FROM users WHERE id = $auctioneer_id";
                                                                                $result = mysqli_query($link, $sql);
                                                                                $currentUser = $result->fetch_array(MYSQLI_ASSOC);
                                                                            ?>
                                                                        <div>Name:
                                                                            <?php
                                                                                echo $currentUser['name'];
                                                                            ?>
                                                                        </div>
                                                                        <div>Age:
                                                                            <?php echo
                                                                                date_diff(date_create($currentUser['date_of_birth']),
                                                                                date_create('now'))->y;
                                                                            ?>
                                                                        </div>
                                                                        </td>
                                                                    </tr>
                                                            <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <?php include 'background.php'; ?>

    <!--Submit Proof Modal-->
    <div class="modal fade" id="submitProofModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Proof of Item Received</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="services.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group col-12">
                            <input name="type" value="1" class="form-control" type="hidden">
                            <input name="auctioneer_id" class="form-control" type="hidden">
                            <input name="item_id" class="form-control" type="hidden">
                            <input name="bidder_id" class="form-control" type="hidden">
                        </div>
                        <div class="form-group col-12">
                            <label><strong>Received Date</strong></label>
                            <input name="date_received" class="form-control" type="date" required>
                        </div>
                        <div class="form-group col-12">
                            <label><strong>Proof of Item Received</strong></label>
                            <div class="control-group" id="fields">
                                <div class="controls">
                                    <div class="entry input-group">
                                        <input class="form-control" type="file" name="proof" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input name="submit_proof" class="btn btn-success" type="submit" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Reject Modal-->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="add-methods.php" method="POST">
                    <div class="modal-body">
                        Are you sure want to reject this item?
                        <input class="hidden" name="user_id">
                        <input class="hidden" name="item_id">
                        <input class="hidden" name="category">
                        <textarea name="reason" class="form-control" placeholder="Write your reason here..." rows="5" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input name="reject_item" type="submit" class="btn btn-danger" value="Reject">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'script.php'; ?>
    <script>
        $(document).on('click', '#btn-submit', function() {
            var $parent = $('.title');
            var $parent_auctioneer_id = $($parent).find('input[name="auctioneer_id"]').val();
            var $parent_item_id = $($parent).find('input[name="item_id"]').val();
            var $parent_bidder_id = $($parent).find('input[name="bidder_id"]').val();

            //set value
            $('#submitProofModal').find('input[name="auctioneer_id"]').val($parent_auctioneer_id);
            $('#submitProofModal').find('input[name="item_id"]').val($parent_item_id);
            $('#submitProofModal').find('input[name="bidder_id"]').val($parent_bidder_id);
        });
    </script>
    <script>
        $(document).ready(function() {
            // Delete 
            $('.delete').click(function() {
                var el = this;

                var deleteId = $(this).data('id');
                var tableName = $(this).data('table-name');

                var confirmalert = confirm("Are you sure you want to delete?");
                if (confirmalert == true) {
                    // AJAX Request
                    $.ajax({
                        url: 'remove.php',
                        type: 'POST',
                        data: {
                            id: deleteId,
                            tableName: tableName
                        },
                        success: function(response) {
                            if (response == 1) {
                                // Remove row from HTML Table
                                $(el).closest('tr').css('background', 'tomato');
                                $(el).closest('tr').fadeOut(800, function() {
                                    $(this).remove();
                                });
                            } else {
                                alert('Invalid data id.');
                            }

                        }
                    });
                }

            });

        });
    </script>
    <script src="js/string-trim.js"></script>

</body>

</html>