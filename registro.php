<?php
require_once 'class/Registro.class.php';
require_once 'class/Respuestas.class.php';

// Instancia de la clase Auth $_auth = new Auth();
$_registro = new Registro();
// Instancia de la clase Respuestas $_respuestas = new Respuestas();
$_respuestas = new Respuestas();

// solo por el metodo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get post body
    $body = file_get_contents('php://input');
    // llamar al registro de Registro y guardarlo en variable
    $registro = $_registro->registro($body);
    // mandar header json
    header('Content-Type: application/json; charset=utf-8');
    // comprobar si existe error
    if ($registro['status'] == 'error') {
        // mandar la respuesta http de login['result']['error_id']
        http_response_code($registro['result']['error_id']);
    } else {
        // mandar la respuesta http de 200
        http_response_code(200);
    }
    //print login en json
    echo json_encode($registro);



} else {
    // mandar header json
    header('Content-Type: application/json; charset=utf-8');
    // mandar la respuesta http de 405
    $registro = $_respuestas->error_405();
    http_response_code($registro['result']['error_id']);
    echo json_encode($registro);
    // error 405
}
