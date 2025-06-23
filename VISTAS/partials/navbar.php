<?php
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
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPageNavbar == 'inicio.php') ? 'active' : ''; ?>" href="inicio.php">
                        <i class="bi bi-house-fill me-2"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPageNavbar == 'autos_nuevos.php') ? 'active' : ''; ?>" href="#">
                        <i class="bi bi-car-front-fill me-2"></i>Autos Nuevos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPageNavbar == 'autos_usados.php') ? 'active' : ''; ?>" href="autos_usados.php"> 
                        <i class="bi bi-car-front me-2"></i>Vehículos Usados
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPageNavbar == 'contacto.php') ? 'active' : ''; ?>" href="contacto.php">
                        <i class="bi bi-envelope-fill me-2"></i>Contacto
                    </a>
                </li>
                <?php 
                if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 3): // Rol Admin
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPageNavbar == 'admin_panel.php') ? 'active' : ''; ?>" href="admin_panel.php">
                            <i class="bi bi-shield-fill-check me-2"></i>Panel Admin
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['usu_id']) && isset($_SESSION['usu_nombre_completo'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($currentPageNavbar == 'escritorio.php' || $currentPageNavbar == 'configuracion_cuenta.php') ? 'active' : ''; ?>" href="#" id="navbarDropdownUserMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill me-2"></i>
                            <?php 
                                $nombre_partes = explode(' ', htmlspecialchars($_SESSION['usu_nombre_completo']));
                                echo $nombre_partes[0]; 
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUserMenu">
                            <li><a class="dropdown-item <?php echo ($currentPageNavbar == 'escritorio.php') ? 'active' : ''; ?>" href="escritorio.php"><i class="bi bi-speedometer2 me-2"></i>Mi Tablero</a></li>
                            <li><a class="dropdown-item <?php echo ($currentPageNavbar == 'configuracion_cuenta.php') ? 'active' : ''; ?>" href="configuracion_cuenta.php"><i class="bi bi-gear-fill me-2"></i>Mi Perfil / Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPageNavbar == 'login.php') ? 'active' : ''; ?>" href="login.php" title="Iniciar Sesión">
                            <i class="bi bi-person-circle me-2"></i> Iniciar Sesión
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Enlace al JS específico del Navbar -->
<script src="../VISTAS/partials/js/navbar.js"></script>