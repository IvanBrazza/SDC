<?php
  /**
    verify-email/ - get the code from the registration email
    and check it against the database to verify the email
    address.
  **/
  require("../lib/common.php");
  $title = "Thanks For Registering";

  if (!empty($_GET))
  {
    $email              = $_GET['email'];
    $verification_code  = $_GET['code'];
    $email_verified     = false;

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

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    $row = $stmt->fetch();

    if ($row['email_verification'] === $verification_code)
    {
      $email_verified = true;
    }

    if ($email_verified === true)
    {
      $query = "
        UPDATE
          users
        SET
          email_verified = 'yes'
      ";

      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute();
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query: " . $ex->getMessage());
      }
    }
  }

?>
<?php include("../lib/header.php"); ?>
  <div class="container">
    <?php if (!$_GET) : ?>
      <h3>Thank you for registering, we've sent an email to you to verify your account, please click on the link in the email.</h3>
    <?php endif; ?>
    <?php if ($_GET) : ?>
      <h3>Thank you. Your email <?php echo $email; ?> is now verified. Redirecting you to the login page...</h3>
      <?php header( "refresh:5;url=../login" ); ?>
    <?php endif; ?>
  </div>
<?php include("../lib/footer.php"); ?>
