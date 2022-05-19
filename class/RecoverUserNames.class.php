<?php

// require once conexion.php and respuestas.class.php
require_once 'conexion/Conexion.php';
require_once 'Respuestas.class.php';

class RecoverUserNames extends Conexion
{
  // crear funcion publica recoverUserNames con un parametro json
  public function recoverUserNames($json)
  {
    // instancia de la clase respuestas
    $_respuestas = new Respuestas();
    // decode json
    $decode = json_decode($json, true);

    // si existe $decode['email'] mandar error 400
    if (!isset($decode['email'])) {
      //mandar header 400
      return $_respuestas->error_400();
    }

    // verificar si el email existe en la base de datos y gruardar en variable true o false
    $checkEmail = $this->checkEmail($decode['email']);
    // si existe el email en la base de datos sacar todos los nombres de usuario a un array
    if ($checkEmail) {
      $userNames = $this->getUserNames($decode['email']);
      // print_r($userNames);
      // envia un correo
      $sendEmail = $this->sendEmail($decode['email'], $userNames);
        // si se envio el correo retorna success
        if ($sendEmail) {
            return $_respuestas->success([
                "mensaje" => "Se envio un correo a".$decode['email']
            ]);
        } else {
            // si no se envio el correo retorna error_500
            return $_respuestas->error_500();
        }
    } else {
      // return error_200 de la clase respuestas y mensaje de error que el email no existe
      return $_respuestas->error_200('El email no existe');
    }
  }

  // crear funcion private checkEmail con un parametro email
  private function checkEmail($email)
  {
    // consulta a la base de datos
    $query = "SELECT COUNT(*) FROM usuarios WHERE email like '$email'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // comprobar si el email existe por lo menos una vez
    if ($result[0]['COUNT(*)'] > 0) {
      // retorna true
      return true;
    } else {
      // retorna false
      return false;
    }
  }

  // crear funcion private getUserNames con un parametro email
  private function getUserNames($email)
  {
    // consulta a la base de datos
    $query = "SELECT username FROM usuarios WHERE email like '$email'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // retorna el array de nombres de usuario
    return $result;
  }

  // crear funcion private sendEmail con dos parametros email y userNames
  private function sendEmail($email, $userNames)
  {
    // llamar a la funcion emailCodeTemplateUserNames y almacenar en variable
    $emailCodeTemplateUserNames = emailCodeTemplateUserNames($userNames);
    $mail = new Mail();
    $enviarMail = $mail->sendMail(
      $email,
      'Recuperación de nombres de usuario',
      $emailCodeTemplateUserNames
    );
    // retornar true si se mando el correo
    if ($enviarMail) {
      return true;
    } else {
      return false;
    }
  }
}
