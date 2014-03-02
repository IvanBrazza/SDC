<head>
  <html lang="en" xml:lang="en" xmlns= "http://www.w3.org/1999/xhtml">
  <meta http-equiv="Content-Language" content="en" />
  <meta name="google-site-verification" content="j7xff2YmYmFCUc5IuJslQ0hvOKaiaHKFqaQXYM-g_VE" />
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="../favicon.ico">
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,400' rel='stylesheet' type='text/css'>
  <link href="../css/jquery-ui.css" rel="stylesheet">
  <link href="../css/main.css" rel="stylesheet">
  <?php if (strpos($_SERVER['REQUEST_URI'], "add-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false) : ?>
    <link href="../css/timepicker.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "gallery") !== false) : ?>
    <link href="../css/gallery.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "get-directions") !== false) : ?>
    <link href="../css/google-maps.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "stats") !== false) : ?>
    <link href="../css/stats.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "add-order") !== false) : ?>
    <link href="../css/tabbed-order.css" rel="stylesheet">
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
        <?php if (!empty($_SESSION['user']) and $_SESSION['user']['username'] !== "admin") : ?>
          <li <?php if ($page === "place-an-order") : ?>class="active"<?php endif; ?>><a href="../place-an-order">Place An Order</a></li>
          <li <?php if ($page === "your-orders") : ?>class="active"<?php endif; ?>><a href="../your-orders">Your Orders</a></li>
        <?php endif; ?>
        <?php if (!empty($_SESSION['user']) and $_SESSION['user']['username'] === "admin") : ?>
          <li <?php if ($page === "all-orders") : ?>class="active"<?php endif; ?>><a href="../all-orders">All Orders</a></li>
          <li <?php if ($page === "customer-list") : ?>class="active"<?php endif; ?>><a href="../customer-list">Customer List</a></li>
          <li <?php if ($page === "stats") : ?>class="active"<?php endif;?>><a href="../stats">Stats</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="container">
    <div class="header">
      <div class="logo"></div>
      <div class="user">
        <span>Welcome to Star Dream Cakes<?php if (!empty($_SESSION['user'])) : ?>, <?php echo $_SESSION['user']['first_name']; ?>!<?php endif; ?></span><br />
        <span class="links">
          <?php if (!empty($_SESSION['user'])) : ?>
            <a href="../edit-account">Edit Account</a> | <a href="../lib/logout.php">Logout</a>
          <?php else : ?>
            <a href="../login">Login</a> | <a href="../register">Register</a>
          <?php endif; ?>
        </span>
      </div>
    </div>
