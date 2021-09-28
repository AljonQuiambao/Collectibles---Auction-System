<?php
//require_once "config.php";

if(isset($_POST["email"]) && (!empty($_POST["email"]))){
    $email = $_POST["email"];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    
if (!$email) {
   $error .="<p>Invalid email address please type a valid email address!</p>";
   } else {
        $sel_query = "SELECT * FROM `users` WHERE email='".$email."'";
        $results = mysqli_query($con,$sel_query);
        $row = mysqli_num_rows($results);
        if ($row==""){
        $error .= "<p>No user is registered with this email address!</p>";
   }
  }
   if($error!="") {
        echo "<div class='error'>".$error."</div>
        <br /><a href='javascript:history.go(-1)'>Go Back</a>";
   } else {
        $expFormat = mktime(
        date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y")
   );
   $expDate = date("Y-m-d H:i:s",$expFormat);
   $key = md5(2418*2+$email);
   $addKey = substr(md5(uniqid(rand(),1)),3,10);
   $key = $key . $addKey;

// Insert data to `password_reset_temp` table
mysqli_query($con,
"INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`)
    VALUES ('".$email."', '".$key."', '".$expDate."');");

        //Email template
        $output='<p>Dear user,</p>';
        $output.='<p>Please click on the following link to reset your password.</p>';
        $output.='<p>-------------------------------------------------------------</p>';
        $output.='<p><a href="https://www.allphptricks.com/forgot-password/reset-password.php?
        key='.$key.'&email='.$email.'&action=reset" target="_blank">
        https://www.allphptricks.com/forgot-password/reset-password.php
        ?key='.$key.'&email='.$email.'&action=reset</a></p>';		
        $output.='<p>-------------------------------------------------------------</p>';
        $output.='<p>Please be sure to copy the entire link into your browser.
            The link will expire after 1 day for security reason.</p>';
        $output.='<p>If you did not request this forgotten password email, no action 
            is needed, your password will not be reset. However, you may want to log into 
            your account and change your security password as someone may have guessed it.</p>';   	
        $output.='<p>Thanks,</p>';
        $output.='<p>Collectibles Team</p>';
        $body = $output; 
        $subject = "Password Recovery - Collectibles";

        $email_to = $email;
        $fromserver = "noreply@collectibles.com"; 
        require("PHPMailer/PHPMailerAutoload.php");
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Host = "mail.collectibles.com"; // Enter your host here
        $mail->SMTPAuth = true;
        $mail->Username = "noreply@collectibles.com"; // Enter your email here
        $mail->Password = "password"; //Enter your password here
        $mail->Port = 25;
        $mail->IsHTML(true);
        $mail->From = "noreply@collectibles.com";
        $mail->FromName = "Collectibles Team";
        $mail->Sender = $fromserver; // indicates ReturnPath header
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($email_to);
        if(!$mail->Send()){
        echo "Mailer Error: " . $mail->ErrorInfo;
} else {
        echo "<div class='error'>
        <p>An email has been sent to you with instructions on how to reset your password.</p>
        </div><br /><br /><br />";
	}
   }
   
} else {
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'header.php' ?>

<body class="bg-gradient-primary">
    <div class="container" style="padding-top: 12rem;">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Forgot Your Password?</h1>
                                        <p class="mb-4">We get it, stuff happens. Just enter your email address below
                                            and we'll send you a link to reset your password!</p>
                                    </div>
                                    <form class="user">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address...">
                                        </div>
                                        <a href="login.html" class="btn btn-primary btn-user btn-block">
                                            Reset Password
                                        </a>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="login.php">Already have an account? Login!</a>
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
<?php } ?>
 
