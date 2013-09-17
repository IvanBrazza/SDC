<?php
  require("../lib/common.php");
  $title = "Memberlist";

  if(empty($_SESSION['user']))
  {
    header("Location: login.php");
    die("Redirecting to login.php");
  }

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
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch(PDOException $ex)
  {
    die("Failed to run query: " . $ex->getMessage());
  }

  $rows = $stmt->fetchAll();
?>
<?php include("../lib/header.php"); ?>
  <div class="container">
    <div class="orders">
      <h1>Memberlist</h1> 
      <table> 
        <tr> 
          <th>ID</th> 
          <th>Username</th> 
          <th>E-Mail Address</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Address</th>
          <th>Postcode</th>
        </tr> 
        <?php foreach($rows as $row): ?>
          <tr> 
            <td><a href="allorders.php?id=<?php echo $row['customer_id']; ?>"><?php echo $row['customer_id']; ?></a></td> 
            <td><?php echo htmlentities($row['username'], ENT_QUOTES, 'UTF-8'); ?></td> 
            <td><?php echo htmlentities($row['email'], ENT_QUOTES, 'UTF-8'); ?></td> 
            <td><?php echo htmlentities($row['first_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['address'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlentities($row['postcode'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
<?php include("../lib/footer.php"); ?>
