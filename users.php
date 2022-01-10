<?php
    // Include config file
    require_once "config.php";

    // Initialize the session
    session_start();

    $sql = "SELECT * FROM users";

    $user_result = mysqli_query($link, $sql);
    $users = $user_result->fetch_all(MYSQLI_ASSOC);

    //print_r($users);

    function filterByUserRole($users, $role)
    {
        return array_filter($users, function ($user) use ($role) {
            if ($user['role'] == $role) {
                return true;
            }
        });
    }

    $bidders = filterByUserRole($users, 1);
    $auctioneers = filterByUserRole($users, 2);
    $multiroles = filterByUserRole($users, 4);
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
                        <span class="fas fa-fw fa-users"></span>
                        User List
                    </h1>

                    <div class="row">
                        <div class="col-12">
                            <?php
                            if (isset($_SESSION['status'])) {
                            ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $_SESSION['status']; ?>
                                </div>
                            <?php
                                unset($_SESSION['status']);
                            }
                            ?>
                            <section id="tabs" class="project-tab">
                                <nav>
                                    <div class="nav nav-tabs nav-fill mb-4" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link active" style="text-align:left;" id="nav-bidders-tab" data-toggle="tab" href="#nav-bidders" role="tab" aria-controls="nav-home" aria-selected="true">
                                            Bidders
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-auctioneers-tab" data-toggle="tab" href="#nav-auctioneers" role="tab" aria-controls="nav-profile" aria-selected="false">
                                            Auctioneers
                                        </a>
                                        <a class="nav-item nav-link" style="text-align:left;" id="nav-multi-tab" data-toggle="tab" href="#nav-multi" role="tab" aria-controls="nav-profile" aria-selected="false">
                                            Multi-role
                                        </a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-bidders" role="tabpanel" aria-labelledby="nav-bidders-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="bidder-users" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-2">Avatar</th>
                                                                <th class="col-1">Name</th>
                                                                <th class="col-2">Address</th>
                                                                <th class="col-1">Username</th>
                                                                <th class="col-1">Date of Birth</th>
                                                                <th class="col-1">Gender</th>
                                                                <th class="col-1">Contact</th>
                                                                <th class="col-1">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($bidders) != []) {
                                                                foreach ($bidders as $bidder) { ?>
                                                                    <tr class="text-center">
                                                                        <td>
                                                                            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 12rem;"
                                                                                src="assets/uploads/<?php echo $bidder['avatar'] ?>" alt="">
                                                                        </td>
                                                                        <td><?php echo $bidder['name']; ?></td>
                                                                        <td><?php echo $bidder['address']; ?></td>
                                                                        <td><?php echo $bidder['username']; ?></td>
                                                                        <td><?php echo date('m-d-Y', strtotime($bidder['date_of_birth'])); ?></td>
                                                                        <td>
                                                                            <div><?php echo $bidder['gender']; ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <div><?php echo $bidder['contact']; ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <button class="btn btn-danger delete" data-id="<?php echo $bidder['id']; ?>" data-table-name="users" title="Delete">
                                                                                Delete
                                                                            </button>
                                                                        </td>
                                                                    </tr>

                                                            <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-auctioneers" role="tabpanel" aria-labelledby="nav-auctioneers-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="auctioneer-users" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-2">Avatar</th>
                                                                <th class="col-1">Name</th>
                                                                <th class="col-2">Address</th>
                                                                <th class="col-1">Username</th>
                                                                <th class="col-1">Date of Birth</th>
                                                                <th class="col-1">Gender</th>
                                                                <th class="col-1">Contact</th>
                                                                <th class="col-2">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($auctioneers) != []) {
                                                                foreach ($auctioneers as $auctioneer) { ?>
                                                                   <tr class="text-center">
                                                                        <td>
                                                                            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 12rem;"
                                                                                src="assets/uploads/<?php echo $auctioneer['avatar'] ?>" alt="">
                                                                        </td>
                                                                        <td><?php echo $auctioneer['name']; ?></td>
                                                                        <td><?php echo $auctioneer['address']; ?></td>
                                                                        <td><?php echo $auctioneer['username']; ?></td>
                                                                        <td><?php echo $auctioneer['date_of_birth']; ?></td>
                                                                        <td>
                                                                            <div><?php echo $auctioneer['gender']; ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <div><?php echo $auctioneer['contact']; ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <button class="btn btn-danger delete" data-id="<?php echo $auctioneer['id']; ?>" data-table-name="users" title="Delete">
                                                                                Delete
                                                                            </button>
                                                                        </td>
                                                                    </tr>

                                                            <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-multi" role="tabpanel" aria-labelledby="nav-multi-tab">
                                        <div class="card shadow mb-4">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered auction-table" id="multi-users" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th class="col-2">Avatar</th>
                                                                <th class="col-1">Name</th>
                                                                <th class="col-2">Address</th>
                                                                <th class="col-1">Username</th>
                                                                <th class="col-1">Date of Birth</th>
                                                                <th class="col-1">Gender</th>
                                                                <th class="col-1">Contact</th>
                                                                <th class="col-2">
                                                                    Actions
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (array_filter($multiroles) != []) {
                                                                foreach ($multiroles as $multirole) { ?>
                                                                   <tr class="text-center">
                                                                        <td>
                                                                            <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 12rem;"
                                                                                src="assets/uploads/<?php echo $multirole['avatar'] ?>" alt="">
                                                                        </td>
                                                                        <td><?php echo $multirole['name']; ?></td>
                                                                        <td><?php echo $multirole['address']; ?></td>
                                                                        <td><?php echo $multirole['username']; ?></td>
                                                                        <td><?php echo $multirole['date_of_birth']; ?></td>
                                                                        <td>
                                                                            <div><?php echo $multirole['gender']; ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <div><?php echo $multirole['contact']; ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <button class="btn btn-danger delete" data-id="<?php echo $multirole['id']; ?>" data-table-name="users" title="Delete">
                                                                                Delete
                                                                            </button>
                                                                        </td>
                                                                    </tr>

                                                            <?php }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </div>

    <?php include 'background.php'; ?>

    <!--Reject Modal-->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="add-methods.php" method="POST">
                    <div class="modal-body">
                        Are you sure want to reject this item?
                        <input class="hidden" name="user_id" >
                        <input class="hidden" name="item_id" >
                        <input class="hidden" name="category" >
                        <textarea name="reason" class="form-control" placeholder="Write your reason here..." rows="5" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input name="reject_item" type="submit" class="btn btn-danger" value="Reject">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'script.php'; ?>
    <script>
        $(document).on('click', '.btn-reject', function() {
            var $parent = $(this).parent();
            var $parent_user_id = $($parent).find('.user_id').val();
            var $parent_item_id = $($parent).find('.item_id').val();
            var $category = $($parent).find('.category').val();

            //set value
            $('#rejectModal').find('input[name="user_id"]').val($parent_user_id);
            $('#rejectModal').find('input[name="item_id"]').val($parent_item_id);
            $('#rejectModal').find('input[name="category"]').val($category);
        });
    </script>
    <script src="js/string-trim.js"></script>
    <script>
        $(document).ready(function() {
            // Delete 
            $('.delete').click(function() {
                var el = this;

                var deleteId = $(this).data('id');
                var tableName = $(this).data('table-name');

                var confirmalert = confirm("Are you sure you want to delete?");
                if (confirmalert == true) {
                    // AJAX Request
                    $.ajax({
                        url: 'remove.php',
                        type: 'POST',
                        data: {
                            id: deleteId,
                            tableName: tableName
                        },
                        success: function(response) {
                            if (response == 1) {
                                // Remove row from HTML Table
                                $(el).closest('tr').css('background', 'tomato');
                                $(el).closest('tr').fadeOut(800, function() {
                                    $(this).remove();
                                });
                            } else {
                                alert('Invalid data id.');
                            }

                        }
                    });
                }

            });

        });
    </script>
    
</body>
</html>