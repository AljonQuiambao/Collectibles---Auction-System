<?php 
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $sql = "SELECT * FROM items
        JOIN item_status ON items.id = item_status.item_id
        JOIN users ON items.user_id = users.id
        JOIN item_status_enum ON items.status = item_status_enum.item_status_enum_id
        JOIN item_category ON items.category = item_category.category_id";

    $result = mysqli_query($link, $sql);
    $items = $result->fetch_all(MYSQLI_ASSOC);
    
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
                        if (isset($_SESSION['success_status'])) 
                        {
                            ?>
                                <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                   <?php echo $_SESSION['success_status'];?>
                                </div>
                            <?php 
                            unset($_SESSION['success_status']);
                        }

                        if (isset($_SESSION['error_status'])) 
                        {
                            ?>
                                <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                   <?php echo $_SESSION['error_status'];?>
                                </div>
                            <?php 
                            unset($_SESSION['error_status']);
                        }
                    ?>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered auction-table" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="col-2">Image</th>
                                            <th class="col-1">Item</th>
                                            <th class="col-2">Details</th>
                                            <th class="col-1">Category</th>
                                            <th class="col-1">Token</th>
                                            <th class="col-2">Bid date</th>
                                            <th class="col-1">Status</th>
                                            <th class="col-2">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item) {  ?>                            
                                            <tr class="text-center">
                                                <td>
                                                    <div id="image-container">
                                                            <?php
                                                                $result = array();
                                                                foreach ($images as $element) {
                                                                    $result[$element['item_id']][] = $element;
                                                                }        
                                                                
                                                                // print_r($images['item_id']);

                                                                foreach ($result as $key => $image) { 
                                                                    ?>
                                                                    <div style="width: 100%;" id="<?php echo $key; ?>" class="carousel slide" data-ride="carousel">
                                                                        <div class="carousel-inner">
                                                                            <?php
                                                                            foreach ($image as $id => $data) {
                                                                                if ($data['item_id'] === $item['item_id'])  {
                                                                                    $imageURL = 'uploads/' . $data["file_name"];
                                                                                    ?>
                                                                                    <div class="carousel-item <?php if ($id === 0) { echo "active";} ?>">
                                                                                        <img class="d-block slider w-100 h-100" src="<?php echo $imageURL; ?>">
                                                                                    </div>
                                                                                <?php 
                                                                                }  
                                                                            } ?>
                                                                        </div>
                                                                    </div>
                                                                <?php 
                                                            } ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input class="item_id" type="hidden" value="<?php echo  $item['item_id']; ?>">
                                                    <input class="user_id" type="hidden" value="<?php echo  $item['user_id']; ?>">
                                                    <input class="category" type="hidden" value="<?php echo  $item['category_id']; ?>">
                                                    <?php echo $item['title']; ?>
                                                </td>
                                                <td class="item-details"><?php echo $item['details']; ?></td>
                                                <td><?php echo $item['category']; ?></td>
                                                <td><?php echo intval($item['token']); ?></td>
                                                <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
                                                <td><?php echo $item['status']; ?></td>
                                                <td>
                                                    <?php if ($item['status'] === 2) { ?>
                                                        <button class="btn btn-success" data-toggle="modal" data-target="#readyToBidModal" title="Ready to Bid">
                                                            Ready
                                                        </button>
                                                    <?php } ?>
                                                    <button class="btn btn-secondary" data-toggle="modal" data-target="#cancelItemModal" title="Cancel Item">
                                                         Cancel
                                                    </button>
                                                    <button class="btn btn-danger delete" data-id="<?php echo $item['item_id']; ?>" data-table-name="items" title="Delete">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php
                                            }
                                        ?>
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
                <div class="modal-body">
                    <div class="form-group col-12">
                        <p>Are you sure want to cancel this item?</p>
                        <textarea name="reason" class="form-control" placeholder="Write your reason here..." rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'script.php'; ?>
    <script src="js/string-trim.js"></script>
    <script>
        $('#readyToBidModal').on('show.bs.modal', function (e) {
            // get information to update quickly to modal view as loading begins
            var $target = e.relatedTarget;//this holds the element who called the modal
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
                            } else {
                                alert('Invalid data id.');
                            }

                        }
                    });
                }

            });

        });
    </script>
</body>

</html>