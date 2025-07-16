<?php
// Habilitar el log de errores en un archivo en lugar de mostrarlo al usuario
ini_set('display_errors', 0); 
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', './../php_error.log'); 

session_start();

// Dependencias de los modelos y la conexión
require_once "./../MODELOS/catalogos_m.php";
require_once "./../MODELOS/vehiculos_m.php";
require_once "./../MODELOS/imagenes_vehiculo_m.php";
require_once "./../CONFIG/Conexion.php"; // Necesario para la nueva función de permisos

// Establecer la cabecera para la respuesta JSON
header('Content-Type: application/json');
// Definir una respuesta por defecto
$response = ['status' => 'error', 'message' => 'Petición no válida o acción no especificada.'];

/**
 * ========================================================================
 * FUNCIÓN DE AYUDA PARA VERIFICAR PERMISOS (Adaptada del primer script)
 * Esta función centraliza la lógica para saber si un usuario puede
 * editar o ver los detalles de un vehículo específico.
 * 
 * @param int $veh_id El ID del vehículo a verificar.
 * @return bool True si el usuario tiene permiso, False si no lo tiene.
 * ========================================================================
 */
function tienePermisoParaVehiculo($veh_id) {
    // Si no hay un usuario logueado, no tiene permiso.
    if (!isset($_SESSION['usu_id']) || !isset($_SESSION['rol_id'])) {
        return false;
    }

    // Un administrador (rol_id = 3) siempre tiene permiso.
    if ($_SESSION['rol_id'] == 3) {
        return true;
    }

    // Para otros roles, verificamos si son los dueños del vehículo (usu_id_gestor).
    try {
        $conexion_obj = new Conexion();
        $mysqli = $conexion_obj->conecta();
        if (!$mysqli) return false; // Falló la conexión

        $stmt = $mysqli->prepare("SELECT usu_id_gestor FROM vehiculos WHERE veh_id = ?");
        $stmt->bind_param("i", $veh_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $es_dueño = false;
        if ($resultado->num_rows > 0) {
            $vehiculo = $resultado->fetch_assoc();
            // Comparamos el dueño del vehículo con el usuario de la sesión actual
            if ($vehiculo['usu_id_gestor'] == $_SESSION['usu_id']) {
                $es_dueño = true;
            }
        }
        $stmt->close();
        return $es_dueño;
    } catch (Exception $e) {
        error_log("Error en la función tienePermisoParaVehiculo: " . $e->getMessage());
        return false; // Ante cualquier error, denegar permiso por seguridad.
    }
}


// Datos estáticos de provincias y ciudades
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
    // ============================
    // MANEJO DE PETICIONES GET
    // ============================
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
            } else { $response['message'] = 'No se pudieron cargar los catálogos básicos.'; }

        } elseif ($accion === 'getModelos' && isset($_GET['marca_id'])) {
            $marca_id = filter_var($_GET['marca_id'], FILTER_VALIDATE_INT);
            if ($marca_id) {
                $modelos = $catalogos_model->getModelosPorMarca($marca_id);
                $response = ['status' => 'success', 'modelos' => $modelos ?: []];
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
             // Lógica de getVehiculosListado sin cambios
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

        } elseif ($accion === 'getTodosLosVehiculosAdmin') {
            if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 3) {
                $response = ['status' => 'error', 'message' => 'Acceso denegado. Permiso de administrador requerido.'];
            } else {
                $vehiculo_model = new Vehiculo();
                $vehiculos_admin = $vehiculo_model->getTodosLosVehiculosAdmin();
                if ($vehiculos_admin !== false) {
                    $response = ['status' => 'success', 'vehiculos' => $vehiculos_admin];
                } else { $response['message'] = 'Error al obtener el listado de vehículos.'; }
            }
        
        } elseif ($accion === 'getDetallesVehiculoParaEdicion' && isset($_GET['veh_id'])) {
            // === LÓGICA DE PERMISOS PARA OBTENER DETALLES (GET) ADAPTADA ====
            $veh_id = filter_var($_GET['veh_id'], FILTER_VALIDATE_INT);
            if (!$veh_id) {
                $response = ['status' => 'error', 'message' => 'ID de vehículo inválido.'];
            } elseif (!tienePermisoParaVehiculo($veh_id)) {
                $response = ['status' => 'error', 'message' => 'Acceso denegado. No tienes permiso para editar este vehículo.'];
            } else {
                // El usuario tiene permiso, procedemos a obtener los datos.
                $vehiculo_model = new Vehiculo();
                $detalles_vehiculo = $vehiculo_model->getDetallesVehiculoParaEdicionDB($veh_id);
                if ($detalles_vehiculo && !isset($detalles_vehiculo['error'])) {
                    $response = ['status' => 'success', 'data' => $detalles_vehiculo];
                } else {
                    $response['message'] = $detalles_vehiculo['error'] ?? 'Error al obtener los detalles del vehículo.';
                }
            }

        } else {
            // Cubre otras acciones GET no definidas
            $response['message'] = 'Acción GET desconocida o parámetros incompletos.';
        }

    // =============================
    // MANEJO DE PETICIONES POST
    // =============================
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
        $accion = $_POST['accion'];

        // Verificación de sesión para todas las acciones POST
        if (!isset($_SESSION['usu_id'])) {
            $response = ['status' => 'error', 'message' => 'No autenticado. Debes iniciar sesión para realizar esta acción.'];
            echo json_encode($response);
            exit();
        }
        
        $es_admin = (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 3);
        
        if ($accion === 'publicarVehiculo') {
            // Lógica para publicar sin cambios...
             $required_fields = ['mar_id', 'mod_id', 'tiv_id', 'veh_condicion', 'veh_anio', 'veh_precio', 'veh_ubicacion_provincia', 'veh_ubicacion_ciudad', 'veh_fecha_publicacion', 'veh_color_exterior', 'veh_detalles_motor', 'veh_descripcion'];
            $missing_fields = [];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || trim($_POST[$field]) === '') { $missing_fields[] = $field; }
            }
            if (isset($_POST['veh_condicion']) && $_POST['veh_condicion'] === 'usado') {
                if (!isset($_POST['veh_kilometraje']) || trim($_POST['veh_kilometraje']) === '') { $missing_fields[] = 'veh_kilometraje (Recorrido para vehículos usados)'; }
                if (!isset($_POST['veh_placa']) || trim($_POST['veh_placa']) === '') { $missing_fields[] = 'veh_placa (Placa para vehículos usados)'; }
                if (!isset($_POST['veh_placa_provincia_origen']) || trim($_POST['veh_placa_provincia_origen']) === '') { $missing_fields[] = 'veh_placa_provincia_origen (para vehículos usados)'; }
                if (!isset($_POST['veh_ultimo_digito_placa']) || trim($_POST['veh_ultimo_digito_placa']) === '') { $missing_fields[] = 'veh_ultimo_digito_placa (para vehículos usados)'; }
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
                    
                    $imagenes_subidas = $_FILES['veh_imagenes'];
                    $imagen_principal_nombre_temporal = $_POST['imagen_principal_nombre_temporal'] ?? null;
                    $upload_dir = __DIR__ . '/../PUBLIC/uploads/vehiculos/' . $veh_id_insertado . '/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0775, true);
                    }

                    $errores_imagenes = [];
                    $imagenes_model = new ImagenesVehiculo_M();

                    foreach ($imagenes_subidas['name'] as $key => $name) {
                        if ($imagenes_subidas['error'][$key] == UPLOAD_ERR_OK) {
                            $tmp_name = $imagenes_subidas['tmp_name'][$key];
                            $original_name = basename(filter_var($name, FILTER_SANITIZE_STRING));
                            $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                            $safe_filename = uniqid('vehiculo_' . $veh_id_insertado . '_', true) . '.' . $extension;
                            $destination = $upload_dir . $safe_filename;

                            if (move_uploaded_file($tmp_name, $destination)) {
                                $url_relativa_db = 'PUBLIC/uploads/vehiculos/' . $veh_id_insertado . '/' . $safe_filename;
                                $es_principal = ($original_name === $imagen_principal_nombre_temporal);
                                
                                $resultado_db = $imagenes_model->insertarImagen($veh_id_insertado, $url_relativa_db, $es_principal);
                                if (!isset($resultado_db['resultado']) || $resultado_db['resultado'] != 1) {
                                    $errores_imagenes[] = "Error al guardar '{$original_name}' en la base de datos: " . ($resultado_db['mensaje'] ?? 'Error desconocido.');
                                }
                            } else {
                                $errores_imagenes[] = "Error al mover el archivo '{$original_name}'.";
                            }
                        }
                    }

                    if (empty($errores_imagenes)) {
                        $response = ['status' => 'success', 'message' => '¡Vehículo publicado con éxito!', 'veh_id' => $veh_id_insertado];
                    } else {
                        $response = ['status' => 'warning', 'message' => 'Vehículo publicado, pero con errores en las imágenes: ' . implode('; ', $errores_imagenes), 'veh_id' => $veh_id_insertado];
                    }
                } else {
                     $response['message'] = $resultado_sp_vehiculo['mensaje'] ?? 'Error al publicar vehículo.';
                }
            }

        } elseif (($accion === 'cambiarEstadoVehiculo' || $accion === 'eliminarVehiculo') && !$es_admin) {
             // Verificación de rol específica para acciones de administrador
            $response = ['status' => 'error', 'message' => 'Acceso denegado. Permiso de administrador requerido.'];

        } elseif ($accion === 'cambiarEstadoVehiculo') {
            // Lógica para cambiar estado sin cambios...
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
            } else { $response['message'] = 'Faltan datos necesarios para cambiar el estado.'; }

        } elseif ($accion === 'eliminarVehiculo') {
             // Lógica para eliminar vehículo sin cambios...
             if (isset($_POST['veh_id'])) {
                $veh_id = filter_var($_POST['veh_id'], FILTER_VALIDATE_INT);
                if (!$veh_id) {
                    $response['message'] = 'ID de vehículo inválido.';
                } else {
                    $vehiculo_model = new Vehiculo();
                    $resultado_eliminacion = $vehiculo_model->eliminarVehiculo($veh_id, $_SESSION['usu_id']);
                    if (isset($resultado_eliminacion['resultado']) && $resultado_eliminacion['resultado'] == 1) {
                        $response = ['status' => 'success', 'message' => $resultado_eliminacion['mensaje']];
                    } else {
                        $response['message'] = $resultado_eliminacion['mensaje'] ?? 'No se pudo eliminar el vehículo.';
                    }
                }
            } else {
                $response['message'] = 'Falta el ID del vehículo para eliminar.';
            }

        } elseif ($accion === 'actualizarVehiculo') {
            // === LÓGICA DE PERMISOS PARA ACTUALIZAR DATOS (POST) ADAPTADA ====
            $veh_id = isset($_POST['veh_id']) ? filter_var($_POST['veh_id'], FILTER_VALIDATE_INT) : null;
            if (!$veh_id) {
                $response = ['status' => 'error', 'message' => 'ID de vehículo no proporcionado o inválido.'];
            } elseif (!tienePermisoParaVehiculo($veh_id)) {
                $response = ['status' => 'error', 'message' => 'Acceso denegado. No tienes permiso para guardar los cambios de este vehículo.'];
            } else {
                // El usuario tiene permiso, procedemos a actualizar.
                $datos_vehiculo = $_POST;
                $imagenes_a_eliminar_str = $_POST['imagenes_a_eliminar'] ?? '';
                $ids_imagenes_a_eliminar = !empty($imagenes_a_eliminar_str) ? explode(',', $imagenes_a_eliminar_str) : [];
                $imagen_principal_actual_id = isset($_POST['imagen_principal_actual_id']) && !empty($_POST['imagen_principal_actual_id']) ? filter_var($_POST['imagen_principal_actual_id'], FILTER_VALIDATE_INT) : null;
                $nueva_imagen_principal_nombre_temporal = isset($_POST['nueva_imagen_principal_nombre_temporal']) && !empty($_POST['nueva_imagen_principal_nombre_temporal']) ? basename($_POST['nueva_imagen_principal_nombre_temporal']) : null;
                $nuevas_imagenes_subidas = $_FILES['veh_imagenes_nuevas'] ?? [];
                
                // Aquí podrías (y deberías) añadir validaciones de campos requeridos como en publicarVehiculo.
                
                $vehiculo_model = new Vehiculo();
                $resultado_actualizacion = $vehiculo_model->actualizarVehiculoDB(
                    $veh_id,
                    $datos_vehiculo,
                    $nuevas_imagenes_subidas,
                    $ids_imagenes_a_eliminar,
                    $imagen_principal_actual_id,
                    $nueva_imagen_principal_nombre_temporal,
                    $_SESSION['usu_id'] // ID del usuario que realiza la acción
                );

                if (isset($resultado_actualizacion['resultado']) && $resultado_actualizacion['resultado'] == 1) {
                    $response = ['status' => 'success', 'message' => $resultado_actualizacion['mensaje'] ?? 'Vehículo actualizado exitosamente.'];
                } else {
                    $response = ['status' => 'error', 'message' => $resultado_actualizacion['mensaje'] ?? 'Error al actualizar el vehículo.'];
                    error_log("Error desde actualizarVehiculoDB para veh_id $veh_id: " . ($resultado_actualizacion['mensaje'] ?? 'Desconocido'));
                }
            }
        } else {
            $response['message'] = 'Acción POST desconocida o no implementada.';
        }

    } else {
        $response['message'] = 'Método de solicitud no soportado o acción no especificada.';
    }

} catch (Exception $e) {
    error_log("Excepción fatal en vehiculos_ajax.php: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
    $response = ['status' => 'error', 'message' => 'Ocurrió un error crítico en el servidor. Contacte a soporte.'];
}

// Imprimir la respuesta final en formato JSON
echo json_encode($response);
?>