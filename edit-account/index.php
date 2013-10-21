<?php
  /**
   edit-account/ - let the user edit account details
   i.e. name, address, postcode, phone number, email
   address, password
  **/
  require("../lib/common.php");
  $title = "Edit Account";
  $page = "edit-account";

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
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
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
      $salt       = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
      $password   = hash('sha256', $_POST['password'] . $salt);
      for ( $i = 0; $i < 65536; $i++ )
      {
        $password = hash('sha256', $password . $salt);
      }
    }
    else
    {
      $password   = null;
      $salt       = null;
    }

    $query = "
      UPDATE 
        users
      SET
        email       = :email,
        postcode    = :postcode,
        phone       = :phone,
        address     = :address,
        first_name  = :first_name,
        last_name   = :last_name
    ";

    if ($password !== null)
    {
      $query .= "
        , password  = :password
        , salt      = :salt
      ";
      $query_params[':password']  = $password;
      $query_params[':salt']      = $salt;
    }

    $query .= "
      WHERE
        customer_id = :user_id
    ";

    $query_params = array(
      ':email'        => $_POST['email'],
      ':user_id'      => $_SESSION['user']['customer_id'],
      ':postcode'     => $_POST['postcode'],
      ':phone'        => $_POST['phone'],
      ':address'      => $_POST['address'],
      ':first_name'   => $_POST['first_name'],
      ':last_name'    => $_POST['last_name']
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

    // Update the _SESSION variables
    $_SESSION['user']['email']        = $_POST['email'];
    $_SESSION['user']['postcode']     = $_POST['postcode'];
    $_SESSION['user']['phone']        = $_POST['phone'];
    $_SESSION['user']['address']      = $_POST['address'];
    $_SESSION['user']['first_name']   = $_POST['first_name'];
    $_SESSION['user']['last_name']    = $_POST['last_name'];

    header("Location: ../edit-account/?update=success");
    die();
  }
  else
  {
    $query = "
      SELECT
        *
      FROM
        users
      WHERE
        customer_id = :id
    ";

    $query_params = array(
      ':id' => $_SESSION['user']['customer_id']
    );

    try
    {
      $stmt     = $db->prepare($query);
      $result   = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getMessage());
    }

    $row = $stmt->fetch();
    $_SESSION['user'] = $row;
  }
?>
<?php include("../lib/header.php"); ?>
    <div class="form">
      <h1>Edit Account</h1>
      <div class="success">
        <span class="success_message">
          <?php echo $display_message; ?>
        </span>
      </div>
      <form action="index.php" method="POST" id="edit-account-form">
        <div>
          <label>Username</label>
          <b><?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?></b>
        </div>
        <div>
          <label for="first_name">First Name</label>
          <input type="text" name="first_name" id="first_name" value="<?php echo htmlentities($_SESSION['user']['first_name'], ENT_QUOTES, 'UTF-8'); ?>" onkeyup="validateInput('#first_name', '#first_name_error')" onchange="validateInput('#first_name', '#first_name_error')"> 
        </div>
        <div id="first_name_error" class="validate-error"></div>
        <div>
          <label for="last_name">Last Name</label>
          <input type="text" name="last_name" id="last_name" value="<?php echo htmlentities($_SESSION['user']['last_name'], ENT_QUOTES, 'UTF-8'); ?>" onkeyup="validateInput('#last_name', '#last_name_error')" onchange="validateInput('#last_name', '#last_name_error')"> 
        </div>
        <div id="last_name_error" class="validate-error"></div>
        <div>
          <label for="email">E-Mail Address</label>
          <input type="text" name="email" id="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>" onkeyup="validateEmail()" onchange="validateEmail()"> 
        </div>
        <div id="email-error" class="validate-error"></div>
        <div>
          <label for="password">Password</label>
          <input type="password" name="password" id="password" value="" /><br /> 
          <small><i>(leave blank if you do not want to change your password)</i></small>
        </div>
        <div>
          <label for="address">Address</label>
          <input type="text" name="address" id="address" value="<?php echo htmlentities($_SESSION['user']['address'], ENT_QUOTES, 'UTF-8'); ?>" onkeyup="validateInput('#address', '#address_error')" onchange="validateInput('#address', '#address_error')">
        </div>
        <div id="address_error" class="validate-error"></div>
        <div>
          <label for="postcode">Postcode</label>
          <input type="text" name="postcode" id="postcode" value="<?php echo htmlentities($_SESSION['user']['postcode'], ENT_QUOTES, 'UTF-8'); ?>" onkeyup="validatePostcode()" onchange="validatePostcode()">
        </div>
        <div id="postcode_error" class="validate-error"></div>
        <div>
          <label for="phone">Phone number</label>
          <input type="text" name="phone" id="phone" value="<?php echo $_SESSION['user']['phone'] ?>" onkeyup="validatePhone()" onchange="validatePhone()">
        </div>
        <div id="phone_error" class="validate-error"></div>
        <input type="submit" value="Update Account" /> 
      </form>
    </div>
<?php include("../lib/footer.php"); ?>
