<?php
  require("common.php");
  include("../vendor/autoload.php");
  use Aws\S3\S3Client;
  $s3 = S3Client::factory(array(
    'key'    => 'AKIAJVGHIKM3L5CY6UBQ',
    'secret' => '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'
  ));
  $bucket = 'SDC-images';

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
          PRIMARY KEY(images)
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
    else if ($_POST['command'] == "add-image")
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
        INSERT INTO " . $tblname . " (
          images
        ) VALUES (
          :image
        )
      ";
      $query_params = array(
        ':image' => $_POST['image']
      );
      $db->runQuery($query, $query_params);

      // Generate new token
      $_SESSION['token'] = rtrim(base64_encode(md5(microtime())),"=");

      $response = array(
        'status' => 'success',
        'token'  => $_SESSION['token']
      );

      echo json_encode($response);
      die();
    }
    else if ($_POST['command'] == "delete-image")
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
        ':gallery_id' => $_POST['gallery_id']
      );
      $db->runQuery($query, $query_params);
      $row = $db->fetch();

      $query = "
        DELETE FROM " .
          $row['table_name'] . "
        WHERE
          images = :images
      ";
      $query_params = array(
        ':images' => $_POST['image']
      );
      $db->runQuery($query, $query_params);

      $keyname = $_POST['gallery_id'] . '/' . $_POST['image'];
      $result = $s3->deleteObject(array(
        'Bucket' => $bucket,
        'Key'    => $keyname
      ));

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

      $keys = $s3->listObjects(array('Bucket' => $bucket, 'Prefix' => $_POST['id'] . "/"))->getPath('Contents/*/Key');

      $result = $s3->deleteObjects(array(
          'Bucket'  => $bucket,
          'Objects' => array_map(function ($key) {
              return array('Key' => $key);
          }, $keys),
      ));

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
