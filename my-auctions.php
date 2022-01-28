<?php
// Include config file
require_once "config.php";

// Initialize the session
session_start();

$current_user_id = trim($_SESSION["id"]);
//print($current_user_id);

$sql = "SELECT * FROM items
        JOIN item_status ON items.id = item_status.item_id
        JOIN users ON items.user_id = users.id
        -- JOIN item_status_enum ON items.status = item_status_enum.item_status_enum_id
        JOIN item_category ON items.category = item_category.category_id";

$result = mysqli_query($link, $sql);
$items = $result->fetch_all(MYSQLI_ASSOC);

//print_r($items);

function filterByUser($items, $user_id)
{
    return array_filter($items, function ($item) use ($user_id) {
        if ($item['user_id'] == $user_id) {
            return true;
        }
    });
}

$filterItems = filterByUser($items, $current_user_id);

function filterByStatus($items, $status)
{
    return array_filter($items, function ($item) use ($status) {
        if ($item['status'] == $status) {
            return true;
        }
    });
}

//print_r($items);

$pendingItems = filterByStatus($filterItems, 1);
$approvedItems = filterByStatus($filterItems, 2);
$rejectItems = filterByStatus($filterItems, 3);
$cancelItems = filterByStatus($filterItems, 6);

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

                    <h1 class="h3 mb-2 text-gray-800 mb-3">
                        <span class="fas fa-fw fa-search"></span>
                        My Auctions
                    </h1>

                    <div class="mt-2 mb-3">
                        <a href="add-item.php" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>
                            <span class="text">
                                Add Item
                            </span>
                        </a>
                    </div>

                    <div class="deleted-message alert alert-success alert-dismissable hidden">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        Item sucessfully deleted.
                    </div>

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

                    <section id="tabs" class="project-tab">
                        <nav>
                            <div class="nav nav-tabs nav-fill mb-4" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" style="text-align:left;" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
                                    <span class="fas fa-fw fa-clock"></span>
                                    Pending
                                </a>
                                <a class="nav-item nav-link" style="text-align:left;" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
                                    <span class="fas fa-fw fa-check"></span>
                                    Approved
                                </a>
                                <a class="nav-item nav-link" style="text-align:left;" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">
                                    <span class="fas fa-fw fa-ban"></span>
                                    Reject
                                </a>
                                <a class="nav-item nav-link" style="text-align:left;" id="nav-cancel-tab" data-toggle="tab" href="#nav-cancel" role="tab" aria-controls="nav-contact" aria-selected="false">
                                    <span class="fas fa-fw fa-window-close""></span>
                                    Cancel
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
                                                        <th class="col-2">Image</th>
                                                        <th class="col-2">Item</th>
                                                        <th class="col-2">Details</th>
                                                        <th class="col-2">Category</th>
                                                        <th class="col-2">Token</th>
                                                        <th class="col-1 hidden">Date Added</th>
                                                        <th class="col-1">Date Added</th>
                                                        <th class="col-3">
                                                            Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (array_filter($pendingItems) != []) {
                                                        foreach ($pendingItems as $item) { ?>
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
                                                                                            <img class="d-block item-slider w-100 h-100" src="<?php echo $imageURL; ?>">
                                                                                        </div>
                                                                                <?php
                                                                                    }
                                                                                } ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php
                                                                    } ?>
                                                                </td>
                                                                <td class="item-details"><?php echo $item['title']; ?></td>
                                                                <td class="item-details"><?php echo $item['details']; ?></td>
                                                                <td><?php echo $item['category']; ?></td>
                                                                <td>₱ <?php echo $item['token']; ?></td>
                                                                <td class="hidden"><?php echo $item['bid_time']; ?></td>
                                                                <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                <td>
                                                                    <input type="hidden" class="category" value="<?php echo $item['category']; ?>"/>
                                                                    <input type="hidden" class="item_id" value="<?php echo $item['item_id']; ?>"/>
                                                                    <input type="hidden" class="user_id" value="<?php echo $item['user_id']; ?>"/>
                                                                    <button class="btn btn-secondary mb-2" data-toggle="modal" data-target="#cancelItemModal" title="Cancel Item">
                                                                        Cancel
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
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <div class="card shadow mb-4">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered auction-table" id="approved-items" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th class="col-2">Image</th>
                                                        <th class="col-2">Item</th>
                                                        <th class="col-2">Details</th>
                                                        <th class="col-1">Category</th>
                                                        <th class="col-2">Token</th>
                                                        <th class="col-1 hidden">Bid date</th>
                                                        <th class="col-1">Bid date</th>
                                                        <th class="col-3">
                                                            Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (array_filter($approvedItems) != []) {
                                                        foreach ($approvedItems as $item) { ?>
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
                                                                                            <img class="d-block item-slider w-100 h-100" src="<?php echo $imageURL; ?>">
                                                                                        </div>
                                                                                <?php
                                                                                    }
                                                                                } ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php
                                                                    } ?>
                                                                </td>
                                                                <td class="item-details"><?php echo $item['title']; ?></td>
                                                                <td class="item-details"><?php echo $item['details']; ?></td>
                                                                <td><?php echo $item['category']; ?></td>
                                                                <td>₱ <?php echo $item['token']; ?></td>
                                                                <td class="hidden"><?php echo $item['bid_time']; ?></td>
                                                                <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                <td>
                                                                    <input type="hidden" class="category" value="<?php echo $item['category']; ?>"/>
                                                                    <input type="hidden" class="item_id" value="<?php echo $item['item_id']; ?>"/>
                                                                    <input type="hidden" class="user_id" value="<?php echo $item['user_id']; ?>"/>
                                                                    <button class="btn btn-success mb-2" data-toggle="modal" data-target="#readyToBidModal" title="Ready to Bid">
                                                                        Ready
                                                                    </button>
                                                                    <button class="btn btn-secondary mb-2" data-toggle="modal" data-target="#cancelItemModal" title="Cancel Item">
                                                                        Cancel
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
                                                        <th class="col-1 hidden">Bid date</th>
                                                        <th class="col-1">Bid date</th>
                                                        <th class="col-1">Admin Reason</th>
                                                        <th class="col-4">
                                                            Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (array_filter($rejectItems) != []) {
                                                        foreach ($rejectItems as $item) { ?>
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
                                                                                            <img class="d-block item-slider w-100 h-100" src="<?php echo $imageURL; ?>">
                                                                                        </div>
                                                                                <?php
                                                                                    }
                                                                                } ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php
                                                                    } ?>
                                                                </td>
                                                                <td class="item-details"><?php echo $item['title']; ?></td>
                                                                <td class="item-details"><?php echo $item['details']; ?></td>
                                                                <td><?php echo $item['category']; ?></td>
                                                                <td>₱ <?php echo $item['token']; ?></td>
                                                                <td class="hidden"><?php echo $item['bid_time']; ?></td>
                                                                <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                <td>
                                                                    <?php
                                                                        $item_id = $item['item_id'];
                                                                        $sql = "SELECT * FROM item_reason WHERE item_id = $item_id";
                                                                        $result = mysqli_query($link, $sql);
                                                                        $item_reason = $result->fetch_array(MYSQLI_ASSOC);
                                                                    ?>
                                                                    <?php echo $item_reason['reason']; ?>
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-secondary mb-2" data-toggle="modal" data-target="#cancelItemModal" title="Cancel Item">
                                                                        Cancel
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

                            <div class="tab-pane fade" id="nav-cancel" role="tabpanel" aria-labelledby="nav-contact-tab">
                                <div class="card shadow mb-4">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered auction-table" id="cancel-items" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th class="col-1">Image</th>
                                                        <th class="col-1">Item</th>
                                                        <th class="col-1">Details</th>
                                                        <th class="col-1">Category</th>
                                                        <th class="col-1">Token</th>
                                                        <th class="col-1 hidden">Bid date</th>
                                                        <th class="col-1">Bid date</th>
                                                        <th class="col-1">Admin Reason</th>
                                                        <th class="col-4">
                                                            Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (array_filter($cancelItems) != []) {
                                                        foreach ($cancelItems as $item) { ?>
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
                                                                                            <img class="d-block item-slider w-100 h-100" src="<?php echo $imageURL; ?>">
                                                                                        </div>
                                                                                <?php
                                                                                    }
                                                                                } ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php
                                                                    } ?>
                                                                </td>
                                                                <td class="item-details"><?php echo $item['title']; ?></td>
                                                                <td class="item-details"><?php echo $item['details']; ?></td>
                                                                <td><?php echo $item['category']; ?></td>
                                                                <td>₱ <?php echo $item['token']; ?></td>
                                                                <td><?php echo $item['bid_time']; ?></td>
                                                                <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                <td>
                                                                    <?php
                                                                        $item_id = $item['item_id'];
                                                                        $sql = "SELECT * FROM item_reason WHERE item_id = $item_id";
                                                                        $result = mysqli_query($link, $sql);
                                                                        $item_reason = $result->fetch_array(MYSQLI_ASSOC);
                                                                    ?>
                                                                    <?php echo $item_reason['reason']; ?>
                                                                </td>
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
                        </div>
                    </section>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>
    <?php include 'background.php'; ?>

    <!--Ready to Bid Modal-->
    <div class="modal fade" id="readyToBidModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Item to Bid</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="services.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group col-12">
                            <p>Are you sure this item is ready for bidding?</p>
                            <input class="item_id" type="hidden" name="item_id">
                            <input class="user_id" type="hidden" name="user_id">
                            <input class="category" type="hidden" name="category">
                            <label><strong>Set Bid Time</strong></label>
                            <input name="bidding_time" class="form-control" type="datetime-local" placeholder="Set Bid Time" required>
                            <label class="mt-2"><strong>Set End Time</strong></label>
                            <input name="end_time" class="form-control" type="datetime-local" placeholder="Set End Time" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input name="ready_to_bid" type="submit" class="btn btn-success" value="Add Item to Bid">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Cancel Modal-->
    <div class="modal fade" id="cancelItemModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Item to Cancel</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="services.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group col-12">
                            <p>Are you sure want to cancel this item?</p>
                            <input class="item_id" type="hidden" name="item_id">
                            <input class="user_id" type="hidden" name="user_id">
                            <input class="category" type="hidden" name="category">
                            <textarea name="reason" class="form-control" placeholder="Write your reason here..." rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                        <input name="cancel_bid_item" type="submit" class="btn btn-success" value="Submit">
                    </div>
            </form>
            </div>
        </div>
    </div>

    <?php include 'script.php'; ?>
    <script src="js/string-trim.js"></script>
    <script>
        $('#readyToBidModal').on('show.bs.modal', function(e) {
            // get information to update quickly to modal view as loading begins
            var $target = e.relatedTarget; //this holds the element who called the modal
            var $item_id = $($target).parents('tr').find('.item_id').val();
            var $user_id = $($target).parents('tr').find('.user_id').val();
            var $category = $($target).parents('tr').find('.category').val();

            //set the values
            $(this).find('.user_id').val($user_id);
            $(this).find('.item_id').val($item_id);
            $(this).find('.category').val($category);
        });

        $('#cancelItemModal').on('show.bs.modal', function(e) {
            // get information to update quickly to modal view as loading begins
            var $target = e.relatedTarget; //this holds the element who called the modal
            var $item_id = $($target).parents('tr').find('.item_id').val();
            var $user_id = $($target).parents('tr').find('.user_id').val();
            var $category = $($target).parents('tr').find('.category').val();

            //set the values
            $(this).find('.user_id').val($user_id);
            $(this).find('.item_id').val($item_id);
            $(this).find('.category').val($category);
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

                                $('.deleted-message').removeClass('hidden');
                            } 

                        }
                    });
                }

            });

        });
    </script>
</body>

</html>