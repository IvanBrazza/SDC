<?php
class Email {
  var $subject;
  var $to;
  var $body;
  var $firstName;
  var $from;

  // A function which sends the email
  public function send() {
    $headers = "From: $this->from" . "\r\n" .
               "Reply-To: $this->from" . "\r\n" .
               "MIME-Version: 1.0" . "\r\n" .
               "Content-type: text/html; charset=iso-8859-1" . "\r\n";

    mail($this->to, $this->subject, $this->body, $headers);
  }

  // A function which sets the body, from and subject
  // to send an email about a new testimonial being submitted
  public function testimonial($testimonialDetails) {
    $this->from       = 'Star Dream Cakes <webmaster@ivanbrazza.biz>';

    $this->subject    = 'New Testimonial Submitted';

    $this->body       = '<html><body>';
    $this->body      .= '<p>A new testimonial has been submitted to the Star Dream Cakes website:</p>';
    $this->body      .= '<p>' . $testimonialDetails['testimonial'] . '</p>';
    $this->body      .= '<p>Submitted by: ' . $testimonialDetails['name'] . '</p><br>';
    $this->body      .= '<p>To approve or delete this testimonial, click on the following link:</p>';
    $this->body      .= '<a href="https://www.ivanbrazza.biz/testimonials/">';
    $this->body      .= 'https://www.ivanbrazza.biz/testimonials/</a>';
    $this->body      .= '</html></body>';
  }

  // A function which sets the body, from and subject
  // to send an email about a status update for an order
  public function statusUpdate($number, $status) {
    $this->from       = "Star Dream Cakes <orders@ivanbrazza.biz>";

    $this->subject    = 'Your Order With Star Dream Cakes';

    $this->body       = '<html><body>';
    $this->body      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->body      .= '<p>Just to let you know that the status of your order ';
    $this->body      .=  $number . ' has been updated to ' . $status . '</p>';
    $this->body      .= '<p>To view your order, click on the link below:<br>';
    $this->body      .= '<a href="https://www.ivanbrazza.biz/your-orders/?order=' . $number;
    $this->body      .= '">https://www.ivanbrazza.biz/your-orders/?order=' . $number . '</a></p>';
    $this->body      .= '<p>If you have any problems, please don\'t hesistate to call.</p>';
    $this->body      .= '<p>Thanks,</p>';
    $this->body      .= '<p>Fran</p>';
  }

  // A function which sets the body, from and subject
  // to send an email containing the order placed
  public function order($orderDetails) {
    $this->from       = "Star Dream Cakes <orders@ivanbrazza.biz>";

    $this->subject    = 'Your Order With Star Dream Cakes';
  
    $this->body       = '<html><body>';
    $this->body      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->body      .= '<p>Here is the order you\'ve requested:</p>';
    $this->body      .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
    $this->body      .= '<tr><th>Order Number</td><td>' . $orderDetails['order_number'] . '</td></tr>';
    $this->body      .= '<tr><th>Date Order Placed</th><td>' . $orderDetails['order_placed'] . '</td></tr>';
    $this->body      .= '<tr><th>Required Date</th><td>' . $orderDetails['datetime'] . '</td></tr>';
    $this->body      .= '<tr><th>Date of Celebration</th><td>' . $orderDetails['celebration_date'] . '</td></tr>';
    $this->body      .= '<tr><th>Comments</th><td>' . $orderDetails['comments'] . '</td></tr>';
    $this->body      .= '<tr><th>Filling</th><td>' . $orderDetails['filling_name'] . '</td></tr>';
    $this->body      .= '<tr><th>Decoration</th><td>' . $orderDetails['decor_name'] . '</td></tr>';
    $this->body      .= '<tr><th>Cake Type</th><td>' . $orderDetails['cake_type'] . '</td></tr>';
    $this->body      .= '<tr><th>Cake Size</th><td>' . $orderDetails['cake_size'] . '</td></tr>';
    $this->body      .= '<tr><th>Delivery Type</th><td>' . $orderDetails['delivery_type'] . '</td></tr>';
    $this->body      .= '</table>';
    $this->body      .= '<p>If you have any problems, please don\'t hesitate to call.</p>';
    $this->body      .= '<p>Thanks,</p>';
    $this->body      .= '<p>Fran</p>';
    $this->body      .= '</body></html>';
  }

