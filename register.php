<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$name = $address = $username = $password = $confirm_password =
    $date_of_birth = $gender = $contact = $role = $avatar =
    $subscription = $payment_option = "";

$name_err = $address_err = $username_err = $password_err =
    $confirm_password_err = $date_of_birth_err = $gender_err = $contact_err = $role_err = $subscription_err = $payment_option_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a name.";
    } else {
        $name = trim($_POST["name"]);
    }

    //Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter a address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else if (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {

        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);

        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    //Validate date of birth
    if (empty(trim($_POST["date_of_birth"]))) {
        $date_of_birth_err = "Please select a birthday.";
    } else {
        $date_of_birth = trim($_POST["date_of_birth"]);
    }


    //Validate gender
    if (empty($_POST['gender'])) {
        $gender_err = "Please select a gender";
    } else {
        $gender = trim($_POST["gender"]);
    }

    //Validate contact number
    $num_length = strlen((string)$_POST["contact"]);
    if (empty(trim($_POST["contact"]))) {
        $contact_err = "Please enter a contact number.";
    } else if ($num_length < 11 || $num_length > 11) {
        $contact_err = "Contact number must have atleast 11 digits.";
    } else {
        $contact = trim($_POST["contact"]);
    }

    //validate user type
    if (empty($_POST['role'])) {
        $role_err = "Please select a role";
    } else {
        $role = trim($_POST["role"]);
    }

    if (array_key_exists('avatar', $_FILES)) {
        if ($_FILES['avatar']['tmp_name'] != '') {
            $filename = strtotime(date('y-m-d H:i')) . '_' . $_FILES['avatar']['name'];
            $move = move_uploaded_file($_FILES['avatar']['tmp_name'], 'assets/uploads/' . $filename);
            $avatar = $filename;
        }
    } else {
        $filename = strtotime(date('y-m-d H:i'));
        $avatar = $filename;
    }

    if (!empty($_POST["subscription"])) {
        $subscription = trim($_POST["subscription"]);
    }

    if (!empty($_POST["payment_option"])) {
        $payment_option = trim($_POST["payment_option"]);
    }

    //payment subscription
    $admin_role = 3;
    $sql = "SELECT * FROM users WHERE role = $admin_role";
    $result = mysqli_query($link, $sql);
    $currentUser = $result->fetch_array(MYSQLI_ASSOC);
    $currentUser_id = $currentUser["id"];

    $checkRecord = mysqli_query($link, "SELECT * FROM tokens WHERE user_id=" .  $currentUser_id);
    $totalrows = mysqli_num_rows($checkRecord);

    if ($totalrows > 0) {
        $final_amount = $checkRecord->fetch_array()['token'] + 200;
        $query_update = "UPDATE tokens SET token = $final_amount
            WHERE user_id = $currentUser_id"; 

        $query_update_run = mysqli_query($link, $query_update); 
    } else {
        $query_update = "INSERT INTO tokens(user_id, token) 
        VALUES ('$currentUser_id', '$final_amount')";

        $query_update_run = mysqli_query($link, $query_update); 
    }

    if ($query_update_run) {
        $query_sql = "INSERT INTO notifications (user_id, item_id, type, notification, status, date_posted) 
                VALUES ('$currentUser_id', '$currentUser_id', 2, 'Successfully paid the account.', 0, NOW())"; 
        $run = mysqli_query($link, $query_sql);
    }

    $user_sql = "SELECT * FROM users WHERE id = $currentUser_id";
    $user_query = mysqli_query($link, $user_sql);
    $user = $user_query->fetch_array(MYSQLI_ASSOC);
    $unread_alert_user = $user['alert_unread_count'] + 1;

    //update notification status
    $query_update = "UPDATE users SET alert_status = 0,
        alert_unread_count = $unread_alert_user
        WHERE id = $currentUser_id"; 
    $run = mysqli_query($link, $query_update); 

    $validate = empty($name_err) &&
        empty($address_err) &&
        empty($username_err) &&
        empty($password_err) &&
        empty($confirm_password_err) &&
        empty($date_of_birth_err) &&
        empty($gender_err) &&
        empty($contact_err) &&
        empty($role_err);

    // Check input errors before inserting in database
    if ($validate) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (name, address, username, password, date_of_birth, gender, role, contact, avatar, subscription, payment_option)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssss",
                $param_name,
                $param_address,
                $param_username,
                $param_password,
                $param_date_of_birth,
                $param_gender,
                $param_role,
                $param_contact,
                $param_avatar,
                $param_subscription,
                $param_payment_option
            );

            // Set parameters
            $param_name = $name;
            $param_address = $address;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_date_of_birth = $date_of_birth;
            $param_gender = $gender;
            $param_role = $role;
            $param_contact = $contact;
            $param_avatar = $avatar;
            $param_subscription = $subscription;
            $param_payment_option = $payment_option;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_status'] = "Your account is sucessfully registered. You can now go to login!";
                header("location: login.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'header.php' ?>

<body class="bg-gradient-primary">
    <div class="container-xl">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-3 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-9" style="overflow: auto;max-height: 800px;">
                        <div class="p-3">
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
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-2 mt-4">Create an Account</h1>
                            </div>
                            <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <div class="col-sm-3 mb-3 mb-sm-0">
                                        <img class="w-100 rounded-circle" src="img/male_avatar.svg" id="cimg">
                                    </div>
                                    <div class="col-sm-9 mt-3 mb-3 mb-sm-0">
                                        <label for="" class="control-label">Avatar</label>
                                        <input type="file" class="form-control form-control-user" name="avatar" onchange="displayImg(this,$(this))" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" name="name" placeholder="Name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?> form-control-user" value="<?php echo $name; ?>" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode==32)" required>
                                        <!-- <span class="invalid-feedback ml-2"><?php echo $name_err; ?></span> -->
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" name="address" placeholder="Address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?> form-control-user" value="<?php echo $address; ?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <input type="text" name="username" placeholder="Username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?> form-control-user" value="<?php echo $username; ?>" required>
                                        <!-- <span class="invalid-feedback ml-2"><?php echo $username_err; ?></span> -->
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" id="password" name="password" placeholder="Password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?> form-control-user" value="<?php echo $password; ?>" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                        <span class="invalid-feedback ml-2"><?php echo $password_err; ?></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?> form-control-user" value="<?php echo $confirm_password; ?>" required>
                                        <span class="invalid-feedback ml-2"><?php echo $confirm_password_err; ?></span>
                                        <span id="password-match-label"></span>
                                    </div>
             
                                </div>
                                <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <ul class="pwd-restrictions">
                                                <li><span id="pwd-restriction-length"></span>Be between 10-16 characters in length</li>
                                                <li><span id="pwd-restriction-upperlower"></span>Contain at least 1 lowercase and 1 uppercase letter</li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <ul class="pwd-restrictions">
                                                <li><span id="pwd-restriction-number"></span>Contain at least 1 number (0–9)</li>
                                                <li><span id="pwd-restriction-special"></span>Contain at least 1 special character (!@#$%^&()'[]"?+-/*)</li>
                                            </ul>
                                        </div>
                                    </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="date" class="form-control form-control-user" id="date-of-birth" placeholder="Date of Birth" name="date_of_birth" required>
                                        <span class="invalid-feedback ml-2"><?php echo $date_of_birth_err; ?></span>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <select id="gender" class="form-control select-control-user" name="gender" required>
                                            <option value="" selected disabled hidden>Gender</option>
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                        <span class="invalid-feedback ml-2"><?php echo $gender_err; ?></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="number" name="contact" placeholder="Contact Number" class="form-control <?php echo (!empty($contact_err)) ? 'is-invalid' : ''; ?> form-control-user" value="<?php echo $contact; ?>" required>
                                        <span class="invalid-feedback ml-2"><?php echo $contact_err; ?></span>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <select id="role" class="form-control select-control-user" name="role" required>
                                            <option value="" selected disabled hidden>Role</option>
                                            <option value="1">Bidder</option>
                                            <option value="2">Auctioneer</option>
                                        </select>
                                        <span class="invalid-feedback ml-2"><?php echo $role_err; ?></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <select id="subscription" class="form-control select-control-user hidden" name="subscription">
                                            <option value="" selected disabled hidden>Subscription</option>
                                            <option value="1">Standard</option>
                                            <option value="2">Premium</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="payment" class="form-group row hidden">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" name="account_name" placeholder="Account Name" class="form-control form-control-user" required>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" name="gcash_number" placeholder="Gcash Number" class="form-control form-control-user" required>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text text-muted">₱</span>
                                            </div>
                                            <input type="number" name="amount" placeholder="Amount" class="form-control form-control-user" value="200" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text text-muted">.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <input type="password" name="pin"  maxlength="6" placeholder="PIN" class="form-control form-control-user" required>
                                    </div>
                                </div>
                        </div>
                        <div class="form-group row mt-2 mb-3">
                            <div class="col-sm-12 mb-3 mb-sm-0 text-center">
                                <input type="checkbox" required name="terms"> I accept the
                                <a href="terms-conditions.php">Terms and Conditions</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-user btn-block" value="Register Account">
                        </div>
                        </form>
                        <hr>
                        <div class="text-center mb-2">
                            <a class="small" href="login.php">Already have an account? Login!</a>
                        </div>
                        <div class="text-center mt-4 mb-2">
                            <a href="landing.php" class="btn btn-sm btn-secondary">
                                <span class="text">
                                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                    Back to Home Page
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php include 'script.php' ?>
    <!-- Payment Modal-->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModallabel">Payment Subscription</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form role="form" action="services.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="user_id" class="form-control" value="<?php echo $user_id; ?>">
                            <input type="text" name="name" placeholder="Account Name" required class="form-control ">
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="number" name="gcashNumber" placeholder="Gcash number" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text text-muted">
                                        <i class="fas fa-phone-square-alt mx-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text text-muted">₱</span>
                                </div>
                                <input type="number" name="amount" placeholder="Amount" class="form-control" value="200" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text text-muted">.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" maxlength="6" name="pin" placeholder="PIN" class="form-control" require>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" name="payment_subscription" class="subscribe btn btn-primary btn-md shadow-sm" value="Payment">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#cimg').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#role").change(function() {
        if ($(this).val() === "2") {
            $('#subscription').removeClass('hidden');
        } else {
            $('#subscription').addClass('hidden');
            $('#payment_option').addClass('hidden');
        }
    });

    $("#subscription").change(function() {
        if ($(this).val() === "2") {
            $('#payment_option').removeClass('hidden');
            $('#payment').removeClass('hidden');
        } else {
            $('#payment').addClass('hidden');
        }
    });

    $("#payment_option").change(function() {
        if ($(this).val() === "2") {
            $("#payment-options").removeClass("hidden");
        } else {
            $('#payment-options').addClass('hidden');
        }
    });

    function keypresshandler(event) {
        var charCode = event.keyCode;
        //Non-numeric character range
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
    }

    $(document).ready(function() {
        $('#password').keyup(function() {
            var s = $('#password').val();
            var pwdLength = /^.{10,16}$/;
            var pwdUpper = /[A-Z]+/;
            var pwdLower = /[a-z]+/;
            var pwdNumber = /[0-9]+/;
            var pwdSpecial = /[!@#$%^&()'[\]"?+-/*={}.,;:_]+/;

            if (pwdLength.test(s)) {
                $('#pwd-restriction-length').addClass('pwd-restriction-checked');
            } else {
                $('#pwd-restriction-length').removeClass('pwd-restriction-checked');
            }
            if (pwdUpper.test(s) && pwdLower.test(s)) {
                $('#pwd-restriction-upperlower').addClass('pwd-restriction-checked');
            } else {
                $('#pwd-restriction-upperlower').removeClass('pwd-restriction-checked');
            }
            if (pwdNumber.test(s)) {
                $('#pwd-restriction-number').addClass('pwd-restriction-checked');
            } else {
                $('#pwd-restriction-number').removeClass('pwd-restriction-checked');
            }
            if (pwdSpecial.test(s)) {
                $('#pwd-restriction-special').addClass('pwd-restriction-checked');
            } else {
                $('#pwd-restriction-special').removeClass('pwd-restriction-checked');
            }
        });

        function updatePasswordsMatchLabel() {
            if ($('#password').val() == $('#confirm-password').val()) {
                $('#password-match-label').text('Super! Your passwords match');
            } else {
                $('#password-match-label').text('Your passwords do not match');
            }
        }

        $('#confirm-password').keyup(function () {
            updatePasswordsMatchLabel();
        });

        if ($('#password-match-label').text().length > 0) {
            updatePasswordsMatchLabel();
        }
    });
</script>