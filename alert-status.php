<?php
    require_once 'config.php';

    //Delete
    $user_id = 0;
    $type = $_POST['type'];
    
    if (isset($_POST['userId'])) {
        $user_id = mysqli_real_escape_string($link, $_POST['userId']);
    }

    if ($type == 1) {
        // Check record exists
        $checkRecord = mysqli_query($link, "SELECT * FROM users WHERE id=" . $user_id);
        $totalrows = mysqli_num_rows($checkRecord);

        if ($totalrows > 0) {
            // Delete item record
            $query = "UPDATE users SET alert_status = 1, alert_unread_count = 0 WHERE id=" . $user_id;
            mysqli_query($link, $query);
            echo 1;
            exit;
        } 
    }

    if ($type == 2) {
        // Check record exists
        $checkRecord = mysqli_query($link, "SELECT * FROM users WHERE id=" . $user_id);
        $totalrows = mysqli_num_rows($checkRecord);

        if ($totalrows > 0) {
            // Delete item record
            $query = "UPDATE users SET message_status = 1, message_unread_count = 0 WHERE id=" . $user_id;
            mysqli_query($link, $query);
            echo 1;
            exit;
        } 
    }

    echo 0;
    exit;
?>