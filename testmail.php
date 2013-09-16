<?php
  $to = "dudeman1996@gmail.com";
  $subject = "test";
  $message = "test";
  $from = "info@ivanbrazza.biz";
  $headers = "From:" . $from;
  mail($to,$subject,$message,$headers);
  echo("Mail sent.");
?>
