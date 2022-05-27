<?php
require_once 'class/Users.class.php';
require_once 'class/Respuestas.class.php';

// Instancia de la clase Auth $_auth = new Auth();
$_usersImages = new Users();
// Instancia de la clase Respuestas $_respuestas = new Respuestas();
$_respuestas = new Respuestas();

// solo por el metodo GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // comprobar si se envió el token pen los parametros
  header('Content-Type: application/json; charset=utf-8');
  if (!$_GET['all']) {
    if (!isset($_GET['token'])) {
      // enviar header json

      //respuesta de error 403 forbidden
      $resp = $_respuestas->error_403();
      http_response_code($resp['result']['error_id']);
      echo json_encode($resp);
    } elseif (!isset($_GET['act'])) {
      // enviar header json

      //respuesta de error 400 bad request
      $resp = $_respuestas->error_400();
      http_response_code($resp['result']['error_id']);
      echo json_encode($resp);
    } elseif (!isset($_GET['user'])) {
      //respuesta de error 400 bad request
      $resp = $_respuestas->error_400();
      http_response_code($resp['result']['error_id']);
      echo json_encode($resp);
    } else {
      // metodo para obtener la imagen del usuario
      $resp = $_usersImages->getImgProfile(
        $_GET['token'],
        $_GET['user'],
        $_GET['act']
      );
      // enviar respuesta de $resp
      // if ($resp['status'] == 'OK') {
      // } else {
      http_response_code($resp['result']['error_id']);
      echo json_encode($resp);
      // }
    }
  } elseif (!isset($_GET['token'])) {
    // enviar header json

    //respuesta de error 403 forbidden
    $resp = $_respuestas->error_403();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } elseif (!isset($_GET['user'])) {
    //respuesta de error 400 bad request
    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } else {
    if ($_GET['searchedUser']){
      $resp = $_usersImages->getAllProfileByUsername(
        $_GET['token'],
        $_GET['user'],
        $_GET['searchedUser']
      );
    } else {
      $resp = $_usersImages->getAllProfile(
        $_GET['token'],
        $_GET['user']
      );
    }
    // // metodo para obtener todos los datos del usuario
    // $resp = $_usersImages->getAllProfile(
    //   $_GET['token'],
    //   $_GET['user']
    // );
    // enviar respuesta de $resp
    // if ($resp['status'] == 'OK') {
    // } else {
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
    // }
  }
}
// solo por el metodo POST
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // comprobar si se envió el token pen los parametros
  header('Content-Type: application/json; charset=utf-8');
  if (!isset($_GET['token'])) {
    // enviar header json

    //respuesta de error 403 forbidden
    $resp = $_respuestas->error_403();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } elseif (!isset($_FILES['imagen'])) {
    // enviar header json

    //respuesta de error 400 bad request
    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } elseif (!isset($_GET['user'])) {
    //respuesta de error 400 bad request
    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } else {
    switch ($_GET['act']) {
      case 'mainprofile':
        // token a variable
        $token = $_GET['token'];
        // user a variable
        $user = $_GET['user'];
        // llamar a la funcion updateImgMainProfile de la clase Users
        $resp = $_usersImages->updateImgProfile(
          $token,
          $user,
          $_FILES['imagen'],
          'img_main_profile'
        );
        break;
      case 'bannerprofile':
        // token a variable
        $token = $_GET['token'];
        // user a variable
        $user = $_GET['user'];
        // llamar a la funcion updateImgMainProfile de la clase Users
        $resp = $_usersImages->updateImgProfile(
          $token,
          $user,
          $_FILES['imagen'],
          'img_banner_profile'
        );
        break;
      default:
        // enviar header json
        header('Content-Type: application/json; charset=utf-8');
        //respuesta de error 400 bad request
        $resp = $_respuestas->error_400();
        http_response_code($resp['result']['error_id']);
        echo json_encode($resp);

        break;
    }
    // enviar respuesta de $resp
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  }
} else {
  // mandar header json
  header('Content-Type: application/json; charset=utf-8');
  // mandar la respuesta http de 405
  $resp = $_respuestas->error_405();
  http_response_code($resp['result']['error_id']);
  echo json_encode($resp);
  // error 405
}
