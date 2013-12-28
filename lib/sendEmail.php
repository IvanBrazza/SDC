<?php
  require("common.php");
  include_once("email.php");
  $email = new Email;

  $query = "
    SELECT
      a.order_number,
      a.order_placed,
      a.datetime,
      a.celebration_date,
      a.comments,
      a.filling,
      a.decoration,
      a.delivery_type,
      b.cake_type,
      b.cake_size
    FROM
      orders a,
      cakes b
    WHERE
      a.order_number = :order_number
    AND
      a.cake_id = b.cake_id
  ";

  $query_params = array(
    ':order_number' => $_GET['order']
  );

  try
  {
    $stmt     = $db->prepare($query);
    $result   = $stmt->execute($query_params);
  }
  catch(PDOException $ex)
  {
    die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
  }

  $row = $stmt->fetch();

  // Email the order details to the user
  $email->order($row["order_number"],
                $row["order_placed"],
                $row["datetime"],
                $row["celebration_date"],
                $row["comments"],
                $row["filling"],
                $row["decoration"],
                $row["cake_type"],
                $row["cake_size"],
                $row["delivery_type"]);
  $email->setFirstName($argv[2]);
  $email->setRecipient($argv[1]);
  echo "[" . date("Y-m-d H:i:s") . "]: Sending mail to " . $argv[1] . "\r\n";
  $email->send();
  echo "[" . date("Y-m-d H:i:s") . "]: Mail sent to " . $argv[1] . ".\r\n";
  die();
?>
