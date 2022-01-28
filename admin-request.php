<?php
// Include config file
require_once "config.php";

// Initialize the session
session_start();

$sql = "SELECT * FROM items 
                JOIN item_status ON items.id = item_status.item_id
                -- JOIN item_reason on items.id = item_reason.item_id
                JOIN users ON items.user_id = users.id
                JOIN item_category ON items.category = item_category.category_id
                WHERE is_deleted = false";

$item_result = mysqli_query($link, $sql);
$items = $item_result->fetch_all(MYSQLI_ASSOC);

//print_r($items);

function filterByStatus($items, $status)
{
    return array_filter($items, function ($item) use ($status) {
        if ($item['status'] == $status) {
            return true;
        }
    });
}

$pendingItems = filterByStatus($items, 1);
$approvedItems = filterByStatus($items, 2);
$rejectItems = filterByStatus($items, 3);
$cancelItems = filterByStatus($items, 6);

$image_sql = "SELECT * FROM images ORDER BY id DESC";
$item_result = mysqli_query($link, $image_sql);
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
                        <span class="fas fa-fw fa-bars"></span>
                        Request Items
                    </h1>

                    <div class="row">
                        <div class="col-12">
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
                                <div class="alert alert-success alert-dismissable">
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
                                        <a class="nav-item nav-link active" style="text-align:left;" id="nav-pending-tab" data-toggle="tab" href="#nav-pending" role="tab" aria-controls="nav-pending" aria-selected="true">
                                            Pending Items
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-approved-tab" data-toggle="tab" href="#nav-approved" role="tab" aria-controls="nav-approved" aria-selected="false">
                                            Approved Items
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-reject-tab" data-toggle="tab" href="#nav-reject" role="tab" aria-controls="nav-reject" aria-selected="false">
                                            Reject Items
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-cancel-tab" data-toggle="tab" href="#nav-cancel" role="tab" aria-controls="nav-reject" aria-selected="false">
                                            Cancel Items
                                        </a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-pending" role="tabpanel" aria-labelledby="nav-pending-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="pending-items" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-3">Image</th>
                                                                <th class="col-2">Item</th>
                                                                <th class="col-1">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1 hidden">Bid Date</th>
                                                                <th class="col-1">Bid Date</th>
                                                                <th class="col-2">Seller Information</th>
                                                                <th class="col-2">
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
                                                                        <td><?php echo $item['title']; ?></td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td><?php echo $item['token']; ?></td>
                                                                        <td class="hidden"><?php echo $item['bid_time']; ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                        <td>
                                                                            <?php
                                                                            $user_id = $item['user_id'];
                                                                            $sql = "SELECT * FROM users WHERE id = $user_id";
                                                                            $result = mysqli_query($link, $sql);
                                                                            $currentUser = $result->fetch_array(MYSQLI_ASSOC);
                                                                            ?>
                                                                            <div>Name: <?php echo $currentUser["name"]; ?></div>
                                                                            <div>Age:
                                                                                <?php echo
                                                                                date_diff(
                                                                                    date_create($item['date_of_birth']),
                                                                                    date_create('now')
                                                                                )->y;
                                                                                ?>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <form action="add-methods.php" method="POST">
                                                                                <input class="user_id" type="hidden" name="user_id" value="<?php echo $item['user_id'] ?>">
                                                                                <input class="item_id" type="hidden" name="item_id" value="<?php echo $item['item_id'] ?>">
                                                                                <input class="category" type="hidden" name="category" value="<?php echo $item['category_id'] ?>">
                                                                                <input name="accept_item" type="submit" class="btn btn-success" value="Accept" onclick="return confirm('Are you sure you want to accept this item?')">
                                                                                <button type="button" class="btn btn-danger btn-reject mt-2" data-toggle="modal" data-target="#rejectModal" title="Reject">
                                                                                    Reject
                                                                                </button>
                                                                            </form>
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
                                    <div class="tab-pane fade" id="nav-approved" role="tabpanel" aria-labelledby="nav-approved-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="approved-items" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-2">Image</th>
                                                                <th class="col-1">Item</th>
                                                                <th class="col-2">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1 hidden">Bid Date</th>
                                                                <th class="col-1">Bid Date</th>
                                                                <th class="col-2">Seller Information</th>
                                                                <th class="col-1">
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
                                                                        <td><?php echo $item['title']; ?></td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td>₱ <?php echo $item['token']; ?></td>
                                                                        <td class="hidden"><?php echo $item['bid_time']; ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                        <td>
                                                                            <div>Name: <?php echo $item['name']; ?></div>
                                                                            <div>Age:
                                                                                <?php echo
                                                                                date_diff(
                                                                                    date_create($item['date_of_birth']),
                                                                                    date_create('now')
                                                                                )->y;
                                                                                ?>
                                                                            </div>
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
                                    <div class="tab-pane fade" id="nav-reject" role="tabpanel" aria-labelledby="nav-reject-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="reject-items" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-2">Image</th>
                                                                <th class="col-1">Item</th>
                                                                <th class="col-5">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1 hidden">Bid Date</th>
                                                                <th class="col-1">Bid Date</th>
                                                                <th class="col-1">Seller Information</th>
                                                                <th class="col-1">Reason for Rejecting</th>
                                                                <th class="col-1">
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
                                                                        <td><?php echo $item['title']; ?></td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td>₱ <?php echo $item['token']; ?></td>
                                                                        <td><?php echo $item['bid_time']; ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                        <td>
                                                                            <div>Name: <?php echo $item['name']; ?></div>
                                                                            <div>Age:
                                                                                <?php echo
                                                                                date_diff(
                                                                                    date_create($item['date_of_birth']),
                                                                                    date_create('now')
                                                                                )->y;
                                                                                ?>
                                                                            </div>
                                                                        </td>
                                                                        <td><?php echo $item['reason']; ?></td>
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

                                    <div class="tab-pane fade" id="nav-cancel" role="tabpanel" aria-labelledby="nav-cancel-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="cancel-items" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-2">Image</th>
                                                                <th class="col-1">Item</th>
                                                                <th class="col-5">Details</th>
                                                                <th class="col-1">Category</th>
                                                                <th class="col-1">Token</th>
                                                                <th class="col-1 hidden">Bid Date</th>
                                                                <th class="col-1">Bid Date</th>
                                                                <th class="col-1">Seller Information</th>
                                                                <th class="col-1">Reason for Cancelling</th>
                                                                <th class="col-1">
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
                                                                        <td><?php echo $item['title']; ?></td>
                                                                        <td class="item-details"><?php echo $item['details']; ?></td>
                                                                        <td><?php echo $item['category']; ?></td>
                                                                        <td><?php echo $item['token']; ?></td>
                                                                        <td>₱ <?php echo $item['bid_time']; ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                                        <td>
                                                                            <div>Name: <?php echo $item['name']; ?></div>
                                                                            <div>Age:
                                                                                <?php echo
                                                                                date_diff(
                                                                                    date_create($item['date_of_birth']),
                                                                                    date_create('now')
                                                                                )->y;
                                                                                ?>
                                                                            </div>
                                                                        </td>
                                                                        <?php
                                                                        $item_id = $item['item_id'];
                                                                        $sql = "SELECT * FROM item_reason WHERE item_id = $item_id";
                                                                        $result = mysqli_query($link, $sql);
                                                                        $item_reason = $result->fetch_array(MYSQLI_ASSOC);
                                                                        ?>
                                                                        <td>
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
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <?php include 'background.php'; ?>

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