<?php
// VISTAS/mis_ventas.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1); 
ini_set('error_log', __DIR__ . './../php_error.log'); 

if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

require_once __DIR__ . './../CONFIG/Conexion.php';
require_once __DIR__ . './../MODELOS/ventas_m.php';

$db_conn_mysqli = null;
try {
    $conexionObj = new Conexion();
    $db_conn_mysqli = $conexionObj->conecta();
} catch (Exception $e) { 
    die("Error crítico: No se pudo conectar a la base de datos."); 
}

if ($db_conn_mysqli === null) { 
    die("Error crítico: Conexión no disponible."); 
}

$ventaModelo = new VentaModelo($db_conn_mysqli);

if (!isset($_SESSION['usu_id'])) {
    header("Location: login.php");
    exit;
}

$ventas = $ventaModelo->obtener_ventas_por_usuario($_SESSION['usu_id']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestión de mis ventas - Panel de control de ventas realizadas">
    <meta name="keywords" content="ventas, gestión, vehículos, facturas">
    <title>Mis Ventas - Panel de Control</title>
    
    <!-- Bootstrap CSS -->
    <link href="./../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Estilos globales -->
    <link href="./../PUBLIC/css/styles.css" rel="stylesheet">
    
    <!-- Estilos específicos de mis ventas -->
    <link href="./CSS/mis_ventas.css" rel="stylesheet">
    
    <!-- Preload para mejor rendimiento -->
    <link rel="preload" href="./../PUBLIC/jquery-3.7.1.min.js" as="script">
    <link rel="preload" href="./JS/mis_ventas.js" as="script">
</head>

<body>
    <!-- Header con navegación -->
    <header id="navbar-placeholder"></header>

    <!-- Contenido principal -->
    <main role="main" class="container">
        <!-- Sección principal de ventas -->
        <section id="lista-ventas" class="fade-in">
            <h2>
                <span>
                    <i class="bi bi-receipt me-2"></i>
                    Mis Ventas
                </span>
            </h2>
            
            <!-- Panel de estadísticas (se genera dinámicamente con JS) -->
            
            <!-- Contenedor de búsqueda (se genera dinámicamente con JS) -->
            
            <!-- Tabla de ventas -->
            <div class="tabla-responsive-contenedor">
                <table class="ventas-table">
                    <thead>
                        <tr>
                            <th scope="col">ID Venta</th>
                            <th scope="col">Fecha Venta</th>
                            <th scope="col">Vehículo</th>
                            <th scope="col">Precio Final</th>
                            <th scope="col">Comprador</th>
                            <th scope="col">Vendedor</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-ventas-body">
                        <?php if (!empty($ventas)): ?>
                            <?php foreach ($ventas as $venta): ?>
                            <tr class="fade-in">
                                <td>
                                    <strong><?php echo htmlspecialchars($venta['vnt_id']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($venta['vnt_fecha_venta']))); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($venta['vehiculo_nombre']); ?>
                                </td>
                                <td>
                                    $<?php echo number_format(floatval($venta['vnt_precio_final']), 2, ',', '.'); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($venta['comprador_nombre']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($venta['vendedor_nombre']); ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="factura.php?id=<?php echo htmlspecialchars($venta['vnt_id']); ?>" 
                                           class="btn btn-primary btn-sm" 
                                           title="Ver Factura"
                                           aria-label="Ver factura de la venta <?php echo htmlspecialchars($venta['vnt_id']); ?>">
                                            <i class="bi bi-receipt" aria-hidden="true"></i>
                                        </a>
                                        
                                        <!-- Botón adicional para detalles (opcional) -->
                                        <button type="button" 
                                                class="btn btn-outline-info btn-sm" 
                                                title="Ver Detalles"
                                                aria-label="Ver detalles de la venta <?php echo htmlspecialchars($venta['vnt_id']); ?>"
                                                onclick="verDetallesVenta(<?php echo htmlspecialchars($venta['vnt_id']); ?>)">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <div class="py-4">
                                        <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
                                        <h5>No tienes ventas registradas</h5>
                                        <p class="mb-0">Cuando realices tu primera venta, aparecerá aquí.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Botones de acción adicionales -->
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div class="d-flex gap-2">
                    <button type="button" 
                            class="btn btn-outline-success btn-sm" 
                            onclick="VentasUtils.exportToCSV()" 
                            title="Exportar a CSV">
                        <i class="bi bi-download"></i> Exportar CSV
                    </button>
                    
                    <button type="button" 
                            class="btn btn-outline-primary btn-sm" 
                            onclick="window.print()" 
                            title="Imprimir lista">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                </div>
                
                <div class="text-muted small">
                    Total de ventas: <strong><?php echo count($ventas); ?></strong>
                </div>
            </div>
        </section>
        
        <!-- Modal para detalles de venta (opcional) -->
        <div class="modal fade" id="modalDetallesVenta" tabindex="-1" aria-labelledby="modalDetallesVentaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetallesVentaLabel">
                            <i class="bi bi-info-circle me-2"></i>
                            Detalles de la Venta
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="contenidoDetallesVenta">
                        <!-- Contenido cargado dinámicamente -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer id="footer-placeholder"></footer>

    <!-- Scripts -->
    <!-- jQuery (requerido para funcionalidad existente) -->
    <script src="./../PUBLIC/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="./../Bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts globales -->
    <script src="./JS/global.js"></script>
    
    <!-- Script específico de mis ventas -->
    <script src="./JS/mis_ventas.js"></script>
    
    <!-- Script inline para funciones específicas -->
    <script>
        
    </script>
</body>

</html>

