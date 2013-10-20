<?php
  /**
    email-order.php - email a copy of the order to the customer.
  **/
  require_once "Mail.php";
  require_once "Mail/mime.php";

  $from       = 'Star Dream Cakes <admin@ivanbrazza.biz>';
  $to         = $_SESSION['user']['email'];
  $subject    = 'Your Order With Star Dream Cakes';

  $html       = '<html><body>';
  $html      .= '<p>Hi ' . $_SESSION["user"]["first_name"] . ',</p>';
  $html      .= '<p>Here is the order you\'ve requested:</p>';
  $html      .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
  $html      .= '<tr><th>Order Number</td><td>' . $order_number . '</td></tr>';
  $html      .= '<tr><th>Date Order Placed</th><td>' . $order_date . '</td></tr>';
  $html      .= '<tr><th>Required Date</th><td>' . $_POST["datetime"] . '</td></tr>';
  $html      .= '<tr><th>Date of Celebration</th><td>' . $_POST["celebration_date"] . '</td></tr>';
  $html      .= '<tr><th>Comments</th><td>' . $_POST["comments"] . '</td></tr>';
  $html      .= '<tr><th>Filling</th><td>' . $_POST["filling"] . '</td></tr>';
  $html      .= '<tr><th>Decoration</th><td>' . $_POST["decoration"] . '</td></tr>';
  $html      .= '<tr><th>Cake Type</th><td>' . $_POST["cake_type"] . '</td></tr>';
  $html      .= '<tr><th>Cake Size</th><td>' . $_POST["cake_size"] . '</td></tr>';
  $html      .= '<tr><th>Delivery Type</th><td>' . $_POST["delivery"] . '</td></tr>';
  $html      .= '</table>';
  $html      .= '<p>If you have any problems, please don\'t hesitate to call.</p>';
  $html      .= '<p>Thanks,</p>';
  $html      .= '<p>Fran</p>';
  $html      .= '</body></html>';

  $text       = "Hi, " . $_SESSION["user"]["first_name"] . ",\r\n" . 
                "Here is the order you've requested:\r\n" .
                "Date Order Placed: " . $_POST["order_date"] . "\r\n" .
                "Required Date: " . $_POST["datetime"] . "\r\n" .
                "Date of Celebration: " . $_POST["celebration_date"] . "\r\n" . 
                "Comments: " . $_POST["comments"] . "\r\n" . 
                "Filling: " . $_POST["filling"] . "\r\n" . 
                "Decoration: " . $_POST["decoration"] . "\r\n" . 
                "Cake Type: " . $_POST["cake_type"] . "\r\n" . 
                "Cake Size: " . $_POST["cake_size"] . "\r\n" . 
                "Delivery Type: " . $_POST["delivery_type"] . "\r\n" . 
                "If you have any problems, please don't hesitate to call.\r\n" . 
                "Thanks,\r\n" . 
                "Fran.";
  
  $crlf       = "\n";
  $headers    = array(
                  'From'          => $from,
                  'Return-Path'   => $from,
                  'Subject'       => $subject
                );

  $mime = new Mail_mime($crlf);
  $mime->setTXTBody($text);
  $mime->setHTMLBody($html);
  
  $body = $mime->get();
  $headers = $mime->headers($headers);

  $mail =& Mail::factory('smtp', array(
    'host'      => 'oxmail.registrar-servers.com',
    'port'      => '25',
    'auth'      => true,
    'username'  => 'admin@ivanbrazza.biz',
    'password'  => 'inspiron1520',
    'timeout'   => '30'
  ));

  $mail->send($to, $headers, $body);

  if (PEAR::isError($mail)) {
    die("Failed to send email: " . $mail->getMessage());
  }
?>
