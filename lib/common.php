<?php
  /**
    lib/common.php - common code to be included in every page.
    This library connects to the database and starts the session.
  **/

  function forceHTTPS()
  {
    $httpsURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if(!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] !== 'on')
    {
      header("Location: $httpsURL");
      die();
    }
  }

  function forceHTTP()
  {
    $httpURL = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if(isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'on')
    {
      header("Location: $httpURL");
      die();
    }
  }

  date_default_timezone_set("GMT");

  include "db.class.php";
  $db = new DB;
  $db->init();

  ini_set( "session.cookie_lifetime", "0" );
  session_start();
?>
