<?php
  /**
    print/ - display a page with order details in a
    format suitable for printing
  **/
  require("../lib/common.php");

  if ($_GET)
  {
    $query = "
      SELECT
        a.*,
        b.filling_price, b.filling_name,
        c.decor_price, c.decor_name,
        d.cake_size, d.cake_type,
        e.first_name, e.last_name, e.address, e.postcode, e.phone, e.email
      FROM
        orders a,
        fillings b,
        decorations c,
        cakes d,
        users e
      WHERE
        order_number = :order_number
      AND
        a.filling_id = b.filling_id
      AND
        a.decor_id = c.decor_id
      AND
        a.cake_id = d.cake_id
      AND
        a.customer_id = e.customer_id
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
    );
    
    $db->runQuery($query, $query_params);
    $row = $db->fetch();

    if ($row['delivery_type'] == "Deliver To Address")
    {
      $query = "
        SELECT
          delivery_charge
        FROM
          delivery
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_GET['order']
      );

      $db->runQuery($query, $query_params);

      $temp = $db->fetch();
      $row['delivery_charge'] = $temp['delivery_charge'];
    }
  }
  else
  {
    header("Location: ../oops");
    die();
  }
?>
<html>
<head>
  <title>Order <?php echo $_GET['order']; ?></title>
</head>
<body>
  <img src="../img/header-logo.png">
  <h1>Order <?php echo $_GET['order']; ?></h1>
  <table>
    <tr>
      <th>Order Placed</th>
      <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
    </tr>
    <tr>
      <th>Required Date</th>
      <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?> </td>
    </tr>
    <tr>
      <th>Date Of Celebration</th>
      <td><?php echo $row['celebration_date']; ?></td>
    </tr>
    <tr>
      <th>Status</th>
      <td><?php echo $row['status']; ?></td>
    </tr>
    <tr>
      <th>Comments</th>
      <td><?php echo htmlentities($row['comments'], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <tr>
      <th>Filling</th>
      <td><?php echo htmlentities($row['filling_name'], ENT_QUOTES, 'UTF-8')." - &pound;".htmlentities($row['filling_price'], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <tr>
      <th>Decoration</th>
      <td><?php echo htmlentities($row['decor_name'], ENT_QUOTES, 'UTF-8')." - &pound;".htmlentities($row['decor_price'], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <tr>
      <th>Cake Type</th>
      <td><?php echo htmlentities($row['cake_type'], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <tr>
      <th>Cake Size</th>
      <td><?php echo htmlentities($row['cake_size'], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <?php if (!empty($row['image'])) : ?>
      <tr>
        <th>Image</th>
        <td>Yes</td>
      </tr>
    <?php endif; ?>
    <tr>
      <th>Base Price</th>
      <td>&pound;<?php echo $row['base_price']; ?></td>
    </tr>
    <?php if ($row['delivery_type'] == "Deliver To Address") : ?>
      <tr>
        <th>Delivery Charge</th>
        <td>&pound;<?php echo $row['delivery_charge']; ?></td>
      </tr>
    <?php endif; ?>
    <tr>
      <th>Delivery Type</th>
      <td><?php echo htmlentities($row['delivery_type'], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <tr>
      <th>Grand Total</th>
      <td>&pound;<?php echo $row['base_price']+$row['delivery_charge']+$row['filling_price']+$row['decor_price']; ?></td>
    </tr>
  </table>
  <script>
    window.print();
  </script>
</body>
</html>
