<?php
  /**
    verify-email/ - get the code from the registration email
    and check it against the database to verify the email
    address.
  **/
  require("../lib/common.php");
  $title = "Thanks For Registering";
  $page = "register";

  // If a verification code is being submitted
  if (!empty($_GET['code'])) {
    $email              = $_GET['email'];
    $verification_code  = $_GET['code'];
    $email_verified     = false;

    // Get details about verification based on email in GET
    $query = "
      SELECT
        email,
        email_verification,
        email_verified
      FROM
        users
      WHERE
        email = :email
    ";

    $query_params = array(
      ':email' => $_GET['email']
    );

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

    if ($row['email_verification'] === $verification_code) {
      $email_verified = true;
    }

    // If the email is verified, update the database accordingly
    if ($email_verified === true) {
      $query = "
        UPDATE
          users
        SET
          email_verified = 'yes'
      ";

      $db->runQuery($query, null);
    }
  }

?>
<?php include("../lib/header.php"); ?>
  <?php if (!empty($_GET['type']) and $_GET['type'] === "register") : ?>
    <h3>Thank you for registering, we've sent an email to you to verify your account, please click on the link in the email.</h3>
  <?php elseif (!empty($_GET['type']) and $_GET['type'] === "edit") : ?>
    <h3>Your account has been updated and we've sent an email to your updated email address. Please click on the link in the email to verify your email address.</h3>
    <?php header("refresh:10;url=../lib/logout.php"); ?>
  <?php else : ?>
    <h3>Thank you. Your email <?php echo $email; ?> is now verified. Redirecting you to the login page...</h3>
    <script>
      setTimeout(function() {
        window.location="../login/";
      }, 6000);
    </script>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
