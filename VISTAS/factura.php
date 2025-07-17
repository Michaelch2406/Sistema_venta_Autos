<?php
// VISTAS/factura.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1); 
ini_set('error_log', './../php_error.log'); 

if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usu_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener el ID de la venta
$vnt_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

if (!$vnt_id) {
    die("ID de venta no válido.");
}

require_once __DIR__ . './../CONFIG/Conexion.php';
require_once __DIR__ . './../MODELOS/facturas_m.php';

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

$facturaModelo = new FacturaModelo($db_conn_mysqli);

// Verificar acceso
if (!$facturaModelo->verificar_acceso_factura($vnt_id, $_SESSION['usu_id'], $_SESSION['rol_id'])) {
    die("No tienes permisos para ver esta factura.");
}

// Obtener datos de la factura
$factura = $facturaModelo->obtener_datos_factura($vnt_id);

if (!$factura) {
    die("Factura no encontrada.");
}

// Función para formatear números
function formatear_numero($numero) {
    return number_format($numero, 2, '.', ',');
}

// Función para formatear fecha
function formatear_fecha($fecha) {
    return date('d/m/Y H:i', strtotime($fecha));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura <?php echo htmlspecialchars($factura['dv_codigo_factura']); ?> - AutoMercado Total</title>
    <link href="./../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .container { max-width: none !important; }
        }
        
        .factura-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        
        .factura-body {
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        
        .info-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .vehiculo-section {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2196f3;
        }
        
        .total-section {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            border: 2px solid #ffc107;
        }
        
        .logo-factura {
            max-height: 60px;
            width: auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container my-4">
        <!-- Botones de acción -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="mis_ventas.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver a Mis Ventas
            </a>
            <div>
                <button onclick="window.print()" class="btn btn-primary me-2">
                    <i class="bi bi-printer me-2"></i>Imprimir
                </button>
                <button onclick="descargarPDF()" class="btn btn-success">
                    <i class="bi bi-download me-2"></i>Descargar PDF
                </button>
            </div>
        </div>

        <!-- Factura -->
        <div class="card shadow">
            <!-- Header de la factura -->
            <div class="factura-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <img src="../PUBLIC/Img/Auto_Mercado_Total_LOGO_BLACK_TEXT.png" 
                                 alt="AutoMercado Total" class="logo-factura me-3"
                                 style="filter: brightness(0) invert(1);">
                            <div>
                                <h2 class="mb-1">AutoMercado Total</h2>
                                <p class="mb-0">Factura de Venta de Vehículo</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <h4><?php echo htmlspecialchars($factura['dv_codigo_factura']); ?></h4>
                        <p class="mb-0">Fecha: <?php echo formatear_fecha($factura['vnt_fecha_venta']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Cuerpo de la factura -->
            <div class="factura-body">
                <!-- Información de las partes -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-section">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-person-fill me-2"></i>Datos del Comprador
                            </h5>
                            <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($factura['comprador_nombre'] . ' ' . $factura['comprador_apellido']); ?></p>
                            <p class="mb-1"><strong>Cédula:</strong> <?php echo htmlspecialchars($factura['comprador_cedula']); ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($factura['comprador_email']); ?></p>
                            <?php if ($factura['comprador_telefono']): ?>
                                <p class="mb-1"><strong>Teléfono:</strong> <?php echo htmlspecialchars($factura['comprador_telefono']); ?></p>
                            <?php endif; ?>
                            <?php if ($factura['comprador_direccion']): ?>
                                <p class="mb-0"><strong>Dirección:</strong> <?php echo htmlspecialchars($factura['comprador_direccion']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <h5 class="text-success mb-3">
                                <i class="bi bi-shop me-2"></i>Datos del Vendedor
                            </h5>
                            <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($factura['vendedor_nombre'] . ' ' . $factura['vendedor_apellido']); ?></p>
                            <p class="mb-1"><strong>Cédula:</strong> <?php echo htmlspecialchars($factura['vendedor_cedula']); ?></p>
                            <p class="mb-0"><strong>Email:</strong> <?php echo htmlspecialchars($factura['vendedor_email']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Información del vehículo -->
                <div class="vehiculo-section mb-4">
                    <h5 class="text-info mb-3">
                        <i class="bi bi-car-front-fill me-2"></i>Datos del Vehículo
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Marca y Modelo:</strong> <?php echo htmlspecialchars($factura['mar_nombre'] . ' ' . $factura['mod_nombre']); ?></p>
                            <p class="mb-1"><strong>Año:</strong> <?php echo htmlspecialchars($factura['veh_anio']); ?></p>
                            <p class="mb-1"><strong>Tipo:</strong> <?php echo htmlspecialchars($factura['tiv_nombre']); ?></p>
                            <p class="mb-1"><strong>Condición:</strong> <?php echo htmlspecialchars(ucfirst($factura['veh_condicion'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Kilometraje:</strong> <?php echo formatear_numero($factura['veh_kilometraje']); ?> km</p>
                            <p class="mb-1"><strong>Color:</strong> <?php echo htmlspecialchars($factura['veh_color_exterior']); ?></p>
                            <?php if ($factura['veh_placa']): ?>
                                <p class="mb-1"><strong>Placa:</strong> <?php echo htmlspecialchars($factura['veh_placa']); ?></p>
                            <?php endif; ?>
                            <?php if ($factura['veh_vin']): ?>
                                <p class="mb-1"><strong>VIN:</strong> <?php echo htmlspecialchars($factura['veh_vin']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($factura['veh_descripcion']): ?>
                        <div class="mt-3">
                            <p class="mb-1"><strong>Descripción:</strong></p>
                            <p class="mb-0"><?php echo htmlspecialchars($factura['veh_descripcion']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Resumen financiero -->
                <div class="total-section">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2">Resumen de la Transacción</h5>
                            <p class="mb-1">Precio acordado de venta del vehículo</p>
                            <?php if ($factura['vnt_notas']): ?>
                                <p class="mb-0 text-muted small"><strong>Notas:</strong> <?php echo htmlspecialchars($factura['vnt_notas']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <h3 class="text-warning mb-0">$<?php echo formatear_numero($factura['vnt_precio_final']); ?> USD</h3>
                        </div>
                    </div>
                </div>

                <!-- Términos y condiciones -->
                <div class="mt-4 p-3 bg-light border-start border-primary border-4">
                    <h6 class="text-primary">Términos y Condiciones:</h6>
                    <ul class="small mb-0">
                        <li>Esta factura constituye un comprobante de la transacción realizada.</li>
                        <li>El vehículo se entrega en las condiciones descritas anteriormente.</li>
                        <li>AutoMercado Total actúa como intermediario en esta transacción.</li>
                        <li>Cualquier reclamo debe realizarse dentro de los primeros 30 días.</li>
                    </ul>
                </div>

                <!-- Pie de factura -->
                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted mb-1">AutoMercado Total - Tu destino confiable para compra y venta de vehículos</p>
                    <p class="text-muted small mb-0">Generado el <?php echo formatear_fecha($factura['dv_fecha_creacion']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="./../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="./../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function descargarPDF() {
            // Esta función puede implementarse con jsPDF o enviando a un endpoint del servidor
            alert('Función de descarga PDF en desarrollo. Por ahora use "Imprimir" y seleccione "Guardar como PDF".');
        }
    </script>
</body>
</html>