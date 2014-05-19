<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns= "http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Language" content="en" />
  <meta name="google-site-verification" content="j7xff2YmYmFCUc5IuJslQ0hvOKaiaHKFqaQXYM-g_VE" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="//www.<?php echo $siteUrl; ?>/favicon.ico">
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,400' rel='stylesheet' type='text/css'>
  <link href="//www.<?php echo $siteUrl; ?>/css/bootstrap.css" rel="stylesheet">
  <link href="//www.<?php echo $siteUrl; ?>/css/jquery-ui.css" rel="stylesheet">
  <link href="//www.<?php echo $siteUrl; ?>/css/cookie.css" rel="stylesheet">
  <link href="//www.<?php echo $siteUrl; ?>/css/nprogress.css" rel="stylesheet">
  <link href="//www.<?php echo $siteUrl; ?>/css/main.css" rel="stylesheet">
  <?php if (strpos($_SERVER['REQUEST_URI'], "gallery") !== false) : ?>
    <link href="//www.<?php echo $siteUrl; ?>/css/gallery.css" rel="stylesheet">
    <link href="//www.<?php echo $siteUrl; ?>/css/lightbox.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "get-directions") !== false) : ?>
    <link href="//www.<?php echo $siteUrl; ?>/css/google-maps.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "register") !== false or
            strpos($_SERVER['REQUEST_URI'], "testimonials") !== false) : ?>
    <link href="//www.<?php echo $siteUrl; ?>/css/recaptcha.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "add-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false or
            strpos($_SERVER['REQUEST_URI'], "edit-order") !== false) : ?>
    <link href="//www.<?php echo $siteUrl; ?>/css/picker.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "place-an-order") !== false) : ?>
    <link href="//www.<?php echo $siteUrl; ?>/css/jquery-fileupload.css" rel="stylesheet">
  <?php endif; ?>
  <?php if (strpos($_SERVER['REQUEST_URI'], "admin") !== false) : ?>
    <link href="//www.<?php echo $siteUrl; ?>/css/jquery-fileupload.css" rel="stylesheet">
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
        <a class="navbar-brand" href="//www.<?php echo $siteUrl; ?>/"><img src="//www.<?php echo $siteUrl; ?>/img/header-nav-logo.png"></a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li <?php if ($page === "home") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/">Home</a></li>
          <li <?php if ($page === "about-us") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/about-us">About Us</a></li>
          <li <?php if ($page === "testimonials") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/testimonials">Testimonials</a></li>
          <li <?php if ($page === "gallery") : ?>class="active"<?php endif;?>><a href="//www.<?php echo $siteUrl; ?>/gallery">Gallery</a></li>
          <?php if (!empty($_SESSION['user']) and $_SESSION['user']['username'] !== "admin") : ?>
            <li <?php if ($page === "place-an-order") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/place-an-order">Place An Order</a></li>
            <li <?php if ($page === "your-orders") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/your-orders">Your Orders</a></li>
          <?php endif; ?>
          <?php if (!empty($_SESSION['user']) and $_SESSION['user']['username'] === "admin") : ?>
            <li <?php if ($page === "all-orders") : ?>class="active"<?php endif; ?>><a href="//www.<?php echo $siteUrl; ?>/all-orders">All Orders</a></li>
            <li <?php if ($page === "admin") : ?>class="active"<?php endif;?>><a href="//www.<?php echo $siteUrl; ?>/admin">Admin</a></li>
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
                <li><a href="//www.<?php echo $siteUrl; ?>/edit-account">Edit Account</a></li>
                <li class="divider"></li>
                <li><a href="//www.<?php echo $siteUrl; ?>/lib/logout.php">Logout</a></li>
              <?php else : ?>
                <li><a href="//www.<?php echo $siteUrl; ?>/login?redirect=<?php echo $_SERVER['REQUEST_URI']; ?>">Login</a></li>
                <li><a href="//www.<?php echo $siteUrl; ?>/register">Register</a></li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container">
  <div class="modal fade" role="dialog" aria-hidden="true" id="success_modal" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body alert alert-success" style="display:block;margin-bottom:0;text-align:center;"></div>
      </div>
    </div>
  </div>
  <div class="modal fade" role="dialog" aria-hidden="true" id="error_modal" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body alert alert-danger" style="display:block;margin-bottom:0;text-align:center;"></div>
      </div>
    </div>
  </div>
