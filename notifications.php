<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $current_user = trim($_SESSION["id"]);
    $sql = "SELECT * FROM notifications WHERE user_id = $current_user ORDER BY date_posted desc";
    $query = mysqli_query($link, $sql);
    $filteredNotifications = $query->fetch_all(MYSQLI_ASSOC);

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
                        Notifications
                    </h1>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th col="1"></th>
                                            <th col="11">Notifications</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (array_filter($filteredNotifications) != []) {
                                            foreach ($filteredNotifications as $notification) {
                                        ?>
                                                <tr>
                                                    <td>
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
                                                           
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <?php echo $notification['notification']; ?>
                                                            <div class="small text-gray-500">
                                                                <?php echo $notification['date_posted']; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php }
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
    </div>
    <?php include 'script.php';  ?>
</body>

</html>