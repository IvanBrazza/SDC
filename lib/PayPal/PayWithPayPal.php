<?php
  /*
    lib/PayPal/PayWithPayPal.php - pay for goods using PayPal
  */

  require("bootstrap.php");
  use PayPal\Api\Amount;
  use PayPal\Api\Details;
  use PayPal\Api\Item;
  use PayPal\Api\ItemList;
  use PayPal\Api\Payer;
  use PayPal\Api\Payment;
  use PayPal\Api\RedirectUrls;
  use PayPal\Api\Transaction;

  $payer = new Payer();
  $payer->setPaymentMethod("paypal");

  $item1 = new Item();
  $item1->setname($_POST['cake_size'] . " " . $_POST['cake_type'] . " cake")
        ->setCurrency("GBP")
        ->setQuantity(1)
        ->setPrice($base_price);

  $query = "
    SELECT
      filling_price, filling_name
    FROM
      fillings
    WHERE
      filling_id = :filling_id
  ";

  $query_params = array(
    ':filling_id' => $_POST['filling']
  );

  try
  {
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch(PDOException $ex)
  {
    die("Failed to execute query: " . $ex->getMessage . " query: " . $query);
  }

  $row = $stmt->fetch();

  $filling = new Item();
  $filling->setname("Filling: " . $row['filling_name'])
          ->setCurrency("GBP")
          ->setQuantity(1)
          ->SetPrice($row['filling_price']);

  $query = "
    SELECT
      decor_price, decor_name
    FROM
      decorations
    WHERE
      decor_id = :decor_id
  ";

  $query_params = array(
    ':decor_id' => $_POST['decoration']
  );

  try
  {
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch(PDOException $ex)
  {
    die("Failed to execute query: " . $ex->getMessage . " query: " . $query);
  }

  $row = $stmt->fetch();

  $decoration = new Item();
  $decoration->setname("Decoration: " . $row['decor_name'])
             ->setCurrency("GBP")
             ->setQuantity(1)
             ->SetPrice($row['decor_price']);

  $itemList = new ItemList();
  $itemList->setItems(array($item1, $filling, $decoration));

  $details = new Details();
  $details->setShipping($deliveryCharge)
          ->setSubtotal($base_price + 10);

  $amount = new Amount();
  $amount->setCurrency("GBP")
         ->setTotal($deliveryCharge + $base_price + 10)
         ->setDetails($details);

  $transaction = new Transaction();
  $transaction->setAmount($amount)
              ->setItemList($itemList)
              ->setDescription("Payment description");

  $redirectUrls = new RedirectUrls();
  $redirectUrls->setReturnUrl("http://www.ivanbrazza.biz/order-placed/?order=" . $order_number . "&failed=false")
               ->setCancelUrl("http://www.ivanbrazza.biz/order-placed/?order=" . $order_number . "&failed=true");

  $payment = new Payment();
  $payment->setIntent("sale")
          ->setPayer($payer)
          ->setRedirectUrls($redirectUrls)
          ->setTransactions(array($transaction));

  try
  {
    $payment->create($apiContext);
  }
  catch (PayPal\Exception\PPConnectionException $ex)
  {
    echo "Exception: " . $ex->getMessage() . PHP_EOL;
    var_dump($ex->getData()); 
    exit(1);
  }

  foreach($payment->getLinks() as $link) {
    if($link->getRel() == 'approval_url') {
      $redirectUrl = $link->getHref();
      break;
    }
  }

  $_SESSION['user']['paymentId'] = $payment->getId();
  if(isset($redirectUrl)) {
    header("Location: $redirectUrl");
    exit;
  }
