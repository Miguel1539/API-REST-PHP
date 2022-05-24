<?php
require_once 'class/Post.class.php';
require_once 'class/Respuestas.class.php';

$_comments = new Post();
// Instancia de la clase Respuestas $_respuestas = new Respuestas();
$_respuestas = new Respuestas();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  header('Content-Type: application/json; charset=utf-8');
  // comporobar que viene el token
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
  // } elseif (!isset($_GET['inicio'])) {
  //   //respuesta de error 400 bad request
  //   $resp = $_respuestas->error_400();
  //   http_response_code($resp['result']['error_id']);
  //   echo json_encode($resp);
  // } elseif (!isset($_GET['fin'])) {
  //   //respuesta de error 400 bad request
  //   $resp = $_respuestas->error_400();
  //   http_response_code($resp['result']['error_id']);
  //   echo json_encode($resp);
  // } 
  }elseif (!isset($_GET['id_post'])) {
    //respuesta de error 400 bad request
    $resp = $_respuestas->error_400();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
  } else {
    //metodo para obtener la imagen del usuario
    $getCommentsByPost = $_comments->getCommentsByPost(
      $_GET['token'],
      $_GET['user'],
      $_GET['id_post']
    );
    //enviar respuesta de $resp
    // if ($resp['status'] == 'OK') {
    // } else {
    http_response_code($getCommentsByPost['result']['error_id']);
    echo json_encode($getCommentsByPost);
    // }
  }
  





} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
  header('Content-Type: application/json; charset=utf-8');
  // sacar a variable el json
  $data = file_get_contents('php://input');
  // llamar a funcion insertComment
  $resp = $_comments->insertComment($data);
  if ($resp['status'] == 'error') {
    // mandar la respuesta http de login['result']['error_id']
    http_response_code($resp['result']['error_id']);
  } else {
    // mandar la respuesta http de 200
    http_response_code(200);
  }

  //print login en json
  echo json_encode($resp);
} else {
  // mandar header json utf8
  header('Content-Type: application/json; charset=utf-8');
  //respuesta de error 405 method not allowed
  $resp = $_respuestas->error_405();
  http_response_code($resp['result']['error_id']);
  echo json_encode($resp);
}
