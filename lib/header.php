<head>
  <title><?php echo $title ?> | Star Dream Cakes</title>
  <link rel="icon" href="http://ivanbrazza.biz/sdc/favicon.ico">
  <link href="../css/roboto.css" rel="stylesheet">
  <link href="../css/main.css" rel="stylesheet">
  <link href="../css/timepicker.css" rel="stylesheet">
  <link href="../css/jquery-ui.css" rel="stylesheet">
</head>
<body>
  <div class="navbar">
    <ul>
      <?php if ($_SESSION) : ?>
        <li><a href="../lib/logout.php">Logout</a></li>
        <li><a href="../edit-account">Edit Account</a></li>
      <?php endif; ?>
      <?php if (!$_SESSION) : ?>
        <li><a href="../login">Login</a></li>
        <li><a href="../register">Register</a></li>
      <?php endif; ?>
      <?php if ($_SESSION and $_SESSION['user']['username'] === "admin") : ?>
        <li><a href="../all-orders">All Orders</a></li>
        <li><a href="../memberlist">Memberlist</a></li>
      <?php endif; ?>
      <?php if ($_SESSION and $_SESSION['user']['username'] !== "admin") : ?>
        <li><a href="../yourorders">Your Orders</a></li>
        <li><a href="../placeanorder">Place An Order</a></li>
      <?php endif; ?>
      <li><a href="../testimonials">Testimonials</a></li>
      <li><a href="#">About Us</a></li>
      <li><a href="../home">Home</a></li>
    </ul>
  </div>
  <script type="text/javascript">
    var RecaptchaOptions = {
      theme : 'clean'
    };
  </script>
