<?php
  /**
    all-orders/ - display all orders, a specific order, or all orders
    by a specific customer number.
  **/
  require("../lib/common.php");
  $page = "all-orders";

  // Only the admin user can access this page
  if(empty($_SESSION['user']) or $_SESSION['user']['username'] !== "admin")
  {
    header("Location: ../login/?redirect=" . $_SERVER['REQUEST_URI']);
    die();
  }

  // Display messages based on GET
  if (!empty($_GET['completed']))
  {
    switch ($_GET['completed']){
      case "success":
        $display_message = "Order completed.";
        break;
      case "fail":
        $display_message = "Complete failed.";
        break;
    }
  }
  else if (!empty($_GET['new-order']))
  {
    if ($_GET['new-order'] === "added")
    {
      $display_message = "Order added.";
    }
  }

  // If a single order is to be displayed, get
  // the details about that order, else if a
  // user ID is in the GET, then get all the
  // orders by that user
  if (!empty($_GET['order']))
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
  }
  else if (!empty($_GET['id']))
  {
    $query = "
      SELECT
        a.order_number, a.order_placed, a.datetime, a.status, a.completed,
        b.first_name, b.last_name, b.address, b.postcode, b.phone, b.email
      FROM
        orders a,
        users b
      WHERE
        a.customer_id = :get_id
      AND
        b.customer_id = a.customer_id
    ";

    $query_params = array(
      ':get_id' => $_GET['id']
    );

    // If sort is in GET then sort the orders
    // by the GET details, otherwise sort by
    // most recent order at the top by default
    if (!empty($_GET['sort']))
    {
      $query .= "
        ORDER BY
          a." . $_GET['col'] . " " . $_GET['sort']
      ;
    }
    else
    {
      $query .= "
        ORDER BY
          a.order_placed ASC
      ";
    }
  }
  else
  {
    $query = "
      SELECT
        order_number,
        order_placed,
        datetime,
        status,
        completed
      FROM
        orders
    ";

    // If sort is in GET then sort the orders
    // by the GET details, otherwise sort by
    // most recent order at the top by default
    if (!empty($_GET['sort']))
    {
      $query .= "
        ORDER BY
          orders." . $_GET['col'] . " " . $_GET['sort']
      ;
    }
    else
    {
      $query .= "
        ORDER BY
          orders.order_placed DESC
      ";
    }
  }

  if (!empty($query_params))
  {
    $db->runQuery($query, $query_params);
  }
  else
  {
    $db->runQuery($query, null);
  }

  if (!empty($_GET['order']))
  {
    $row = $db->fetch();
    // If the delivery type is deliver rather than
    // collection, then get the delivery details
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
    $rows = $db->fetchAll();
  }

  // If a single order is being displayed or all orders
  // by a customer, create a new Delivery object and
  // calculate the distance to be displayed
  if (!empty($_GET['order']) or !empty($_GET['id']))
  {
    include("../lib/delivery.class.php");
    $delivery = new Delivery;

    if (!empty($_GET['id']))
    {
      $delivery->setAddress($rows[0]['address']);
      $delivery->setPostcode($rows[0]['postcode']);
    }
    else
    {
      $delivery->setAddress($row['address']);
      $delivery->setPostcode($row['postcode']);
    }
    $delivery->calculateDistance();
  }

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  // Set the page title based on what is being displayed
  if (!$_GET or !empty($_GET['completed']) or !empty($_GET['sort']))
  {
    $title = "All Orders";
  }
  else
  {
    if (!empty($_GET['id']))
    {
      $title = $rows[0]['first_name'] . "'s Orders | Star Dream Cakes";
    }
    else
    {
      $title = $row['first_name'] . "'s Orders | Star Dream Cakes";
    }
  }
  
  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");
