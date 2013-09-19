<?php
  /**
    email-order.php - email a copy of the order to the customer.
  **/
  $from       = '<admin@ivanbrazza.biz>';
  $to         = $_SESSION['user']['email'];
  $subject    = 'Your Order With Star Dream Cakes';
  $message    = "Hi " . $_SESSION['user']['first_name'] . ",\n\nHere is the order you've requested: \n\n\n" . $_POST['order'] . "\n\n\nIt should be with you by " . $_POST['datetime'] . ".\n\nIf you have any problems don't hesitate to call.\n\nThanks,\nFran";
  $headers    = "From:" . $from;

  mail($to, $subject, $message, $headers);
?>
