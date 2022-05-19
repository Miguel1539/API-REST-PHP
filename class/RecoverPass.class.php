<?php
// require once conexion.php and respuestas.class.php
require_once 'conexion/Conexion.php';
require_once 'Respuestas.class.php';

// include_once '../../PHP-Mailer/index.php';
// include_once './assets/emailTemplates/plantillasCorreos.php';

class RecoverPass extends Conexion
{
  // crear funcion publica recoverPass con un parametro json
  public function checkValidUser($json)
  {
    // instancia de la clase respuestas
    $_respuestas = new Respuestas();
    // decode json
    $decode = json_decode($json, true);

    // si existe $decode['userName'] mandar error 400
    if (!isset($decode['userName'])) {
      //mandar header 400
      return $_respuestas->error_400();
    }

    // verificar si el UserName existe en la base de datos y gruardar en variable true o false
    $checkUserName = $this->checkUserName($decode['userName']);

    // comprobar checkUserName
    if ($checkUserName) {
      // comprobar si ya se inserto un codigo en la base de datos
      $checkCode = $this->checkCode($decode['userName']);
      if ($checkCode) {
        // retorna error que ya se mando un codigo al correo
        return $_respuestas->error_200('Ya se mando un codigo al correo');
      } else {
        // generar el codigo e insertarlo en la base de datos
        $inserSuccess = $this->generateCode($decode['userName']);
        if ($inserSuccess) {
          // enviar correo electronico con el codigo
          $sendEmail = $this->sendEmail($decode['userName']);
          if ($sendEmail) {
            // retorna success
            return $_respuestas->success([
              'mensaje' =>
                'Se mando un codigo al correo electronico correctamente',
            ]);
          } else {
            // retorna error 500 al enviar el correo
            return $_respuestas->error_500(
              'Error al enviar el correo, intente más tarde'
            );
          }
        } else {
          // retorna error 500
          return $_respuestas->error_500();
        }
      }
    } else {
      // retorna el error_200 de la clase respuestas
      return $_respuestas->error_200('El usuario no existe');
    }
  }

  // crear funcion privada checkUserName
  private function checkUserName($userName)
  {
    // query para verificar si el nombre existe en la base de datos
    $query = "SELECT COUNT(*) FROM usuarios WHERE username like '$userName'";
    // ejecutar query
    $result = parent::obtenerDatos($query);

    // comprobar si el nombre existe
    if ($result[0]['COUNT(*)'] == 1) {
      // retorna true
      return true;
    } else {
      // retorna false
      return false;
    }
  }

  // crear funcion privada generateCode
  private function generateCode($userName)
  {
    // generar codigo de 6 digitos
    $code = rand(100000, 999999);
    // query para obtener el id del usuario
    $query = "SELECT ID_user FROM usuarios WHERE username like '$userName'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // obtener id del usuario en variable
    $idUser = $result[0]['ID_user'];

    // query para insertar codigo en la base de datos tabla passRecoveryCodes

    $query = "INSERT INTO passRecoveryCodes (user_id, code) VALUES ($idUser, '$code')";
    // ejecutar query
    $result = parent::nonQuery($query);
    // retorna true si se inserto el codigo
    if ($result) {
      return true;
    } else {
      return false;
    }
  }

  // crear funcion privada checkCode
  private function checkCode($userName)
  {
    // query para obtener el id del usuario
    $query = "SELECT ID_user FROM usuarios WHERE username like '$userName'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // obtener id del usuario en variable
    $idUser = $result[0]['ID_user'];
    // query para verificar si existe codigo en la base de datos
    $query = "SELECT COUNT(*) FROM passRecoveryCodes WHERE user_id like '$idUser'";
    // ejecutar query
    $result = parent::obtenerDatos($query);

    // comprobar si existe codigo
    if ($result[0]['COUNT(*)'] == 1) {
      // retorna true
      return true;
    } else {
      // retorna false
      return false;
    }
  }

  // crear funcion privada sendEmail
  private function sendEmail($userName)
  {
    // query para obtener el id del usuario y el email
    $query = "SELECT ID_user, email FROM usuarios WHERE username like '$userName'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // obtener id del usuario y el email en variable
    $idUser = $result[0]['ID_user'];
    $email = $result[0]['email'];
    // query para obtener el codigo
    $query = "SELECT code FROM passRecoveryCodes WHERE user_id like '$idUser'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // obtener codigo en variable
    $code = $result[0]['code'];

    // llamar a la funcion emailCodeTemplate y almacenar en variable
    $emailCodeTemplate = emailCodeTemplate($code);

    $mail = new Mail();
    // llamar al metodo sendMail de la clase Mail
    $enviarMail = $mail->sendMail(
      $email,
      'Código de verificación',
      $emailCodeTemplate
    );
    // retornar true si se mando el correo
    if ($enviarMail) {
      return true;
    } else {
      return false;
    }
  }

