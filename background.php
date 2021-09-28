<?php
if (isset($_POST['submit_feedback'])) {
    $user_id = trim($_SESSION["id"]);
    $feedback = $_POST["feedback"];
    $comment = $_POST["comment"];

    $query = "INSERT INTO feedbacks(user_id, feedback, comment) 
                VALUES ('$user_id', '$feedback', '$comment')";
    $query_run = mysqli_query($link, $query);
}
?>

<!-- Feedback Button-->
<a class="feedback rounded" href="#" data-toggle="modal" data-target="#feedbackModal">
    <i class="fas fa-comment-dots fa-2x mt-2"></i>
</a>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up mt-3"></i>
</a>

<div id="feedbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Feedback</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center"> <i class="far fa-file-alt fa-4x mb-3 animated rotateIn icon1"></i>
                <h3>Your opinion matters</h3>
                <h5>
                    Help us improve our system?
                    <strong>Give us your feedback.</strong>
                </h5>
                <hr>
                <h4>Your Rating</h4>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="row">
                    <div class="form-check col-4 text-center">
                        <div class="mini-container"> <i class="fas fa-smile-beam fa-3x"></i>
                            <div>
                                <input name="feedback" type="radio" value="Satisfied" required>
                            </div>
                            <p class="text-center"><small><strong>Satisfied</strong></small></p>
                        </div>
                    </div>
                    <div class="form-check col-4 text-center">
                        <div class="mini-container"> <i class="fas fa-meh fa-3x"></i>
                            <div>
                                <input name="feedback" type="radio" value="Neutral">
                            </div>
                            <p class="text-center"><small><strong>Neutral</strong></small></p>
                        </div>
                    </div>
                    <div class="form-check col-4 text-center">
                        <div class="mini-container"> <i class="fas fa-tired fa-3x"></i>
                            <div>
                                <input name="feedback" type="radio" value="Unhappy">
                            </div>
                            <p class="text-center">
                                <small><strong>Unhappy</strong></small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-center p-2">
                    <h4>Comments</h4>
                </div>
                <div class="p-2">
                    <textarea name="comment" class="form-control" type="textarea" placeholder="Your Message" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <input type="submit" name="submit_feedback" class="btn btn-primary pull-right" value="Send" />
                    <a href="#" class="btn btn-outline-primary" data-dismiss="modal">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Are you sure you want to Logout?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!--Delete Modal-->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Are you sure want to delete?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger" href="#">Delete</a>
            </div>
        </div>
    </div>
</div>