<?php
// require once conexion.php and respuestas.class.php
require_once 'conexion/Conexion.php';
require_once 'Respuestas.class.php';

class Registro extends conexion
{
     // function registro con un parametro json
     public function registro($json)
     {
          // instancia de la clase respuestas
          $_respuestas = new Respuestas();
          // decode json  
          $decode = json_decode($json, true);

          //verificar que existe username, password y email
          if (!isset($decode['username']) || !isset($decode['password']) || !isset($decode['email'])) {
               // error de parametros
               // retorna el error_400 de la clase respuestas

               //   print_r($decode);
               return $_respuestas->error_400();
          } else {
               // sacar a variables en json
               $usuario = $decode['username'];
               $password = $decode['password'];
               $email = $decode['email'];
               // function checkUser return true or false
               $checkUser = $this->checkUser($usuario);
               // function checkEmail return true or false
               $checkEmail = $this->checkEmail($email);
               // insertar el usuario en la base de datos con insertUser si los dos son true
               if ($checkUser && $checkEmail) {
                    // insertar el usuario en la base de datos con insertUser
                    $insertUser = $this->insertUser($usuario, $password, $email);
                    // comprobar si se inserto el usuario
                    if ($insertUser) {
                         // retorna el success de la clase respuestas
                         return $_respuestas->success([
                              "mensaje" => "Usuario registrado correctamente"
                         ]);
                    } else {
                         // retorna el error_500 de la clase respuestas
                         return $_respuestas->error_500();
                    }
               } else {
                    // error de usuario o email
                    // retorna el error_200 de la clase respuestas
                    if (!$checkUser) {
                         return $_respuestas->error_200("El usuario $usuario ya existe");
                    } else {
                         return $_respuestas->error_200("El email $email no es valido");
                    }
               }
          }
     }

     // crear funcion privada checkUser
     private function checkUser($usuario)
     {
          // query para comprobar si existe el usuario
          $query = "SELECT COUNT(*)  FROM usuarios WHERE username = '$usuario'";
          // ejecutar query
          $result = parent::obtenerDatos($query);
          // echo $result[0]['COUNT(*)'];
          // comprobar si existe el usuario y retonrnar true o false
          if ($result[0]['COUNT(*)'] == 0) {
               return true;
          } else {
               return false;
          }
     }

     // crear funcion privada checkEmail
     private function checkEmail($email)
     {
          $key = "UNEbBgadXef6sB7dkYGji";
          $url = "https://app.verificaremails.com/api/verifyEmail?secret=" . $key . "&email=" . $email;
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          $response = curl_exec($ch);
          // echo $response;
          curl_close($ch);

          // si la respuesta es ok retorna true
          if ($response == "ok") {
               return true;
          } else {
               return false;
          }
     }
     // crear funcion privada insertUser
     private function insertUser($usuario, $password, $email)
     {
          // cifrar la contraseña
          $password = password_hash($password, PASSWORD_DEFAULT);
          // query para insertar el usuario
          $query = "INSERT INTO usuarios (username, password, email) VALUES ('$usuario', '$password', '$email')";
          // ejecutar query
          $result = parent::nonQuery($query);
          // comprobar si se inserto el usuario
          if ($result) {
               return true;
          } else {
               return false;
          }
     }
}
