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
    <link href="./../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tus Estilos Personalizados -->
    <link href="./../PUBLIC/css/styles.css" rel="stylesheet">
    <!-- Estilos Específicos para el Registro -->
    <link href="./CSS/registro.css" rel="stylesheet">
  <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 registro-body">
  <!-- Loader mejorado -->
  <div id="page-loader" class="enhanced-loader">
    <div class="loader-content">
      <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
      <p class="loader-text">Cargando...</p>
    </div>
  </div>

  <!-- Partículas de fondo -->
  <div class="particles-bg">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
  </div>

  <header id="navbar-placeholder"></header>
  
  <!-- Logo con animación -->
  <div class="logo-container text-center mb-4 mt-5 pt-3">
    <div class="logo-wrapper animate-fade-in">
      <img src="../PUBLIC/Img/Auto_Mercado_Total_LOGO_BLACK_TEXT.png" alt="Logo AutoMercado" class="logo-img">
      <div class="logo-glow"></div>
    </div>
  </div>

  <!-- Título principal -->
  <div class="container mt-4">
    <h1 class="main-title text-center mb-4 animate-slide-up">
      <span class="title-icon"><i class="bi bi-person-plus-fill"></i></span>
      Registro de Usuario
      <div class="title-underline"></div>
    </h1>
  </div>

  <main class="flex-grow-1 d-flex align-items-center justify-content-center py-4 content-hidden">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8 col-xl-7">
          <!-- Card principal mejorada -->
          <div class="registration-card shadow-lg animate-scale-in">
            <div class="card-header-custom">
              <h2 class="card-title-custom text-center mb-0">
                <i class="bi bi-shield-check"></i>
                Crea tu cuenta
              </h2>
              <p class="card-subtitle text-center">Únete a nuestra comunidad</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
              <form id="registroForm" class="enhanced-form row g-3 needs-validation" novalidate>
                <!-- Información Personal -->
                <div class="col-12">
                  <div class="section-title">
                    <i class="bi bi-person-badge"></i>
                    <span>Información Personal</span>
                  </div>
                </div>
                
                <div class="col-md-12">
                  <div class="floating-label-group">
                    <input type="text" class="form-control floating-input" id="regUsuario" name="usu_usuario" placeholder=" " required>
                    <label for="regUsuario" class="floating-label">
                      <i class="bi bi-at"></i>
                      Nombre de Usuario
                    </label>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback">Por favor, ingresa un nombre de usuario.</div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="floating-label-group">
                    <input type="text" class="form-control floating-input" id="regNombre" name="usu_nombre" placeholder=" " required>
                    <label for="regNombre" class="floating-label">
                      <i class="bi bi-person"></i>
                      Nombre(s)
                    </label>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback">Por favor, ingresa tu nombre.</div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="floating-label-group">
                    <input type="text" class="form-control floating-input" id="regApellido" name="usu_apellido" placeholder=" " required>
                    <label for="regApellido" class="floating-label">
                      <i class="bi bi-person"></i>
                      Apellido(s)
                    </label>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback">Por favor, ingresa tu apellido.</div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="floating-label-group">
                    <input type="email" class="form-control floating-input" id="regEmail" name="usu_email" placeholder=" " required>
                    <label for="regEmail" class="floating-label">
                      <i class="bi bi-envelope"></i>
                      Correo Electrónico
                    </label>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback">Por favor, ingresa un correo electrónico válido.</div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="floating-label-group">
                    <input type="text" class="form-control floating-input" id="regCedula" name="usu_cedula" placeholder=" " required pattern="\d{10,13}">
                    <label for="regCedula" class="floating-label">
                      <i class="bi bi-card-text"></i>
                      Cédula
                    </label>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback">Ingresa una cédula o RUC válido (10 o 13 dígitos).</div>
                  </div>
                </div>

                <!-- Seguridad -->
                <div class="col-12 mt-4">
                  <div class="section-title">
                    <i class="bi bi-shield-lock"></i>
                    <span>Seguridad de la Cuenta</span>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="floating-label-group">
                    <input type="password" class="form-control floating-input" id="regPassword" name="usu_password" placeholder=" " required minlength="8">
                    <label for="regPassword" class="floating-label">
                      <i class="bi bi-lock"></i>
                      Contraseña
                    </label>
                    <button class="password-toggle toggle-password" type="button" data-target="regPassword">
                      <i class="bi bi-eye-slash"></i>
                    </button>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback">Ingresa una contraseña (mínimo 8 caracteres).</div>
                    <!-- Indicador de fortaleza -->
                    <div class="password-strength">
                      <div class="strength-bar">
                        <div class="strength-fill"></div>
                      </div>
                      <div class="strength-text">Fortaleza de contraseña</div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="floating-label-group">
                    <input type="password" class="form-control floating-input" id="regPasswordConfirm" name="usu_password_confirm" placeholder=" " required minlength="8">
                    <label for="regPasswordConfirm" class="floating-label">
                      <i class="bi bi-lock-fill"></i>
                      Confirmar Contraseña
                    </label>
                    <button class="password-toggle toggle-password" type="button" data-target="regPasswordConfirm">
                      <i class="bi bi-eye-slash"></i>
                    </button>
                    <div class="validation-icon"></div>
                    <div class="invalid-feedback" id="passwordConfirmError">Las contraseñas no coinciden.</div>
                  </div>
                </div>

                <!-- Información Adicional -->
                <div class="col-12 mt-4">
                  <div class="section-title">
                    <i class="bi bi-info-circle"></i>
                    <span>Información Adicional</span>
                    <small>(Opcional)</small>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="floating-label-group">
                    <input type="tel" class="form-control floating-input" id="regTelefono" name="usu_telefono" placeholder=" ">
                    <label for="regTelefono" class="floating-label">
                      <i class="bi bi-telephone"></i>
                      Teléfono
                    </label>
                    <div class="validation-icon"></div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="floating-label-group">
                    <input type="date" class="form-control floating-input" id="regFnacimiento" name="usu_fnacimiento" placeholder=" ">
                    <label for="regFnacimiento" class="floating-label">
                      <i class="bi bi-calendar"></i>
                      Fecha de Nacimiento
                    </label>
                    <div class="validation-icon"></div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="floating-label-group">
                    <input type="text" class="form-control floating-input" id="regDireccion" name="usu_direccion" placeholder=" ">
                    <label for="regDireccion" class="floating-label">
                      <i class="bi bi-geo-alt"></i>
                      Dirección
                    </label>
                    <div class="validation-icon"></div>
                  </div>
                </div>

                <!-- Términos y condiciones mejorados -->
                <div class="col-12">
                  <div class="custom-checkbox-wrapper mt-3">
                    <input class="custom-checkbox" type="checkbox" id="terms" name="accept_terms" required>
                    <label class="custom-checkbox-label" for="terms">
                      <span class="checkmark">
                        <i class="bi bi-check"></i>
                      </span>
                      <span class="checkbox-text">
                        Acepto los <a href="terminos.html" target="_blank" class="terms-link">términos y condiciones</a>
                      </span>
                    </label>
                    <div class="invalid-feedback">Debes aceptar los términos y condiciones.</div>
                  </div>
                </div>

                <!-- Botón de envío mejorado -->
                <div class="col-12 d-grid mt-4">
                  <button type="submit" class="btn-submit">
                    <span class="btn-text">
                      <i class="bi bi-person-plus"></i>
                      Crear mi cuenta
                    </span>
                    <span class="btn-loading">
                      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      Registrando...
                    </span>
                    <div class="btn-success-check">
                      <i class="bi bi-check-circle-fill"></i>
                    </div>
                  </button>
                </div>

                <!-- Link de login -->
                <div class="col-12 text-center mt-3">
                  <p class="login-link">
                    ¿Ya tienes una cuenta? 
                    <a href="./login.php" class="link-primary">Inicia sesión aquí</a>
                  </p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer id="footer-placeholder"></footer>
  
  <script src="./../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="./../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./JS/global.js"></script>
  <script src="./JS/registro.js"></script>
</body>
</html>

