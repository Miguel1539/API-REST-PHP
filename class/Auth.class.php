<?php
// require once conexion.php and respuestas.class.php
require_once 'conexion/Conexion.php';
require_once 'Respuestas.class.php';


class Auth extends Conexion
{


    // funcion login con un parametro json
    public function login($json)
    {
        //instancia de la clase respuestas
        $_respuestas = new Respuestas();
        // decode json
        $decode = json_decode($json, true);

        if (!isset($decode['username']) || !isset($decode['password'])) {
            //error de parametros
            // retorna el error_400 d ela clase respuestas
            return $_respuestas->error_400();
        } else {
            // sacar a variables en json
            $usuario = $decode['username'];
            $password = $decode['password'];

            // obtener datos del usuario con getUser en variable
            $datos = $this->getUser($usuario);

            //comprobar si exixte o no el usuario
            if ($datos) {
                //comprobar si la contraseña es correcta

                if (password_verify($password, $datos[0]['password'])) {
                // if ($datos[0]['password'] == $password) {
                    // crear el token
                    $token = $this->createToken($datos[0]['ID_user']);
                    // se guardo el token
                    if ($token) {
                        // retorna el token
                        return $_respuestas->success([
                            'token' => $token
                        ]);
                    } else {
                        // retorna el error_500
                        return $_respuestas->error_500();
                    }
                } else {
                    //error de contraseña
                    return $_respuestas->error_200("Contraseña incorrecta");
                    // return $datos;
                }
            } else {
                //error de usuario no existe

                return $_respuestas->error_200("El usuario $usuario no existe");
            }
        }
    }

    //metodo privado para obener los datos del usuario
    private function getUser($username)
    {
        //query para obtener los datos del usuario
        $query = "SELECT ID_user,username,email,password FROM usuarios WHERE username = '$username'";
        $datos = parent::obtenerDatos($query);
        //comprobar si el username exixte
        if (count($datos) > 0) {
            //retorna los datos del usuario
            return $datos;
        } else {
            return 0;
        }
    }

    //metodo para crear el token
    private function createToken($id)
    {
        //crear token
        $token = md5(uniqid(rand(), true));
        $date = date('Y-m-d H:i:s');

        //comprobar si existe un usuario con tokens
        $query = "SELECT COUNT(*) FROM tokens WHERE `user_id` = $id";

        $datos = parent::obtenerDatos($query);


        // si existe un token para ese usuario se actualiza la fecha y se retorna el token
        if ($datos[0]['COUNT(*)'] != 0) {
            $query = "UPDATE tokens SET token = '$token', fecha = '$date' WHERE `user_id` = '$id'";

            $datos = parent::nonQuery($query);
            // echo $datos;
            if ($datos) {
                return $token;
            } else {
                return 0;
            }
        } else {
            // echo "holoa";
            //si no existe un token para ese usuario se crea uno nuevo
            $query = "INSERT INTO tokens (user_id,token,fecha) VALUES ('$id','$token','$date')";
            $datos = parent::nonQuery($query);
            if ($datos) {
                return $token;
            } else {
                return 0;
            }
        }


        //query para crear el token
        // $query = "INSERT INTO tokens VALUES (NULL,'$id','$token','$date')";
        //ejecutar query
        // try {
        //     //code...
        //     $datos = parent::nonQuery($query);
        //     //retorna el token
        //     return $token;
        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return 0;
        // }

        //comprobar si se creo el token
        // if ($datos) {
        //     //retorna el token
        //     return $token;
        // } else {
        //     return 0;
        // }
    }

    // metodo publico para verificar que el token existe
    public function verifyToken($json){
        //instancia de la clase respuestas
        $_respuestas = new Respuestas();
        // decode json
        $decode = json_decode($json, true);
        // comprobar si el token y el userName existe
        if (!isset($decode['token']) || !isset($decode['userName'])) {
            //error de parametros
            // retorna el error_400 d ela clase respuestas
            return $_respuestas->error_400();
        } 

        // llamar a funcion para sacar el id del usuario
        $id = $this->getIdUser($decode['userName']);
        if (!$id[0]) {
            //error de usuario no existe
            return $_respuestas->error_200("El usuario no existe");    
        }
        //sacar el id del usuario
        $id = $id[1];
        //sacar el token a variable
        $token = $decode['token'];
        
        // llamar a funcion para comprobar que el token existe y actualizar la fecha
        $datos = $this->verifyTokenExist($id,$token);

        //comprobar si el token existe
        if ($datos) {
            //retorna success
            return $_respuestas->success([
                "mensaje" => "token valido"
            ]);
        } else {
            //retorna error
            return $_respuestas->error_200("El token no existe");
        }


    }

    //metodo privado para obtener el id del usuario
    private function getIdUser($username)
    {
        //query para obtener el id del usuario
        $query = "SELECT ID_user FROM usuarios WHERE username = '$username'";
        $datos = parent::obtenerDatos($query);
        //comprobar si el username exixte
        if (count($datos) > 0) {
            //retorna el id del usuario
            return [1,$datos[0]['ID_user']];
        } else {
            return [0,0];
        }
    }

    //metodo privado para comprobar que el token existe
    private function verifyTokenExist($id,$token)
    {
        //query para comprobar que el token existe
        $query = "SELECT COUNT(*) FROM tokens WHERE `user_id` = '$id' AND `token` = '$token'";
        $datos = parent::obtenerDatos($query);
        //comprobar si el token existe
        if ($datos[0]['COUNT(*)'] != 0) {
            //actualizar la fecha
             $date = date('Y-m-d H:i:s');
            $query = "UPDATE tokens SET fecha = '$date' WHERE `user_id` = '$id' AND `token` = '$token'";
            $datos = parent::nonQuery($query);
            //retorna true si se encontro el token
            return true;
        } else {
            //retorna false si no se encontro el token  
            return false;
        }
    }
}
