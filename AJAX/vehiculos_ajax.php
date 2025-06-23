<?php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../php_error.log'); 

session_start();
require_once __DIR__ . "/../MODELOS/catalogos_m.php";
require_once __DIR__ . "/../MODELOS/vehiculos_m.php";
require_once __DIR__ . "/../MODELOS/imagenes_vehiculo_m.php";

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Petición no válida o acción no especificada.'];

$provincias_ciudades = [
    "Azuay" => ["Cuenca", "Gualaceo", "Paute", "Sígsig", "Chordeleg", "Santa Isabel", "Girón", "Nabón", "Camilo Ponce Enríquez"],
    "Bolívar" => ["Guaranda", "San Miguel", "Chimbo", "Caluma", "Echeandía", "Las Naves"],
    "Cañar" => ["Azogues", "La Troncal", "Biblián", "Cañar", "El Tambo", "Suscal"],
    "Carchi" => ["Tulcán", "San Gabriel", "El Ángel", "Mira", "Bolívar (Carchi)", "Montúfar"],
    "Chimborazo" => ["Riobamba", "Guano", "Alausí", "Chambo", "Colta", "Cumandá", "Pallatanga"],
    "Cotopaxi" => ["Latacunga", "La Maná", "Pujilí", "Salcedo", "Saquisilí", "Sigchos"],
    "El Oro" => ["Machala", "Pasaje", "Santa Rosa", "Huaquillas", "Arenillas", "Piñas", "El Guabo"],
    "Esmeraldas" => ["Esmeraldas", "Atacames", "Quinindé (Rosa Zárate)", "San Lorenzo", "Muisne"],
    "Galápagos" => ["Puerto Baquerizo Moreno", "Puerto Ayora", "Puerto Villamil"],
    "Guayas" => ["Guayaquil", "Durán", "Daule", "Samborondón", "Milagro", "General Villamil (Playas)", "El Triunfo", "Naranjal", "Balzar", "Yaguachi", "Velasco Ibarra", "Pedro Carbo", "Naranjito", "Lomas de Sargentillo"],
    "Imbabura" => ["Ibarra", "Otavalo", "Atuntaqui", "Cotacachi", "Pimampiro", "Urcuquí"],
    "Loja" => ["Loja", "Catamayo", "Macará", "Cariamanga", "Saraguro", "Gonzanamá"],
    "Los Ríos" => ["Babahoyo", "Quevedo", "Buena Fe", "Ventanas", "Vinces", "Valencia", "Montalvo"],
    "Manabí" => ["Portoviejo", "Manta", "Chone", "Jipijapa", "Montecristi", "El Carmen", "Bahía de Caráquez", "Calceta", "Pedernales", "Jaramijó"],
    "Morona Santiago" => ["Macas", "Sucúa", "Gualaquiza", "Limón Indanza", "Palora"],
    "Napo" => ["Tena", "Archidona", "El Chaco", "Baeza"],
    "Orellana" => ["Francisco de Orellana (El Coca)", "La Joya de los Sachas", "Loreto"],
    "Pastaza" => ["Puyo", "Mera", "Santa Clara", "Arajuno"],
    "Pichincha" => ["Quito", "Sangolquí (Rumiñahui)", "Cayambe", "Machachi", "Tabacundo"],
    "Santa Elena" => ["Santa Elena", "La Libertad", "Salinas"],
    "Santo Domingo de los Tsáchilas" => ["Santo Domingo", "La Concordia"],
    "Sucumbíos" => ["Nueva Loja (Lago Agrio)", "Shushufindi", "Cascales", "Cuyabeno"],
    "Tungurahua" => ["Ambato", "Baños de Agua Santa", "Pelileo", "Patate", "Quero"],
    "Zamora Chinchipe" => ["Zamora", "Yantzaza", "El Pangui", "Centinela del Cóndor"]
];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) {
        $accion = $_GET['accion'];
        $catalogos_model = new Catalogos();

        if ($accion === 'getCatalogos') {
            $marcas = $catalogos_model->getMarcas();
            $tipos = $catalogos_model->getTiposVehiculo();
            $provincias_nombres = array_keys($provincias_ciudades);
            sort($provincias_nombres);

            if ($marcas !== false && $tipos !== false) {
                $response = ['status' => 'success', 'marcas' => $marcas, 'tipos_vehiculo' => $tipos, 'provincias' => $provincias_nombres];
            } else {
                $response['message'] = 'No se pudieron cargar los catálogos básicos. Verifique la conexión y los datos base.';
                error_log("vehiculos_ajax.php: getCatalogos - Marcas o Tipos devolvieron false.");
            }
        } elseif ($accion === 'getModelos' && isset($_GET['marca_id'])) {
            $marca_id = filter_var($_GET['marca_id'], FILTER_VALIDATE_INT);
            if ($marca_id) {
                $modelos = $catalogos_model->getModelosPorMarca($marca_id);
                if ($modelos !== false) {
                    $response = ['status' => 'success', 'modelos' => $modelos];
                } else {
                    $response['message'] = 'No se encontraron modelos para esta marca o hubo un error.';
                    $response['modelos'] = [];
                    error_log("vehiculos_ajax.php: getModelos - marca_id $marca_id devolvió false o vacío.");
                }
            } else { $response['message'] = 'ID de marca inválido.'; }
        } elseif ($accion === 'getCiudades' && isset($_GET['provincia'])) {
            $provincia_seleccionada = $_GET['provincia'];
            if (array_key_exists($provincia_seleccionada, $provincias_ciudades)) {
                $ciudades = $provincias_ciudades[$provincia_seleccionada];
                sort($ciudades);
                $response = ['status' => 'success', 'ciudades' => $ciudades];
            } else { $response = ['status' => 'error', 'message' => 'Provincia no válida.', 'ciudades' => []]; }
        } elseif ($accion === 'getMisVehiculos') {
            if (!isset($_SESSION['usu_id'])) {
                $response = ['status' => 'error', 'message' => 'No autenticado.'];
            } else {
                $vehiculo_model = new Vehiculo();
                $mis_vehiculos = $vehiculo_model->getVehiculosPorGestor($_SESSION['usu_id']);
                if ($mis_vehiculos !== false) {
                    $response = ['status' => 'success', 'vehiculos' => $mis_vehiculos];
                } else { $response['message'] = 'Error al obtener tus vehículos.'; }
            }
        } elseif ($accion === 'getVehiculosListado') {
            $filtros = [];
            $filtros['condicion'] = isset($_GET['condicion']) ? $_GET['condicion'] : 'todos';
            if (isset($_GET['mar_id']) && !empty($_GET['mar_id'])) $filtros['mar_id'] = filter_var($_GET['mar_id'], FILTER_VALIDATE_INT);
            if (isset($_GET['mod_id']) && !empty($_GET['mod_id'])) $filtros['mod_id'] = filter_var($_GET['mod_id'], FILTER_VALIDATE_INT);
            if (isset($_GET['tiv_id']) && !empty($_GET['tiv_id'])) $filtros['tiv_id'] = filter_var($_GET['tiv_id'], FILTER_VALIDATE_INT);
            if (isset($_GET['precio_min']) && $_GET['precio_min'] !== '') $filtros['precio_min'] = filter_var($_GET['precio_min'], FILTER_VALIDATE_FLOAT);
            if (isset($_GET['precio_max']) && $_GET['precio_max'] !== '') $filtros['precio_max'] = filter_var($_GET['precio_max'], FILTER_VALIDATE_FLOAT);
            if (isset($_GET['anio_min']) && !empty($_GET['anio_min'])) $filtros['anio_min'] = filter_var($_GET['anio_min'], FILTER_VALIDATE_INT);
            if (isset($_GET['anio_max']) && !empty($_GET['anio_max'])) $filtros['anio_max'] = filter_var($_GET['anio_max'], FILTER_VALIDATE_INT);
            if (isset($_GET['provincia']) && !empty($_GET['provincia'])) $filtros['provincia'] = trim($_GET['provincia']);
            
            $filtros['pagina'] = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($filtros['pagina'] < 1) $filtros['pagina'] = 1;
            $filtros['items_por_pagina'] = isset($_GET['items_por_pagina']) ? (int)$_GET['items_por_pagina'] : 9;
             if ($filtros['items_por_pagina'] < 1) $filtros['items_por_pagina'] = 9;


            $vehiculo_model = new Vehiculo();
            $data = $vehiculo_model->getVehiculosListado($filtros);

            if (isset($data['error'])) {
                 $response = ['status' => 'error', 'message' => $data['error']];
                 error_log("vehiculos_ajax.php: getVehiculosListado - Error del modelo: " . $data['error']);
            } else {
                $response = [
                    'status' => 'success',
                    'vehiculos' => $data['vehiculos'],
                    'total_vehiculos' => $data['total'],
                    'pagina_actual' => $filtros['pagina'],
                    'items_por_pagina' => $filtros['items_por_pagina'],
                    'total_paginas' => ($filtros['items_por_pagina'] > 0) ? ceil($data['total'] / $filtros['items_por_pagina']) : 0
                ];
            }
        } else {
            $response['message'] = 'Acción GET desconocida o no implementada.';
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        $roles_permitidos_modificar = [1, 2, 3]; // Cliente/Vendedor (1), Asesor (2), Administrador (3)
        
        if (!isset($_SESSION['usu_id']) || !in_array($_SESSION['rol_id'], $roles_permitidos_modificar)) {
            $response = ['status' => 'error', 'message' => 'Acceso denegado para esta acción POST.'];
            echo json_encode($response);
            exit();
        }

        if ($accion === 'publicarVehiculo') {
            $required_fields = ['mar_id', 'mod_id', 'tiv_id', 'veh_condicion', 'veh_anio', 'veh_precio', 'veh_ubicacion_provincia', 'veh_ubicacion_ciudad', 'veh_fecha_publicacion', 'veh_color_exterior', 'veh_detalles_motor', 'veh_descripcion'];
            $missing_fields = [];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || trim($_POST[$field]) === '') { $missing_fields[] = $field; }
            }
            if (isset($_POST['veh_condicion']) && $_POST['veh_condicion'] === 'usado') {
                if (!isset($_POST['veh_kilometraje']) || trim($_POST['veh_kilometraje']) === '') { $missing_fields[] = 'veh_kilometraje (Recorrido)'; }
                if (!isset($_POST['veh_placa_provincia_origen']) || trim($_POST['veh_placa_provincia_origen']) === '') { $missing_fields[] = 'veh_placa_provincia_origen'; }
                if (!isset($_POST['veh_ultimo_digito_placa']) || trim($_POST['veh_ultimo_digito_placa']) === '') { $missing_fields[] = 'veh_ultimo_digito_placa'; }
            }

            if (!empty($missing_fields)) {
                $response['message'] = 'Faltan campos obligatorios: ' . implode(', ', $missing_fields);
            } elseif (!isset($_FILES['veh_imagenes']) || empty($_FILES['veh_imagenes']['name'][0])) {
                $response['message'] = 'Debes subir al menos una imagen.';
            } else {
                $datos_vehiculo = $_POST;
                $datos_vehiculo['usu_id_gestor'] = $_SESSION['usu_id'];
                $vehiculo_model = new Vehiculo();
                $resultado_sp_vehiculo = $vehiculo_model->insertarVehiculo($datos_vehiculo);

                if (isset($resultado_sp_vehiculo['resultado']) && $resultado_sp_vehiculo['resultado'] == 1) {
                    $veh_id_insertado = $resultado_sp_vehiculo['veh_id'];
                    $mensajes_imagenes = []; $errores_imagenes = 0;
                    if (isset($_FILES['veh_imagenes']) && $veh_id_insertado) {
                        try {
                            $imagenes_model = new ImagenesVehiculo_M();
                            $upload_dir_base = __DIR__ . '/../PUBLIC/uploads/vehiculos/';
                            $upload_dir = $upload_dir_base . $veh_id_insertado . '/';
                            if (!file_exists($upload_dir) && !is_dir($upload_dir)) { 
                                if (!mkdir($upload_dir, 0775, true)) { 
                                    throw new Exception("No se pudo crear el directorio de imágenes: " . $upload_dir);
                                }
                            }
                            $is_first_image = true;
                            foreach ($_FILES['veh_imagenes']['name'] as $key => $name) {
                                if ($_FILES['veh_imagenes']['error'][$key] == UPLOAD_ERR_OK) {
                                    $tmp_name = $_FILES['veh_imagenes']['tmp_name'][$key];
                                    $original_name = basename(filter_var($name, FILTER_SANITIZE_STRING));
                                    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
                                    if (!in_array($extension, $allowed_extensions)) { $mensajes_imagenes[] = "'$original_name': Tipo no permitido ($extension)."; $errores_imagenes++; continue; }
                                    $safe_filename = uniqid('vehiculo_' . $veh_id_insertado . '_', true) . '.' . $extension;
                                    $destination = $upload_dir . $safe_filename;
                                    if (move_uploaded_file($tmp_name, $destination)) {
                                        $url_relativa_db = 'PUBLIC/uploads/vehiculos/' . $veh_id_insertado . '/' . $safe_filename;
                                        $resultado_sp_imagen = $imagenes_model->insertarImagen($veh_id_insertado, $url_relativa_db, $is_first_image);
                                        if (isset($resultado_sp_imagen['resultado']) && $resultado_sp_imagen['resultado'] == 1) { $mensajes_imagenes[] = "'$original_name': OK."; } 
                                        else { $mensajes_imagenes[] = "'$original_name': Error BD (" . ($resultado_sp_imagen['mensaje'] ?? 'desconocido') . ")."; $errores_imagenes++; if(file_exists($destination)) unlink($destination); }
                                        if ($is_first_image) $is_first_image = false;
                                    } else { $mensajes_imagenes[] = "Error moviendo '$original_name'."; $errores_imagenes++; }
                                } elseif ($_FILES['veh_imagenes']['error'][$key] != UPLOAD_ERR_NO_FILE) { $mensajes_imagenes[] = "Error upload '$name': cod " . $_FILES['veh_imagenes']['error'][$key]; $errores_imagenes++; }
                            }
                        } catch (Exception $e_img) {
                             $mensajes_imagenes[] = "Excepción al procesar imágenes: " . $e_img->getMessage(); $errores_imagenes++;
                        }
                    }
                    $mensaje_final_vehiculo = $resultado_sp_vehiculo['mensaje'];
                    if (!empty($mensajes_imagenes)) { $mensaje_final_vehiculo .= " | Imágenes: " . implode("; ", $mensajes_imagenes); }
                    if ($errores_imagenes > 0) { $response = ['status' => 'warning', 'message' => $mensaje_final_vehiculo, 'veh_id' => $veh_id_insertado]; } 
                    else { $response = ['status' => 'success', 'message' => $mensaje_final_vehiculo, 'veh_id' => $veh_id_insertado]; }
                } else { $response['message'] = $resultado_sp_vehiculo['mensaje'] ?? 'Error al publicar vehículo.'; }
            }
        } elseif ($accion === 'cambiarEstadoVehiculo') {
            if (isset($_POST['veh_id'], $_POST['nuevo_estado'])) {
                $veh_id = filter_var($_POST['veh_id'], FILTER_VALIDATE_INT);
                $nuevo_estado = trim(filter_var($_POST['nuevo_estado'], FILTER_SANITIZE_STRING));
                $estados_validos = ['disponible', 'reservado', 'vendido', 'desactivado'];
                if (!$veh_id) { $response['message'] = 'ID de vehículo inválido.'; } 
                elseif (!in_array($nuevo_estado, $estados_validos)) { $response['message'] = 'El nuevo estado proporcionado no es válido.'; } 
                else {
                    $vehiculo_model = new Vehiculo();
                    $resultado_actualizacion = $vehiculo_model->actualizarEstadoVehiculo($veh_id, $nuevo_estado, $_SESSION['usu_id']);
                    if (isset($resultado_actualizacion['resultado']) && $resultado_actualizacion['resultado'] == 1) {
                        $response = ['status' => 'success', 'message' => $resultado_actualizacion['mensaje']];
                    } else { $response['message'] = $resultado_actualizacion['mensaje'] ?? 'No se pudo actualizar el estado del vehículo.'; }
                }
            } else { $response['message'] = 'Faltan datos necesarios (ID de vehículo o nuevo estado) para cambiar el estado.'; }
        } else {
            $response['message'] = 'Acción POST desconocida o no implementada.';
        }
    } else {
         // Si no es GET ni POST, o no hay 'accion'
         $response['message'] = 'Método de solicitud no soportado o acción no especificada.';
    }
} catch (Exception $e) {
    error_log("Excepción general en vehiculos_ajax.php: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
    $response = ['status' => 'error', 'message' => 'Ocurrió un error crítico en el servidor. Detalles: ' . $e->getMessage()];
}

echo json_encode($response);
?>