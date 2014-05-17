<?php
class Delivery {
  var $address;
  var $postcode;
  var $deliveryCharge;
  var $distance;

  // A function to set the address
  public function setAddress($addressVar) {
    $this->address = str_replace(" ", "+", $addressVar);
  }

  // A function to set the postcode
  public function setPostcode($postcodeVar) {
    $this->postcode = str_replace(" ", "+", $postcodeVar);
  }

  // A function that calculates the distance between
  // the clients house and the customers house using
  // the Google Maps Distance Matrix API
  public function calculateDistance() {
    $destination = $this->address . "," . $this->postcode;
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=95+Hoe+Lane,EN35SW&destinations=" . $destination . "&sensor=false&units=imperial";
    $json = file_get_contents($url);
    $data = json_decode($json, TRUE);
    $meters = $data['rows'][0]['elements'][0]['distance']['value'];
    $this->distance = round($meters * 0.000621371);
  }

  // A recursively-called function that calculates
  // the delivery charge
  private function recursiveDelivery($miles, $i, $j) {
    $i += 5;
    $j += 3;

    if ($miles <= $i)
    {
      $this->deliveryCharge = $j;
      return;
    }
    else
    {
      $this->recursiveDelivery($miles, $i, $j);
    }
  }

  // A function that uses a recursive function
  // to calculate the delivery charge
  public function calculateDeliveryCharge() {
    if ($this->distance <= 5)
    {
      $this->deliveryCharge = 0;
    }
    else if ($this->distance >= 50)
    {
      $this->deliveryCharge = "Collection Only";
    }
    else
    {
      $remaining_miles = $this->distance - 5;
      $this->recursiveDelivery($remaining_miles, 0, 0);
    }
  }

  // A function that returns the delivery charge
  public function getDeliveryCharge() {
    return $this->deliveryCharge;
  }

  // A function that returns the distance
  public function getDistance() {
    return $this->distance;
  }
}
?>
