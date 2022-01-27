<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $current_user_id = trim($_SESSION["id"]);
    $sql = "SELECT * FROM items 
        JOIN item_status ON items.id = item_status.item_id
        JOIN users ON items.user_id = users.id
        JOIN item_category ON items.category = item_category.category_id
        -- JOIN item_status_enum ON items.status = item_status_enum.item_status_enum_id
        WHERE is_deleted = false";

    $result = mysqli_query($link, $sql);
    $items = $result->fetch_all(MYSQLI_ASSOC);

    function filterByUser($items, $user_id)
    {
        return array_filter($items, function ($item) use ($user_id) {
            if ($item['user_id'] == $user_id) {
                return true;
            }
        });
    }

    $filterItems = filterByUser($items, $current_user_id);

    // print_r($items);

    function filterByStatus($items, $status)
    {
        return array_filter($items, function ($item) use ($status) {
            if ($item['status'] == $status) {
                return true;
            }
        });
    }

    $soldItems = filterByStatus($filterItems, 5);

    print_r($soldItems);

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
                        <i class="fas fa-tag"></i>
                        Sold Items
                    </h1>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-bordered auction-table" id="sold-items" width="100%" cellspacing="0">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="col-2">Image</th>
                                            <th class="col-1">Item</th>
                                            <th class="col-2">Details</th>
                                            <th class="col-1">Category</th>
                                            <th class="col-1">Token</th>
                                            <th class="col-2">Bid date</th>
                                            <th class="col-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($soldItems as $item) {  ?>                            
                                            <tr class="text-center">
                                                <td>
                                                <div id="image-container">
                                                    <?php
                                                        $result = array();
                                                        foreach ($images as $element) {
                                                            $result[$element['item_id']][] = $element;
                                                        }        
                                                        
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
                                                    <input name="auctioneer_id" type="hidden" value="<?php echo $param_id; ?>">
                                                    <input name="bidder_id" type="hidden" value="<?php echo $item['user_id']; ?>">
                                                    <input name="item_id" type="hidden" value="<?php echo  $item['id']; ?>">
                                                    <?php echo $item['title']; ?>
                                                </td>
                                                <td class="item-details"><?php echo $item['details']; ?></td>
                                                <td><?php echo $item['category']; ?></td>
                                                <td>
                                                    ₱ <?php echo number_format((float)$item['token'], 2, '.', ''); ?>
                                                </td>
                                                <td><?php echo date('m-d-Y', strtotime($item['bid_time'])); ?></td>
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
    <?php include 'background.php';  ?>

    <!--Submit Proof Modal-->
    <div class="modal fade" id="submitProofModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Proof of Delivery</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group col-12">
                        <input name="type" value="2" class="form-control" type="hidden">
                        <input name="auctioneer_id" class="form-control" type="hidden">
                        <input name="item_id" class="form-control" type="text">
                        <input name="bidder_id" class="form-control" type="text">
                    </div>
                    <div class="form-group col-12">
                        <label><strong>Delivery Date</strong></label>
                        <input name="date_received" class="form-control" type="date" placeholder="Set Bid Time">
                    </div>
                    <div class="form-group col-12">
                        <label><strong>Proof of Delivery</strong></label>
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
            </div>
        </div>
    </div>                                      
    </div>
    <?php include 'script.php';  ?>
    <script src="js/string-trim.js"></script>
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
</body>

</html>