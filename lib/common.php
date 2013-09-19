<?php
  /**
    lib/common.php - common code to be included in every page.
    This library connects to the database and starts the session.
  **/
  
  $username   = "root";
  $password   = "you are not my supervisor";
  $host       = "localhost";
  $dbname     = "sdc_db";
  $options    = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 

  try
  {
    $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
  }
  catch(PDOException $ex)
  {
    die("Failed to connect to database: " . $ex->getMessage());
  }

  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  ini_set( "session.cookie_lifetime", "0" );
  session_start();
?>
