<?php
  /**
    email-verification.php - send an email verification to the customer
    when they register.
  **/
  require_once "Mail.php";

  $from     = "Star Dream Cakes <admin@ivanbrazza.biz>";
  $to       = $_POST['email'];
  $subject  = "Register Your Email";
  $body     = "Hi " . $_POST['first_name'] . ",\n\nThank you for registering with Star Dream Cakes. Please click the link below to verify your account:\n\nhttp://www.ivanbrazza.biz/sdc/verify-email/?email=" . $_POST['email'] . "&code=" . $email_verification . "\n\n\nThank You,\nStar Dream Cakes";
  $headers  = array(
                'From'    => $from,
                'To'      => $to,
                'Subject' => $subject
              );
  
  $smtp = Mail::factory('smtp', array(
    'host'      => 'oxmail.registrar-servers.com',
    'port'      => '25',
    'auth'      => true,
    'username'  => 'admin@ivanbrazza.biz',
    'password'  => 'inspiron1520',
    'timeout'   => '30'
  ));

  $mail = $smtp->send($to, $headers, $body);

  if (PEAR::isError($mail)) {
    die("Failed to send email: " . $mail->getMessage());
  }
?>
