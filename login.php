<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password, role, gender, avatar FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role, $gender, $avatar);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username; 
                            $_SESSION["gender"] = $gender;  

                            $_SESSION["login_id"] = $id;
                            $_SESSION["login_name"] = $username; 
                            $_SESSION["login_avatar"] = $avatar; 
                            
                            switch ($role) {
                                case 1:
                                    header("location: welcome.php");
                                    break;

                                case 2:
                                    header("location: my-auctions.php");
                                    break;
                                
                                case 3:
                                    header("location: admin-dashboard.php");
                                    break;
                                
                                default:
                                    header("location: welcome.php");
                                    break;
                            }

                        } else{
                            $login_err = '<div class="alert alert-danger alert-dismissable" id="flash-msg">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                Invalid username or password
                            </div>';
                        }
                    }
                } else{
                    $login_err = '<div class="alert alert-danger alert-dismissable" id="flash-msg">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        Invalid username or password
                    </div>';
                }
            } else{
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
<?php include 'header.php'?>

<body class="bg-gradient-primary">
    <div class="container h-100">
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
        <div class="row h-100 justify-content-center align-items-center" style="padding-top: 10rem;">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div style="padding: 5rem 3rem;">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Login</h1>
                                    </div>
                                    <span class="ml-2"><?php echo $login_err; ?></span>
                                    <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <div class="form-group">
                                            <input type="text" name="username" placeholder="Username" 
                                                class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?> form-control-user"
                                                value="<?php echo $username; ?>">    
                                            <span class="invalid-feedback ml-2"><?php echo $username_err; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" placeholder="Password"
                                                class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?> form-control-user">
                                            <span class="invalid-feedback ml-2"><?php echo $password_err; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-user btn-block" value="Login">
                                        </div>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="reset-password.php">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                     <div class="text-center mt-4">
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
        </div>
    </div>

    <?php include 'script.php' ?>
</body>
</html>