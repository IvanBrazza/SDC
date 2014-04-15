<?php
  include("common.php");

  $query = "
    SELECT
      *
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

  echo json_encode($row);
  die();
