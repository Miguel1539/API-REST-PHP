<?php

require_once 'class/RecoverPass.class.php';
require_once 'class/Respuestas.class.php';

include_once '../PHP-Mailer/index.php';
include_once 'class/assets/emailTemplates/plantillasCorreos.php';

// instancia de la clase RecoverPass $_recoverPass = new RecoverPass();
$_recoverPass = new RecoverPass();
// instancia de la clase Respuestas $_respuestas = new Respuestas();
$_respuestas = new Respuestas();

// solo por el metodo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // TODO: POST method para solicitar un codigo y enviarlo por correo
  // get post body
  $body = file_get_contents('php://input');
  // llamar al registro de RecoverPass y guardarlo en variable
  $recoverPass = $_recoverPass->checkValidUser($body);
  // mandar header json
  header('Content-Type: application/json; charset=utf-8');
  // comprobar si existe error
  if ($recoverPass['status'] == 'error') {
    // mandar la respuesta http de login['result']['error_id']
    http_response_code($recoverPass['result']['error_id']);
  } else {
    // mandar la respuesta http de 200
    http_response_code(200);
  }
  //print login en json
  echo json_encode($recoverPass);
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
  // TODO: PUT method para camibiar la contraseña
  // get post body
  $body = file_get_contents('php://input');
  // llamar a verifyCode de RecoverPass y guardarlo en variable
  $verifyCode = $_recoverPass->changePassword($body);

  // mandar header json
  header('Content-Type: application/json; charset=utf-8');
  // comprobar si existe error
  if ($verifyCode['status'] == 'error') {
    // mandar la respuesta http de login['result']['error_id']
    http_response_code($verifyCode['result']['error_id']);
  } else {
    // mandar la respuesta http de 200
    http_response_code(200);
  }
  //print respuesta en json
  echo json_encode($verifyCode);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // TODO: GET method para ver si el codigo es correcto
  // get params
  $userName = $_GET['userName'];
  $code = $_GET['code'];
  $params = ['userName' => $userName, 'code' => $code];

  $body = json_encode($params);
  // llamar a verifyCode de RecoverPass y guardarlo en variable
  $verifyCode = $_recoverPass->verifyCode($body);
  // mandar header json
  header('Content-Type: application/json; charset=utf-8');
  // comprobar si existe error
  if ($verifyCode['status'] == 'error') {
    // mandar la respuesta http de login['result']['error_id']
    http_response_code($verifyCode['result']['error_id']);
    echo json_encode($verifyCode);
  } else {
    // mandar la respuesta http de 200
    http_response_code(200);
    echo json_encode($verifyCode);
  }
} else {
  // mandar header json
  header('Content-Type: application/json; charset=utf-8');
  // mandar la respuesta http de 405
  $recoverPass = $_respuestas->error_405();
  http_response_code($recoverPass['result']['error_id']);
  echo json_encode($recoverPass);
  // error 405
}

?>
