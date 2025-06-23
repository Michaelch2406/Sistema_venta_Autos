<?php
session_start();
require_once("../MODELOS/usuarios.php");
$usuario_model = new Usuario();

if (isset($_GET["usu"], $_GET["cla"])) {
    $user = $_GET["usu"];
    $pass = $_GET["cla"];
    $result = $usuario_model->logeo($user, $pass);

    if ($result && $row = $result->fetch_assoc()) {
        // Si Usuario existe se guarda los datos en $_SESSION
        $_SESSION['usuario_id'] = $row['usu_id'];
        $_SESSION['usuario_nombre'] = $row['usu_nombre'];
        $_SESSION['usuario_apellido'] = $row['usu_apellido'];
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "0";
}
