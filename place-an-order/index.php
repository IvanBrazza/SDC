<?php
  require("../common.php");
  $title = "Place An Order";

  if(empty($_SESSION['user']))
  {
    header("Location: ../login/?e=pao");
    die();
  }

  if (!empty($_POST))
  {
    $query = "
      INSERT INTO orders (
        customer_id,
        order_number,
        order_date,
        datetime,
        celebration_date,
        status,
        customer_order,
        filling,
        decoration,
        delivery
      ) VALUES (
        :customer_id,
        :order_number,
        :order_date,
        :datetime,
        :celebration_date,
        :status,
        :customer_order,
        :filling,
        :decoration,
        :delivery
      )
    ";

    $order_number = $_SESSION['user']['customer_id'] . rand(10000,99999);
    $order_date = date('Y-m-d');
    $status = "Processing";

    $query_params = array(
      ':customer_id' => $_SESSION['user']['customer_id'],
      ':order_number' => $order_number,
      ':order_date' => $order_date,
      ':datetime' => $_POST['datetime'],
      ':celebration_date' => $_POST['celebration_date'],
      ':status' => $status,
      ':customer_order' => $_POST['order'],
      ':filling' => $_POST['filling'],
      ':decoration' => $_POST['decoration'],
      ':delivery' => $_POST['delivery']
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

    include("emailorder.php");
  }
?>
<?php include("../header.php"); ?>
  <div class="container">
  <div class="form">
    <h1>Place An Order</h1>
    <form action="index.php" method="POST" data-validate="parsley">
      <div>
        <label for="filling">Filling</label>
        <div class="parsley-container">
          <select name="filling" id="filling">
            <option value="None">None</option>
            <option value="Butter Cream">Butter Cream</option>
            <option value="Chocolate">Chocolate</option>
          </select>
        </div>
      </div>
      <div>
        <label for="decoration">Cake to be decorated in</label>
        <div class="parsley-container">
          <select name="decoration" id="decoration">
            <option value="None">None</option>
            <option value="Royal Icing">Royal Icing</option>
            <option value="Regal Icing">Regal Icing</option>
            <option value="Butter Cream">Butter Cream</option>
            <option value="Chocolate">Chocolate</option>
            <option value="Coconut">Coconut</option>
          </select>
        </div>
      </div>
      <div>
        <label for="celebration_date">Date of celebration</label>
        <div class="parsley-container">
          <input type="text" name="celebration_date" class="date" data-required="true" data-type="dateIso" data-error-message="Please enter a date">
        </div>
      </div>
      <div id="order">
        <label for="order">Your order</label>
        <div class="parsley-container">
          <textarea name="order" id="order" rows="6" cols="30" data-trigger="change" data-required="true" data-error-message="Please enter your order"></textarea>
        </div>
      </div>
      <div>
        <label for="delivery">Delivery options</label>
        <div class="parsley-container">
          <select name="delivery" id="delivery">
            <option value="Collection">You will collect the cake</option>
            <option value="Deliver To Address">The cake will be delivered to your address</option>
          </select>
        </div>
      </div>
      <div>
        <label for="datetime">Date and time to collect/deliver order</label>
        <div class="parsley-container">
          <input type="text" id="datetime" name="datetime" data-required="true" data-trigger="change" data-error-message="Please enter a date and time">
        </div>
      </div>
      <br /><br />
      <input type="submit" value="Submit Order" />
    </form>
  </div>
  </div>
<?php include("../footer.php");
