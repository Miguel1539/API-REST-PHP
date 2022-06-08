<?php

require_once 'conexion/Conexion.php';
require_once 'Respuestas.class.php';

class Users extends Conexion
{
  public function updateImgProfile($token, $username, $img, $dest)
  {
    $_respuestas = new Respuestas();
    // validar usuario
    $respuesta = $this->validateUser($username);
    // si el usuario existe
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $this->validateToken($token, $userID);
      // si el token es valido
      if ($respuesta) {
        // validar si es una imagen
        $respuesta = $this->validateImg($img);
        // si es una imagen
        if ($respuesta) {
          // guardar la imagen
          $respuesta = $this->saveImg($img, $userID, $dest);
          // si se guardo la imagen
          if ($respuesta) {
            return $_respuestas->success([
              'mensaje' => 'Imagen actualizada correctamente',
            ]);
          } else {
            return $_respuestas->error_500();
          }
        } else {
          // enviar respuesta de error 200 solo se permite jpg y png
          return $_respuestas->error_200('Solo se permite jpg y png');
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuestas->error_403();
      }
    } else {
      // enviar respuesta de error 200 usuario no existe
      return $_respuestas->error_200("El usuario $username no existe");
    }
  }
  public function updateDescriptionProfile($token, $username, $desciption)
  {
    $_respuestas = new Respuestas();
    // validar usuario
    $respuesta = $this->validateUser($username);
    // print_r($respuesta);
    // si el usuario existe
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $this->validateToken($token, $userID);
      // si el token es valido
      if ($respuesta) {
        // actualizar la descripcion
        $respuesta = $this->updateDescription($userID, $desciption);
        // si se actualizo la descripcion
        if ($respuesta) {
          return $_respuestas->success([
            'mensaje' => 'Descripcion actualizada correctamente',
          ]);
        } else {
          // error 200  ya existe la descripcion
          return $_respuestas->error_200('Ya existe la descripcion');
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuestas->error_403();
      }
    } else {
      // enviar respuesta de error 200 usuario no existe
      return $_respuestas->error_200("El usuario $username no existe");
    }
  }
  public function getImgProfile($token, $username, $dbImgName)
  {
    $_respuestas = new Respuestas();
    // validar usuario
    $respuesta = $this->validateUser($username);
    // si el usuario existe
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $this->validateToken($token, $userID);
      // si el token es valido
      if ($respuesta) {
        // obtener la imagen
        $respuesta = $this->getImg($userID, $dbImgName);
        // si se obtuvo la imagen
        if ($respuesta) {
          //   return $_respuestas->success([
          //     'imagen' => $respuesta,
          //   ]);
          // qutar el primer . de el string $respuesta
          $respuesta = substr($respuesta, 1);
          // generar url para mostrar la imagen
          $url = 'http://projectdaw.duckdns.org:3377/API-REST/' . $respuesta;
          return $_respuestas->success([
            'imagen' => $url,
          ]);
        } else {
          return $_respuestas->error_500();
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuestas->error_403();
      }
    } else {
      // enviar respuesta de error 200 usuario no existe
      return $_respuestas->error_200("El usuario $username no existe");
    }
  }

  public function getAllProfile($token, $username)
  {
    $_respuestas = new Respuestas();
    // validar usuario
    $respuesta = $this->validateUser($username);
    // si el usuario existe
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $this->validateToken($token, $userID);
      // si el token es valido
      if ($respuesta) {
        // obtener todos los datos del usuario
        $respuesta = $this->getAll($userID);
        // si se obtuvo la imagen
        if ($respuesta) {
          return $_respuestas->success([
            'datos' => $respuesta,
          ]);
        } else {
          return $_respuestas->error_500();
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuestas->error_403();
      }
    } else {
      // enviar respuesta de error 200 usuario no existe
      return $_respuestas->error_200("El usuario $username no existe");
    }
  }

  public function getAllProfileByUsername($token, $username, $searchedUser)
  {
    $_respuestas = new Respuestas();
    // validar usuario
    $respuesta = $this->validateUser($username);
    // si el usuario existe
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $this->validateToken($token, $userID);

      // si el token es valido
      if ($respuesta) {
        // validar que el usuario buscado exista
        $respuesta = $this->validateUser($searchedUser);
        // si el usuario existe
        if ($respuesta[0]) {
          $searchedUserID = $respuesta[1];
          // obtener todos los datos del usuario
          $respuesta = $this->getAll($searchedUserID);
          // si se obtuvieron los datos
          if ($respuesta) {
            unset($respuesta['email']);
            return $_respuestas->success([
              'datos' => $respuesta,
            ]);
          } else {
            return $_respuestas->error_500();
          }
        } else {
          // enviar respuesta de error 200 usuario no existe
          return $_respuestas->error_200("El usuario $searchedUser no existe");
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuestas->error_403();
      }
    } else {
      // enviar respuesta de error 200 usuario no existe
      return $_respuestas->error_200("El usuario $username no existe");
    }
  }

