<?php
  /**
    email-order.php - email a copy of the order to the customer.
  **/
  require_once "Mail.php";

  $from       = 'Star Dream Cakes <admin@ivanbrazza.biz>';
  $to         = $_SESSION['user']['email'];
  $subject    = 'Your Order With Star Dream Cakes';
  $body       = "Hi " . $_SESSION['user']['first_name'] . ",\n\nHere is the order you've requested: \n\n\n" . $_POST['order'] . "\n\n\nIt should be with you by " . $_POST['datetime'] . ".\n\nIf you have any problems don't hesitate to call.\n\nThanks,\nFran";
  $headers    = array(
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
