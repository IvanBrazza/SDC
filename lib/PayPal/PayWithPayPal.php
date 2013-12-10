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
  $item1->setname($_POST['cake_size'] . " " . $_POST['cake_type'] . " cake, filled with " . $_POST['filling'] . 
                  ", decorated with " . $_POST['decoration'])
        ->setCurrency("GBP")
        ->setQuantity(1)
        ->setPrice($base_price);

  $itemList = new ItemList();
  $itemList->setItems(array($item1));

  $details = new Details();
  $details->setShipping($delivery_charge)
          ->setSubtotal($base_price);

  $amount = new Amount();
  $amount->setCurrency("GBP")
         ->setTotal($delivery_charge + $base_price)
         ->setDetails($details);

  $transaction = new Transaction();
  $transaction->setAmount($amount)
              ->setItemList($itemList)
              ->setDescription("Payment description");

  $redirectUrls = new RedirectUrls();
  $redirectUrls->setReturnUrl("http://www.ivanbrazza.biz/order-placed/")
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
