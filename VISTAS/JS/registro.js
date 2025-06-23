// registro.js
$(document).ready(function() {
  // 1) Toggle password visibility
  $('.toggle-password').click(function() {
    const target = $(this).data('target');
    const $input = $('#' + target);
    const type = $input.attr('type') === 'password' ? 'text' : 'password';
    $input.attr('type', type);
    $(this).find('i').toggleClass('bi-eye bi-eye-slash');
  });

  // 2) Solo letras y espacios para nombre y apellido
  function soloLetrasConEspacio(evt) {
    const char = String.fromCharCode(evt.which || evt.keyCode);
    if (!/[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]/.test(char)) {
      evt.preventDefault();
    }
  }
  $('#regNombre, #regApellido').on('keypress paste', soloLetrasConEspacio);

  // 3) Validación de teléfono al perder foco
  function validarTelefonoJS(valor) {
    return /^\+?\d{7,15}$/.test(valor);
  }
  $('#regTelefono').on('blur', function() {
    const val = $(this).val().trim();
    if (val && !validarTelefonoJS(val)) {
      $(this).addClass('is-invalid');
    } else {
      $(this).removeClass('is-invalid');
    }
  });

  // 4) Validación mínima de dirección
  $('#regDireccion').on('blur', function() {
    const val = $(this).val().trim();
    if (val && val.length < 5) {
      $(this).addClass('is-invalid');
    } else {
      $(this).removeClass('is-invalid');
    }
  });

  // 5) Fecha de nacimiento 0–99 años
  const hoy = new Date();
  const fechaMax = hoy.toISOString().split('T')[0];
  const fechaMinDate = new Date(hoy.getFullYear() - 99, hoy.getMonth(), hoy.getDate());
  const fechaMin = fechaMinDate.toISOString().split('T')[0];
  $('#regFnacimiento').attr({ min: fechaMin, max: fechaMax });

  // 6) Submit: validaciones combinadas
  $('#registroForm').on('submit', function(event) {
    const form = this;
    let valid = true;

    // Password match
    const pw = $('#regPassword').val();
    const pwc = $('#regPasswordConfirm').val();
    if (pw !== pwc) {
      $('#regPasswordConfirm').addClass('is-invalid');
      $('#passwordConfirmError').show();
      valid = false;
    } else {
      $('#regPasswordConfirm').removeClass('is-invalid');
      $('#passwordConfirmError').hide();
    }

    // Nombre/apellido regex
    const nameRe = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$/;
    if (!nameRe.test($('#regNombre').val().trim())) {
      $('#regNombre').addClass('is-invalid');
      valid = false;
    }
    if (!nameRe.test($('#regApellido').val().trim())) {
      $('#regApellido').addClass('is-invalid');
      valid = false;
    }

    // Email HTML5 ya lo valida, pero reforzamos pattern
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    if (!emailRe.test($('#regEmail').val().trim())) {
      $('#regEmail').addClass('is-invalid');
      valid = false;
    }

    // Teléfono
    const telVal = $('#regTelefono').val().trim();
    if (telVal && !validarTelefonoJS(telVal)) {
      $('#regTelefono').addClass('is-invalid');
      valid = false;
    }

    // Dirección
    const dirVal = $('#regDireccion').val().trim();
    if (dirVal && dirVal.length < 5) {
      $('#regDireccion').addClass('is-invalid');
      valid = false;
    }

    // Fecha de nacimiento
    const fnVal = $('#regFnacimiento').val();
    if (fnVal) {
      const sel = new Date(fnVal);
      if (sel < fechaMinDate || sel > hoy) {
        $('#regFnacimiento').addClass('is-invalid');
        valid = false;
      }
    }

    // Bootstrap validation nativa
    if (!form.checkValidity() || !valid) {
      event.preventDefault();
      event.stopPropagation();
      $(form).addClass('was-validated');
      return;
    }

    // Si todo ok, se añade spinner y se hace AJAX
    event.preventDefault();
    $(form).addClass('was-validated');
    const formData = $(form).serialize();
    const $btn = $(form).find('button[type="submit"]');
    const origText = $btn.html();
    $btn.prop('disabled', true).html(
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registrando...'
    );

    $.ajax({
      url: '../AJAX/registro_ajax.php',
      method: 'POST',
      data: formData,
      dataType: 'json'
    })
    .done(function(resp) {
      if (resp.status === 'success') {
        alert(resp.message + " Ahora puedes iniciar sesión.");
        form.reset();
        $(form).removeClass('was-validated');
      } else {
        alert('Error: ' + resp.message);
      }
    })
    .fail(function(xhr, status, err) {
      alert('Error de servidor: ' + status);
      console.error(xhr.responseText);
    })
    .always(function() {
      $btn.prop('disabled', false).html(origText);
    });
  });
});
