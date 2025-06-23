<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="../PUBLIC/estilos_inicio1.css">
  <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
  <script src="../VISTAS/JS/inicio1.js"></script>
</head>
<body>
  <div class="login-container">
    <h2>Sistema de Gestión de Usuarios</h2>
    <form id="loginForm">
      <div class="input-group">
        <label for="usu">Usuario:</label>
        <input type="text" id="usu" name="usu"  placeholder="Usuario" required>
      </div>
      <div class="input-group">
        <label for="cla">Clave:</label>
        <input type="text" id="cla" name="cla"  placeholder="Clave" required>
      </div>
      <button type="button" id="btn_logearse" onclick="logeo()">Aceptar</button>
    </form>
  </div>
</body>
</html>