  // crear funcion publica verifyCode
  public function verifyCode($data)
  {
    // instancia de la clase respuestas
    $_respuestas = new Respuestas();
    // decodificar el json
    $decode = json_decode($data, true);
    // verificar si existe 'code' y 'userName' si alguno no existe retorna error_400
    if (!isset($decode['code']) || !isset($decode['userName'])) {
      return $_respuestas->error_400();
    }

    // verificar si el codigo existe en la base de datos y gruardar en variable true o false
    $checkCode = $this->contrastCode($decode['userName'], $decode['code']);

    // comprobar checkCode
    if ($checkCode[0]) {
      // retorna success
      return $_respuestas->success([
        'mensaje' => $checkCode[1],
      ]);
    } else {
      // retorna error_200 de la clase respuestas
      return $_respuestas->error_200($checkCode[1]);
    }
  }

  // crear funcion privada contrastCode
  private function contrastCode($userName, $code)
  {
    // query para obtener el id del usuario
    $query = "SELECT ID_user FROM usuarios WHERE username like '$userName'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // obtener id del usuario en variable
    $idUser = $result[0]['ID_user'];
    // query para verificar si existe codigo en la base de datos
    $query = "SELECT COUNT(*) FROM passRecoveryCodes WHERE user_id like '$idUser'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // comprobar si existe codigo
    if ($result[0]['COUNT(*)'] == 1) {
      // query para obtener el codigo
      $query = "SELECT code FROM passRecoveryCodes WHERE user_id like '$idUser'";
      // ejecutar query
      $result = parent::obtenerDatos($query);
      // obtener codigo en variable
      $codeDB = $result[0]['code'];
      // comparar codigo en la base de datos con el codigo enviado
      if ($codeDB == $code) {
        // retorna true
        return [true, 'El codigo es correcto'];
      } else {
        // retorna false
        return [false, 'El codigo es incorrecto, intente nuevamente'];
      }
    } else {
      // retorna false
      return [false, 'No existe el codigo para este usuario'];
    }
  }

  // crear funcion publica changePassword
  public function changePassword($data)
  {
    // instancia de la clase respuestas
    $_respuestas = new Respuestas();
    // decodificar el json
    $decode = json_decode($data, true);
    // verificar si existe 'password' y 'userName' si alguno no existe retorna error_400
    if (!isset($decode['newPassword']) || !isset($decode['userName'])) {
      return $_respuestas->error_400();
    }

    // llamar a la funcion changePassword y almacenar en variable
    $changePassword = $this->changePasswordDB(
      $decode['userName'],
      $decode['newPassword']
    );

    // comprobar changePassword
    if ($changePassword[0]) {
      //borrar el codigo de la base de datos
      $this->deleteCode($decode['userName']);
      // retorna success
      return $_respuestas->success([
        'mensaje' => $changePassword[1],
      ]);
    } else {
      // comprobar si el error de $changePassword[2] es 500 o 200
      if ($changePassword[2] == 500) {
        // retorna error_500 de la clase respuestas
        return $_respuestas->error_500($changePassword[1]);
      } else {
        // retorna error_200 de la clase respuestas
        return $_respuestas->error_200($changePassword[1]);
      }
    }
  }

  // crear funcion privada changePasswordDB
  private function changePasswordDB($userName, $newPassword)
  {
    // query para obtener el id del usuario
    $query = "SELECT ID_user FROM usuarios WHERE username like '$userName'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // obtener id del usuario en variable
    $idUser = $result[0]['ID_user'];
    // query para verificar si existe codigo en la base de datos
    $query = "SELECT COUNT(*) FROM passRecoveryCodes WHERE user_id like '$idUser'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // comprobar si existe codigo
    if ($result[0]['COUNT(*)'] == 1) {
      // encriptar password
      $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
      // query para actualizar el password
      $query = "UPDATE usuarios SET password = '$newPassword' WHERE ID_user like '$idUser'";
      // ejecutar query
      $result = parent::nonQuery($query);
      echo 'hola';
      // comprobar si ha fallado
      if ($result) {
        // verdadero y mensaje
        return [true, 'La contraseña se actualizo correctamente'];
      } else {
        // falso y mensaje
        return [false, 'Error al actualizar la contraseña', 500];
      }
    } else {
      // falso y mensaje
      return [false, 'No existe el codigo para este usuario', 200];
    }
  }
  

  // crear funcion privada deleteCode
  private function deleteCode($userName)
  {
    // query para obtener el id del usuario
    $query = "SELECT ID_user FROM usuarios WHERE username like '$userName'";
    // ejecutar query
    $result = parent::obtenerDatos($query);
    // obtener id del usuario en variable
    $idUser = $result[0]['ID_user'];
    // query para borrar el codigo
    $query = "DELETE FROM passRecoveryCodes WHERE user_id like '$idUser'";
    // ejecutar query
    $result = parent::nonQuery($query);
    // comprobar si ha fallado
    if ($result) {
      // verdadero y mensaje
      return true;
    } else {
      // falso y mensaje
      return false;
    }
  }
}
