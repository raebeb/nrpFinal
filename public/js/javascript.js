$.extend( true, $.fn.dataTable.defaults, {
  "language": {
      "decimal":        "",
      "emptyTable":     "No hay datos disponibles",
      "info":           "Mostrando _START_ a _END_ de _TOTAL_ entradas",
      "infoEmpty":      "Mostrando 0 a 0 de 0 entradas",
      "infoFiltered":   "(filtrado de _MAX_ entradas totales)",
      "infoPostFix":    "",
      "thousands":      ",",
      "lengthMenu":     "Mostrar _MENU_ entradas",
      "loadingRecords": "cargando...",
      "processing":     "procesando...",
      "search":         "Buscar:",
      "zeroRecords":    "No se encontraron registros coincidentes",
      "paginate": {
          "first":      "Primero",
          "last":       "Último",
          "next":       "Siguiente",
          "previous":   "Anterior"
      },
      "aria": {
          "sortAscending":  ": activate to sort column ascending",
          "sortDescending": ": activate to sort column descending"
      }
  }
});

$(document).ready(function(){
    $('#csv-history').DataTable({

    });
});

$(document).ready(function(){
    $('#access_log').DataTable({
      "order": [[2,"desc"]]
    });
});

$(document).ready(function(){
    $('#manage_users').DataTable({
      
    });
});

$('.collapse').collapse();