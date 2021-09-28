<?php
    require_once 'config.php';

    //Delete
    $id = 0;
    $tableName = $_POST['tableName'];
    
    if (isset($_POST['id'])) {
        $id = mysqli_real_escape_string($link, $_POST['id']);
    }

    if ($id > 0) {
        // Check record exists
        $checkRecord = mysqli_query($link, "SELECT * FROM $tableName WHERE id=" . $id);
        $totalrows = mysqli_num_rows($checkRecord);

        if ($totalrows > 0) {
            // Delete item record
            $query = "DELETE FROM $tableName WHERE id=" . $id;
            mysqli_query($link, $query);
            echo 1;
            exit;
        } else {
            echo 0;
            exit;
        }
    }

    echo 0;
    exit;
?>