?>
<?php include("../lib/header.php"); ?>
  <!-- if user clicked on order number or searched for an order -->
  <?php if (!empty($_GET['order'])) : ?>
    <?php if (!empty($row['image'])) : ?>
      <div class="modal fade" id="image-modal" role="modal" aria-hidden="true" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title" id="myModalLabel">Order Image</h4>
            </div>
            <div class="modal-body">
              <img src="https://s3.amazonaws.com/SDC-images/gallery/<?php echo $row['customer_id'] . "/" . $row['image']; ?>" class="modal-image">
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <div class="row">
      <div class="col-md-6">
        <h1>Order <?php echo $row['order_number']; ?>
        <?php if ($row['completed'] === "0") : ?>
          <form id="complete-order">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number" id="order_number">
            <button type="submit" class="btn btn-default">Complete Order</button>
          </form>
        <?php else : ?>
          (completed)
        <?php endif; ?>
        </h1>
      </div>
      <div class="col-md-6">
        <div class="alert alert-danger" id="error_message" style="margin-top:20px;"></div>
        <div class="alert alert-success" id="success_message" style="margin-top:20px;"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
        <table id="single_order" class="table table-condensed">
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
            <td>
              <?php if ($row['completed'] === "0") : ?>
                <form class="form-inline update" role="form">
                  <div class="form-group">
                    <select name="status" class="form-control">
                      <option <?php if ($row['status'] == "Processing") : ?>selected<?php endif; ?> value="Processing">Processing</option>
                      <option <?php if ($row['status'] == "Dispatched") : ?>selected<?php endif; ?> value="Dispatched">Dispatched</option>
                    </select>
                  </div>
                  <input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number">
                  <input type="hidden" value="<?php echo $row['first_name']; ?>" name="first_name">
                  <input type="hidden" value="<?php echo $row['email']; ?>" name="email">
                  <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
                  <button type="submit" class="btn btn-default btn-sm">Update</button>
                </form>
              <?php else : ?>
                <?php echo $row['status']; ?>
              <?php endif; ?>
            </td>
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
              <td><button class="btn btn-link" data-toggle="modal" data-target="#image-modal">Click here to view image</button></td>
            </tr>
          <?php endif; ?>
          <tr>
            <th>Base Price</th>
            <td>
              <?php if ($row['completed'] === "0") : ?>
                <form class="form-inline update" role="form">
                  <input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number">
                  <div class="form-group">
                    <label>&pound;</label>
                    <input name="base_price" type="text" value="<?php echo $row['base_price']; ?>" class="form-control" style="width:45px;">
                  </div>
                  <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                  <button type="submit" class="btn btn-default btn-sm">Update</button>
                </form>
              <?php else : ?>
                <?php echo "&pound;" . $row['base_price']; ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php if ($row['delivery_type'] == "Deliver To Address") : ?>
            <tr>
              <th>Delivery Charge</th>
              <td>
                <?php if ($row['completed'] === "0") : ?>
                  <form class="form-inline update" role="form">
                    <input type="hidden" value="<?php echo $row['order_number']; ?>" name="order_number">
                    <div class="form-group">
                      <label>&pound;</label>
                      <input name="delivery_charge" type="text" value="<?php echo $row['delivery_charge']; ?>" style="width:45px;" class="form-control">
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    <button type="submit" class="btn btn-default btn-sm">Update</button>
                  </form>
                <?php else : ?>
                  <?php echo "&pound;" . $row['delivery_charge']; ?>
                <?php endif; ?>
              </td>
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
      </div>
      <div class="col-md-4">
        <p>Placed by <?php echo htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <br />
        <span class="title">Address:</span><br />
        <?php echo htmlentities($row['address'], ENT_QUOTES, 'UTF-8'); ?><br />
        <?php echo htmlentities($row['postcode'], ENT_QUOTES, 'UTF-8'); ?><br />
        <i>(<?php echo $delivery->getDistance(); ?> miles away)</i><br/>
        <a href="//www.<?php echo $siteUrl; ?>/get-directions?id=<?php echo $row['customer_id']; ?>"><button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-road"></span>   Get directions to this address</button></a>
        <br />
        <br />
        <span class="title">Phone: </span>
        <?php echo $row['phone']; ?><br />
        <br /><br />
        <?php if ($row['difference'] != 0) : ?>
          <div class="alert alert-danger" style="display:block;">
            <span class="glyphicon glyphicon-warning-sign"></span>
            There is a difference on this order of <strong>&pound;<?php echo $row['difference']; ?></strong>.
            <?php if ($row['difference'] > 0) : ?>
              This means that Star Dream Cakes owes <?php echo htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8'); ?> <b>&pound;<?php echo abs($row['difference']); ?></b>.
            <?php elseif ($row['difference'] < 0) : ?>
              This mean that <?php echo htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8'); echo " "; echo htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8'); ?> owes Star Dream Cakes <b>&pound;<?php echo abs($row['difference']); ?></b>.
            <?php endif; ?>
            <br>
            A difference occurs if the customer has edited their order after it has been placed, and the total between the original
            and the edited order has changed.
          </div>
        <?php endif; ?>
      </div>
    </div>
  <!-- show all orders by a customer -->
  <?php elseif (!empty($_GET['id'])) : ?>
    <div class="row">
      <div class="col-md-12">
        <h1>Orders placed by <?php echo htmlentities($rows[0]['first_name'], ENT_QUOTES, 'UTF-8') . " " . htmlentities($rows[0]['last_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <span class="title">Address:</span><br />
        <?php echo htmlentities($rows[0]['address'], ENT_QUOTES, 'UTF-8'); ?><br />
        <?php echo htmlentities($rows[0]['postcode'], ENT_QUOTES, 'UTF-8'); ?><br />
        <i>(<?php echo $delivery->getDistance(); ?> miles away)</i><br />
        <a href="//www.<?php echo $siteUrl; ?>/get-directions?id=<?php echo $_GET['id']; ?>"><button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-road"></span>   Get directions to this address</button></a>
        <br />
        <br />
        <span class="title">Phone: </span>
        <?php echo htmlentities($rows[0]['phone'], ENT_QUOTES, 'UTF-8'); ?><br />
        <br><br>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <?php if (empty($rows)) : ?>
          <h3>There are no outstanding orders</h3>
        <?php else : ?>
          <table class="table table-hover" id="orders-js">
            <caption>Outstanding Orders</caption>
            <thead>
              <tr>
                <th>Order Number <span class="arrows"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?id=<?php echo $_GET['id']; ?>&sort=DESC&col=order_number"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=order_number"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Order Placed <span class="arrows"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?id=<?php echo $_GET['id']; ?>&sort=DESC&col=order_placed"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=order_placed"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Required Date <span class="arrows"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?id=<?php echo $_GET['id']; ?>&sort=DESC&col=datetime"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=datetime"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Status <span class="arrows"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?id=<?php echo $_GET['id']; ?>&sort=DESC&col=status"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=status"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($rows as $row): ?>
                <?php if ($row['completed'] == 0) : ?>
                  <tr>
                    <td><a href="//www.<?php echo $siteUrl; ?>/all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
                    <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                    <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                    <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table id="orders-js" class="table table-hover">
          <caption>Completed Orders</caption>
          <thead>
            <tr>
              <th>Order Number <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?id=<?php echo $row['customer_id']; ?>&sort=DESC&col=order_number"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=order_number"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Order Placed <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?id=<?php echo $row['customer_id']; ?>&sort=DESC&col=order_placed"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=order_placed"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Required Date <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?id=<?php echo $row['customer_id']; ?>&sort=DESC&col=datetime"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=datetime"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Status <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?id=<?php echo $row['customer_id']; ?>&sort=DESC&col=status"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=status"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $row): ?>
              <?php if ($row['completed'] == 1) : ?>
                <tr>
                  <td><a href="//www.<?php echo $siteUrl; ?>/all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
                  <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                  <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                  <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else : ?>
    <!--Show all orders-->
    <div class="row">
      <div class="col-md-12">
        <h1>All Orders</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5">
        <form action="../all-orders" method="GET" id="order_search" class="form-inline" role="form">
          <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['token']; ?>" />
          <div class="input-group">
            <input type="search" id="order_number" name="order" class="form-control" placeholder="Enter order number" />
            <span class="input-group-btn">
              <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>   Search all orders</button>
            </span>
          </div>
        </form>
        <script>
          var orderNumbers = [
            <?php
              foreach ($rows as $row)
              {
                echo "\"" . $row['order_number'] . "\",";
              }
            ?>
          ];
        </script>
      </div>
      <div class="col-md-5">
        <div class="alert alert-danger" id="error_message" style="max-height: 34px;padding-top: 6px;"></div>
        <div class="alert alert-success" style="max-height:34px;padding-top:6px;<?php if ($display_message) : ?>display:block<?php endif; ?>">
          <?php echo $display_message; ?>
        </div>
      </div>
      <div class="col-md-2">
        <a href="//www.<?php echo $siteUrl; ?>/add-order" class="pull-right"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus"></span>   Add Order</button></a>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <?php if (empty($rows)) : ?>
          <h3>There are no outstanding orders</h3>
        <?php else : ?>
          <table class="table table-hover" id="orders-js">
            <caption>Outstanding Orders</caption>
            <thead>
              <tr>
                <th>Order Number <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=DESC&col=order_number"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=order_number"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Order Placed <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=DESC&col=order_placed"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=order_placed"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Required Date <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=DESC&col=datetime"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=datetime"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
                <th>Status <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=DESC&col=status"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=status"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($rows as $row): ?>
                <?php if ($row['completed'] == 0) : ?>
                  <tr>
                    <td><a href="//www.<?php echo $siteUrl; ?>/all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
                    <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                    <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                    <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table id="orders-js" class="table table-hover">
          <caption>Completed Orders</caption>
          <thead>
            <tr>
              <th>Order Number <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=DESC&col=order_number"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=order_number"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Order Placed <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=DESC&col=order_placed"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=order_placed"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Required Date <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=DESC&col=datetime"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=datetime"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
              <th>Status <span class="arrow"><a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=DESC&col=status"><span class="glyphicon glyphicon-chevron-up"></span></a> <a href="//www.<?php echo $siteUrl; ?>/all-orders/?sort=ASC&col=status"><span class="glyphicon glyphicon-chevron-down"></span></a></span></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $row): ?>
              <?php if ($row['completed'] == 1) : ?>
                <tr>
                  <td><a href="//www.<?php echo $siteUrl; ?>/all-orders/?order=<?php echo $row['order_number']; ?>"></a><?php echo $row['order_number']; ?></td>
                  <td><?php echo substr(htmlentities($row['order_placed'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                  <td><?php echo substr(htmlentities($row['datetime'], ENT_QUOTES, 'UTF-8'), 0, -3); ?></td>
                  <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
