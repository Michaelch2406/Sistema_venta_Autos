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
                // El antiguo "Panel Admin" se integra o reemplaza por "Mi Tablero"
                // if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 3): // Rol Admin
                // ?>
                     <li class="nav-item">
                         <a class="nav-link <?php echo ($currentPageNavbar == 'admin_panel.php') ? 'active' : ''; ?>" href="admin_panel.php">
                             <i class="bi bi-shield-fill-check me-2"></i>Panel Admin
                         </a>
                     </li>
                <?php //endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['usu_id']) && isset($_SESSION['usu_nombre_completo'])): ?>
                    <?php
                        // Determinar el enlace para "Mi Tablero"
                        $dashboard_link = "escritorio.php"; // Default para roles no admin
                        $dashboard_page_check = 'escritorio.php';
                        if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 3) { // Rol Admin ID es 3
                            $dashboard_link = "admin_panel.php";
                            $dashboard_page_check = 'admin_panel.php';
                        }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPageNavbar == $dashboard_page_check) ? 'active' : ''; ?>" href="<?php echo $dashboard_link; ?>">
                            <i class="bi bi-speedometer2 me-2"></i>Mi Tablero
                        </a>
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