<?php
  /**
    order-placed/ - thank the user for their order and
    email a confirmation to them or delete it from the
    database if they cancelled it
  **/
  require("../lib/common.php");
  if (!empty($_GET) and $_GET['failed'] == "true")
  {
    $title = "Order cancelled";
  }
  else
  {
    $title = "Thanks for your order!";
  }
  $page = "place-an-order";

  // If the order was successful, get the order details
  // and send an email confirmation. Otherwise, delete the
  // order from the database if it was not successful
  if (!empty($_GET) and $_GET['failed'] == "false")
  {
    include("../lib/email.class.php");
    $email = new Email;

    $query = "
      SELECT
        a.order_number,
        a.order_placed,
        a.datetime,
        a.celebration_date,
        a.comments,
        a.delivery_type,
        a.base_price,
        a.image,
        b.cake_type,
        b.cake_size,
        c.filling_name,
        c.filling_price,
        d.decor_name,
        d.decor_price
      FROM
        orders a,
        cakes b,
        fillings c,
        decorations d
      WHERE
        a.order_number = :order_number
      AND
        a.cake_id = b.cake_id
      AND
        a.filling_id = c.filling_id
      AND
        a.decor_id = d.decor_id
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

    // Email the order details to the user
    $email->setFirstName($_SESSION['user']['first_name']);
    $email->setRecipient($_SESSION['user']['email']);
    $email->order($row);
    $email->send();

    // Email the order details to Fran/Helmira
    $email->setFirstName("Ivan");
    $email->setRecipient("dudeman1996@gmail.com");
    $email->orderAdmin($row, $_SESSION['user']);
    $email->send();
  }
  else if (!empty($_GET) and $_GET['failed'] == "true")
  {
    $query = "
      SELECT
        delivery_type
      FROM
        orders
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
    );

    $db->runQuery($query, $query_params);

    $row = $db->fetch();

    if ($row['delivery_type'] == "Deliver To Address")
    {
      $query = "
        DELETE FROM
          delivery
        WHERE
          order_number = :order_number
      ";

      $query_params = array(
        ':order_number' => $_GET['order']
      );

      $db->runQuery($query, $query_params);
    }
    $query = "
      DELETE FROM
        orders
      WHERE
        order_number = :order_number
    ";

    $query_params = array(
      ':order_number' => $_GET['order']
    );

    $db->runQuery($query, $query_params);
  }
?>
<?php include("../lib/header.php"); ?>
  <?php if (!empty($_GET['failed']) and $_GET['failed'] == "true") : ?>
    <h3>Your order has been cancelled.</h3>
  <?php else : ?>
    <h2>Thank You</h2>
    <p>Here's what you ordered:</p>
    <?php if (!empty($row['image'])) : ?>
      <div class="image-view">
        <img src="<?php echo str_replace("/home/ivanrsfr/www/", "../", $row['image']); ?>" height="400px">
        <div class="close">X</div>
      </div>
    <?php endif; ?>
    <table id="single_order">
      <tr>
        <th>Required Date</th>
        <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?> </td>
      </tr>
      <tr>
        <th>Date Of Celebration</th>
        <td><?php echo $row['celebration_date']; ?></td>
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
        <th>Cake Size</th>
        <td><?php echo htmlentities($row['cake_size'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <tr>
        <th>Cake Type</th>
        <td><?php echo htmlentities($row['cake_type'], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
      <?php if (!empty($row['image'])) : ?>
        <tr>
          <th>Image</th>
          <td><a href="javascript:" id="image-link">Click here to view image</a></td>
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
        <td>
          <?php echo htmlentities($row['delivery_type'], ENT_QUOTES, 'UTF-8'); ?>
          <?php if ($row['delivery_type'] === "Collection") : ?>
            <a href="../get-directions/">Get Directions</a>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <th>Grand Total</th>
        <td>&pound;<?php echo $row['base_price']+$deliveryrow['delivery_charge']+$row['filling_price']+$row['decor_price']; ?></td>
      </tr>
    </table>
    <p>A copy of your order has been emailed to you. Any further updates to your order will be sent to you by email</p>
    <a href="../print/?order=<?php echo $_GET['order']; ?>" target="_blank">Click here to print your order</a>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
