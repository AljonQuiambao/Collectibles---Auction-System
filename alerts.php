<?php
    // Include config file
    require_once "config.php";

    $current_user = trim($_SESSION["id"]);
    $sql = "SELECT * FROM notifications WHERE user_id = $current_user ORDER BY date_posted desc";
    $query = mysqli_query($link, $sql);
    $filteredNotifications = $query->fetch_all(MYSQLI_ASSOC);
    $top_notif = array_slice($filteredNotifications, 0, 3);

    $user = mysqli_query($link, "SELECT * FROM users WHERE id=" . $current_user);
    $totalrows = mysqli_num_rows($user);

    $alert_status = 0;
    $message_status = 0;

    $user_sql = "SELECT * FROM users WHERE id = $current_user";
    $user_query = mysqli_query($link, $user_sql);
    $user = $user_query->fetch_array(MYSQLI_ASSOC);

    $thread_count = $link->query("SELECT * from thread where concat('[',REPLACE(user_ids,',','],['),']') like '%[{$_SESSION['login_id']}]%' ");
?>

<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" data-user-id="<?php echo $current_user; ?>"  href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Counter - Alerts -->
        <?php if ($user['alert_status'] == 0) { ?>
            <span class="badge badge-danger badge-counter">
                <?php echo count($filteredNotifications); ?>
            </span>
        <?php } ?>
    </a>
    <!-- Dropdown - Alerts -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">
            Notifications
        </h6>
        <?php if (array_filter($top_notif) != []) {
            foreach ($top_notif as $notification) {
             ?>
                <a class="dropdown-item d-flex align-items-center" href="notifications.php">
                    <div class="mr-3">
                         <?php switch ($notification['type']) {
                            case 1:
                                echo '<div class="icon-circle bg-primary">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>';
                                break;

                            case 2:
                                echo '<div class="icon-circle bg-success">
                                    <i class="fas fa-check text-white"></i>
                                </div>';
                                break;
                            
                            case 3:
                                echo '<div class="icon-circle bg-danger">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>';
                                break;    
                            
                            default:
                                echo '<div class="icon-circle bg-primary">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>';
                                break;
                        } ?>
                    </div>
                    <div>
                        <div class="small text-gray-500"><?php echo date('m-d-Y', strtotime($notification['date_posted'])); ?></div>
                        <span class="font-weight-bold"><?php echo $notification['notification']; ?></span>
                    </div>
                </a>
            <?php 
                }
            } ?>
        <a class="dropdown-item text-center small text-gray-500" href="notifications.php">Show All Notifications</a>
    </div>
</li>

<!-- Nav Item - Messages -->
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" data-user-id="<?php echo $current_user; ?>"  href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-envelope fa-fw"></i>
        <!-- Counter - Messages -->
        <?php if ($user['message_status'] == 0) { ?>
            <span class="badge badge-danger badge-counter">
                <?php echo count($thread_count->fetch_all()); ?>
            </span>
        <?php } ?>
    </a>
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
        <h6 class="dropdown-header">
            Messages
        </h6>
        <?php
            $thread = $link->query("SELECT * from thread where concat('[',REPLACE(user_ids,',','],['),']') like '%[{$_SESSION['login_id']}]%' ");
            while ($row = $thread->fetch_array()) :
                $user = $link->query("SELECT * FROM users where id in ({$row['user_ids']}) and id!= {$_SESSION['login_id']} ")->fetch_array();
                $msg = $link->query("SELECT * FROM messages where convo_id = {$row['id']} and user_id='{$user['id']}' and status = 0 order by id desc limit 1 ")->num_rows;
                $msges = $link->query("SELECT * FROM messages where convo_id = {$row['id']} and user_id='{$user['id']}' order by id desc limit 1")->fetch_array();
        ?>
        <a class="dropdown-item d-flex align-items-center" href="index.php?page=home">
            <div class="dropdown-list-image mr-3">
                <img class="rounded-circle" src="assets/uploads/<?php echo $user['avatar']; ?>" alt="...">
                <div class="status-indicator bg-success"></div>
            </div>
            <div class="font-weight-bold">
                <div class="text-truncate">
                   <?php echo $msges['message'] ? $msges['message']  : "No response yet"; ?>
                </div>
                <div class="small text-gray-500"><?php echo ucwords($user['name']) ?></div>
            </div>
        </a>
        <?php endwhile; ?>
        <a class="dropdown-item text-center small text-gray-500" href="index.php?page=home">Read More Messages</a>
    </div>
</li>
