<?php
class DB {
  var $result;
  var $database;

  // A function which creates a connection to the databse
  public function init() {
    $username         = "ivanrsfr";
    $password         = "inspiron1520";
    $host             = "localhost";
    $dbname           = "ivanrsfr_sdc";
    $options          = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    $display_message  = "";
  
    try
    {
      $this->database = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
    }
    catch(PDOException $ex)
    {
      die("Failed to connect to database: " . $ex->getMessage());
    }
  
    $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  // A function which runs a query passed as parameters,
  // if it fails, email the webmaster and redirect the
  // user to the oops page
  public function runQuery($query, $query_params) {
    try
    {
      $this->result = $this->database->prepare($query);
      if ($query_params != null)
      {
        $this->result->execute($query_params);
      }
      else
      {
        $this->result->execute();
      }
    }
    catch(PDOException $ex)
    {
      error_log("[" . date("Y-m-d H:i") . "] Error running query: $query\r\n", 3, "/home/ivanrsfr/error.log");
      error_log($ex->getMessage(), 3, "/home/ivanrsfr/error.log");
      $headers = "From: <webmaster@ivanbrazza.biz>" . "\r\n" .
                 "Reply-To: <webmaster@ivanbrazza.biz>" . "\r\n" .
                 "MIME-Version: 1.0" . "\r\n" .
                 "Content-type: text/html; charset=iso-8859-1" . "\r\n";
      $subject = "Star Dream Cakes Error";
      $body = "<html><body>
        <p>There's been an error over at Star Dream Cakes. It happened at " . date("Y-m-d H:i") . ".</p>
        <p>Below is the error and the query.</p><br />
        <p>Query:<br />" . $query . "</p><br />
        <p>Error:<br />" . $ex->getMessage() . "</p><br />
        </body></html>
      ";
      mail("dudeman1996@gmail.com", $subject, $body, $headers);
      header("Location: http://www.ivanbrazza.biz/oops");
      die();
    }
  }

  // A function to fetch a single row from the query
  public function fetch() {
    return $this->result->fetch();
  }

  // A function to fetch multiple rows from the query
  public function fetchAll() {
    return $this->result->fetchAll();
  }
}
?>
