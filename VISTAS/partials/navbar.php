<?php
// Usamos @ para suprimir la advertencia si la sesión ya fue iniciada en la página padre.
@session_start(); 
$currentPageNavbar = basename($_SERVER['PHP_SELF']);
?>
<!-- Enlace al CSS específico del Navbar -->
<link rel="stylesheet" href="../VISTAS/partials/css/navbar.css">

<nav class="navbar navbar-expand-lg navbar-dark navbar-enhanced fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="inicio.php">
            <img src="../PUBLIC/Img/Auto_Mercado_Total_LOGO4_SIN_FONDO.png" alt="Logo AutoMercado" width="80" height="30" class="d-inline-block align-text-top me-3">
            AutoMercado Total
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Menú Principal Izquierdo -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPageNavbar == 'inicio.php') ? 'active' : ''; ?>" href="inicio.php"><i class="bi bi-house-fill me-2"></i>Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPageNavbar == 'autos_nuevos.php') ? 'active' : ''; ?>" href="#"><i class="bi bi-car-front-fill me-2"></i>Autos Nuevos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPageNavbar == 'autos_usados.php') ? 'active' : ''; ?>" href="autos_usados.php"><i class="bi bi-car-front me-2"></i>Vehículos Usados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPageNavbar == 'contacto.php') ? 'active' : ''; ?>" href="contacto.php"><i class="bi bi-envelope-fill me-2"></i>Contacto</a>
                </li>
                
                <?php
                // --- LÓGICA PRINCIPAL PARA PANELES DE USUARIO ---
                // Solo se muestra si el usuario ha iniciado sesión.
                if (isset($_SESSION['rol_id'])):
                    $panel_text = '';
                    $panel_url = '';
                    $panel_page_name = '';

                    // Caso para Administrador (Rol 3)
                    if ($_SESSION['rol_id'] == 3) {
                        $panel_text = 'Panel Admin';
                        $panel_url = 'admin_panel.php';
                        $panel_page_name = 'admin_panel.php';
                    } 
                    // Caso para Cliente o Asesor (Roles 1 y 2)
                    elseif ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 2) {
                        $panel_text = 'Mi Panel';
                        $panel_url = 'escritorio.php';
                        $panel_page_name = 'escritorio.php';
                    }

                    // Si se encontró un rol válido, muestra el enlace al panel
                    if (!empty($panel_url)):
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPageNavbar == $panel_page_name) ? 'active' : ''; ?>" href="<?php echo $panel_url; ?>">
                            <i class="bi bi-person-workspace me-2"></i><?php echo $panel_text; ?>
                        </a>
                    </li>
                <?php 
                    endif;
                endif; 
                ?>
            </ul>
            
            <!-- Menú de Usuario Derecho -->
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['usu_id']) && isset($_SESSION['usu_nombre_completo'])): ?>
                    <!-- Si el usuario inició sesión, muestra un menú desplegable -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i><?php echo htmlspecialchars($_SESSION['usu_nombre_completo']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                            <li><a class="dropdown-item" href="configuracion_cuenta.php"><i class="bi bi-person-fill-gear me-2"></i>Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="mis_vehiculos.php"><i class="bi bi-card-list me-2"></i>Mis Publicaciones</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Si no ha iniciado sesión, muestra el botón para hacerlo -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPageNavbar == 'login.php') ? 'active' : ''; ?>" href="login.php" title="Iniciar Sesión">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Enlace al JS específico del Navbar -->
<script src="../VISTAS/partials/js/navbar.js"></script>