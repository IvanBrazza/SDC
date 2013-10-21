<?php
  require("../lib/common.php");
  $title = "Forgot Password";

  if (!empty($_GET['e']))
  {
    if ($_GET['e'] === "email")
    {
      $display_message = "That email isn't registered with us.";
    }
  }

  if ($_POST)
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
      die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
    }

    $row = $stmt->fetch();

    if (!$row)
    {
      header("../forgot-password/?e=email");
      die();
    }
    else
    {
      $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
      $plainpassword = uniqid();
      $password = hash('sha256', $plainpassword . $salt);
      for ($i = 0; $i < 65536; $i++)
      {
        $password = hash('sha256', $password . $salt);
      }

      $query = "
        UPDATE
          users
        SET
          password = :password,
          salt     = :salt
        WHERE
          email    = :email
      ";

      $query_params = array(
        ':password' => $password,
        ':salt'     => $salt,
        ':email'    => $row['email']
      );

      try
      {
        $stmt     = $db->prepare($query);
        $result   = $stmt->execute($query_params);
      }
      catch(PDOException $ex)
      {
        die("Failed to execute query: " . $ex->getMessage() . " query: " . $query);
      }

      include "../lib/email.php";
      emailPassword($to, $row['email'], $plainpassword);
    }
  }
?>
<?php include("../lib/header.php"); ?>
  <?php if ($_POST) : ?>
    <p>We've sent you a new password to your email address <?php echo $_POST['email']; ?>.</p>
  <?php else : ?>
    <div class="form">
      <h1>Forgot Password</h1>
      <p>Enter the email you registered with below and we'll send you an email containing your new password.</p>
      <form action="index.php" method="POST">
        <div>
          <label for="email">EMail</label>
          <input type="text" name="email" id="email" onkeyup="validateEmail()" onchange="validateEmail()">
        </div>
        <div id="email-error" class="validate-error"></div>
        <div class="error">
          <span class="error_message">
            <?php echo $display_message; ?>
          </span>
        </div>
        <input type="submit" value="Reset Password" name="submit">
      </form>
    </div>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
