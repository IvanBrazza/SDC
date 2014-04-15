<?php
  include("../common.php");

  $query = "
    SELECT
      address,
      postcode
    FROM
      users
    WHERE
      customer_id = :customer_id
  ";

  $query_params = array(
    ':customer_id' => $_POST['id']
  );

  $db->runQuery($query, $query_params);
  $row = $db->fetch();

  $response = array(
    "address"  => $row['address'],
    "postcode" => $row['postcode']
  );

  echo json_encode($response);
  die();
