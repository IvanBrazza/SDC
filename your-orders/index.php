<?php
  require("common.php");
  
  if(empty($_SESSION['user']))
  {
    header("Location: login.php");
    die("Redirecting to login.php");
  }
  
  $query = "
    SELECT
      customer_id,
      order_number,
      order_date,
      required_date,
      status,
      customer_order
    FROM
      orders
    WHERE
      customer_id = :customer_id
  ";

  $query_params = array(
    ':customer_id' => $_SESSION['user']['customer_id']
  );

  try
  {
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch(PDOException $ex)
  {
    die("Failed to run query: " . $ex->getMessage());
  }

  $rows = $stmt->fetchAll();
?>
<head>
  <title>Private | Star Dream Cakes</title>
  <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
  <link href="css/main.css" rel="stylesheet">
</head>
<body>
  <div class="navbar">
    <ul>
      <li><a href="logout.php">Logout</a></li>
      <li><a href="edit_account.php">Edit Account</a></li>
      <?php if ($_SESSION['user']['username'] === "admin") : ?>
        <li><a href="allorders.php">All Orders</a></li>
        <li><a href="memberlist.php">Memberlist</a></li>
      <?php endif; ?>
      <li class="active"><a href="#">Your Orders</a></li>
      <li><a href="placeanorder.php">Place An Order</a></li>
      <li><a href="testimonials.php">Testimonials</a></li>
      <li><a href="#">About Us</a></li>
      <li><a href="index.php">Home</a></li>
    </ul>
  </div>
  <div class="container">
    <div class="orders">
      <h1><?php echo $_SESSION['user']['username'];?>'s Orders</h1>
      <table style="">
        <tr>
          <th>Order Number</th>
          <th>Order Date</th>
          <th>Required Date</th>
          <th>Status</th>
          <th>Your Order</th>
        </tr>
        <?php foreach($rows as $row): ?>
          <tr>
            <td><?php echo htmlentities($row['order_number'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['required_date'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="word-wrap: break-word;"><?php echo htmlentities($row['customer_order'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>
