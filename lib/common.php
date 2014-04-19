<?php
  /**
    lib/common.php - common code to be included in every page.
    This library connects to the database and starts the session.
  **/

  // Redirect all HTTP traffic to HTTPS
  if(!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] !== 'on')
  {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    die();
  }

  // Make sure the timezone is GMT for the date() function
  date_default_timezone_set("GMT");

  // Create a new DB class and run the init routine
  include "db.class.php";
  $db = new DB;
  $db->init();

  // Start the session
  ini_set( "session.cookie_lifetime", "0" );
  session_start();
?>
