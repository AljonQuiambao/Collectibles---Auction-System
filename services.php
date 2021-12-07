<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    if (isset($_POST['ready_to_bid'])) {
        $item_id = $_POST['item_id'];
        $auctioneer_id = $_POST['user_id'];
        $category = $_POST['category'];
        $bidding_time = $_POST['bidding_time'];

        $query = "INSERT INTO bidding_sessions(item_id, auctioneer_id, bidding_time) VALUES ('$item_id', '$auctioneer_id', '$bidding_time')"; 
        $query_run = mysqli_query($link, $query);

        if ($query_run) {
            $query_status = "UPDATE item_status SET status = 4 
                            WHERE user_id = $auctioneer_id && 
                            item_id = $item_id &&
                            category = $category"; 
            mysqli_query($link, $query_status);

            if ($query_status) {
                 //update notifications here
                        $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                        VALUES ('$auctioneer_id', '$item_id', 2, 'Your item is now available for bidding. Bidders can now see and start to bid.', 0, NOW())"; 
                $run = mysqli_query($link, $query_sql);

                $_SESSION['status'] = "Your item is now available for bidding. Bidders can now see and start to bid.";
                header("location: my-auctions.php");
                exit();
            } else {
                return false;
            }
        } else {
            $_SESSION['status'] = "Item is not added for bidding. Please try again!";
            header("location: my-auctions.php");
            exit();
        }
    }

    if (isset($_POST['counter_submit'])) { 
        $auctioneer_id = $_POST['auctioneer_id'];
        $bidder_id = $_POST['bidder_id'];
        $item_id = $_POST['item_id'];
        $category = $_POST['category'];

        $query = "INSERT INTO bid_items(auctioneer_id, bidder_id, item_id, status, date_bid_end) 
            VALUES ('$auctioneer_id', '$bidder_id', '$item_id', 2, NOW())"; 
        $query_run = mysqli_query($link, $query);

        if ($query_run) {
            $query_update = "UPDATE item_status SET status = 5 
                    WHERE user_id = $auctioneer_id && 
                    item_id = $item_id &&
                    category = $category"; 

            $query_update_run = mysqli_query($link, $query_update);

            header("location: item-details.php?item_id=$item_id");
            exit();
        }
        else {
            header("location: item-details.php?item_id=$item_id");
            exit();
        }
    }

    if (isset($_POST['submit_proof'])) { 
        $type = $_POST['type'];
        $auctioneer_id = $_POST['auctioneer_id'];
        $bidder_id = $_POST['bidder_id'];
        $item_id = $_POST['item_id'];
        $proof = $_POST['proof'];
        $date_received = $_POST['date_received'];
        $imgContent = '';

        if(!empty($_FILES["proof"]["name"])) { 
            // Get file info 
            $fileName = basename($_FILES["proof"]["name"]); 
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
             
            // Allow certain file formats 
            $allowTypes = array('jpg','png','jpeg','gif'); 
            if(in_array($fileType, $allowTypes)){ 
                $image = $_FILES['proof']['tmp_name']; 
                $imgContent = addslashes(file_get_contents($image)); 
            }
        }

        $query = "INSERT INTO item_proof(auctioneer_id, bidder_id, item_id, proof, type, date_received, date_submit) 
            VALUES ('$auctioneer_id', '$bidder_id', '$item_id', '$imgContent', '$type', '$date_received', NOW())"; 
        $query_run = mysqli_query($link, $query);

        if ($query_run) {
            //for bidder
            $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                 VALUES ('$bidder_id', '$item_id', 2, 'Your proof already submitted to administrator.', 0, NOW())"; 
            $run = mysqli_query($link, $query_sql);

            $_SESSION['status'] = "Your proof already submitted to administrator.";
            header("location: my-bidding.php");
            exit();
        }
        else {
            $_SESSION['status'] = "Proof is not added. Please try again!";
            header("location: my-bidding.php");
            exit();
        }
    }

    if (isset($_POST['update_user'])) { 
        $user_id = trim($_SESSION["id"]);
        $name = $_POST['name'];
        $address = $_POST['address'];
        $username = $_POST['username'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $contact = $_POST['contact'];

        $checkRecord = mysqli_query($link, "SELECT * FROM users WHERE id=" . $user_id);
        $totalrows = mysqli_num_rows($checkRecord);

        if ($totalrows > 0) {
            $query = "UPDATE users SET name = '$name', 
                        address = '$address',
                        username = '$username', 
                        date_of_birth = '$date_of_birth',
                        gender = '$gender',
                        contact = '$contact'
                        WHERE id = $user_id"; 
            $query_run = mysqli_query($link, $query);    
        }

        if ($query_run) {
            $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                 VALUES ('$bidder_id', '$item_id', 2, 'You update your profile successfully!', 0, NOW())"; 
            $run = mysqli_query($link, $query_sql);

            $_SESSION['success_status'] = "You update your profile successfully!";
            header("location: profile.php");
            exit();
        }
        else {
            $_SESSION['error_status'] = $totalrows;
            header("location: profile.php");
            exit();
        }
    }

    if (isset($_POST['cashin'])) { 
        $user_id = $_POST['user_id'];
        $amount = $_POST['amount'];

        $checkRecord = mysqli_query($link, "SELECT * FROM tokens WHERE user_id=" . $user_id);
        $totalrows = mysqli_num_rows($checkRecord);

        if ($totalrows > 0) {
            $final_amount = $checkRecord->fetch_array()['token'] + $amount;
            $query_update = "UPDATE tokens SET token = $final_amount 
                WHERE user_id = $user_id"; 

            $query_update_run = mysqli_query($link, $query_update); 
        } else {
            $query_update = "INSERT INTO tokens(user_id, token) 
            VALUES ('$user_id', '$amount')";

            $query_update_run = mysqli_query($link, $query_update); 
        }

        if ($query_update_run) {
            $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                 VALUES ('$bidder_id', '$item_id', 2, 'Successfully cash in your account.', 0, NOW())"; 
            $run = mysqli_query($link, $query_sql);
            
            $_SESSION['success_status'] = "Successfully cash in your account!";
            header("location: my-tokens.php");
            exit();
        }
        else {
            $_SESSION['error_status'] = "Have an error on cash in to your account. Please try again!";
            header("location: my-tokens.php");
            exit();
        }
    }

    if (isset($_POST['cashout'])) { 
        $user_id = $_POST['user_id'];
        $amount = $_POST['amount'];

        $checkRecord = mysqli_query($link, "SELECT * FROM tokens WHERE user_id=" . $user_id);
        $totalrows = mysqli_num_rows($checkRecord);

        if ($checkRecord->fetch_array()['token'] < $amount)
            $_SESSION['error_status'] = "Your balance is less than the amount you want to cash out. Please try again!";
            header("location: my-tokens.php");
            exit();

        if ($totalrows > 0) {
            $final_amount = $checkRecord->fetch_array()['token'] - $amount;
            $query_update = "UPDATE tokens SET token = $final_amount 
                WHERE user_id = $user_id"; 

            $query_update_run = mysqli_query($link, $query_update); 
        } else {
            $query_update = "INSERT INTO tokens(user_id, token) 
            VALUES ('$user_id', '$amount')";

            $query_update_run = mysqli_query($link, $query_update); 
        }

        if ($query_update_run) {
            $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                 VALUES ('$bidder_id', '$item_id', 2, 'Successfully cash out your account.', 0, NOW())"; 
            $run = mysqli_query($link, $query_sql);
            
            $_SESSION['success_status'] = "Successfully cash out your account!";
            header("location: my-tokens.php");
            exit();
        }
        else {
            $_SESSION['error_status'] = "Have an error on cash out to your account. Please try again!";
            header("location: my-tokens.php");
            exit();
        }
    }
?>