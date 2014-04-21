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

    if ($_POST['type'] == "filling")
    {
      if ($_POST['command'] == "delete")
      {
        $query = "
          DELETE FROM
            fillings
          WHERE
            filling_id = :filling_id
        ";

        $query_params = array(
          ':filling_id' => $_POST['id']
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
      else if ($_POST['command'] == "add")
      {
        $query = "
          SELECT
            MAX(filling_id)
          FROM
            fillings
        ";

        $db->runQuery($query, null);
        $row = $db->fetch();

        $query = "
          INSERT INTO fillings (
            filling_id,
            filling_name,
            filling_price
          ) VALUES (
            :filling_id,
            :filling_name,
            :filling_price
          )
        ";

        $query_params = array(
          ':filling_id'    => $row['MAX(filling_id)'] + 1,
          ':filling_name'  => $_POST['filling_name'],
          ':filling_price' => $_POST['filling_price']
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
      else if ($_POST['command'] == "edit")
      {
        $query = "
          UPDATE
            fillings
          SET
            filling_name  = :filling_name,
            filling_price = :filling_price
          WHERE
            filling_id = :filling_id
        ";

        $query_params = array(
          ':filling_id'    => $_POST['id'],
          ':filling_name'  => $_POST['filling_name'],
          ':filling_price' => $_POST['filling_price']
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
    }

    if ($_POST['type'] == "decoration")
    {
      if ($_POST['command'] == "delete")
      {
        $query = "
          DELETE FROM
            decorations
          WHERE
            decor_id = :decor_id
        ";

        $query_params = array(
          ':decor_id' => $_POST['id']
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
      else if ($_POST['command'] == "add")
      {
        $query = "
          SELECT
            MAX(decor_id)
          FROM
            decorations
        ";

        $db->runQuery($query, null);
        $row = $db->fetch();

        $query = "
          INSERT INTO decorations (
            decor_id,
            decor_name,
            decor_price
          ) VALUES (
            :decor_id,
            :decor_name,
            :decor_price
          )
        ";

        $query_params = array(
          ':decor_id'    => $row['MAX(decor_id)'] + 1,
          ':decor_name'  => $_POST['decor_name'],
          ':decor_price' => $_POST['decor_price']
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
      else if ($_POST['command'] == "edit")
      {
        $query = "
          UPDATE
            decorations
          SET
            decor_name  = :decor_name,
            decor_price = :decor_price
          WHERE
            decor_id = :decor_id
        ";

        $query_params = array(
          ':decor_id'    => $_POST['id'],
          ':decor_name'  => $_POST['decor_name'],
          ':decor_price' => $_POST['decor_price']
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
    }
  }
