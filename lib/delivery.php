<?php
class Delivery {
  var $address;
  var $postcode;
  var $deliveryCharge;
  var $distance;

  function setAddress($addressVar) {
    $this->address = str_replace(" ", "+", $addressVar);
  }

  function setPostcode($postcodeVar) {
    $this->postcode = str_replace(" ", "+", $postcodeVar);
  }

  function calculateDistance() {
    $destination = $this->address . "," . $this->postcode;
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=95+Hoe+Lane,EN35SW&destinations=" . $destination . "&sensor=false&units=imperial";
    $json = file_get_contents($url);
    $data = json_decode($json, TRUE);
    $meters = $data['rows'][0]['elements'][0]['distance']['value'];
    $this->distance = round($meters * 0.000621371);
  }

  function calculateDeliveryCharge() {
    $remaining_miles = $this->distance - 5;
    $remaining_miles = round($remaining_miles / 5) * 5;
    if ($remaining_miles <= 0)
    {
      $this->deliveryCharge = 0;
    }
    else
    {
      for ($i = 5, $j = 1; $i <= 50; $i = $i + 5, $j++)
      {
        if ($remaining_miles == $i)
        {
          $this->deliveryCharge = $j;
        }
      }
    }
  }

  function getDeliveryCharge() {
    return $this->deliveryCharge;
  }

  function getDistance() {
    return $this->distance;
  }
}
?>
