<?php

require_once 'conexion/Conexion.php';
require_once 'Respuestas.class.php';
require_once 'Users.class.php';

class Post extends Conexion
{
  public function insertPost($token, $username, $descripcion, $img)
  {
    $_respuesta = new Respuestas();
    // instancia de la clase Users $_users = new Users();
    $_users = new Users();
    // validar usuario
    $respuesta = $_users->validateUser($username);
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $_users->validateToken($token, $userID);
      if ($respuesta) {
        // validar si es una imagen
        $respuesta = $_users->validateImg($img);
        if ($respuesta) {
          $respuesta = $this->createPost($userID, $descripcion, $img);
          if ($respuesta) {
            return $_respuesta->success([
              'mensaje' => 'Imagen insertó correctamente',
            ]);
          } else {
            return $_respuesta->error_500();
          }
        } else {
          // enviar respuesta de error 400
          return $_respuesta->error_400();
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuesta->error_403();
      }
    } else {
      return $_respuesta->error_200("El usuario $username no existe");
    }
  }

  //   crear funcion privada para crear post
  private function createPost($userID, $descripcion, $img)
  {
    // variable con el destino de las fotos de los posts

    $dest = 'img_posts';
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
            // crear query para insertar post
            $sql = "INSERT INTO publicaciones (`user_id`, `descripcion`, `foto`) VALUES ($userID, '$descripcion', '$imgDestination')";
            // ejecutar query
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

  public function insertComment($json)
  {
    $_respuesta = new Respuestas();
    // instancia de la clase Users $_users = new Users();
    $_users = new Users();
    // json decode
    $data = json_decode($json, true);
    // validar usuario
    $respuesta = $_users->validateUser($data['user']);
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $_users->validateToken($data['token'], $userID);
      if ($respuesta) {
        // validar que exista un comentario
        if ($data['comment'] != '') {
          // validar que edPublicaciones exista y sea valido
          if (is_numeric($data['postID'])) {
            // crear query para insertar comentario
            $sql = "INSERT INTO comentarios (`user_id`, `publicacion_id`, `comentario`) VALUES ($userID, $data[postID], '$data[comment]')";
            // ejecutar query
            $datos = parent::nonQuery($sql);
            // retorna success si se realizo correctamente
            if ($datos) {
              return $_respuesta->success([
                'mensaje' => 'Comentario insertó correctamente',
              ]);
            } else {
              // retorna error
              return $_respuesta->error_500();
            }
          } else {
            // retorna error
            return $_respuesta->error_400();
          }
        } else {
          // retorna error
          return $_respuesta->error_400();
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuesta->error_403();
      }
    } else {
      return $_respuesta->error_200(
        'El usuario' . $data['user'] . ' no existe'
      );
    }
  }

  public function getUsersByUsername($token, $username, $inicio, $fin)
  {
    $_respuesta = new Respuestas();
    // instancia de la clase Users $_users = new Users();
    $_users = new Users();
    // validar usuario
    $respuesta = $_users->validateUser($username);
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $_users->validateToken($token, $userID);
      if ($respuesta) {
        // crear query para obtener los usuarios
        $sql = "SELECT * FROM `publicaciones` WHERE `user_id` = $userID ORDER BY `ID_publicacion` DESC LIMIT $inicio,$fin";
        // ejecutar query
        $datos = parent::obtenerDatos($sql);
        // print_r($datos);
        // retorna success si se realizo correctamente
        if ($datos) {
          // formatear url de las imagenes
          foreach ($datos as $key => $value) {
            // añadir active: true, drawer: false
            $datos[$key]['active'] = true;
            $datos[$key]['drawer'] = false;
            
            $datos[$key]['comentarios'] = [[
              'id' => '6',
              'user_name' => 'Juan1',
              'comment' => 'Hola',
            ],
            [
              'id' => '7',
              'user_name' => 'adios',
              'comment' => 'aiods',
            ],
          ];
            // quitar el . del principio de la url
            $datos[$key]['foto'] = substr($datos[$key]['foto'], 1);
            $datos[$key]['foto'] =
              'http://projectdaw.duckdns.org:3377/API-REST' .
              $datos[$key]['foto'];
            // quitar el userid del array
            unset($datos[$key]['user_id']);
          }
          return $_respuesta->success($datos);
        } else {
          // retorna error
          return $_respuesta->error_500();
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuesta->error_403();
      }
    } else {
      return $_respuesta->error_200('El usuario' . $username . ' no existe');
    }
  }
}
