<?php

class Conexion
{
  private $server;
  private $user;
  private $password;
  private $database;
  private $port;
  private mysqli $conexion;
  function __construct()
  {
    $datos = $this->datosConexion();
    foreach ($datos as $value) {
      $this->server = $value['server'];
      $this->user = $value['user'];
      $this->password = $value['password'];
      $this->database = $value['database'];
      $this->port = $value['port'];
    }

    $this->conexion = new mysqli(
      $this->server,
      $this->user,
      $this->password,
      $this->database,
      $this->port
    );
    if ($this->conexion->errno) {
      echo "err$this->portor";
      die();
    }
  }

  private function datosConexion()
  {
    $dirname = dirname(__FILE__);
    $json = file_get_contents($dirname . '/config.json');
    return json_decode($json, true);
  }
  public function hola()
  {
    //pruebas posterior borrado
    return $this->conexion->query(
      'SELECT * FROM `usuarios` WHERE `UsuarioId`=10'
    );
  }

  public function obtenerDatos(string $sqlQuery)
  {
    $resultados = [];
    foreach ($this->conexion->query($sqlQuery) as $value) {
      array_push($resultados, $value);
    }
    return $resultados;
  }

  public function nonQuery(string $sql)
  {
    $this->conexion->query($sql);
    return $this->conexion->affected_rows;
  }

  //inserts
  public function nonQueryId(string $sql)
  {
    $this->conexion->query($sql);
    $filas = $this->conexion->affected_rows;
    if ($filas >= 1) {
      return $this->conexion->insert_id;
    } else {
      return 0;
    }
  }
}
