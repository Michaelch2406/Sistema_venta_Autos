<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		http_response_code(200);
		exit(0);
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		class ValidadorUsuarioCompleto {
			
			private function calcularSumaCedula($cedula) {
				return array_sum(str_split($cedula));
			}
			
			private function invertirNombre($nombre) {
				return strrev($nombre);
			}
			
			private function transformarApellido($apellido) {
				$resultado = '';
				$letras = mb_str_split($apellido);
				foreach ($letras as $indice => $letra) {
					$resultado .= ($indice % 2 == 0) ? mb_strtoupper($letra) : mb_strtolower($letra);
				}
				return $resultado;
			}
			
			private function calcularEdad($fechaNacimiento) {
				$fechaNacimiento = new DateTime($fechaNacimiento);
				$hoy = new DateTime();
				$edad = $hoy->diff($fechaNacimiento)->y;
				return $edad;
			}


			
			public function procesarFormData($postData) {
				$respuesta = [
					'success' => false,
					'message' => '',
					'data' => [],
					'errors' => [],
					'debug_info' => []
				];

				try {
					$respuesta['debug_info'] = [
						'timestamp' => date('Y-m-d H:i:s'),
						'received_data_count' => count($postData),
						'user_agent' => $postData['user_agent'] ?? 'No disponible'
					];

					$errores = [];
					$datosValidados = [];

					// ===== VALIDAR CAMPOS =====
					$nombre = trim($postData['txt_nombre'] ?? '');
					$apellido = trim($postData['txt_apellido'] ?? '');
					$cedula = trim($postData['txt_cedula'] ?? '');
					$fnacimiento = $postData['dt_fnacimiento'] ?? '';

					$datosValidados['txt_nombre'] = strtoupper($nombre);
					$datosValidados['txt_apellido'] = strtoupper($apellido);
					$datosValidados['txt_cedula'] = $cedula;
					$datosValidados['dt_fnacimiento'] = $fnacimiento;
					$datosValidados['suma_cedula'] = $this->calcularSumaCedula($cedula);
					$datosValidados['nombre_invertido'] = $this->invertirNombre($nombre);
					$datosValidados['apellido_transformado'] = $this->transformarApellido($apellido);
					$datosValidados['edad'] = $this->calcularEdad($fnacimiento);





					$respuesta['success'] = true;
					$respuesta['message'] = 'Datos recibidos y procesados correctamente!';
					$respuesta['data'] = $datosValidados;
					http_response_code(200); 

				} catch (Exception $e) {
					$respuesta['success'] = false;
					$respuesta['message'] = 'Ocurrió un error inesperado en el servidor.';
					$respuesta['errors']['general'] = $e->getMessage();
					http_response_code(500); // Internal Server Error
				}

				echo json_encode($respuesta);
				exit;
			}
		}

		$validador = new ValidadorUsuarioCompleto();
		$validador->procesarFormData($_POST);

	} else {
		http_response_code(405);
		echo json_encode([
			'success' => false,
			'message' => 'Método no permitido. Este endpoint solo acepta solicitudes POST.',
			'code' => 'MethodNotAllowed'
		]);
		exit;
	}
?>