  public function getListUsers($token, $username, $stringToSearch)
  {
    $_respuestas = new Respuestas();
    // validar usuario
    $respuesta = $this->validateUser($username);
    // si el usuario existe
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $this->validateToken($token, $userID);
      // si el token es valido
      if ($respuesta) {
        // obtener una lista con los usuarios
        $respuesta = $this->getList($stringToSearch);
        if ($respuesta) {
          // modificar la url de las imagenes
          foreach ($respuesta as $key => $value) {
            if ($value['foto_perfil'] != null) {
              $respuesta[$key]['foto_perfil'] =
                'http://projectdaw.duckdns.org:3377/API-REST' .
                substr($value['foto_perfil'], 1);
            } else {
              $respuesta[$key]['foto_perfil'] =
                'http://projectdaw.duckdns.org:3377/API-REST/img/default/genericUser2.jpg';
            }
          }

          return $_respuestas->success([
            'lista' => $respuesta,
          ]);
        } else {
          // error 200 no existe ningun usuario con ese nombre
          return $_respuestas->error_200(
            'No existe ningun usuario con ese nombre'
          );
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuestas->error_403();
      }
    } else {
      // enviar respuesta de error 200 usuario no existe
      return $_respuestas->error_200("El usuario $username no existe");
    }
  }

  public function setFolow($json)
  {
    // $_respuestas = new Respuestas();
    // obtener los datos del json
    $body = json_decode($json, true);
    // validar que los datos sean correctos
    $respuesta = $this->validateFollow($body);
    return $respuesta;
  }

