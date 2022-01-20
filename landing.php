<?php
    //Include config file
    require_once "config.php";

    $sql = "SELECT * FROM feedbacks 
            LEFT JOIN users ON feedbacks.user_id = users.id";

    $item_feedbacks = mysqli_query($link, $sql);
    $feedbacks = $item_feedbacks->fetch_all(MYSQLI_ASSOC);

    // print_r($feedbacks);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Collectibles - Auction System</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="landing-assets/img/favicon.ico" rel="icon">
  <link href="landing-assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="css/collectibles-auctions.css" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="landing-assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="landing-assets/vendor/icofont/icofont.min.css" rel="stylesheet">
  <link href="landing-assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="landing-assets/vendor/owl.carousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="landing-assets/vendor/venobox/venobox.css" rel="stylesheet">
  <link href="landing-assets/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="landing-assets/css/landing.css" rel="stylesheet">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top  header-transparent ">
    <div class="container d-flex align-items-center">

      <div class="logo mr-auto">
        <h1 class="text-light">
            <a href="landing.php">
              Collectibles
            </a>
        </h1>
      </div>

      <nav class="nav-menu d-none d-lg-block">
        <ul>
          <li><a href="#features">Site Features</a></li>
          <li><a href="#testimonials">Feedbacks</a></li>
          <li><a href="#pricing">Subscription</a></li>
          <li><a href="#faq">F.A.Q.</a></li>
          <li class="register">
            <a href="register.php">
              Register
            </a>
          </li>
          <li class="login">
            <a href="login.php">
              Login
            </a>
          </li>
        </ul>
      </nav><!-- .nav-menu -->

    </div>
  </header><!-- End Header -->

  <section id="hero" class="d-flex align-items-center">

    <div class="container">
      <div class="row">
        <div class="col-lg-6 d-lg-flex flex-lg-column justify-content-center align-items-stretch pt-5 pt-lg-0 order-2 order-lg-1" data-aos="fade-up">
          <div>
            <h1>Online Auction System</h1>
            <h2>Over 1000+ Auctions online right now!</h2>
            <a href="item-display.php" class="download-btn"><i class="bx bxs-chevron-right-circle"></i> View Auction Items</a>
          </div>
        </div>
        <div class="col-lg-6 d-lg-flex flex-lg-column align-items-stretch order-1 order-lg-2 hero-img" data-aos="fade-up">
          <img src="landing-assets/img/landing_display.png" class="img-fluid" alt="">
        </div>
      </div>
    </div>

  </section>

  <main id="main">

    <section id="features" class="features">
      <div class="container">

        <div class="section-title">
          <h2>Site Features</h2>
          <p>
              Below are the list of features that this system can provide
          </p>
        </div>

        <div class="row no-gutters">
          <div class="col-xl-7 d-flex align-items-stretch order-2 order-lg-1">
            <div class="content d-flex flex-column justify-content-center">
              <div class="row">
                <div class="col-md-6 icon-box" data-aos="fade-up">
                  <i class="bx bx-spreadsheet"></i>
                  <h4>View auction items</h4>
                  <p>Auctioneer and Bidder can view all of the auction items.</p>
                </div>
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
                  <i class="bx bx-message-dots"></i>
                  <h4>Feedbacks</h4>
                  <p>Bidder can give feedbacks/ ratings to the bid items that user received.</p>
                </div>
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="200">
                  <i class="bx bx-message"></i>
                  <h4>Comments</h4>
                  <p>User can give comment(s) or ask a question(s) to the bid items.</p>
                </div>
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="300">
                  <i class="bx bx-money"></i>
                  <h4>Manage payments</h4>
                  <p>The user can management the payment by using Cash In/Out with the used on the payment provider.</p>
                </div>
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="400">
                  <i class="bx bx-credit-card"></i>
                  <h4>Subscriptions</h4>
                  <p>Auctioneer can avail any subscription and become a premium member of the auction system.</p>
                </div>
                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="500">
                  <i class="bx bx-id-card"></i>
                  <h4>Evidence and Proof</h4>
                  <p>Auctioneer must submit proof of delivery, while bidder can submit proof of item received.</p>
                </div>
              </div>
            </div>
          </div>
          <div class="image col-xl-5 d-flex align-items-stretch justify-content-center order-1 order-lg-2" data-aos="fade-left" data-aos-delay="100">
            <img src="landing-assets/img/bidding.png" class="img-fluid" alt="">
          </div>
        </div>

      </div>
    </section><!-- End App Features Section -->

    <!-- ======= Testimonials Section ======= -->
    <section id="testimonials" class="testimonials section-bg">
      <div class="container">

        <div class="section-title">
          <h2>Feedbacks</h2>
          <p>When people are offered consistent, actionable feedback, they can gain better insight into their successes and opportunities for improvement.</p>
        </div>

        <div class="owl-carousel testimonials-carousel" data-aos="fade-up">
          <?php if (array_filter($feedbacks) != []) {
            foreach ($feedbacks as $feedback) { ?>
              <div class="testimonial-wrap">
                <div class="testimonial-item">
                  <img src="assets/uploads/<?php echo $feedback["avatar"]; ?>" class="testimonial-img" alt="">
                  <h3><?php echo $feedback["name"]; ?></h3>
                  <h4><?php echo $feedback["feedback"]; ?></h4>
                  <p style="word-wrap: break-word;">
                    <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                      <?php echo $feedback["comment"]; ?>
                    <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                  </p>
                </div>
              </div>
              <?php  }
            } ?>
        </div>

      </div>
    </section><!-- End Testimonials Section -->

    <section id="pricing" class="pricing">
      <div class="container">

        <div class="section-title">
          <h2>Subscription</h2>
          <p>Auctioneer can avail subscriptions plan to the system</p>
        </div>

        <div class="row no-gutters">
          <div class="col-lg-6 box featured" data-aos="fade-up">
            <h3>Premium</h3>
            <h4>₱ 199.00<span>per month</span></h4>
            <ul>
            <li><i class="bx bx-check"></i> Can view auction item(s)</li>
              <li><i class="bx bx-check"></i> Can check details of the item(s)</li>
              <li><i class="bx bx-check"></i> Can register one account</li>
              <li><i class="bx bx-check"></i> <span>Can submit/add item(s) to be auctions</span></li>
              <li><i class="bx bx-check"></i> <span>Can start bidding session</span></li>
              <li><i class="bx bx-check"></i> <span>You can add unlimited item(s)</span></li>
              <li><i class="bx bx-check"></i> <span>Auction item(s) are placed on the top search</span></li>
            </ul>
            <a href="register.php" class="get-started-btn">Get Started</a>
          </div>

          <div class="col-lg-6 box" data-aos="fade-left">
            <h3>Standard</h3>
            <h4>₱ 0.00<span>per month</span></h4>
            <ul>
            <li><i class="bx bx-check"></i> Can view auction item(s)</li>
              <li><i class="bx bx-check"></i> Can check details of the item(s)</li>
              <li><i class="bx bx-check"></i> Can register one account</li>
              <li><i class="bx bx-check"></i> <span>Can submit/add item(s) to be auctions</span></li>
              <li><i class="bx bx-check"></i> <span>Can start bidding session</span></li>
              <li><i class="bx bx-check"></i> <span>You can only add five (5) item(s) per month</span></li>
              <li class="na"><i class="bx bx-x"></i> <span>Auction item(s) are placed on the top search</span></li>
            </ul>
            <a href="register.php" class="get-started-btn">Get Started</a>
          </div>
        </div>
      </div>
    </section><!-- End Pricing Section -->

    <!-- ======= Frequently Asked Questions Section ======= -->
    <section id="faq" class="faq section-bg">
      <div class="container">

        <div class="section-title">
          <h2>Frequently Asked Questions</h2>
        </div>

        <div class="accordion-list">
          <ul>
          <li data-aos="fade-up">
              <i class="bx bx-help-circle icon-help"></i> 
                  <a data-toggle="collapse" class="collapse" href="#accordion-list">
                    What do you mean by bidding?
                   <i class="bx bx-chevron-down icon-show"></i>
                   <i class="bx bx-chevron-up icon-close"></i>
                  </a>
              <div id="accordion-list" class="collapse show" data-parent=".accordion-list">
                <div class="ml-4">
                    <p>Bidding is to to submit to someone's orders; perform services for someone:
                      After he was promoted to vice president at the bank, 
                      he expected everyone around him to do his bidding.</p>
                </div>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="50">
              <i class="bx bx-help-circle icon-help"></i> 
                  <a data-toggle="collapse" class="collapsed" href="#accordion-list-1">
                    What are the steps on bidding process?
                   <i class="bx bx-chevron-down icon-show"></i>
                   <i class="bx bx-chevron-up icon-close"></i>
                  </a>
              <div id="accordion-list-1" class="collapse" data-parent=".accordion-list">
                <div class="ml-4">
                    <p>1. Register your interest.</p>
                    <p>2. Attend briefing sessions.</p>
                    <p>3. Develop your bid response strategy.</p>
                    <p>4. Review recent awarded contracts.</p>
                    <p>5. Write a compelling bid.</p>
                    <p>6. Understand the payment terms.</p>
                    <p>7. Provide References.</p>
                    <p>8. Check and submit your bid.</p>
                </div>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="100">
              <i class="bx bx-help-circle icon-help"></i>
                <a data-toggle="collapse" href="#accordion-list-2" class="collapsed">
                  What is the purpose of bidding?
                  <i class="bx bx-chevron-down icon-show"></i>
                  <i class="bx bx-chevron-up icon-close"></i>
                </a>
              <div id="accordion-list-2" class="collapse" data-parent=".accordion-list">
                <p>      
                    Bidding is <strong>used to determine the cost or value of something</strong>. Bidding can be performed by a 
                    person under influence of a product or service based on the context of the situation.
                    In the context of auctions, stock exchange, 
                    or real estate the price offer a business or individual is willing to pay is called a bid.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="200">
              <i class="bx bx-help-circle icon-help"></i> 
                  <a data-toggle="collapse" href="#accordion-list-3" class="collapsed">
                      How do you win a bid?
                    <i class="bx bx-chevron-down icon-show"></i>
                    <i class="bx bx-chevron-up icon-close"></i>
                  </a>
              <div id="accordion-list-3" class="collapse" data-parent=".accordion-list">
                <div class="ml-4">
                      <p>1. Get Preapproved. Preapproval is a step most buyers will take anyway, but it's absolutely 
                        essential for anyone in a competitive bidding situation</p>
                      <p>2. AKnow Your Financial Limits.</p>
                      <p>3. Remove Some or All Contingencies.</p>
                      <p>4. Be Flexible on the Move-in Date.</p>
                      <p>5. Write a Personal Note.</p>
                  </div>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="400">
              <i class="bx bx-help-circle icon-help"></i> 
                <a data-toggle="collapse" href="#accordion-list-5" class="collapsed">
                  How long does the bidding process take? 
                  <i class="bx bx-chevron-down icon-show"></i>
                  <i class="bx bx-chevron-up icon-close"></i></a>
              <div id="accordion-list-5" class="collapse" data-parent=".accordion-list">
                <p>
                    Based on this information, best practices show that it takes <strong>about two and a half weeks</strong> for a tendering process
                    from the day you send the RFP to awarding a tender.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="400">
              <i class="bx bx-help-circle icon-help"></i> 
                <a data-toggle="collapse" href="#accordion-list-6" class="collapsed">
                  What is the process of bidding?
                  <i class="bx bx-chevron-down icon-show"></i>
                  <i class="bx bx-chevron-up icon-close"></i></a>
              <div id="accordion-list-6" class="collapse" data-parent=".accordion-list">
                <p>              
                    The bidding process is <strong>used to select a vendor for subcontracting a project</strong>, or for 
                    purchasing products and services that are required for a project.
                    The vendors analyze the bid and calculate the cost at which they can complete the project.
                </p>
              </div>
            </li>

          </ul>
        </div>

      </div>
    </section><!-- End Frequently Asked Questions Section -->
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="container py-4">
      <div class="copyright text-center">
          Copyright &copy; <strong><span>Collectibles</span></strong>
      </div>
      <div class="credits">
        Auction System - <?php echo date("Y"); ?>
      </div>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

  <!-- Vendor JS Files -->
  <script src="landing-assets/vendor/jquery/jquery.min.js"></script>
  <script src="landing-assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="landing-assets/vendor/jquery.easing/jquery.easing.min.js"></script>
  <script src="landing-assets/vendor/php-email-form/validate.js"></script>
  <script src="landing-assets/vendor/owl.carousel/owl.carousel.min.js"></script>
  <script src="landing-assets/vendor/venobox/venobox.min.js"></script>
  <script src="landing-assets/vendor/aos/aos.js"></script>

  <!-- Template Main JS File -->
  <script src="landing-assets/js/main.js"></script>

</body>

</html>