<?php
  include("../lib/common.php");
  $title = "Get Directions";
  $page = "all-orders";

  if (empty($_SESSION['user']) or $_SESSION['user']['username'] !== "admin") {
    header("Location: ../login");
    die();
  }

  if ($_GET)
  {
    $query = "
      SELECT
        first_name,
        last_name,
        address,
        postcode
      FROM
        users
      WHERE
        customer_id = :customer_id
    ";

    $query_params = array(
      ':customer_id' => $_GET['id']
    );

    try
    {
      $stmt   = $db->prepare($query);
      $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
      die("Failed to execute query: " . $ex->getmessage() . " query: " . $query);
    }

    $row = $stmt->fetch();

    include "../lib/delivery.php";
    $delivery = new Delivery();
    
    $delivery->setAddress($row['address']);
    $delivery->setPostcode($row['postcode']);
    $delivery->calculateDistance();
  }
?>
<?php include("../lib/header.php"); ?>
  <?php if ($_GET) : ?>
    <h1>Get Directions</h1>
    <p>Directions to:</p>
    <p><?php echo $row['first_name'] . " " . $row['last_name']; ?><br />
    <?php echo $row['address']; ?><br />
    <?php echo $row['postcode']; ?><br />
    <i>(<?php echo $delivery->getDistance(); ?> miles away)</i></p>

    <div id="directions-panel"></div>
    <div id="map-canvas"></div>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCKeZpb8doUO3DbEqT3t-uRJYsbEPbD3AE&sensor=false"></script>
    <script>
      var directionsDisplay;
      var directionsService = new google.maps.DirectionsService();
      var map;
      var origin = "95+Hoe+Lane,EN35SW";
      var destination = <?php echo json_encode(str_replace(" ", "+", $row['address']) . "," . str_replace(" ", "+", $row['postcode'])); ?>;
      var center = new google.maps.LatLng(51.666394, -0.048700);
  
      directionsDisplay = new google.maps.DirectionsRenderer();
      var mapOptions = {
        zoom: 32,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: center
        }
      map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
      directionsDisplay.setMap(map);
      directionsDisplay.setPanel(document.getElementById("directions-panel"));
  
      var request = {
          origin: origin,
          destination: destination,
          travelMode: google.maps.TravelMode.DRIVING,
          unitSystem: google.maps.UnitSystem.IMPERIAL
      };
      directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(response);
        }
      });
    </script>
  <?php else : ?>
    <h1>Error getting directions</h1>
  <?php endif; ?>
<?php include("../lib/footer.php"); ?>
