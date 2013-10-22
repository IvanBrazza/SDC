<?php
  function calculateDistance($address, $postcode) {
    $address = str_replace(" ", "+", $address);
    $postcode = str_replace(" ", "+", $postcode);
    $origins = $address . "," . $postcode;
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $origins . "&destinations=95+Hoe+Lane,EN35SW&sensor=false&units=imperial";
    $json = file_get_contents($url);
    $data = json_decode($json, TRUE);
    $meters = $data['rows'][0]['elements'][0]['distance']['value'];
    $miles = $meters * 0.000621371;
    $miles = round($miles);
    return $miles;
  }
?>