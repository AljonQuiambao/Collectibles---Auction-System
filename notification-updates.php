<?php
    require_once 'config.php';

    //Delete
    $biddder_id = 0;
    $auctioneerId =0;
    $item_id = 0;
    
    if (isset($_POST['bidderId'])) {
        $biddder_id = mysqli_real_escape_string($link, $_POST['bidderId']);
    }

    if (isset($_POST['auctioneerId'])) {
        $auctioneer_id = mysqli_real_escape_string($link, $_POST['auctioneerId']);
    }

    if (isset($_POST['itemId'])) {
        $item_id = mysqli_real_escape_string($link, $_POST['itemId']);
    }

    $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
            VALUES ('$biddder_id', '$item_id', 2, 'You are the highest and winner for the recent bidding', 0, NOW())"; 
    $run = mysqli_query($link, $query_sql);

    $biddder = mysqli_query($link, "SELECT * FROM users WHERE id=" . $auctioneer_id);
    $biddderName = $biddder->fetch_array()['name'] ? $biddder->fetch_array()['name'] : 'bidder';

    $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
        VALUES ('$auctioneer_id', '$item_id', 2, $biddderName. 'is the highest and winner for the recent bid.', 0, NOW())"; 
    $run = mysqli_query($link, $query_sql);

    if ($run) {
        echo 1;
        exit;
    }

    echo 0;
    exit;
?>