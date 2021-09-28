<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    //save comment data
    if (isset($_POST['save_comment'])) {

        // Check if username is empty
        if (empty(trim($_POST["comment"]))) {
            $comment_err = '<div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    Comments is required.
                </div>';
        } 

        $user_id = $_POST['user_id'];
        $item_id = $_POST['item_id'];
        $comment = $_POST['comment'];

        $query = "INSERT INTO comments (user_id, item_id, comment) VALUES ('$user_id', '$item_id', '$comment')"; 
        $query_run = mysqli_query($link, $query);

        if ($query_run) {
            $_SESSION['success_status'] = "Comment sucessfully added";
            header("location: item-details.php?item_id=$item_id");
            exit();
        }
        else {
            $_SESSION['error_status'] = "Comment not added";
            header("location: item-details.php?item_id=$item_id");
            exit();
        }
    }

    //accept items
    if (isset($_POST['accept_item'])) {
        $user_id = $_POST['user_id'];
        $item_id = $_POST['item_id'];
        $category = $_POST['category'];
        
        $query = "UPDATE item_status SET status = 2 
                    WHERE user_id = $user_id && 
                    item_id = $item_id &&
                    category = $category"; 

        $query_run = mysqli_query($link, $query);
        if ($query_run) {

            //update notifications here
            $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                    VALUES ('$user_id', '$item_id', 2, 
                    'Your item is successfully accepted by admin.', 0, NOW())"; 

            $run = mysqli_query($link, $query_sql);

            $_SESSION['success_status'] = "Item accept sucessfully.";
            header("location: admin-request.php");
            exit();
        }
        else {
            $_SESSION['error_status'] = "Item not accept. Please try again!";
            header("location: admin-request.php");
            exit();
        }
    }

    //reject items
    if (isset($_POST['reject_item'])) {
        $user_id = $_POST['user_id'];
        $item_id = $_POST['item_id'];
        $category = $_POST['category'];
        $reason = $_POST['reason'];
        
        $query = "UPDATE item_status SET status = 3,
                    reason = '$reason'
                    WHERE user_id = $user_id && 
                    item_id = $item_id &&
                    category = $category"; 

        $query_run = mysqli_query($link, $query);
        if ($query_run) {
            
            //update notifications here
            $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                        VALUES ('$user_id', '$item_id', 3, 'Unfortunately, Your item is rejected by admin.', 0, NOW())"; 
            $run = mysqli_query($link, $query_sql);

            $_SESSION['status'] = "Item rejected sucessfully.";
            header("location: admin-request.php");
            exit();
        }
        else {
            $_SESSION['status'] = "Item not rejected. Please try again.";
            header("location: admin-request.php");
            exit();
        }
    }
?>