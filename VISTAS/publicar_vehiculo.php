<?php
session_start();
$roles_permitidos_publicar = [1, 2, 3]; // Cliente/Vendedor, Asesor, Administrador
if (!isset($_SESSION['usu_id']) || !in_array($_SESSION['rol_id'], $roles_permitidos_publicar)) {
    echo "<!DOCTYPE html><html><head><title>Acceso Denegado</title><link href='../Bootstrap/css/bootstrap.min.css' rel='stylesheet'></head><body class='container mt-5'><div class='alert alert-danger'><h1>Acceso Denegado</h1><p>No tienes permisos para acceder a esta página.</p><a href='escritorio.php' class='btn btn-primary'>Volver al Escritorio</a></div></body></html>";
    exit();
}
$current_year = date('Y');
$years_options = '';
for ($year = $current_year + 1; $year >= 1950; $year--) {
    $years_options .= "<option value=\"$year\">$year</option>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Vehículo - AutoMercado Total</title>
    <link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../PUBLIC/css/styles.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/trefoil.js"></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div id="page-loader">
        <l-trefoil size="50" stroke="5" stroke-length="0.15" bg-opacity="0.1" speed="1.4" color="#0d6efd"></l-trefoil>
    </div>
    <header id="navbar-placeholder"></header>
    <main class="flex-grow-1 content-hidden">
        <div class="container py-4">
            <div class="pt-4 mb-4 text-center">
                <h1 class="display-5 fw-bold publishing-title">Publicar Nuevo Vehículo</h1>
                <p class="lead text-muted col-lg-8 mx-auto">Ingresa los detalles de tu vehículo. Los campos con <span class="text-danger fw-bold">*</span> son obligatorios.</p>
            </div>
            <form id="publicarVehiculoForm" class="needs-validation" novalidate enctype="multipart/form-data">
                <input type="hidden" name="accion" value="publicarVehiculo">
                <div class="card-form-section">
                    <div class="card-header"><i class="bi bi-info-circle-fill me-2"></i>Información Principal</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="mar_id" class="form-label">Marca <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg" id="mar_id" name="mar_id" required>
                                    <option value="" selected disabled>Selecciona...</option></select>
                                <div class="invalid-feedback">Selecciona la marca.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="mod_id" class="form-label">Modelo <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg" id="mod_id" name="mod_id" required disabled>
                                    <option value="" selected disabled>Selecciona marca...</option></select>
                                <div class="invalid-feedback">Selecciona el modelo.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="tiv_id" class="form-label">Tipo de Vehículo <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg" id="tiv_id" name="tiv_id" required>
                                    <option value="" selected disabled>Selecciona tipo...</option></select>
                                <div class="invalid-feedback">Selecciona el tipo.</div>
                            </div>
                            <div class="col-md-6"><label for="veh_subtipo_vehiculo" class="form-label">Subtipo <span class="text-muted">(Ej: Doble Cabina)</span></label><input type="text" class="form-control" id="veh_subtipo_vehiculo" name="veh_subtipo_vehiculo" placeholder="Opcional"></div>
                            <div class="col-md-6"><label for="veh_anio" class="form-label">Año Fabricación <span class="text-danger">*</span></label><select class="form-select form-select-lg" id="veh_anio" name="veh_anio" required><option value="" selected disabled>Selecciona año...</option><?php echo $years_options; ?></select><div class="invalid-feedback">Selecciona año.</div></div>
                            <div class="col-md-6"><label for="veh_condicion" class="form-label">Condición <span class="text-danger">*</span></label><select class="form-select form-select-lg" id="veh_condicion" name="veh_condicion" required><option value="" selected disabled>Selecciona...</option><option value="nuevo">Nuevo (0 km)</option><option value="usado">Usado</option></select><div class="invalid-feedback">Indica condición.</div></div>
                            <div class="col-md-6" id="kilometraje_div_container"><label for="veh_kilometraje" class="form-label" id="label_kilometraje">Recorrido (km)</label><input type="number" class="form-control form-control-lg" id="veh_kilometraje" name="veh_kilometraje" placeholder="Ej: 25000" min="0"><div class="invalid-feedback" id="kilometraje_feedback">Ingresa recorrido.</div></div>
                            <div class="col-12"><div class="row g-3" id="campos_placa_group" style="display: none;"><div class="col-md-6"><label for="veh_placa_provincia_origen" class="form-label">Provincia de Placa</label><select class="form-select" id="veh_placa_provincia_origen" name="veh_placa_provincia_origen"><option value="" selected disabled>Selecciona...</option></select><div class="invalid-feedback">Selecciona provincia.</div></div><div class="col-md-6"><label for="veh_ultimo_digito_placa" class="form-label">Último Dígito Placa</label><select class="form-select" id="veh_ultimo_digito_placa" name="veh_ultimo_digito_placa"><option value="" selected disabled>Selecciona...</option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="Sin Placa">Sin Placa</option></select><div class="invalid-feedback">Selecciona dígito.</div></div></div></div>
                            <div class="col-md-4"><label for="veh_precio" class="form-label">Precio (USD) <span class="text-danger">*</span></label><input type="number" class="form-control form-control-lg" id="veh_precio" name="veh_precio" placeholder="Ej: 15000.00" required step="0.01" min="500" max="5000000"><div class="invalid-feedback">Precio entre $500-$5M.</div></div>
                            <div class="col-md-4"><label for="veh_vin" class="form-label">VIN <span class="text-muted">(Opcional)</span></label><input type="text" class="form-control" id="veh_vin" name="veh_vin" placeholder="Chasis (17 caract.)" maxlength="17" pattern="[A-HJ-NPR-Z0-9]{17}"><div class="invalid-feedback">VIN 17 carac. (sin I,O,Q).</div></div>
                            <div class="col-md-4"><label for="veh_fecha_publicacion" class="form-label">Fecha Publicación <span class="text-danger">*</span></label><input type="date" class="form-control" id="veh_fecha_publicacion" name="veh_fecha_publicacion" required value="<?php echo date('Y-m-d'); ?>"><div class="invalid-feedback">Ingresa fecha.</div></div>
                        </div>
                    </div>
                </div>

                <div class="card-form-section">
                    <div class="card-header"><i class="bi bi-geo-alt-fill me-2"></i>Ubicación y Apariencia</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6"><label for="veh_ubicacion_provincia" class="form-label">Provincia Dónde se Encuentra <span class="text-danger">*</span></label><select class="form-select" id="veh_ubicacion_provincia" name="veh_ubicacion_provincia" required><option value="" selected disabled>Selecciona...</option></select><div class="invalid-feedback">Selecciona provincia.</div></div>
                            <div class="col-md-6"><label for="veh_ubicacion_ciudad" class="form-label">Ciudad Dónde se Encuentra <span class="text-danger">*</span></label><select class="form-select" id="veh_ubicacion_ciudad" name="veh_ubicacion_ciudad" required disabled><option value="" selected disabled>Selecciona provincia...</option></select><div class="invalid-feedback">Selecciona ciudad.</div></div>
                            <div class="col-md-6"><label for="veh_color_exterior" class="form-label">Color Exterior <span class="text-danger">*</span></label><input type="text" class="form-control" id="veh_color_exterior" name="veh_color_exterior" placeholder="Ej: Rojo brillante" required><div class="invalid-feedback">Ingresa color exterior.</div></div>
                            <div class="col-md-6"><label for="veh_color_interior" class="form-label">Color Interior</label><input type="text" class="form-control" id="veh_color_interior" name="veh_color_interior" placeholder="Ej: Cuero negro"></div>
                        </div>
                    </div>
                </div>

                <div class="card-form-section">
                     <div class="card-header"><i class="bi bi-tools me-2"></i>Especificaciones Técnicas</div>
                     <div class="card-body p-4">
                         <div class="row g-3">
                            <div class="col-md-12"><label for="veh_detalles_motor" class="form-label">Detalles del Motor <span class="text-danger">*</span></label><textarea class="form-control" id="veh_detalles_motor" name="veh_detalles_motor" rows="2" placeholder="Ej: 2.0L Turbo, 4 cilindros, 250 HP" required></textarea><div class="invalid-feedback">Ingresa detalles del motor.</div></div>
                            <div class="col-md-4"><label for="veh_tipo_transmision" class="form-label">Transmisión</label><select class="form-select" id="veh_tipo_transmision" name="veh_tipo_transmision"><option value="" selected>Opcional...</option><option value="Automatica">Automática</option><option value="Manual">Manual</option><option value="CVT">CVT</option><option value="Semi-automatica">Semi-automática</option><option value="Otra">Otra</option></select></div>
                            <div class="col-md-4"><label for="veh_traccion" class="form-label">Tracción</label><select class="form-select" id="veh_traccion" name="veh_traccion"><option value="" selected>Opcional...</option><option value="Delantera">Delantera</option><option value="Trasera">Trasera</option><option value="4x4">4x4 / 4WD</option><option value="AWD">AWD</option><option value="Otro">Otro</option></select></div>
                            <div class="col-md-4"><label for="veh_tipo_combustible" class="form-label">Combustible</label><select class="form-select" id="veh_tipo_combustible" name="veh_tipo_combustible"><option value="" selected>Opcional...</option><option value="Gasolina">Gasolina</option><option value="Diesel">Diesel</option><option value="Hibrido">Híbrido</option><option value="Electrico">Eléctrico</option><option value="Otro">Otro</option></select></div>
                            <div class="col-md-4"><label for="veh_tipo_direccion" class="form-label">Dirección</label><select class="form-select" id="veh_tipo_direccion" name="veh_tipo_direccion"><option value="" selected>Opcional...</option><option value="Mecanica">Mecánica</option><option value="Hidraulica">Hidráulica</option><option value="Electroasistida">Electroasistida</option><option value="Electrica">Eléctrica</option><option value="Otra">Otra</option></select></div>
                            <div class="col-md-4"><label for="veh_tipo_vidrios" class="form-label">Vidrios</label><select class="form-select" id="veh_tipo_vidrios" name="veh_tipo_vidrios"><option value="" selected>Opcional...</option><option value="Manuales">Manuales</option><option value="Electricos Delanteros">Eléctricos Delanteros</option><option value="Electricos Completos">Eléctricos Completos</option><option value="Otro">Otro</option></select></div>
                            <div class="col-md-4"><label for="veh_sistema_climatizacion" class="form-label">Climatización</label><select class="form-select" id="veh_sistema_climatizacion" name="veh_sistema_climatizacion"><option value="" selected>Opcional...</option><option value="Ninguno">Ninguno</option><option value="Aire Acondicionado">A/C</option><option value="Climatizador Manual">Climatizador Manual</option><option value="Climatizador Automatico">Climatizador Automático</option><option value="Climatizador Bi-Zona">Climatizador Bi-Zona</option><option value="Otro">Otro</option></select></div>
                         </div>
                    </div>
                </div>
                
                <div class="card-form-section">
                     <div class="card-header"><i class="bi bi-journal-text me-2"></i>Descripción y Extras</div>
                     <div class="card-body p-4">
                        <div class="row g-3">
                             <div class="col-12">
                                <label class="form-label">Detalles Extra <span class="text-muted">(Selecciona los que apliquen)</span></label>
                                <div class="optional-fields-group p-3 border rounded">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="Acepto Vehiculo Como Parte de Pago" id="extraAceptoVehiculo" name="veh_detalles_extra[]"><label class="form-check-label" for="extraAceptoVehiculo">Acepto Vehículo (Parte Pago)</label></div></div>
                                        <div class="col-sm-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="Unico Dueño" id="extraUnicoDueno" name="veh_detalles_extra[]"><label class="form-check-label" for="extraUnicoDueno">Único Dueño</label></div></div>
                                        <div class="col-sm-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="Garantia de Casa Vigente" id="extraGarantiaCasa" name="veh_detalles_extra[]"><label class="form-check-label" for="extraGarantiaCasa">Garantía de Casa</label></div></div>
                                        <div class="col-sm-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="Documentos al Dia" id="extraDocumentosDia" name="veh_detalles_extra[]"><label class="form-check-label" for="extraDocumentosDia">Documentos al Día</label></div></div>
                                        <div class="col-sm-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="Matricula Pagada" id="extraMatriculaPagada" name="veh_detalles_extra[]"><label class="form-check-label" for="extraMatriculaPagada">Matrícula Pagada</label></div></div>
                                        <div class="col-sm-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="Negociable" id="extraNegociable" name="veh_detalles_extra[]"><label class="form-check-label" for="extraNegociable">Precio Negociable</label></div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="veh_descripcion" class="form-label">Descripción Adicional <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="veh_descripcion" name="veh_descripcion" rows="5" placeholder="Cuenta más sobre tu vehículo: historial de mantenimiento, por qué lo vendes, etc." required></textarea>
                                <div class="invalid-feedback">Ingresa una descripción.</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-form-section">
                    <div class="card-header"><i class="bi bi-images me-2"></i>Multimedia</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="veh_imagenes" class="form-label">Imágenes del Vehículo (Principal + Adicionales) <span class="text-danger">*</span></label>
                                <input class="form-control form-control-lg" type="file" id="veh_imagenes" name="veh_imagenes[]" multiple accept="image/jpeg, image/png, image/webp" required>
                                <small class="form-text text-muted">Selecciona al menos una imagen. Máximo 10. La primera será la principal.</small>
                                <div class="invalid-feedback">Debes subir al menos una imagen.</div>
                                <div id="imagePreviewContainer" class="mt-3 d-flex flex-wrap gap-2 border p-2 rounded bg-light" style="min-height: 120px;">
                                    <small class="text-muted align-self-center mx-auto">Previsualización aparecerá aquí...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 mb-4">
                    <button type="reset" class="btn btn-outline-secondary btn-lg">Limpiar Formulario</button>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-send-fill me-2"></i>Publicar Anuncio
                    </button>
                </div>
                 <div id="formSubmissionMessage" class="mt-3"></div>
            </form>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
    <script src="../PUBLIC/jquery-3.7.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../VISTAS/JS/global.js"></script>
    <script src="../VISTAS/JS/publicar_vehiculo.js"></script>
</body>
</html>