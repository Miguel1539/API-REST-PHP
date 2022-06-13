<?php
require_once 'class/Post.class.php';
require_once 'class/Respuestas.class.php';

$_posts = new Post();
// Instancia de la clase Respuestas $_respuestas = new Respuestas();
$_respuestas = new Respuestas();

//solo por el metodo get
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // enviar header json utf8
  header('Content-Type: application/json; charset=utf-8');
  // comporobar get token
  if (!isset($_GET['token'])) {
    //enviar header json
    $resp = $_respuestas->error_403();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } elseif (!isset($_GET['user'])) {
    //respuesta de error 400 bad request

    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } elseif (!isset($_GET['inicio'])) {
    //respuesta de error 400 bad request
    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } elseif (!isset($_GET['fin'])) {
    //respuesta de error 400 bad request
    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } else {
    if ($_GET['searchedUser'] === 'al') {
      $getUsersByUsername = $_posts->getAllPosts(
        $_GET['token'],
        $_GET['user'],
        $_GET['inicio'],
        $_GET['fin']
      );
    }elseif ($_GET['searchedUser'] === 'fa'){
      $getUsersByUsername = $_posts->getFavoritePosts(
        $_GET['token'],
        $_GET['user'],
        $_GET['inicio'],
        $_GET['fin']
      );
    }else {
      //metodo para obtener la imagen del usuario
      $getUsersByUsername = $_posts->getUsersByUsername(
        $_GET['token'],
        $_GET['user'],
        $_GET['inicio'],
        $_GET['fin'],
        $_GET['searchedUser']
      );
    }
    //enviar respuesta de $resp
    // if ($resp['status'] == 'OK') {
    // } else {
    http_response_code($getUsersByUsername['result']['error_id']);
    echo json_encode($getUsersByUsername);
    // }
  }

  // SELECT * FROM `publicaciones` ORDER BY `ID_publicacion` DESC LIMIT 1,10
  // SELECT * FROM `publicaciones` WHERE `user_id` = (SELECT ID_user FROM usuarios WHERE username like 'paco2') ORDER BY `ID_publicacion` DESC LIMIT 1,10;
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
  header('Content-Type: application/json; charset=utf-8');
  if (!isset($_GET['token'])) {
    //enviar header json
    $resp = $_respuestas->error_403();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } elseif (!isset($_GET['user'])) {
    //respuesta de error 400 bad request

    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } elseif (!isset($_FILES['imagen'])) {
    //respuesta de error 400 bad request
    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } else {
    //metodo para obtener la imagen del usuario
    $resp = $_posts->insertPost(
      $_GET['token'],
      $_GET['user'],
      $_GET['descripcion'],
      $_FILES['imagen']
    );
    //enviar respuesta de $resp
    // if ($resp['status'] == 'OK') {
    // } else {
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
    // }
  }
} else {
  // mandar header json utf8
  header('Content-Type: application/json; charset=utf-8');
  //respuesta de error 405 method not allowed
  $resp = $_respuestas->error_405();
  http_response_code($resp['result']['error_id']);
  echo json_encode($resp);
}