  // A function which sets the body, from and subject
  // to send a verification email
  public function verification($code) {
    $this->from       = "Star Dream Cakes <noreply@ivanbrazza.biz>";

    $this->subject    = "Register Your Email";

    $this->body       = '<html><body>';
    $this->body      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->body      .= '<p>Thank you for registering with Star Dream Cakes. Please click the link below to verify your account:</p>';
    $this->body      .= '<a href="http://www.ivanbrazza.biz/verify-email/?email=' . $this->to . '&code=' . $code . '">http://www.ivanbrazza.biz/verify-email/?email=' . $this->to . '&code=' . $code . '</a>';
    $this->body      .= '<br />';
    $this->body      .= '<p>Thank you,<br />';
    $this->body      .= 'Star Dream Cakes</p>';
    $this->body      .= '</body></html>';
  }

  // A function which sets the body, from and subject
  // to send a reset password email
  public function password($password) {
    $this->from       = "Star Dream Cakes <noreply@ivanbrazza.biz>";

    $this->subject    = "Your new password";

    $this->body       = '<html><body>';
    $this->body      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->body      .= '<p>You are receiving this email because you requested a new password for Star Dream Cakes. Here is your new password:</p>';
    $this->body      .= '<p>' . $password . '</p>';
    $this->body      .= '<p>Thank you,<br />';
    $this->body      .= 'Star Dream Cakes</p>';
    $this->body      .= '</body></html>';
  }

  // A function which sets the body, from and subject
  // to send an email to the client to notify them
  // of a new order
  public function orderAdmin($orderDetails, $customerDetails) {
    $this->from       = "Star Dream Cakes <orders@ivanbrazza.biz>";

    $this->subject    = 'A New Order Has Been Placed';

    $this->body       = '<html><body>';
    $this->body      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->body      .= '<p>A new order has been placed on the Star Dream Cakes website:</p>';
    $this->body      .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
    $this->body      .= '<tr><th>Order Number</td><td>' . $orderDetails['order_number'] . '</td></tr>';
    $this->body      .= '<tr><th>Date Order Placed</th><td>' . $orderDetails['order_placed'] . '</td></tr>';
    $this->body      .= '<tr><th>Required Date</th><td>' . $orderDetails['datetime'] . '</td></tr>';
    $this->body      .= '<tr><th>Date of Celebration</th><td>' . $orderDetails['celebration_date'] . '</td></tr>';
    $this->body      .= '<tr><th>Comments</th><td>' . $orderDetails['comments'] . '</td></tr>';
    $this->body      .= '<tr><th>Filling</th><td>' . $orderDetails['filling_name'] . '</td></tr>';
    $this->body      .= '<tr><th>Decoration</th><td>' . $orderDetails['decor_name'] . '</td></tr>';
    $this->body      .= '<tr><th>Cake Type</th><td>' . $orderDetails['cake_type'] . '</td></tr>';
    $this->body      .= '<tr><th>Cake Size</th><td>' . $orderDetails['cake_size'] . '</td></tr>';
    $this->body      .= '<tr><th>Delivery Type</th><td>' . $orderDetails['delivery_type'] . '</td></tr>';
    $this->body      .= '</table>';
    $this->body      .= '<p>Placed by: ' . $customerDetails['first_name'] . " " . $customerDetails['last_name'] . "</p>";
    $this->body      .= '<p>You can view the order here: <a href="https://www.ivanbrazza.biz/all-orders/?order=';
    $this->body      .= $orderDetails['order_number'] . '">https://www.ivanbrazza.biz/all-orders/?order=';
    $this->body      .= $orderDetails['order_number'] . '</a></p>';
    $this->body      .= '</body></html>';
  }

  public function requestTestimonial($orderNumber) {
    $this->from       = "Star Dream Cakes <orders@ivanbrazza.biz>";

    $this->subject    = 'Your Order Is Complete';

    $this->body       = '<html><body>';
    $this->body      .= '<p>Hi ' . $this->firstName . ',</p>';
    $this->body      .= '<p>Thank you for your order ' . $orderNumber . ', we hope you enjoyed it.</p>';
    $this->body      .= '<p>We would appreciate if you could leave a testimonial on our website regarding ';
    $this->body      .= 'your order. To do so click the following link:</p>';
    $this->body      .= '<a href="https://www.ivanbrazza.biz/testimonials/#submit">https://www.ivanbrazza.biz/testimonials/#submit</a>';
    $this->body      .= '<br><p>Thank you,<br>';
    $this->body      .= 'Star Dream Cakes</p>';
  }

  // A function which sets the recipient
  public function setRecipient($recipient) {
    $this->to = $recipient;
  }

  // A function which sets the first name
  public function setFirstName($namevar) {
    $this->firstName = $namevar;
  }
}
