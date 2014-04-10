<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns= "http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Language" content="en" />
  <meta name="google-site-verification" content="j7xff2YmYmFCUc5IuJslQ0hvOKaiaHKFqaQXYM-g_VE" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="../favicon.ico">
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,400' rel='stylesheet' type='text/css'>
  <link href="../css/bootstrap.css" rel="stylesheet">
  <link href="../css/jquery-ui.css" rel="stylesheet">
  <link href="../css/cookie.css" rel="stylesheet">
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
  <?php if (strpos($_SERVER['REQUEST_URI'], "register") !== false or
            strpos($_SERVER['REQUEST_URI'], "testimonials") !== false) : ?>
    <link href="../css/recaptcha.css" rel="stylesheet">
  <?php endif; ?>
</head>
<body>
  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="../home"><img src="../img/header-nav-logo.png"></a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
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
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#"></a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <?php if (!empty($_SESSION['user'])) : ?>
                Welcome, <?php echo $_SESSION['user']['first_name']; ?>!
              <?php else : ?>
                Welcome to Star Dream Cakes!
              <?php endif; ?> <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <?php if (!empty($_SESSION['user'])) : ?>
                <li><a href="../edit-account">Edit Account</a></li>
                <li class="divider"></li>
                <li><a href="../lib/logout.php">Logout</a></li>
              <?php else : ?>
                <li><a href="../login">Login</a></li>
                <li><a href="../register">Register</a></li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container">
