<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $sql = "SELECT * FROM item_proof
                JOIN items ON item_proof.item_id = items.id
                JOIN users ON item_proof.bidder_id = users.id
                JOIN item_category ON items.category = item_category.category_id
                WHERE is_deleted = false";

    $item_result = mysqli_query($link, $sql);
    $items = $item_result->fetch_all(MYSQLI_ASSOC);

    //print_r($items);
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
                        <span class="fas fa-fw fa-check"></span>
                        Payment Confirmation
                    </h1>

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
                                            Bidders
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
                                            Auctioneers
                                        </a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="pending-items" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-1">Item</th>
                                                                <th class="col-2">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1">Bid Date</th>
                                                                <th class="col-2">Bidder</th>
                                                                <th class="col-2">Proof</th>
                                                                <th class="col-2">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($items) != []) {
                                                                foreach ($items as $item) { ?>
                                                                    <tr class="text-center">                                                                    
                                                                        <td><?php echo $item['title']; ?></td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td><?php echo $item['token']; ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                        <td>
                                                                            <div><?php echo $item['name']; ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 12rem;"
                                                                                src="data:image/png;charset=utf8;base64,<?php echo base64_encode($item['proof']); ?>" /> 
                                                                        </td>
                                                                        <td>
                                                                            <form action="services.php" method="POST">
                                                                                <input class="user_id" type="hidden" name="user_id" value="<?php echo $item['user_id'] ?>">
                                                                                <input class="item_id" type="hidden" name="item_id" value="<?php echo $item['item_id'] ?>">
                                                                                <input class="category" type="hidden" name="category" value="<?php echo $item['category_id'] ?>">
                                                                                <input class="amount" type="hidden" name="user_id" value="<?php echo $item['token'] ?>">
                                                                                <input name="confirm_payment_bidder" type="submit" class="btn btn-success" value="Confirm">
                                                                            </form>
                                                                            <button class="btn btn-danger delete mt-2" data-id="<?php echo $item['item_id']; ?>" data-table-name="item_proof" title="Delete">
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
                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="approved-items" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-1">Item</th>
                                                                <th class="col-3">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1">Bid Date</th>
                                                                <th class="col-2">Seller Information</th>
                                                                <th class="col-2">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($items) != []) {
                                                                foreach ($items as $item) { ?>
                                                                    <tr class="text-center">
                                                                        <td><?php echo $item['title']; ?></td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td><?php echo $item['token']; ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                        <td>
                                                                            <div>Name: <?php echo $item['name']; ?></div>
                                                                            <div>Age:
                                                                                <?php echo
                                                                                date_diff(
                                                                                    date_create($item['date_of_birth']),
                                                                                    date_create(date_default_timezone_get())
                                                                                )->y;
                                                                                ?>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <form action="services.php" method="POST">
                                                                                <input class="user_id" type="hidden" name="user_id" value="<?php echo $item['user_id'] ?>">
                                                                                <input class="item_id" type="hidden" name="item_id" value="<?php echo $item['item_id'] ?>">
                                                                                <input class="category" type="hidden" name="category" value="<?php echo $item['category_id'] ?>">
                                                                                <input name="confirm_payment_auctioneer" type="submit" class="btn btn-success" value="Confirm">
                                                                            </form>
                                                                            <button type="button" class="btn btn-danger mt-2" data-toggle="modal" data-target="#deleteModal" title="Delete">
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

    <!--Reject Modal-->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
                        <input class="hidden" name="user_id" >
                        <input class="hidden" name="item_id" >
                        <input class="hidden" name="category" >
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
        $(document).on('click', '.btn-reject', function() {
            var $parent = $(this).parent();
            var $parent_user_id = $($parent).find('.user_id').val();
            var $parent_item_id = $($parent).find('.item_id').val();
            var $category = $($parent).find('.category').val();

            //set value
            $('#rejectModal').find('input[name="user_id"]').val($parent_user_id);
            $('#rejectModal').find('input[name="item_id"]').val($parent_item_id);
            $('#rejectModal').find('input[name="category"]').val($category);
        });
    </script>
    <script src="js/string-trim.js"></script>
    
</body>
</html>