<?php
// Include config file
require_once "config.php";

// Initialize the session
session_start();

$sql = "SELECT * FROM images ORDER BY id DESC";
$item_result = mysqli_query($link, $sql);
$images = $item_result->fetch_all(MYSQLI_ASSOC);

$param_id = trim($_SESSION["id"]);
$user_sql = "SELECT * FROM tokens WHERE user_id = $param_id";
$user_result = mysqli_query($link, $user_sql);
$user = $user_result->fetch_array(MYSQLI_ASSOC);


//Save auction data
if (isset($_POST['save_item_data'])) {
    $user_id = trim($_SESSION["id"]);
    $title = $_POST['title'];
    $category = $_POST['category'];
    $details = $_POST['details'];
    $token = $_POST['token'];

    foreach ($title as $index => $titles) {
        $s_title = $titles;
        $s_user_id = $user_id;
        $s_category = $category[$index];
        $s_details = $details[$index];
        $s_item_images = strtotime(date('y-m-d H:i')) . '_' . $user_id;
        $s_token = $token[$index];
        $s_status = 1;

        $query = "INSERT INTO items(user_id, title, category, details, item_images, token, status)
             VALUES ('$s_user_id', '$s_title', '$s_category', '$s_details', '$s_item_images', '$s_token', '$s_status')";
        $query_run = mysqli_query($link, $query);

        if ($query_run) {
            $item_id = $link->insert_id;

            //update item status
            $query_status = "INSERT INTO item_status(user_id, item_id, category, status)
                            VALUES ('$s_user_id', '$item_id', '$s_category', '$s_status')";

            mysqli_query($link, $query_status);

            // File upload configuration 
            $targetDir = "uploads/";
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

            $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = '';
            if (!empty($_FILES)) {
                $fileNames = array_filter($_FILES['files']['name']);
                foreach ($_FILES['files']['name'] as $key => $val) {
                    // File upload path 
                    $fileName = strtotime(date('y-m-d H:i')) . '_' . basename($_FILES['files']['name'][$key]);
                    $targetFilePath = $targetDir . $fileName;

                    // Check whether file type is valid 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                    if (in_array($fileType, $allowTypes)) {
                        // Upload file to server 
                        if (move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)) {
                            // Image db insert sql 
                            $insertValuesSQL .= "('" . $s_user_id . "', '" . $item_id . "', '" . $fileName . "', NOW()),";
                        } else {
                            $errorUpload .= $_FILES['files']['name'][$key] . ' | ';
                        }
                    } else {
                        $errorUploadType .= $_FILES['files']['name'][$key] . ' | ';
                    }
                }

                // Error message 
                $errorUpload = !empty($errorUpload) ? 'Upload Error: ' . trim($errorUpload, ' | ') : '';
                $errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . trim($errorUploadType, ' | ') : '';
                $errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;

                if (!empty($insertValuesSQL)) {
                    $insertValuesSQL = trim($insertValuesSQL, ',');
                    // Insert image file name into database 
                    $insert = $link->query("INSERT INTO images (auctioneer_id, item_id, file_name, uploaded_on) VALUES $insertValuesSQL");
                    if ($insert) {
                        $statusMsg = "Files are uploaded successfully." . $errorMsg;
                    } else {
                        $statusMsg = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $statusMsg = "Upload failed! " . $errorMsg;
                }
            } else {
                $statusMsg = 'Please select a file to upload.';
            }
            //update notifications here
            $query_sql = "Insert into notifications (user_id, item_id, type, notification, status, date_posted) 
                    values ('$user_id', '$item_id', 1, 'Your item ($s_title) is successfully added. 
                    just wait for approval of admin for this item.
                    you may check the status of the item on the my auction page.', 0, now())";
            $run = mysqli_query($link, $query_sql);

            //update notification status
            $query_update = "UPDATE users SET alert_status = 0 
                    WHERE id = $user_id"; 

            $query_update_run = mysqli_query($link, $query_update); 
        }
    }

    if ($query_run) {
        $_SESSION['success_status'] = "Auction items inserted sucessfully.";
        header("Location: my-auctions.php");
        exit();
    } else {
        $_SESSION['error_status'] = "Auction items not inserted. Please try again!";
        header("Location: my-auctions.php");
        exit();
    }
}
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
                        Add Items
                    </h1>

                    <p>
                        You can add multiple items just click 'Add More Item' button.
                    </p>

                    <div class="mt-4 mb-4">
                        <button id="add-more-items" type="button" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus"></i>
                            Add More Item
                        </button>
                        <input type="hidden" id="user_subscription" name="user_subscription" value="<?php echo $user['subscription'] ;?>"/>
                    </div>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                        <div id="content-panel" class="row"></div>
                        <div class="row">
                            <div class="col-12">
                                <a href="my-auctions.php" class="btn btn-secondary btn-lg">
                                    Cancel
                                </a>
                                <button type="submit" name="save_item_data" class="btn btn-primary btn-lg ml-2">
                                    <i class="fas fa-save"></i>
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <?php include 'background.php'; ?>
    <?php include 'script.php'; ?>

    <script>
        var addCols = function(num, cardLength) {
            for (var i = 1; i <= num; i++) {
                var $parentPanel = $('<div class="col-sm-4 mb-4"></div>');
                var $card = $('<div class="card">\
                        <div class="card-header">\
                            <div class="float-left"><strong>\
                                    <span class="title">Item\
                                    </span></strong></div>\
                            <div class="float-right btn-close">\
                                <button type="button" class="close">\
                                <span class="float-right">\
                                    <i class="fas fa-times-circle"></i>\
                                </span>\
                                </button>\
                            </div>\
                        </div>\
                        <div class="card-body">\
                            <div class="form-group">\
                                <input class="form-control" name="title[]" type="text" placeholder="Title" required>\
                            </div>\
                            <div class="form-group">\
                                <select name="category[]" class="form-control" required>\
                                    <option value="" selected disabled hidden>Category</option>\
                                    <option value="1">Albums</option>\
                                    <option value="2">Coins</option>\
                                    <option value="3">Paintings</option>\
                                    <option value="4">Sports Related</option>\
                                    <option value="5">Toys</option>\
                                </select>\
                            </div>\
                            <div class="form-group">\
                                <textarea name="details[]" class="form-control" rows="6" placeholder="Details" minlength="50" required></textarea>\
                            </div>\
                            <div class="form-group">\
                                <div class="input-group mb-3">\
                                    <div class="input-group-prepend">\
                                        <div class="input-group-text">₱</div>\
                                    </div>\
                                    <input name="token[]" class="form-control" type="number" placeholder="Price/ Token" required>\
                                    <div class="input-group-append">\
                                        <div class="input-group-text">.00</div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="row form-group">\
                                <div class="col-12 col-md-12">\
                                    <div class="control-group" id="fields">\
                                        <div class="controls">\
                                            <div class="entry input-group upload-input-group">\
                                                <input id="file-upload" class="form-control" name="files[]" type="file" accept="image/*" required>\
                                                <button class="btn btn-upload btn-success btn-add" type="button">\
                                                <i class="fa fa-plus"></i>\
                                            </button>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>');

                $('#file-upload').attr("name", "item_images");
                if (cardLength == 0) {
                    $card.find(".btn-close").addClass('hidden');
                }
                $card.find('.title').text('Item ' + (cardLength + 1));
                $card.appendTo($parentPanel);
                $parentPanel.appendTo('#content-panel');

                if ($('#user_subscription').val() != 2) {
                    if ($('.card').length > 4) {
                        $("#add-more-items").attr('disabled', 'disabled');
                    }
                }
            }

            $('.close').on('click', function(e) {
                e.stopPropagation();
                var $target = $(this).parents('.col-sm-4');

                $target.hide('slow', function() {
                    $target.remove();
                });

                if (($('.card').length - 1) >= 4) {
                    $("#add-more-items").removeAttr('disabled');
                }
            });
        };

        $(document).ready(function() {
            addCols('1', $('.card').length);
            return false;
        });

        $("#add-more-items").click(function() {
            addCols('1', $('.card').length);
            return false;
        });
    </script>

    <script>
        $(function() {
            $(document).on('click', '.btn-add', function(e) {
                e.preventDefault();

                var controlForm = $(this).parents('.controls:first'),
                    currentEntry = $(this).parents('.entry:first'),
                    newEntry = $(currentEntry.clone()).appendTo(controlForm);

                newEntry.find('input').val('');
                controlForm.find('.entry:not(:last) .btn-add')
                    .removeClass('btn-add').addClass('btn-remove')
                    .removeClass('btn-success').addClass('btn-danger')
                    .html('<span class="fa fa-trash"></span>');

                console.log($('.upload-input-group').length - 1);

                if ($('.upload-input-group').length > 4) {
                    controlForm.find('.entry:last .btn-add')
                        .attr('disabled', 'disabled');
                }
            }).on('click', '.btn-remove', function(e) {
                $(this).parents('.entry:first').remove();
                $('.btn-add').removeAttr('disabled');

                e.preventDefault();
                return false;
            });
        });
    </script>
</body>

</html>