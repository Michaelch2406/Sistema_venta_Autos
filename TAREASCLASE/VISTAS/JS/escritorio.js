$(document).ready(function() {
  $('#tblUsuarios').DataTable({
    "asProcessing": true,
    "asServerSide": true,
    "ajax": {
      "url": "../AJAX/escritorio.php?op=listar",
      "type": "GET",
      "dataType": "json",
      "error": function (xhr, error, thrown) {
        console.error("Error en AJAX de DataTables: ", error, thrown, xhr.responseText);
        alert("Error al cargar datos de la tabla. Revise la consola para más detalles.");
      }
    },
    "bdestroy": true,
    "iDisplayLength": 10,
    "order": [[1, "asc"]],
    "responsive": true,
    "dom": 'Blfrtip',
    "buttons": [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json",
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sSearch": "Buscar:",
        "oPaginate": {
            "sFirst": "Primero",
            "sLast": "Último",
            "sNext": "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    }
  });

  llenarCombo();

  $('#sel_usuarios').on('change', function() {
    let opt = $(this).find('option:selected');
    $('#sel_usuarios1').val(opt.val());
    llenarCampos(opt);
  });

  $('#sel_usuarios1').on('change', function() {
    let opt = $(this).find('option:selected');
    $('#sel_usuarios').val(opt.val());
    llenarCampos(opt);
  });

  $(document).on('click', '.editar-usuario', function(){
    let btn = $(this);
    $('#txt_cedula').val(btn.data('cedula'));
    $('#txt_nombre').val(btn.data('nombre'));
    $('#txt_apellido').val(btn.data('apellido'));
    $('#txt_usuario').val(btn.data('usuario'));
    
    $('#sel_usuarios').val(btn.data('id'));
    $('#sel_usuarios1').val(btn.data('id'));
  });
});

function llenarCombo(){
  $.get("../AJAX/escritorio.php?op=combo_usu", function(html){
    $('#sel_usuarios, #sel_usuarios1').html(html);
  });
}

function llenarCampos(opt){
  $('#txt_cedula').val(opt.data('cedula'));
  $('#txt_nombre').val(opt.data('nombre'));
  $('#txt_apellido').val(opt.data('apellido'));
  $('#txt_usuario').val(opt.data('usuario'));
}