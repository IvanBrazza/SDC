<?php
  function sendEmail($subject, $to, $html, $text)
  {
    require_once "Mail.php";
    require_once "Mail/mime.php";

    $from = 'Star Dream Cakes <admin@ivanbrazza.biz>';
    $crlf = "\n";
    $headers = array(
                 'From'         => $from,
                 'Return-Path'  => $from,
                 'Subject'      => $subject
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
  }

  function emailStatusUpdate($to, $name, $number, $status)
  {
    $subject    = 'Your Order With Star Dream Cakes';

    $html       = '<html><body';
    $html      .= '<p>Hi ' . $name . ',</p>';
    $html      .= '<p>Just to let you know that the status of your order ';
    $html      .=     $number . ' has been updated to ' . $status . '</p>';
    $html      .= '<br />';
    $html      .= '<p>If you have any problems, please don\'t hesistate to call.</p>';
    $html      .= '<p>Thanks,</p>';
    $html      .= '<p>Fran</p>';

    $text       = "Hi " . $name . ",\r\n";
    $text      .= "Just to let you know that the status of your order ";
    $text      .= $number . " has been updated to " . $status . "\r\n";
    $text      .= "If you have any problems, please don't hesistate to call.\r\n";
    $text      .= "Thanks,\r\n";
    $text      .= "Fran";

    sendEmail($subject, $to, $html, $text);
  }

  function emailOrder($to, $name, $number, $date, $datetime, $celebration_date, $comments, $filling, $decoration, $cake_type, $cake_size, $delivery)
  {
    $subject    = 'Your Order With Star Dream Cakes';
  
    $html       = '<html><body>';
    $html      .= '<p>Hi ' . $name . ',</p>';
    $html      .= '<p>Here is the order you\'ve requested:</p>';
    $html      .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
    $html      .= '<tr><th>Order Number</td><td>' . $number . '</td></tr>';
    $html      .= '<tr><th>Date Order Placed</th><td>' . $date . '</td></tr>';
    $html      .= '<tr><th>Required Date</th><td>' . $datetime . '</td></tr>';
    $html      .= '<tr><th>Date of Celebration</th><td>' . $celebration_date . '</td></tr>';
    $html      .= '<tr><th>Comments</th><td>' . $comments . '</td></tr>';
    $html      .= '<tr><th>Filling</th><td>' . $filling . '</td></tr>';
    $html      .= '<tr><th>Decoration</th><td>' . $decoration . '</td></tr>';
    $html      .= '<tr><th>Cake Type</th><td>' . $cake_type . '</td></tr>';
    $html      .= '<tr><th>Cake Size</th><td>' . $cake_size . '</td></tr>';
    $html      .= '<tr><th>Delivery Type</th><td>' . $delivery . '</td></tr>';
    $html      .= '</table>';
    $html      .= '<p>If you have any problems, please don\'t hesitate to call.</p>';
    $html      .= '<p>Thanks,</p>';
    $html      .= '<p>Fran</p>';
    $html      .= '</body></html>';
  
    $text       = "Hi, " . $name . ",\r\n" . 
                  "Here is the order you've requested:\r\n" .
                  "Order Number: " . $number . "\r\n" . 
                  "Date Order Placed: " . $order_date . "\r\n" .
                  "Required Date: " . $datetime . "\r\n" .
                  "Date of Celebration: " . $celebration_date . "\r\n" . 
                  "Comments: " . $comments . "\r\n" . 
                  "Filling: " . $filling . "\r\n" . 
                  "Decoration: " . $decoration . "\r\n" . 
                  "Cake Type: " . $cake_type . "\r\n" . 
                  "Cake Size: " . $cake_size . "\r\n" . 
                  "Delivery Type: " . $delivery_type . "\r\n" . 
                  "If you have any problems, please don't hesitate to call.\r\n" . 
                  "Thanks,\r\n" . 
                  "Fran.";
    
    sendEmail($subject, $to, $html, $text);
  }

  function emailVerification($to, $name, $code)
  {
    $subject    = "Register Your Email";

    $html       = '<html><body>';
    $html      .= '<p>Hi ' . $name . ',</p>';
    $html      .= '<p>Thank you for registering with Star Dream Cakes. Please click the link below to verify your account:</p>';
    $html      .= '<a href="http://www.ivanbrazza.biz/verify-email/?email=' . $to . '&code=' . $code . '>http://www.ivanbrazza.biz/verify-email/?email=' . $to . '&code=' . $code . '</a>';
    $html      .= '<br />';
    $html      .= '<p>Thank you,<br />';
    $html      .= 'Star Dream Cakes</p>';
    $html      .= '</body></html>';

    $text       = "Hi " . $name . ",\r\nThank you for registering with Star Dream Cakes. Please click the link below to verify your account:" . 
                  "http://www.ivanbrazza.biz/verify-email/?email=" . $to . "&code=" . $code . "\r\n\r\nThank You,\r\nStar Dream Cakes";
    
    sendEmail($subject, $to, $html, $text);
  }
  
  function emailPassword($to, $name, $password)
  {
    $subject    = "Your new password";

    $html       = '<html><body>';
    $html      .= '<p>Hi ' . $name . ',</p>';
    $html      .= '<p>You are receiving this email because you requested a new password for Star Dream Cakes. Here is your new password:</p>';
    $html      .= '<p>' . $password . '</p>';
    $html      .= '<p>Thank you,<br />';
    $html      .= 'Star Dream Cakes</p>';
    $html      .= '</body></html>';

    $text       = "Hi " . $name . ",\r\n" . 
                  "You are receiving this email because you requested a new password for Star Dream Cakes. Here is your new password:\r\n" . 
                  $password . "\r\n" . 
                  "Thank you,\r\n" . 
                  "Star Dream Cakes";
    
    sendEmail($subject, $to, $html, $text);
  }
?>
