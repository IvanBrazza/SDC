<head>
  <html lang="en" xml:lang="en" xmlns= "http://www.w3.org/1999/xhtml">
  <meta http-equiv="Content-Language" content="en" />
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="../favicon.ico">
  <link href="../css/main.css" rel="stylesheet">
  <?php if ($_SERVER['REQUEST_URI'] === "/add-order/" or $_SERVER['REQUEST_URI'] === "/place-an-order/") : ?>
    <link href="../css/timepicker.css" rel="stylesheet">
    <link href="../css/jquery-ui.css" rel="stylesheet">
  <?php endif; ?>
  <?php if ($_SERVER['REQUEST_URI'] === "/gallery/") : ?>
    <link href="../css/flexslider.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (substr($_SERVER['REQUEST_URI'], 0, 16) === "/get-directions/") : ?>
    <link href="../css/google-maps.css" rel="stylesheet">
  <?php endif; ?>
  <?php if ($_SERVER['REQUEST_URI'] === "/stats/") : ?>
    <link href="../css/stats.css" rel="stylesheet">
  <?php endif; ?>
  <?php if ($_SERVER['REQUEST_URI'] === "/place-an-order/") : ?>
    <link href="../css/place-an-order.css" rel="stylesheet">
  <?php endif; ?>
</head>
<body>
  <div class="navbar">
    <div class="nav-container">
      <ul>
        <li <?php if ($page === "home") : ?>class="active"<?php endif; ?>><a href="../home">Home</a></li>
        <li <?php if ($page === "about-us") : ?>class="active"<?php endif; ?>><a href="../about-us">About Us</a></li>
        <li <?php if ($page === "testimonials") : ?>class="active"<?php endif; ?>><a href="../testimonials">Testimonials</a></li>
        <li <?php if ($page === "gallery") : ?>class="active"<?php endif;?>><a href="../gallery">Gallery</a></li>
        <?php if ($_SESSION['user'] and $_SESSION['user']['username'] !== "admin") : ?>
          <li <?php if ($page === "place-an-order") : ?>class="active"<?php endif; ?>><a href="../place-an-order">Place An Order</a></li>
          <li <?php if ($page === "your-orders") : ?>class="active"<?php endif; ?>><a href="../your-orders">Your Orders</a></li>
        <?php endif; ?>
        <?php if ($_SESSION['user'] and $_SESSION['user']['username'] === "admin") : ?>
          <li <?php if ($page === "all-orders") : ?>class="active"<?php endif; ?>><a href="../all-orders">All Orders</a></li>
          <li <?php if ($page === "customer-list") : ?>class="active"<?php endif; ?>><a href="../customer-list">Customer List</a></li>
          <li <?php if ($page === "stats") : ?>class="active"<?php endif;?>><a href="../stats">Stats</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="container">
    <div class="header">
      <div class="logo">
        <img src="../img/logo.jpg" alt="logo" height="50px" />
      </div>
      <div class="user">
        <span>Welcome to Star Dream Cakes<?php if ($_SESSION['user']) : ?>, <?php echo $_SESSION['user']['first_name']; ?>!<?php endif; ?></span><br />
        <span class="links">
          <?php if ($_SESSION['user']) : ?>
            <a href="../edit-account">Edit Account</a> | <a href="../lib/logout.php">Logout</a>
          <?php else : ?>
            <a href="../login">Login</a> | <a href="../register">Register</a>
          <?php endif; ?>
        </span>
      </div>
    </div>