  // crear funcion privada para validar los datos del seguidor
  private function validateFollow($body)
  {
    $_respuestas = new Respuestas();
    // sacar a variables
    $token = $body['token'];
    $follower = $body['user'];
    $followed = $body['userToFolow'];

    // validar el follower
    $respuesta = $this->validateUser($follower);
    // si el usuario existe
    if ($respuesta[0]) {
      $follower_id = $respuesta[1];
      // validar el token
      $respuesta = $this->validateToken($token, $follower_id);
      // si el token es valido
      if ($respuesta) {
        // validar el followed
        $respuesta = $this->validateUser($followed);
        if ($respuesta[0]) {
          $followed_id = $respuesta[1];
          // validar que el usuario no se siga a si mismo
          if ($follower_id != $followed_id){
            // seguir al usuario
            $respuesta = $this->follow($follower_id, $followed_id);
            if ($respuesta) {
              return $_respuestas->success([
                'msg' => $respuesta,
              ]);
            } else {
              return $_respuestas->error_200('No se pudo seguir al usuario');
            }
          }else{
            return $_respuestas->error_200(
              'No puedes seguirte a ti mismo'
            );
          }
        } else {
          return $_respuestas->error_200("El usuario $followed no existe");
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuestas->error_403();
      }
    } else {
      return $_respuestas->error_200("El usuario $follower no existe");
    }
  }
  private function follow ($follower_id, $followed_id)
  {
    // ver si ya lo sigue
    $query = "SELECT COUNT(*) FROM seguidores WHERE seguidor_id = $follower_id AND seguido_id = $followed_id";
    $result = parent::obtenerDatos($query);
    // print_r($result);
    if ($result[0]['COUNT(*)'] > 0){
      // borrar el seguimiento
      $query = "DELETE FROM seguidores WHERE seguidor_id = $follower_id AND seguido_id = $followed_id";
      $result = parent::nonQuery($query);
      if ($result) {
        // retorna un mensaje  de exito
        return 'Ya no sigues a este usuario';
      } else {
        return false;
      }
    }else {
      // insertar el seguidor
      $query = "INSERT INTO seguidores (seguidor_id, seguido_id) VALUES ($follower_id, $followed_id)";
      $result = parent::nonQuery($query);
      if ($result) {
        // retorna un mensaje  de exito
        return "Se ha seguido al usuario correctamente";
      } else {
        return false;
      }
    }
  }
  // crear funcion privada para obtener lista de usuario getList
  private function getList($stringToSearch)
  {
    // sacar un maximo de 4 usuarios
    $sql = "SELECT username,descripcion,foto_perfil FROM usuarios WHERE username LIKE '%$stringToSearch%' LIMIT 8";
    $result = parent::obtenerDatos($sql);
    if ($result) {
      return $result;
    } else {
      return false;
    }
  }

  // crear una funcion privada para validar el usuario y el token
  public function validateUser($username)
  {
    // validar usuario
    $sql = "SELECT COUNT(*),ID_user FROM usuarios WHERE `username` LIKE '$username'";
    $datos = parent::obtenerDatos($sql);
    // comprobar si el usuario existe
    if ($datos[0]['COUNT(*)'] > 0) {
      // retorna success
      return [true, $datos[0]['ID_user']];
    } else {
      // retorna error
      return [false, 0];
    }
  }

  // crear una funcion privada para validar el usuario y el token
  public function validateToken($token, $userID)
  {
    // validar usuario
    $sql = "SELECT COUNT(*) FROM tokens WHERE `token` LIKE '$token' AND `user_id` = $userID";
    $datos = parent::obtenerDatos($sql);
    // comprobar si el usuario existe
    if ($datos[0]['COUNT(*)'] == 1) {
      // retorna success
      return true;
    } else {
      // retorna error
      return false;
    }
  }

  // crear una funcion privada para validar la imagen
  public function validateImg($img)
  {
    // validar si es una imagen
    if ($img['type'] == 'image/jpeg' || $img['type'] == 'image/png') {
      // retorna success
      return true;
    } else {
      // retorna error
      return false;
    }
  }

  // crear una funcion privada para guardar la imagen
  private function saveImg($img, $userID, $dest)
  {
    if ($dest === 'img_main_profile') {
      $campo = 'foto_perfil';
    } else {
      $campo = 'foto_banner';
    }
    // comprobar si existe una imagne para el usuario, si existe se elimina y se guarda la nueva
    $sql = "SELECT $campo FROM usuarios WHERE  `ID_user` = $userID";
    $datos = parent::obtenerDatos($sql);
    if ($datos[0][$campo] != null) {
      // eliminar la imagen
      unlink($datos[0][$campo]);
    }

    // guardar la imagen
    $imgName = $img['name'];
    $imgType = $img['type'];
    $imgTmp = $img['tmp_name'];
    $imgSize = $img['size'];
    $imgError = $img['error'];
    $imgExt = explode('.', $imgName);
    $imgActualExt = strtolower(end($imgExt));
    $allowed = ['jpg', 'jpeg', 'png'];
    if (in_array($imgActualExt, $allowed)) {
      if ($imgError === 0) {
        if ($imgSize < 1000000) {
          $imgNameNew = uniqid('', true) . '.' . $imgActualExt;
          $imgDestination = './img/' . $dest . '/' . $imgNameNew;
          if (move_uploaded_file($imgTmp, $imgDestination)) {
            // actualizar el campo de la base de datos
            $sql = "UPDATE usuarios SET `$campo` = '$imgDestination' WHERE `ID_user` = $userID";
            $datos = parent::nonQuery($sql);
            // retorna success si se realizo correctamente
            if ($datos) {
              return true;
            } else {
              // retorna error
              return false;
            }
          } else {
            return false;
          }
        } else {
          // retorna error
          return false;
        }
      } else {
        // retorna error
        return false;
      }
    } else {
      // retorna error
      return false;
    }
  }

  // crear una funcion privada para obtener la imagen
  private function getImg($userID, $dbImgName)
  {
    if ($dbImgName === 'mainprofile') {
      $campo = 'foto_perfil';
    } else {
      $campo = 'foto_banner';
    }
    // obtener la imagen
    $sql = "SELECT $campo FROM usuarios WHERE  `ID_user` = $userID";
    $datos = parent::obtenerDatos($sql);
    if ($datos[0][$campo] != null) {
      // retorna success si se realizo correctamente
      return $datos[0][$campo];
    } else {
      // retorna error
      return false;
    }
  }

  // crear una funcion privada para obtener todos los datos del usuario
  private function getAll($userID)
  {
    // obtener todos los datos del usuario
    $sql = "SELECT * FROM usuarios WHERE  `ID_user` = $userID";
    $sqlFollowers = "SELECT COUNT(*) FROM seguidores WHERE `seguido_id` = $userID";
    $sqlFollowing = "SELECT COUNT(*) FROM seguidores WHERE `seguidor_id` = $userID";
    $datos = parent::obtenerDatos($sql);
    $datosFollowers = parent::obtenerDatos($sqlFollowers);
    $datosFollowing = parent::obtenerDatos($sqlFollowing);
    // añadir datos seguidores y siguendo
    $datos[0]['seguidores'] = $datosFollowers[0]['COUNT(*)'];
    $datos[0]['seguidos'] = $datosFollowing[0]['COUNT(*)'];
    //quitar la contraseña, ID_user y datos vacios
    unset($datos[0]['password']);
    unset($datos[0]['ID_user']);
    foreach ($datos[0] as $key => $value) {
      if ($value == null) {
        unset($datos[0][$key]);
      }
    }
    // si tiene foto_banner o foto_perfil cambiar el valor de la imagen a la url
    if ($datos[0]['foto_banner'] != null) {
      $datos[0]['foto_banner'] =
        'http://projectdaw.duckdns.org:3377/API-REST' .
        substr($datos[0]['foto_banner'], 1);
    }
    if ($datos[0]['foto_perfil'] != null) {
      $datos[0]['foto_perfil'] =
        'http://projectdaw.duckdns.org:3377/API-REST' .
        substr($datos[0]['foto_perfil'], 1);
    }
    // print_r($datos[0]);
    if ($datos[0]) {
      // retorna success si se realizo correctamente
      return $datos[0];
    } else {
      // retorna error
      return false;
    }
  }

  // crear funcion privada updateDescription
  private function updateDescription($userID, $description)
  {
    // actualizar la descripcion
    $sql = "UPDATE usuarios SET `descripcion` = '$description' WHERE `ID_user` = $userID";
    $datos = parent::nonQuery($sql);
    print_r($datos);
    // retorna success si se realizo correctamente
    if ($datos) {
      return true;
    } else {
      // retorna error
      return false;
    }
  }
}
