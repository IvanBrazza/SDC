<?php
  /**
    email-verification.php - send an email verification to the customer
    when they register.
  **/
  $from     = "Star Dream Cakes <donotreply@ivanbrazza.biz>";
  $to       = $_POST['email'];
  $subject  = "Register Your Email";
  $message  = "Hi " . $_POST['first_name'] . ",\n\nThank you for registering with Star Dream Cakes. Please click the link below to verify your account:\n\nhttp://www.ivanbrazza.biz/sdc/verify-email/?email=" . $_POST['email'] . "&code=" . $email_verification . "\n\n\nThank You,\nStar Dream Cakes";
  $headers  = "From:" . $from;

  mail($to, $subject, $message, $headers);
?>
