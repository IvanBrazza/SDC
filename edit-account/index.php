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

  // Generate token
  $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

  if (!empty($_GET['update']))
  {
    if ($_GET['update'] === "success")
    {
      $display_message = "Account updated.";
    }
  }
  else if (!empty($_GET['e']))
  {
    if ($_GET['e'] === "email")
    {
      $display_message = "That email address is already in use.";
    }
  }

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
?>
<?php include("../lib/header.php"); ?>
    <div class="form">
      <h1>Edit Account</h1>
      <div class="success">
        <span class="success_message" id="success_message">
          <?php if (!empty($_GET) and !empty($_GET['update'])) {echo $display_message;} ?>
        </span>
      </div>
      <div class="error">
        <span class="error_message" id="error_message">
          <?php if (!empty($_GET) and !empty($_GET['e'])) {echo $display_message;} ?>
        </span>
      </div>
      <form action="index.php" method="POST" id="edit-account-form">
        <div>
          <label>Username</label>
          <b><?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?></b>
        </div>
        <div>
          <label for="first_name">First Name</label>
          <input type="text" name="first_name" id="first_name" value="<?php echo htmlentities($_SESSION['user']['first_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#first_name', '#first_name_error')"> 
        </div>
        <div id="first_name_error" class="validate-error"></div>
        <div>
          <label for="last_name">Last Name</label>
          <input type="text" name="last_name" id="last_name" value="<?php echo htmlentities($_SESSION['user']['last_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#last_name', '#last_name_error')"> 
        </div>
        <div id="last_name_error" class="validate-error"></div>
        <div>
          <label for="email">E-Mail Address</label>
          <input type="text" name="email" id="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.email()"> 
        </div>
        <div id="email-error" class="validate-error"></div>
        <div>
          <label for="password">Password</label>
          <input type="password" name="password" id="password" value="" /><br /> 
          <small><i>(leave blank if you do not want to change your password)</i></small>
        </div>
        <div>
          <label for="address">Address</label>
          <input type="text" name="address" id="address" value="<?php echo htmlentities($_SESSION['user']['address'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.input('#address', '#address_error')">
        </div>
        <div id="address_error" class="validate-error"></div>
        <div>
          <label for="postcode">Postcode</label>
          <input type="text" name="postcode" id="postcode" value="<?php echo htmlentities($_SESSION['user']['postcode'], ENT_QUOTES, 'UTF-8'); ?>" onchange="validate.postcode()">
        </div>
        <div id="postcode_error" class="validate-error"></div>
        <div>
          <label for="phone">Phone number</label>
          <input type="text" name="phone" id="phone" value="<?php echo $_SESSION['user']['phone'] ?>" onchange="validate.phone()">
        </div>
        <div id="phone_error" class="validate-error"></div>
        <input type="hidden" value="<?php echo $_SESSION['token']; ?>" name="token">
        <input type="submit" value="Update Account" />
        <span class="ajax-load"></span>
      </form>
    </div>
<?php include("../lib/footer.php"); ?>
