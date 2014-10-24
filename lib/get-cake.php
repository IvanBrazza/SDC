<?php
  /**
    lib/get-cake.php - a script which is called via AJAX
    which gets the cake price to display on the order form
  **/
  require("common.php");
  
  if ($_POST) {
    $query = "
      SELECT
        cake_price
      FROM
        cakes
      WHERE
        cake_size = :cake_size
      AND
        cake_type = :cake_type
    ";

    $query_params = array(
      ':cake_size' => $_POST['size'],
      ':cake_type' => $_POST['type']
    );

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

    $response = array(
      'status' => 'success',
      'price'  => $row['cake_price']
    );

    echo json_encode($response);
  }
?>
