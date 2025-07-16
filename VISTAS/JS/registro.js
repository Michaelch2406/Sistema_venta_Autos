// registro.js - Versión corregida
$(document).ready(function() {
  // Inicialización de animaciones
  initializeAnimations();
  
  // Inicialización de validaciones en tiempo real
  initializeRealTimeValidation();
  
  // Inicialización del indicador de fortaleza de contraseña
  initializePasswordStrength();

  // 1) Toggle password visibility mejorado
  $('.toggle-password').click(function() {
    const target = $(this).data('target');
    const $input = $('#' + target);
    const $icon = $(this).find('i');
    const type = $input.attr('type') === 'password' ? 'text' : 'password';
    
    $input.attr('type', type);
    $icon.toggleClass('bi-eye bi-eye-slash');
    
    // Animación del botón
    $(this).addClass('clicked');
    setTimeout(() => $(this).removeClass('clicked'), 200);
  });

  // 2) Solo letras y espacios para nombre y apellido (mejorado)
  function soloLetrasConEspacio(evt) {
    const char = String.fromCharCode(evt.which || evt.keyCode);
    if (!/[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]/.test(char)) {
      evt.preventDefault();
      // Efecto visual de error
      $(evt.target).addClass('shake-error');
      setTimeout(() => $(evt.target).removeClass('shake-error'), 500);
    }
  }
  $('#regNombre, #regApellido').on('keypress paste', soloLetrasConEspacio);

  // 3) Validación de teléfono al perder foco (mejorada)
  function validarTelefonoJS(valor) {
    return /^\+?\d{7,15}$/.test(valor);
  }
  
  $('#regTelefono').on('blur', function() {
    const val = $(this).val().trim();
    const $group = $(this).closest('.floating-label-group');
    
    if (val && !validarTelefonoJS(val)) {
      setFieldError($group, 'Formato de teléfono inválido');
    } else {
      setFieldSuccess($group);
    }
  });

  // 4) Validación mínima de dirección (mejorada)
  $('#regDireccion').on('blur', function() {
    const val = $(this).val().trim();
    const $group = $(this).closest('.floating-label-group');
    
    if (val && val.length < 5) {
      setFieldError($group, 'La dirección debe tener al menos 5 caracteres');
    } else {
      setFieldSuccess($group);
    }
  });

  // 5) Fecha de nacimiento 0–99 años (mejorada)
  const hoy = new Date();
  const fechaMax = hoy.toISOString().split('T')[0];
  const fechaMinDate = new Date(hoy.getFullYear() - 99, hoy.getMonth(), hoy.getDate());
  const fechaMin = fechaMinDate.toISOString().split('T')[0];
  $('#regFnacimiento').attr({ min: fechaMin, max: fechaMax });

  // 6) Submit: validaciones combinadas (corregido)
  $('#registroForm').on('submit', function(event) {
    const form = this;
    let valid = true;

    // Animación de envío
    $(form).addClass('submitting');

    // Password match
    const pw = $('#regPassword').val();
    const pwc = $('#regPasswordConfirm').val();
    const $pwcGroup = $('#regPasswordConfirm').closest('.floating-label-group');
    
    if (pw !== pwc) {
      setFieldError($pwcGroup, 'Las contraseñas no coinciden');
      $('#regPasswordConfirm').addClass('is-invalid');
      $('#passwordConfirmError').show();
      valid = false;
    } else {
      setFieldSuccess($pwcGroup);
      $('#regPasswordConfirm').removeClass('is-invalid');
      $('#passwordConfirmError').hide();
    }

    // Nombre/apellido regex
    const nameRe = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$/;
    const $nombreGroup = $('#regNombre').closest('.floating-label-group');
    const $apellidoGroup = $('#regApellido').closest('.floating-label-group');
    
    if (!nameRe.test($('#regNombre').val().trim())) {
      setFieldError($nombreGroup, 'Solo se permiten letras y espacios');
      $('#regNombre').addClass('is-invalid');
      valid = false;
    } else {
      setFieldSuccess($nombreGroup);
      $('#regNombre').removeClass('is-invalid');
    }
    
    if (!nameRe.test($('#regApellido').val().trim())) {
      setFieldError($apellidoGroup, 'Solo se permiten letras y espacios');
      $('#regApellido').addClass('is-invalid');
      valid = false;
    } else {
      setFieldSuccess($apellidoGroup);
      $('#regApellido').removeClass('is-invalid');
    }

    // Email validation
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const $emailGroup = $('#regEmail').closest('.floating-label-group');
    
    if (!emailRe.test($('#regEmail').val().trim())) {
      setFieldError($emailGroup, 'Formato de email inválido');
      $('#regEmail').addClass('is-invalid');
      valid = false;
    } else {
      setFieldSuccess($emailGroup);
      $('#regEmail').removeClass('is-invalid');
    }

    // Teléfono
    const telVal = $('#regTelefono').val().trim();
    const $telGroup = $('#regTelefono').closest('.floating-label-group');
    
    if (telVal && !validarTelefonoJS(telVal)) {
      setFieldError($telGroup, 'Formato de teléfono inválido');
      $('#regTelefono').addClass('is-invalid');
      valid = false;
    } else {
      $('#regTelefono').removeClass('is-invalid');
    }

    // Dirección
    const dirVal = $('#regDireccion').val().trim();
    const $dirGroup = $('#regDireccion').closest('.floating-label-group');
    
    if (dirVal && dirVal.length < 5) {
      setFieldError($dirGroup, 'La dirección debe tener al menos 5 caracteres');
      $('#regDireccion').addClass('is-invalid');
      valid = false;
    } else {
      $('#regDireccion').removeClass('is-invalid');
    }

    // Fecha de nacimiento
    const fnVal = $('#regFnacimiento').val();
    const $fnGroup = $('#regFnacimiento').closest('.floating-label-group');
    
    if (fnVal) {
      const sel = new Date(fnVal);
      if (sel < fechaMinDate || sel > hoy) {
        setFieldError($fnGroup, 'Fecha de nacimiento inválida');
        $('#regFnacimiento').addClass('is-invalid');
        valid = false;
      } else {
        $('#regFnacimiento').removeClass('is-invalid');
      }
    }

    // Bootstrap validation nativa
    if (!form.checkValidity() || !valid) {
      event.preventDefault();
      event.stopPropagation();
      $(form).addClass('was-validated').removeClass('submitting');
      
      // Scroll al primer error
      const firstError = $(form).find('.is-invalid').first();
      if (firstError.length) {
        firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return;
    }

    // Si todo ok, se añade spinner y se hace AJAX
    event.preventDefault();
    $(form).addClass('was-validated');
    const formData = $(form).serialize();
    const $btn = $(form).find('.btn-submit');
    
    // Animación del botón
    $btn.addClass('loading');
    
    $.ajax({
      url: './../AJAX/registro_ajax.php',
      method: 'POST',
      data: formData,
      dataType: 'json'
    })
    .done(function(resp) {
      console.log("Registro success response:", resp);
      if (resp.status === 'success') {
        // Animación de éxito
        $btn.removeClass('loading').addClass('success');
        
        setTimeout(() => {
          showSuccessMessage(resp.message + " Ahora puedes iniciar sesión.");
          form.reset();
          $(form).removeClass('was-validated submitting');
          resetAllFields();
          $btn.removeClass('success');
        }, 1500);
      } else {
        $btn.removeClass('loading');
        showErrorMessage('Error: ' + resp.message);
        $(form).removeClass('submitting');
      }
    })
    .fail(function(xhr, status, err) {
      console.error("Registro AJAX error:", status, err, xhr.responseText);
      $btn.removeClass('loading');
      showErrorMessage('Error de servidor: ' + status);
      console.error(xhr.responseText);
      $(form).removeClass('submitting');
    });
  });

  // Funciones auxiliares mejoradas
  function initializeAnimations() {
    // Animación de entrada para elementos
    $('.animate-fade-in, .animate-slide-up, .animate-scale-in').each(function(index) {
      $(this).css('animation-delay', (index * 0.1) + 's');
    });

    // Animación de partículas
    $('.particle').each(function(index) {
      $(this).css({
        'animation-delay': (Math.random() * 2) + 's',
        'left': Math.random() * 100 + '%',
        'animation-duration': (3 + Math.random() * 2) + 's'
      });
    });
  }

  function initializeRealTimeValidation() {
    // Validación en tiempo real para todos los campos
    $('.floating-input').on('input blur', function() {
      const $this = $(this);
      const $group = $this.closest('.floating-label-group');
      const value = $this.val().trim();

      // Remover estados previos
      $group.removeClass('has-error has-success');
      
      if (value) {
        $group.addClass('has-content');
        
        // Validación específica por campo
        if ($this.attr('type') === 'email') {
          const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
          if (emailRe.test(value)) {
            setFieldSuccess($group);
          }
        } else if ($this.attr('required') && value.length > 0) {
          setFieldSuccess($group);
        }
      } else {
        $group.removeClass('has-content');
      }
    });
  }

  function initializePasswordStrength() {
    $('#regPassword').on('input', function() {
      const password = $(this).val();
      const strength = calculatePasswordStrength(password);
      updatePasswordStrengthIndicator(strength);
    });
  }

  function calculatePasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 8) score += 1;
    if (password.length >= 12) score += 1;
    if (/[a-z]/.test(password)) score += 1;
    if (/[A-Z]/.test(password)) score += 1;
    if (/[0-9]/.test(password)) score += 1;
    if (/[^A-Za-z0-9]/.test(password)) score += 1;
    
    return score;
  }

  function updatePasswordStrengthIndicator(strength) {
    const $strengthBar = $('.strength-fill');
    const $strengthText = $('.strength-text');
    
    const levels = [
      { text: 'Muy débil', class: 'very-weak', width: '16%' },
      { text: 'Débil', class: 'weak', width: '32%' },
      { text: 'Regular', class: 'fair', width: '48%' },
      { text: 'Buena', class: 'good', width: '64%' },
      { text: 'Fuerte', class: 'strong', width: '80%' },
      { text: 'Muy fuerte', class: 'very-strong', width: '100%' }
    ];
    
    const level = levels[Math.min(strength, 5)];
    
    $strengthBar.removeClass('very-weak weak fair good strong very-strong')
               .addClass(level.class)
               .css('width', level.width);
    
    $strengthText.text(level.text);
  }

  function setFieldError($group, message) {
    $group.addClass('has-error').removeClass('has-success');
    if (message && $group.find('.invalid-feedback').length) {
      $group.find('.invalid-feedback').text(message);
    }
  }

  function setFieldSuccess($group) {
    $group.addClass('has-success').removeClass('has-error');
  }

  function resetAllFields() {
    $('.floating-label-group').removeClass('has-content has-error has-success');
    $('.floating-input').removeClass('is-valid is-invalid');
    $('.strength-fill').css('width', '0%').removeClass('very-weak weak fair good strong very-strong');
    $('.strength-text').text('Fortaleza de contraseña');
  }

  function showSuccessMessage(message) {
    // Crear notificación de éxito
    const $notification = $(`
      <div class="success-notification">
        <i class="bi bi-check-circle-fill"></i>
        <span>${message}</span>
      </div>
    `);
    
    $('body').append($notification);
    
    setTimeout(() => {
      $notification.addClass('show');
    }, 100);
    
    setTimeout(() => {
      $notification.removeClass('show');
      setTimeout(() => $notification.remove(), 300);
    }, 4000);
  }

  function showErrorMessage(message) {
    // Crear notificación de error
    const $notification = $(`
      <div class="error-notification">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>${message}</span>
      </div>
    `);
    
    $('body').append($notification);
    
    setTimeout(() => {
      $notification.addClass('show');
    }, 100);
    
    setTimeout(() => {
      $notification.removeClass('show');
      setTimeout(() => $notification.remove(), 300);
    }, 4000);
  }
});

