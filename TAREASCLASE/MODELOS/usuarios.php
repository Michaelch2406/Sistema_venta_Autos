<?php
  require_once("../CONFIG/Conexion.php");

  class Usuario {
    private $conn;

    public function __construct() {
      $this->conn = new Conexion();
    }

    function logeo($usu, $cla) {
      $sql = "CALL login_usuario('$usu','$cla')";
      return $this->conn->ejecutarSP($sql);
    }

    function listar() {
      $sql = "SELECT
                usu_id,
                usu_cedula,
                usu_nombre,
                usu_apellido,
                usu_clave,
                usu_usuario
              FROM usuarios
              ORDER BY usu_id ASC";
      return $this->conn->ejecutarSP($sql);
    }
  }
?>
