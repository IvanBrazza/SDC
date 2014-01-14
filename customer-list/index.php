<?php
  /**
    customer-list/ - display a list of all users and link each user
    to the all-orders/ page where a list of all orders for that
    user can be viewed.
  **/
  require("../lib/common.php");
  $title = "Customer List";
  $page = "customer-list";

  if(empty($_SESSION['user']) or $_SESSION['user']['username'] !== "admin")
  {
    header("Location: ../login");
    die();
  }

  forceHTTPS();

  $query = "
    SELECT
      customer_id,
      username,
      email,
      first_name,
      last_name,
      address,
      postcode
    FROM
      users
  ";

  $query_params = array(
    ':user_id' => $_SESSION['user']['customer_id']
  );

  try
  {
    $stmt     = $db->prepare($query);
    $result   = $stmt->execute($query_params);
  }
  catch(PDOException $ex)
  {
    die("Failed to run query: " . $ex->getMessage());
  }

  $rows = $stmt->fetchAll();
?>
<?php include("../lib/header.php"); ?>
  <h1>Customer List</h1> 
  <table id="orders-js">
    <thead>
      <tr> 
        <th>Username</th> 
        <th>E-Mail Address</th>
        <th>First Name</th>
        <th>Last Name</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($rows as $row): ?>
        <tr>
          <td><a href="../all-orders/?id=<?php echo $row['customer_id']; ?>"></a><?php echo htmlentities($row['username'], ENT_QUOTES, 'UTF-8'); ?></td> 
          <td><?php echo htmlentities($row['email'], ENT_QUOTES, 'UTF-8'); ?></td> 
          <td><?php echo htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php include("../lib/footer.php"); ?>
