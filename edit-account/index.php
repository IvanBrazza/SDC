<?php
  require("../lib/common.php");
  $title = "Edit Account";

  if (empty($_SESSION['user']))
  {
    header("Location: ../login");
    die();
  }

  if (!empty($_GET['update']))
  {
    if ($_GET['update'] === "success")
    {
      $display_message = "Account updated.";
    }
  }

  if (!empty($_POST))
  {
    // Check if updated email is already in use
    if ($_POST['email'] != $_SESSION['user']['email'])
    {
      $query = "
        SELECT
          *
        FROM
          users
        WHERE
          email = :email
      ";

      $query_params = array(
        ':email' => $_POST['email']
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

      $row = $stmt->fetch();
      if ($row)
      {
        die("This email address is already in use");
      }
    }
    
    // Update password
    if (!empty($_POST['password']))
    {
      $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
      $password = hash('sha256', $_POST['password'] . $salt);
      for ( $i = 0; $i < 65536; $i++ )
      {
        $password = hash('sha256', $password . $salt);
      }
    }
    else
    {
      $password = null;
      $salt = null;
    }

    $query = "
      UPDATE 
        users
      SET
        email = :email,
        postcode = :postcode,
        phone = :phone,
        address = :address
    ";

    if ($password !== null)
    {
      $query .= "
        , password = :password
        , salt = :salt
      ";
      $query_params[':password'] = $password;
      $query_params[':salt'] = $salt;
    }

    $query .= "
      WHERE
        customer_id = :user_id
    ";

    $query_params = array(
      ':email' => $_POST['email'],
      ':user_id' => $_SESSION['user']['customer_id'],
      ':postcode' => $_POST['postcode'],
      ':phone' => $_POST['phone'],
      ':address' => $_POST['address']
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

    $_SESSION['user']['email'] = $_POST['email'];
    $_SESSION['user']['postcode'] = $_POST['postcode'];
    $_SESSION['user']['phone'] = $_POST['phone'];
    $_SESSION['user']['address'] = $_POST['address'];

    header("Location: ../edit-account/?update=success");
    die();
  }
?>
<?php include("../lib/header.php"); ?>
  <div class="container">
    <div class="form">
      <h1>Edit Account</h1>
      <div class="success">
        <span class="success_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <form action="index.php" method="POST" data-validate="parsley">
        <div>
          <label>Username</label>
          <b><?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?></b>
        </div>
        <div>
          <label for="email">E-Mail Address</label>
          <div class="parsley-container">
            <input type="text" name="email" id="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>" data-required="true" data-type="email" data-error-message="Please enter an email address" data-trigger="change" /> 
          </div>
        </div>
        <div>
          <label for="password">Password</label>
          <div class="parsley-container">
            <input type="password" name="password" id="password" value="" /><br /> 
          </div><br />
          <small><i>(leave blank if you do not want to change your password)</i></small>
        </div>
        <div>
          <label for="address">Address</label>
          <div class="parsley-container">
            <input type="text" name="address" id="address" value="<?php echo htmlentities($_SESSION['user']['address'], ENT_QUOTES, 'UTF-8'); ?>" data-required="true" data-error-message="Please enter your address" data-trigger="change">
          </div>
        </div>
        <div>
          <label for="postcode">Postcode</label>
          <div class="parsley-container">
            <input type="text" name="postcode" id="postcode" value="<?php echo htmlentities($_SESSION['user']['postcode'], ENT_QUOTES, 'UTF-8'); ?>" data-required="true" data-error-message="Please enter your postcode" data-trigger="change"/>
          </div>
        </div>
        <div>
          <label for="phone">Phone number</label>
          <div class="parsley-container">
            <input type="text" name="phone" id="phone" value="<?php echo $_SESSION['user']['phone'] ?>" data-required="true" data-error-message="Please enter your phone number" data-trigger="change" />
          </div>
        </div>
        <input type="submit" value="Update Account" /> 
      </form>
    </div>
  </div>
<?php include("../lib/footer.php"); ?>
