<?php
class Email {
  var $subject;
  var $to;
  var $html;
  var $text;
  var $firstName;

  function send()
  {
    require_once "Mail.php";
    require_once "Mail/mime.php";

    $from = 'Star Dream Cakes <admin@ivanbrazza.biz>';
    $crlf = "\n";
    $headers = array(
                 'From'         => $from,
                 'Return-Path'  => $from,
                 'Subject'      => $this->subject
               );

    $mime = new Mail_mime($crlf);
    $mime->setTXTBody($this->text);
    $mime->setHTMLBody($this->html);
    
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
  
    $mail->send($this->to, $headers, $body);
  
    if (PEAR::isError($mail))
    {
      error_log("Failed to send email: " . $mail->getMessage(), 0);
      die();
    }
  }

  function statusUpdate($number, $status)
  {
    $this->subject    = 'Your Order With Star Dream Cakes';

    $this->html       = '<html><body';
    $this->html      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->html      .= '<p>Just to let you know that the status of your order ';
    $this->html      .=     $number . ' has been updated to ' . $status . '</p>';
    $this->html      .= '<br />';
    $this->html      .= '<p>If you have any problems, please don\'t hesistate to call.</p>';
    $this->html      .= '<p>Thanks,</p>';
    $this->html      .= '<p>Fran</p>';

    $this->text       = "Hi " . $this->firstName . ",\r\n";
    $this->text      .= "Just to let you know that the status of your order ";
    $this->text      .= $number . " has been updated to " . $status . "\r\n";
    $this->text      .= "If you have any problems, please don't hesistate to call.\r\n";
    $this->text      .= "Thanks,\r\n";
    $this->text      .= "Fran";
  }

  function order($number, $date, $datetime, $celebration_date, $comments, $filling, $decoration, $cake_type, $cake_size, $delivery)
  {
    $this->subject    = 'Your Order With Star Dream Cakes';
  
    $this->html       = '<html><body>';
    $this->html      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->html      .= '<p>Here is the order you\'ve requested:</p>';
    $this->html      .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
    $this->html      .= '<tr><th>Order Number</td><td>' . $number . '</td></tr>';
    $this->html      .= '<tr><th>Date Order Placed</th><td>' . $date . '</td></tr>';
    $this->html      .= '<tr><th>Required Date</th><td>' . $datetime . '</td></tr>';
    $this->html      .= '<tr><th>Date of Celebration</th><td>' . $celebration_date . '</td></tr>';
    $this->html      .= '<tr><th>Comments</th><td>' . $comments . '</td></tr>';
    $this->html      .= '<tr><th>Filling</th><td>' . $filling . '</td></tr>';
    $this->html      .= '<tr><th>Decoration</th><td>' . $decoration . '</td></tr>';
    $this->html      .= '<tr><th>Cake Type</th><td>' . $cake_type . '</td></tr>';
    $this->html      .= '<tr><th>Cake Size</th><td>' . $cake_size . '</td></tr>';
    $this->html      .= '<tr><th>Delivery Type</th><td>' . $delivery . '</td></tr>';
    $this->html      .= '</table>';
    $this->html      .= '<p>If you have any problems, please don\'t hesitate to call.</p>';
    $this->html      .= '<p>Thanks,</p>';
    $this->html      .= '<p>Fran</p>';
    $this->html      .= '</body></html>';
  
    $this->text       = "Hi, " . $this->firstName . ",\r\n" . 
                        "Here is the order you've requested:\r\n" .
                        "Order Number: " . $number . "\r\n" . 
                        "Order Placed: " . $order_placed . "\r\n" .
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
  }

  function verification($code)
  {
    $this->subject    = "Register Your Email";

    $this->html       = '<html><body>';
    $this->html      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->html      .= '<p>Thank you for registering with Star Dream Cakes. Please click the link below to verify your account:</p>';
    $this->html      .= '<a href="http://www.ivanbrazza.biz/verify-email/?email=' . $to . '&code=' . $code . '>http://www.ivanbrazza.biz/verify-email/?email=' . $to . '&code=' . $code . '</a>';
    $this->html      .= '<br />';
    $this->html      .= '<p>Thank you,<br />';
    $this->html      .= 'Star Dream Cakes</p>';
    $this->html      .= '</body></html>';

    $this->text       = "Hi " . $this->firstName . ",\r\nThank you for registering with Star Dream Cakes. Please click the link below to verify your account:" . 
                        "http://www.ivanbrazza.biz/verify-email/?email=" . $to . "&code=" . $code . "\r\n\r\nThank You,\r\nStar Dream Cakes";
  }
  
  function password($password)
  {
    $this->subject    = "Your new password";

    $this->html       = '<html><body>';
    $this->html      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->html      .= '<p>You are receiving this email because you requested a new password for Star Dream Cakes. Here is your new password:</p>';
    $this->html      .= '<p>' . $password . '</p>';
    $this->html      .= '<p>Thank you,<br />';
    $this->html      .= 'Star Dream Cakes</p>';
    $this->html      .= '</body></html>';

    $this->text       = "Hi " . $this->firstName . ",\r\n" . 
                        "You are receiving this email because you requested a new password for Star Dream Cakes. Here is your new password:\r\n" . 
                        $password . "\r\n" . 
                        "Thank you,\r\n" . 
                        "Star Dream Cakes";
  }

  function setRecipient($recipient)
  {
    $this->to = $recipient;
  }

  function setFirstName($namevar)
  {
    $this->firstName = $namevar;
  }
}
