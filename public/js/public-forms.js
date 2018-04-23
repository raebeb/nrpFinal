$(document).ready(function() {
    var max_fields      = 4; 
    var wrapper         = $(".input_fields_wrap"); 
    var add_button      = $(".add_field_button"); 
    
    var x = 1; 
    $(add_button).click(function(e){ 
        e.preventDefault();
        if(x < max_fields){ 
            x++; 
            $(wrapper).append('<div class="form-group col-md-4"> <label for="name-shift">Ingrese nombre de turno</label> <input type="text" class="form-control" id="name-shift" name="name-shift[]"> </div> <div class="form-group col-md-4"> <label for="quantity-hours">Cantidad de horas</label> <input type="number" class="form-control" id="quantity-hours" name="quantity-hours[]"> </div> <div class="form-group col-md-4"> <label for="max-quantity-consecutive">Cantidad maxima consecutiva</label> <input type="number" class="form-control" id="max-consecutive" name="max-consecutive[]"> </div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});

$('input[name="daterange"]').daterangepicker({
    "dateLimit": {
        "days": 7
    },
    "locale": {
        "format": "DD/MM/YYYY HH:mm",
        "separator": "-",
        "applyLabel": "Confirmar",
        "cancelLabel": "Cancel",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "Custom",
        "daysOfWeek": [
            "Lunes",
            "Martes",
            "Miércoles",
            "Jueves",
            "Viernes",
            "Sábado",
            "Domingo"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
        "firstDay": 1
    },
    "startDate": "01/01/2018",
    "endDate": "01/31/2018"
}, function(start, end, label) {
  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
});
