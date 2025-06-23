<?php
session_start();
if (isset($_SESSION['usu_id'])) {
    header('Location: escritorio.php');
    exit();
}
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <title>Registro - AutoMercado Total</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS Local -->
  <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Tus Estilos Personalizados -->
  <link href="../PUBLIC/css/styles.css" rel="stylesheet">

  <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>

</head>

<body class="d-flex flex-column min-vh-100 login-bg">

  <div id="page-loader">
    <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
  </div>

  <!-- Encabezado -->
  <header id="navbar-placeholder"></header>

  <div class="text-center mb-4 mt-5 pt-3">
    <img src="../PUBLIC/Img/Logoautomercado-total.png" alt="Logo AutoMercado" width="100">
  </div>

  <!-- Título -->
  <div class="container mt-4"> <!-- Reducido margen superior del título -->
    <h1 class="text-center mb-4">Registro de Usuario</h1>
  </div>

  <!-- Formulario de Registro -->
  <main class="flex-grow-1 d-flex align-items-center justify-content-center py-4 content-hidden">
    <!-- Añadido content-hidden -->
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8 col-xl-7">
          <div class="card shadow-lg">
            <div class="card-body p-4 p-md-5">
              <h2 class="card-title text-center mb-4">Crea tu cuenta</h2>
              <form id="registroForm" class="row g-3 needs-validation" novalidate>
                <!-- ID añadido, action y method eliminados -->
                <div class="col-md-12">
                  <label for="regUsuario" class="form-label">Nombre de Usuario</label>
                  <input type="text" class="form-control" id="regUsuario" name="usu_usuario"
                    placeholder="Ej: juanperez88" required>
                  <div class="invalid-feedback">Por favor, ingresa un nombre de usuario.</div>
                </div>
                <div class="col-md-6">
                  <label for="regNombre" class="form-label">Nombre(s)</label>
                  <input type="text" class="form-control" id="regNombre" name="usu_nombre" placeholder="Juan Carlos"
                    required>
                  <div class="invalid-feedback">Por favor, ingresa tu nombre.</div>
                </div>
                <div class="col-md-6">
                  <label for="regApellido" class="form-label">Apellido(s)</label>
                  <input type="text" class="form-control" id="regApellido" name="usu_apellido" placeholder="Pérez Gómez"
                    required>
                  <div class="invalid-feedback">Por favor, ingresa tu apellido.</div>
                </div>
                <div class="col-12">
                  <label for="regEmail" class="form-label">Correo Electrónico</label>
                  <input type="email" class="form-control" id="regEmail" name="usu_email"
                    placeholder="nombre@ejemplo.com" required>
                  <div class="invalid-feedback">Por favor, ingresa un correo electrónico válido.</div>
                </div>

                <div class="col-md-6">
                  <label for="regPassword" class="form-label">Contraseña</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="regPassword" name="usu_password"
                      placeholder="Mínimo 8 caracteres" required minlength="8">
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="regPassword"><i
                        class="bi bi-eye-slash"></i></button>
                    <div class="invalid-feedback">Por favor, ingresa una contraseña (mínimo 8 caracteres).</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="regPasswordConfirm" class="form-label">Confirmar Contraseña</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="regPasswordConfirm" name="usu_password_confirm"
                      placeholder="Repite tu contraseña" required minlength="8">
                    <button class="btn btn-outline-secondary toggle-password" type="button"
                      data-target="regPasswordConfirm"><i class="bi bi-eye-slash"></i></button>
                    <div class="invalid-feedback" id="passwordConfirmError">Las contraseñas no coinciden o es muy corta.
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="regTelefono" class="form-label">Teléfono <span
                      class="text-muted">(Opcional)</span></label>
                  <input type="tel" class="form-control" id="regTelefono" name="usu_telefono" placeholder="0991234567">
                </div>
                <div class="col-md-6">
                  <label for="regFnacimiento" class="form-label">Fecha de Nacimiento <span
                      class="text-muted">(Opcional)</span></label>
                  <input type="date" class="form-control" id="regFnacimiento" name="usu_fnacimiento">
                </div>
                <div class="col-12">
                  <label for="regDireccion" class="form-label">Dirección <span
                      class="text-muted">(Opcional)</span></label>
                  <input type="text" class="form-control" id="regDireccion" name="usu_direccion"
                    placeholder="Calle Principal 123 y Secundaria">
                </div>
                <div class="col-12">
                  <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="terms" name="accept_terms" required>
                    <label class="form-check-label" for="terms">
                      Acepto los <a href="terminos.html" target="_blank">términos y condiciones</a>
                    </label>
                    <div class="invalid-feedback">Debes aceptar los términos y condiciones.</div>
                  </div>
                </div>
                <div class="col-12 d-grid mt-3">
                  <button type="submit" class="btn btn-primary btn-lg">Registrarse</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Pie de página -->
  <?php include __DIR__ . '/partials/footer.php'; ?>

  <!-- Scripts -->
  <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
  <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/registro.js"></script>
</body>

</html>