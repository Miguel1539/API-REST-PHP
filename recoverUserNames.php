<?php

require_once 'class/RecoverUserNames.class.php';
require_once 'class/Respuestas.class.php';

include_once '../PHP-Mailer/index.php';
include_once 'class/assets/emailTemplates/plantillasCorreos.php';
// crear instancia de la clase RecoverUserNames
$_recoverUserNames = new RecoverUserNames();
// crear instancia de la clase Respuestas
$_respuestas = new Respuestas();

// solo por el metodo get
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  
  // sacar las variables de la url
  $email = $_GET['email'];
  // print_r($email);
  //decode email
  $email = urldecode($email);
  // convertir el email a json
  $email = ['email' => $email];
  $email = json_encode($email);

  $body = $email;

  // llamar a checkValidUser de RecoverUserNames y guardarlo en variable
  $recoverUserNames = $_recoverUserNames->recoverUserNames($body);
  // mandar header json
  header('Content-Type: application/json; charset=utf-8');
  // comprobar si existe error
  if ($recoverUserNames['status'] == 'error') {
    // mandar la respuesta http de login['result']['error_id']
    http_response_code($recoverUserNames['result']['error_id']);
  } else {
    // mandar la respuesta http de 200
    http_response_code(200);
  }
  //print login en json
  echo json_encode($recoverUserNames);
} else {
  // mandar header json
  header('Content-Type: application/json; charset=utf-8');
  // mandar la respuesta http de 405
  $recoverUserNames = $_respuestas->error_405();
  http_response_code($recoverUserNames['result']['error_id']);
  echo json_encode($recoverUserNames);
  // error 405
}
