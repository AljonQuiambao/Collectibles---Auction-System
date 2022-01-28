<?php
    require_once 'config.php';

    //Delete
    $bidder_id = 0;
    $auctioneer_id =0;
    $item_id = 0;
    $current_bid = 0;
    
    if (isset($_POST['bidderId'])) {
        $bidder_id = mysqli_real_escape_string($link, $_POST['bidderId']);
    }

    if (isset($_POST['auctioneerId'])) {
        $auctioneer_id = mysqli_real_escape_string($link, $_POST['auctioneerId']);
    }

    if (isset($_POST['itemId'])) {
        $item_id = mysqli_real_escape_string($link, $_POST['itemId']);
    }

    if (isset($_POST['currentBid'])) {
        $current_bid = mysqli_real_escape_string($link, $_POST['currentBid']);
    }

     //for bidder
     $bidder = mysqli_query($link, "SELECT * FROM tokens WHERE user_id=" . $bidder_id);
     $bidderRecords = mysqli_num_rows($bidder);

    if ($bidderRecords > 0) {
        $final_amount = $bidder->fetch_array()['token'] - $current_bid;
        $query_update = "UPDATE tokens SET token = $final_amount 
            WHERE user_id = $bidder_id"; 

        $query_update_run = mysqli_query($link, $query_update); 
    } else {
        $final_amount = $bidder->fetch_array()['token'] - $current_bid;
        $query_update = "INSERT INTO tokens(user_id, token) 
            VALUES ('$bidder_id', '$final_amount')";

        $query_update_run = mysqli_query($link, $query_update); 
    }

    $bet_query_sql = "INSERT INTO winning_bet (item_id, bidder_id, auctioneer_id, token, date_win) 
            VALUES ('$item_id', '$bidder_id', '$auctioneer_id', '$current_bid', NOW())"; 
    $bet_run = mysqli_query($link, $bet_query_sql);

    //for bidder notitcaion
    $bidder_query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
            VALUES ('$bidder_id', '$item_id', 2, 'You are the highest and winner for the recent bidding', 0, NOW())"; 
    $bidder_run = mysqli_query($link, $bidder_query_sql);


    //for auctioneer notification
    $biddder = mysqli_query($link, "SELECT * FROM users WHERE id=" . $auctioneer_id);
    $biddderName = $biddder->fetch_array()['name'] ? $biddder->fetch_array()['name'] : 'bidder';

    $auctioneer_query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
        VALUES ('$auctioneer_id', '$item_id', 2, $biddderName. 'is the highest and winner for the recent bid.', 0, NOW())"; 
    $auctioneer_run = mysqli_query($link, $auctioneer_query_sql);

    if ($auctioneer_run) {
        echo 1;
        exit;
    }

    echo 0;
    exit;
?>