<?php
require_once 'class/Auth.class.php';
require_once 'class/Respuestas.class.php';

// Instancia de la clase Auth $_auth = new Auth();
$_auth = new Auth();
// Instancia de la clase Respuestas $_respuestas = new Respuestas();
$_respuestas = new Respuestas();

// solo por el metodo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get post body
    $body = file_get_contents('php://input');

    // llamar al login de Auth y guardarlo en variable
    $login = $_auth->login($body);

    // mandar header json
    header('Content-Type: application/json; charset=utf-8');

    // comprobar si existe error
    if ($login['status'] == 'error') {
        // mandar la respuesta http de login['result']['error_id']
        http_response_code($login['result']['error_id']);
    } else {
        // mandar la respuesta http de 200
        http_response_code(200);
    }


    //print login en json
    echo json_encode($login);



} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // get params
    $userName = $_GET['userName'];
    $token = $_GET['token'];
    $params = ['userName' => $userName, 'token' => $token];

    // mandar json_encode($params);
    $body = json_encode($params);

    $verifyToken = $_auth->verifyToken($body);

    // mandar header json
    header('Content-Type: application/json; charset=utf-8');

    // comprobar si existe error
    if ($verifyToken['status'] == 'error') {
        // mandar la respuesta http de login['result']['error_id']
        http_response_code($verifyToken['result']['error_id']);
    } else {
        // mandar la respuesta http de 200
        http_response_code(200);
    }

    //print respuesta en json
    echo json_encode($verifyToken);

}else {
    // mandar header json
    header('Content-Type: application/json; charset=utf-8');
    // mandar la respuesta http de 405
    $login = $_respuestas->error_405();
    http_response_code($login['result']['error_id']);
    echo json_encode($login);
    // error 405
}
