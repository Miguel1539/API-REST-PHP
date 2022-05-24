<?php
require_once 'class/Post.class.php';
require_once 'class/Respuestas.class.php';

$_like = new Post();
// Instancia de la clase Respuestas $_respuestas = new Respuestas();
$_respuestas = new Respuestas();

// solo por el metodo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    // get post body
    $body = file_get_contents('php://input');

    $like = $_like->setLike($body);
    // comprobar si existe error
    if ($like['status'] == 'error') {
        // mandar la respuesta http de like['result']['error_id']
        http_response_code($like['result']['error_id']);
    } else {
        // mandar la respuesta http de 200
        http_response_code(200);
    }

    //print like en json
    echo json_encode($like);
} else {
    //respuesta de error 405 method not allowed
    $resp = $_respuestas->error_405();
    http_response_code($resp['result']['error_id']);
    echo json_encode($resp);
}