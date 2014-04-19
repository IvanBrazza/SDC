<?php
  /**
    customer-list/ - display a list of all users and link each user
    to the all-orders/ page where a list of all orders for that
    user can be viewed.
  **/
  require("../lib/common.php");
  $title = "Customer List";
  $page = "customer-list";

  // Only the admin user can access this page
  if(empty($_SESSION['user']) or $_SESSION['user']['username'] !== "admin")
  {
    header("Location: ../login");
    die();
  }

  // Get customer details
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

  $db->runQuery($query, null);

  $rows = $db->fetchAll();
?>
<?php include("../lib/header.php"); ?>
<div class="row">
  <div class="col-md-12">
    <h1>Customer List</h1> 
  </div>
</div>
<div class="row">
  <div class="col-md-5">
    <form action="../all-orders" method="GET" id="customer_search" class="form-inline" role="form">
      <input type="hidden" id="token" name="token" value="<?php echo $_SESSION['token']; ?>" />
      <div class="input-group">
        <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Enter customer name">
        <span class="input-group-btn">
          <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>   Search all customers</button>
        </span>
      </div>
    </form>
    <script>
      var customerNames = [
        <?php
          foreach ($rows as $row)
          {
            echo "\"" . $row['first_name'] . " " . $row['last_name'] . "\",";
          }
        ?>
      ];
    </script>
  </div>
  <div class="col-md-5">
    <div class="alert alert-danger" id="error_message" style="max-height: 34px;padding-top: 6px;"></div>
  </div>
  <div class="col-md-2"></div>
</div>
<div class="row">
  <div class="col-md-12">
    <table id="orders-js" class="table table-hover">
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
  </div>
</div>
<?php include("../lib/footer.php"); ?>
