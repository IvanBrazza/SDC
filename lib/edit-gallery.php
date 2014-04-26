<?php
  require("common.php");

  if (!empty($_POST))
  {
    if ($_POST['token'] != $_SESSION['token'] or empty($_POST['token']))
    {
      $response = array(
        "status" => "error",
        "error" => "Invalid token (try refreshing the page)"
      );

      echo json_encode($response);
      die();
    }

    if ($_POST['command'] == "add")
    {
      $tblname = "gallery_" . strtolower(str_replace(" ", "_", $_POST['gallery_name']));
      $query = "
        SELECT
          MAX(gallery_id)
        FROM
          gallery
      ";
      $db->runQuery($query, null);
      $row = $db->fetch();

      $query = "
        INSERT INTO gallery (
          gallery_id,
          gallery_name,
          table_name
        ) VALUES (
          :gallery_id,
          :gallery_name,
          :table_name
        )
      ";
      $query_params = array(
        ':gallery_id'   => $row['MAX(gallery_id)'] + 1,
        ':gallery_name' => $_POST['gallery_name'],
        ':table_name'   => $tblname
      );
      $db->runQuery($query, $query_params);

      $query = "
        CREATE TABLE " . $tblname . "(
          images VARCHAR(255) NOT NULL,
          PRIMARY KEY(gallery_id),
          FOREIGN KEY(gallery_id) REFERENCES gallery(gallery_id)
        ) ENGINE=InnoDB
      ";
      $db->runQuery($query, null);

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        'status' => 'success',
        'token'  => $_SESSION['token']
      );

      echo json_encode($response);
      die();
    }
    else if ($_POST['command'] == "delete")
    {
      $query = "
        SELECT
          table_name
        FROM
          gallery
        WHERE
          gallery_id = :gallery_id
      ";
      $query_params = array(
        ':gallery_id' => $_POST['id']
      );
      $db->runQuery($query, $query_params);
      $row = $db->fetch();
      $tblname = $row['table_name'];

      $query = "
        DELETE FROM
          gallery
        WHERE
          gallery_id = :gallery_id
      ";
      $query_params = array(
        ':gallery_id' => $_POST['id']
      );
      $db->runQuery($query, $query_params);

      $query = "
        DROP TABLE " . $tblname
      ;
      $db->runQuery($query, null);

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        'status' => 'success',
        'token'  => $_SESSION['token']
      );

      echo json_encode($response);
      die();
    }
  }
