<?php
    // Include config file
    require_once "config.php";
?>

<div class="col-md-4 col-sm-12">
    <h4 class="mt-2">Comment Section</h4>
    <form action="add-methods.php" method="POST">
        <input class="hidden" name="user_id" value="<?php echo $param_id ?>" type="text">
        <input class="hidden" name="item_id" value="<?php echo $item_id ?>" type="text">
        <textarea id="comment" name="comment" class="form-control" placeholder="Write your comment here..." rows="8" required></textarea>
        <input id="save-comment" name="save_comment" type="submit" class="btn btn-primary btn-user btn-block mt-4" value="Post">
    </form>

    <div class="col-12 mt-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" colspan="2">
                        <h6>Auctioneer Information:</h6>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Name:</th>
                    <td><?php echo $auctioneer['username']; ?></td>
                </tr>
                <tr>
                    <th scope="row">Contact:</th>
                    <td><?php echo $auctioneer['contact']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-8 col-sm-12 card shadow">
    <div class="comment-wrapper">
        <div class="panel panel-info">
            <div class="panel-header p-4">
                <h4>All comments 
                    <strong><?php 
                        if (count($comments) > 0) {
                            echo '('.count($comments).')';
                        }
                    ?></strong>
                </h4>
            </div>
            <div class="panel-body">
                <ul class="media-list">
                    <?php
                        if (isset($_SESSION['success_status'])) {
                        ?>
                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?php echo $_SESSION['success_status']; ?>
                            </div>
                        <?php
                            unset($_SESSION['success_status']);
                        }
                    ?>
                    <?php
                        if (isset($_SESSION['error_status'])) {
                        ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?php echo $_SESSION['error_status']; ?>
                            </div>
                        <?php
                            unset($_SESSION['error_status']);
                        }
                    ?>
                    <?php if (array_filter($comments) != []) {
                        foreach ($comments as $comment) { ?>
                            <li class="media">
                                <a href="#" class="pull-left mr-4">
                                    <img src="img/<?php echo strtolower($comment['gender']) ?>_avatar.svg" alt="" class="img-circle">
                                </a>
                                <div class="media-body">
                                    <strong class="text-success">@<?php echo $comment['username']; ?></strong>
                                    <span class="text-muted pull-right">
                                        <small class="text-muted date-posted"><?php echo $comment['date_posted']; ?></small>
                                    </span>
                                    <p>
                                        <?php echo $comment['comment']; ?>
                                    </p>
                                </div>
                            </li>
                    <?php }
                    } else { ?>
                        <div class="alert alert-primary" role="alert">
                            No comment available in this item.
                        </div>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>