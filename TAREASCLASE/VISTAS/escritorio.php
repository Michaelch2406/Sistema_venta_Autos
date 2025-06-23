<?php
session_start();
if (empty($_SESSION['usuario_id'])) {
    echo <<<HTML
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceso Denegado</title>
  <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container vh-100 d-flex flex-column justify-content-center align-items-center">
    <div class="alert alert-warning text-center w-75">¡Atención! Usted no se ha logeado.</div>
    <a href="inicio1.php" class="btn btn-primary btn-lg mt-3">ACEPTAR</a>
  </div>
  <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Escritorio de Usuarios</title>

  <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
  <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../DataTables/datatables.min.js"></script>
  <script src="../VISTAS/JS/escritorio.js"></script>

  <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../DataTables/datatables.min.css" rel="stylesheet">
  <link href="../PUBLIC/estilos_escritorio.css" rel="stylesheet">

  
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Bienvenido, <span
          class="text-primary"><?= htmlspecialchars($_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido']); ?></span>
      </h2>
      <a href="logout.php" class="btn btn-outline-secondary">Cerrar sesión</a>
    </div>

    <div class="mb-3">
      <select id="sel_usuarios" class="form-select"></select>
    </div>

    <div id="tbl_usuarios_wrapper" class="card shadow-sm bg-white rounded mb-3">
      <table id="tblUsuarios" class="table table-bordered table-hover table-sm w-100">
        <thead>
          <tr>
            <th>OPCIONES</th>
            <th>ID</th>
            <th>CEDULA</th>
            <th>NOMBRE</th>
            <th>APELLIDO</th>
            <th>CLAVE</th>
            <th>USUARIO</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    
    <br>
    <div class="mb-3">
      <select id="sel_usuarios1" class="form-select"></select>
    </div>
    <div class="row mb-3">
      <div class="col"><input type="text" id="txt_cedula" class="form-control" placeholder="Cédula"></div>
      <div class="col"><input type="text" id="txt_nombre" class="form-control" placeholder="Nombre"></div>
      <div class="col"><input type="text" id="txt_apellido" class="form-control" placeholder="Apellido"></div>
      <div class="col"><input type="text" id="txt_usuario" class="form-control" placeholder="Usuario"></div>
    </div>
  </div>
</body>
</html>