<?php
  require("common.php");

  // Get order details based on the order number
  $query = "
    SELECT
      a.*, b.*, c.decor_name, c.decor_price, d.filling_name, d.filling_price
    FROM
      orders a, cakes b, decorations c, fillings d
    WHERE
      order_number = :order_number
    AND
      b.cake_id = a.cake_id
    AND
      a.decor_id = c.decor_id
    AND
      a.filling_id = d.filling_id
  ";

  $query_params = array(
    ':order_number' => $_POST['order']
  );

  $db->runQuery($query, $query_params);

  $row = $db->fetch();

  // If the order was a delivery
  if ($row['delivery_type'] === "Deliver To Address")
  {
    // Get the delivery details for the order
    $query = "
      SELECT
        delivery_charge
      FROM
        delivery
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_POST['order']
    );

    $db->runQuery($query, $query_params);

    $deliveryrow = $db->fetch();
    $row['delivery_charge'] = $deliveryrow['delivery_charge'];
  }
  else
  {
    $row['delivery_charge'] = 0;
  }

  echo json_encode($row);
  die();
