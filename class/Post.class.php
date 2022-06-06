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

  public function getUsersByUsername(
    $token,
    $username,
    $inicio,
    $fin,
    $searchedUser
  ) {
    $_respuesta = new Respuestas();
    // instancia de la clase Users $_users = new Users();
    $_users = new Users();

    // validar searchedUser
    $respuesta = $_users->validateUser($searchedUser);
    if ($respuesta[0]) {
      $searchedUserID = $respuesta[1];
    }
    // validar usuario
    $respuesta = $_users->validateUser($username);
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $_users->validateToken($token, $userID);
      if ($respuesta) {
        // crear query para obtener las publicaciones de los usuarios
        if ($searchedUser) {
          $sql = "SELECT * FROM `publicaciones` WHERE `user_id` = $searchedUserID ORDER BY `ID_publicacion` DESC LIMIT $inicio,$fin";
        } else {
          $sql = "SELECT * FROM `publicaciones` WHERE `user_id` = $userID ORDER BY `ID_publicacion` DESC LIMIT $inicio,$fin";
        }
        // ejecutar query
        $datos = parent::obtenerDatos($sql);
        // print_r($datos);
        // retorna success si se realizo correctamente
        if ($datos) {
          // formatear url de las imagenes
          foreach ($datos as $key => $value) {
            // añadir active: true, drawer: false
            // query para saber si el usuario dio like
            $sql = "SELECT COUNT(*) FROM `likes` WHERE `user_id` = $userID AND `publicacion_id` = $value[ID_publicacion]";
            $like = parent::obtenerDatos($sql);
            if ($like[0]['COUNT(*)'] > 0) {
              $datos[$key]['active'] = false;
            } else {
              $datos[$key]['active'] = true;
            }
            $datos[$key]['drawer'] = false;

            $datos[$key]['comments'] = [];

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
          return $_respuesta->error_200('No hay publicaciones');
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuesta->error_403();
      }
    } else {
      return $_respuesta->error_200('El usuario' . $username . ' no existe');
    }
  }
  public function getAllPosts($token, $user, $inicio, $fin)
  {
    $_respuesta = new Respuestas();
    // instancia de la clase Users $_users = new Users();
    $_users = new Users();
    // validar usuario
    $respuesta = $_users->validateUser($user);
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $_users->validateToken($token, $userID);
      if ($respuesta) {
        // crear query para obtener las publicaciones de los usuarios
        $sql = "SELECT * FROM `publicaciones` ORDER BY `ID_publicacion` DESC LIMIT $inicio,$fin";
        // ejecutar query
        $datos = parent::obtenerDatos($sql);
        // print_r($datos);
        // retorna success si se realizo correctamente
        if ($datos) {
          // formatear url de las imagenes
          foreach ($datos as $key => $value) {
            // añadir la imagen del perfil y su username del usuario que hizo la publicacion
            $sql = "SELECT `foto_perfil`, `username` FROM `usuarios` WHERE `ID_user` = $value[user_id]";
            $data = parent::obtenerDatos($sql);
            $datos[$key]['username']= $data[0]['username'];
            if ($data[0]['foto_perfil'] == null) {
              $datos[$key]['foto_perfil'] = 'http://projectdaw.duckdns.org:3377/API-REST/img/default/genericUser2.jpg';
            } else {
              $datos[$key]['foto_perfil'] =
                'http://projectdaw.duckdns.org:3377/API-REST' .
                substr($data[0]['foto_perfil'], 1);
            }
            // $datos[$key]['foto_perfil'] ='http://projectdaw.duckdns.org:3377/API-REST' . substr($data[0]['foto_perfil'], 1);
            // print_r($data);
            // $datos[$key]['foto_perfil'] = substr($data[0]['foto_perfil'], 1);
            // $datos[$key]['foto_perfil'] =
            //   
            //   $datos[$key]['foto_perfil'];
            // añadir active: true, drawer: false
            // query para saber si el usuario dio like
            $sql = "SELECT COUNT(*) FROM `likes` WHERE `user_id` = $userID AND `publicacion_id` = $value[ID_publicacion]";
            $like = parent::obtenerDatos($sql);
            if ($like[0]['COUNT(*)'] > 0) {
              $datos[$key]['active'] = false;
            } else {
              $datos[$key]['active'] = true;
            }
            $datos[$key]['drawer'] = false;

            $datos[$key]['comments'] = [];

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
          return $_respuesta->error_200('No hay publicaciones');
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuesta->error_403();
      }
    } else {
      return $_respuesta->error_200('El usuario' . $user . ' no existe');
    }

  }

  public function getCommentsByPost($token, $username, $id_post)
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
        // crear query para obtener los comentaarios de una publicacion
        $sql = "SELECT * FROM `comentarios` WHERE `publicacion_id` = $id_post ORDER BY `ID_comentario` DESC";
        // ejecutar query
        $datos = parent::obtenerDatos($sql);
        // print_r($datos);
        // comprobar si esta vacio
        if ($datos) {
          // cambiar user_id por userName
          foreach ($datos as $key => $value) {
            $userid = $datos[$key]['user_id'];
            // obtener el nombre del usuario
            $sql = "SELECT `username` FROM `usuarios` WHERE `ID_user` = $userid";
            $datos[$key]['username'] = parent::obtenerDatos($sql)[0][
              'username'
            ];
            // quitar el userid del array
            unset($datos[$key]['user_id']);
          }
          // retonar success con $datos
          return $_respuesta->success($datos);
        } else {
          // retorna no hay resultados
          return $_respuesta->error_200(
            'No hay comentarios para esta publicación'
          );
        }
      } else {
        // enviar respuesta de error 403 forbidden
        return $_respuesta->error_403();
      }
    } else {
      return $_respuesta->error_200('El usuario' . $username . ' no existe');
    }
  }

  public function setLike($json)
  {
    $_respuesta = new Respuestas();
    // instancia de la clase Users $_users = new Users();
    $_users = new Users();

    // decodificar json
    $data = json_decode($json, true);
    // print_r($data);
    // validar que exista el usuario, el token y el id_post en $data
    if (
      !isset($data['user']) ||
      !isset($data['token']) ||
      !isset($data['postID'])
    ) {
      return $_respuesta->error_400();
    }

    // validar usuario
    $respuesta = $_users->validateUser($data['user']);
    if ($respuesta[0]) {
      $userID = $respuesta[1];
      // validar token
      $respuesta = $_users->validateToken($data['token'], $userID);
      if ($respuesta) {
        // validar que el post exista
        $sql = "SELECT COUNT(*) FROM `publicaciones` WHERE `ID_publicacion` = $data[postID]";
        $datos = parent::obtenerDatos($sql);
        if ($datos[0]['COUNT(*)'] > 0) {
          // validar que el usuario no haya dado like a ese post
          $sql = "SELECT COUNT(*) FROM `likes` 
            WHERE `user_id` = $userID AND 
            `publicacion_id` = $data[postID]";
          $datos = parent::obtenerDatos($sql);
          if ($datos[0]['COUNT(*)'] == 0) {
            // insertar like
            $sql = "INSERT INTO `likes` (`user_id`, `publicacion_id`) VALUES ($userID, $data[postID])";
            $datos = parent::nonQuery($sql);
            if ($datos) {
              // retorna success
              return $_respuesta->success('Like agregado');
            } else {
              // retorna error
              return $_respuesta->error_500('Error al agregar like');
            }
          } else {
            $sql = "DELETE FROM `likes` WHERE `user_id` = $userID AND `publicacion_id` = $data[postID]";
            $datos = parent::nonQuery($sql);
            if ($datos) {
              // retorna success
              return $_respuesta->success('Like eliminado');
            } else {
              // retorna error
              return $_respuesta->error_500('Error al eliminar like');
            }
          }
        } else {
          return $_respuesta->error_200(
            'La publicación ' . $data['postID'] . ' no existe'
          );
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
}
