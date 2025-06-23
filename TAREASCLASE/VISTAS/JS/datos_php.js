$(document).ready(function() {

    $('#txt_cedula').validarCedulaEC({
        strict: true, 
        events: "blur", 
        the_classes: "invalid",
        onValid: function() {
            console.log('Cédula válida:', this.value);
            $(this).removeClass('invalid').addClass('valid');
        },
        onInvalid: function() {
            console.log('Cédula inválida:', this.value);
            $('#mensaje').text().css('color', 'red');
            $(this).removeClass('valid').addClass('invalid');
        }
    });
    
    $('#txt_cedula').on('input', function() {
        if ($(this).val().length === 0) {
            $('#mensaje').text('');
            $(this).removeClass('valid invalid');
        }
    });
    
    $('#txt_cedula').on('keypress', function(e) {
        if (!/[0-9]/.test(String.fromCharCode(e.which))) {
            e.preventDefault();
        }
    });
	
	$('#txt_nombre, #txt_apellido').on('input', function() {
		const valor = $(this).val();
		const regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]*$/;

		if (!regex.test(valor)) {
			$(this).val(valor.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, ''));
		}
	});

	
	function campoNoVacio(valor) {
		return valor.trim() !== '';
	}

	function validarNombreApellido(texto) {
		const regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
		return regex.test(texto.trim());
	}

	function validarCedulaEcuatoriana(cedula) {
		if (cedula.length !== 10) return false;

		const digitos = cedula.split('').map(Number);
		const codigoProvincia = parseInt(cedula.substring(0, 2), 10);

		if (codigoProvincia < 1 || codigoProvincia > 24) return false;

		const digitoVerificador = digitos.pop();
		let suma = 0;

		for (let i = 0; i < digitos.length; i++) {
			let valor = digitos[i];
			if (i % 2 === 0) {
				valor *= 2;
				if (valor > 9) valor -= 9;
			}
			suma += valor;
		}

		const modulo = suma % 10;
		const digitoCalculado = modulo === 0 ? 0 : 10 - modulo;

		return digitoCalculado === digitoVerificador;
	}

	
	function enviarDatosConFormData() {
                let formData = new FormData();
                
                formData.append('action', 'validar_usuario_completo');
                
                formData.append('txt_nombre', $('#txt_nombre').val().trim());
                formData.append('txt_apellido', $('#txt_apellido').val().trim());
                formData.append('dt_fnacimiento', $('#dt_fnacimiento').val());
                formData.append('txt_cedula', $('#txt_cedula').val());

                formData.append('timestamp', new Date().toISOString());
                formData.append('user_agent', navigator.userAgent);
               
                $.ajax({
                    url: "http://localhost/Sistema_venta_Autos/AJAX/datos.php",
                    type: "POST",
                    data: formData,
                    processData: false, 
                    contentType: false, 
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                        mostrarResultado(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        
                        let errorMsg = 'Error de conexión con el servidor';
                        if (status === 'timeout') {
                            errorMsg = 'Tiempo de espera agotado. Inténtalo de nuevo.';
                        } else if (xhr.status === 404) {
                            errorMsg = 'Archivo PHP no encontrado (Error 404)';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Error interno del servidor (Error 500)';
                        }
                        
                        mostrarResultado({
                            success: false,
                            message: errorMsg,
                            error_details: error
                        });
                    },
                    complete: function() {
                        $('#btn_aceptar_php').prop('disabled', false).text('Validar y Procesar Datos');
                    }
                });
                
                console.log('Datos enviados:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
            }
	function mostrarResultado(response) {
		if (response.success) {
			const data = response.data;

			$('#suma').text('La suma es: ' + data.suma_cedula);

			$('#apellido').text('Apellido: ' + data.apellido_transformado);

			$('#btn_calcular_edad').off('click').on('click', function(e) {
				e.preventDefault();
				alert('Edad: ' + data.edad + ' años');
			});

			alert('Nombre invertido: ' + data.nombre_invertido);

			$('#resultado').fadeIn();
		} else {
			alert('Error: ' + response.message);
		}
	}

	$('#btn_aceptar_php').on('click', function(e) {
		e.preventDefault();

		const nombre = $('#txt_nombre').val();
		const apellido = $('#txt_apellido').val();
		const fechaNacimiento = $('#dt_fnacimiento').val();
		const cedula = $('#txt_cedula').val();

		if (!campoNoVacio(nombre) || !validarNombreApellido(nombre)) {
			alert('Por favor, ingrese un nombre válido.');
			return;
		}

		if (!campoNoVacio(apellido) || !validarNombreApellido(apellido)) {
			alert('Por favor, ingrese un apellido válido.');
			return;
		}

		if (!campoNoVacio(fechaNacimiento)) {
			alert('Por favor, seleccione su fecha de nacimiento.');
			return;
		}

		if (!validarCedulaEcuatoriana(cedula)) {
			alert('Por favor, ingrese una cédula ecuatoriana válida.');
			return;
		}

		enviarDatosConFormData();
	});

	});

$(document).ready(function() {
    let hoy = new Date();
    let fechaMinima = new Date(hoy.getFullYear() - 99, hoy.getMonth(), hoy.getDate());
    
    $('#dt_fnacimiento').attr({
        'min': fechaMinima.toISOString().split('T')[0],
        'max': hoy.toISOString().split('T')[0]
    });
});