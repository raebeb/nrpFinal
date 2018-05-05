
var input = csvCreate.file;
var reader = new FileReader;

input.addEventListener('change', onChange);

function onChange(event) {
  var file = event.target.files[0];

  reader.readAsText(file);

  reader.onload = onLoad;

}

function checkRut(rut) {
  // Despejar Puntos
  var valor = rut.value.replace('.','');
  // Despejar Guión
  valor = valor.replace('-','');

  rut.value = valor;
}

  function onLoad() {
  var result = reader.result;
  var lineas = result.split('\n');
  var i = 0
  var k = 0;
  var m = 0;
  var k2 = 0
  var espacio='';
  var linea='';
  var seccion = 0;

  $("#mytable tbody tr").remove();
  $("#mytable2 tbody tr").remove();
  $("#mytable3 tbody tr").remove();
  $("#mytable4 tbody tr").remove();
  $("#mytable5 tbody tr").remove();
  $("#mytable6 tbody tr").remove();
  $("#mytable7 tbody tr").remove();

  $("#edit-csv-button").html('<button class="btn btn-primary mb-2" type="button" data-toggle="collapse" data-target="#editCSV" aria-expanded="false" aria-controls="editCSV">Editar</button>');

  for(var linea of lineas) {
      var split = linea.split(";");
      if(k2==3){
        espacio=linea;
      }
      k2++;
      if(split[0]!=espacio && linea.includes("#")==false && k2>7){
        if(split[0].includes("SOLICITUD")){
          seccion++;
        }
        if(split[0].includes("VACACIONES")){
          seccion++;
        }
        if(split[0].includes("MEDICAS")){
          seccion++;
        }
        if(split[0].includes("FAMILIAR")){
          seccion++;
        }
        if(split[0].includes("BLOQUEO")){
          seccion++;
        }
        if(split[0].includes("EMBARAZADA")){
          seccion++;
        }
        if(seccion == 0){
          $("#mytable tbody").append('<tr>');
          $("#mytable tbody").append('<td><input class="form-control-plaintext" type="number" value='+split[0]+' name="ID[]"/></td><td><input class="form-control-plaintext" type="text" value='+split[1]+' name="name[]"/></td><td><input class="form-control-plaintext" type="text" value='+split[2]+' name="lastname[]"/></td>');

          if(split[3] == '0'){
            $("#mytable tbody").append('<td><label><input type="checkbox" value="0" name="checkbox-lunes-'+split[0]+'"/><span class="label-text"></span></label></td>');
          }else{
            $("#mytable tbody").append('<td><label><input type="checkbox" value="1" name="checkbox-lunes-'+split[0]+'" checked/><span class="label-text"></span></label></td>');
          }
          

          if(split[4] == '0'){
            $("#mytable tbody").append('<td><label><input type="checkbox" value="0" name="checkbox-martes-'+split[0]+'"/><span class="label-text"></span></label></td>');
          }else{
            $("#mytable tbody").append('<td><label><input type="checkbox" value="1" name="checkbox-martes-'+split[0]+'" checked/><span class="label-text"></span></label></td>');
          }
          

          if(split[5] == '0'){
            $("#mytable tbody").append('<td><label><input type="checkbox" value="0" name="checkbox-miercoles-'+split[0]+'"/><span class="label-text"></span></label></td>>');
          }else{
            $("#mytable tbody").append('<td><label><input type="checkbox" value="1" name="checkbox-miercoles-'+split[0]+'" checked/><span class="label-text"></span></label></td>');
          }
          
          if(split[6] == '0'){
            $("#mytable tbody").append('<td><label><input type="checkbox" value="0" name="checkbox-jueves-'+split[0]+'"/><span class="label-text"></span></label></td>>');
          }else{
            $("#mytable tbody").append('<td><label><input type="checkbox" value="1" name="checkbox-jueves-'+split[0]+'" checked/><span class="label-text"></span></label></td>');
          }

          if(split[7] == '0'){
            $("#mytable tbody").append('<td><label><input type="checkbox" value="0" name="checkbox-viernes-'+split[0]+'"/><span class="label-text"></span></label></td>>');
          }else{
            $("#mytable tbody").append('<td><label><input type="checkbox" value="1" name="checkbox-viernes-'+split[0]+'" checked/><span class="label-text"></span></label></td>');
          }

          if(split[8] == '0'){
            $("#mytable tbody").append('<td><label><input type="checkbox" value="0" name="checkbox-sabado-'+split[0]+'" /><span class="label-text"></span></label></td>');
          }else{
            $("#mytable tbody").append('<td><label><input type="checkbox" value="1" name="checkbox-sabado-'+split[0]+'" checked/><span class="label-text"></span></label></td>');
          }

          if(split[9] == '0'){
            $("#mytable tbody").append('<td><label><input type="checkbox" value="0" name="checkbox-domingo-'+split[0]+'" /><span class="label-text"></span></label></td>');
          }else{
            $("#mytable tbody").append('<td><label><input type="checkbox" value="1" name="checkbox-domingo-'+split[0]+'" checked/><span class="label-text"></span></label></td>');
          }

        if(split[10]=='3'){
          $("#mytable tbody").append('<td><select class="form-control form-control-sm" name="turno_preferido[]" id="turno_preferido"><option value="3">Tercer turno</option><option value="4">Cuarto turno</option></select></td>');
        }else{
          $("#mytable tbody").append('<td><select class="form-control form-control-sm" name="turno_preferido[]" id="turno_preferido"><option value="4">Cuarto turno</option><option value="3">Tercer turno</option></select></td>');
        }  

          $("#mytable tbody").append('<td><select class="form-control-prioridad pull-right" name="prioriedad[]" id="prioriedad"><option value="1">1</option><option value="2" >2</option><option value="3">3</option><option value="4">4</option><option value="5" selected>5</option><option value="6" >6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></td>');

          $("#mytable tbody").append('</tr>');

        }
        if(seccion == 1 && Number(split[0])>0 ){
          var i = 1;
          $("#mytable2 tbody").append('<tr id='+split[0]+'></tr>');
          $('#'+split[0]).append('<input class="form-input rut" type="number" value='+split[0]+' name="ID_Solicitudes[]"/>');
          while (i<8){
            if(Number.isInteger(Number(split[i]))==0){
              split[i]=0;
            }
              $('#'+split[0]).append('<td><input class="form-input request-days" type="number" value='+ Number(split[i])+' name="Dias_Solicitados'+i+'[]"/></td>');
              i++;
          }
        }
        if(seccion == 2 && Number(split[0])>0 ){
          $("#mytable3").append('<tr><td><input class="form-input rut" type="number" value='+split[0]+' name="ID_Vacaciones[]"/></td><td><input class="form-input request-days" type="number" value='+split[1]+' name="Inicio_Vacaciones[]"/></td><td><input class="form-input request-days" type="number" value='+split[2]+' name="Termino_Vacaciones[]"/></td></tr>');
        }
        if(seccion == 3 && Number(split[0])>0 ){
        $("#mytable4 tbody").append('<tr><td><input class="form-input rut" type="number" value='+split[0]+' name="ID_Licencias[]"/></td><td><input class="form-input request-days" type="number" value='+split[1]+' name="Inicio_Licencias[]"/></td><td><input class="form-input request-days" type="number" value='+split[2]+' name="Termino_Licencias[]"/></td></tr>');
        }
        if(seccion == 4 && Number(split[0])>0 ){
          $("#mytable5").append('<tr><td><input class="form-input rut" type="number" value='+split[0]+' name="ID_Luto[]"/></td><td><input class="form-input request-days" type="number" value='+split[1]+' name="Inicio_Luto[]"/></td><td><input class="form-input request-days" type="number" value='+split[2]+' name="Termino_Luto[]"/></td></tr>');
        }
        if(seccion == 5 && Number(split[0])>0){ //bloqueo
          $("#mytable6 tbody").append('<tr><td><input class="form-input rut" type="number" value='+split[0]+' name="ID_Bloqueo[]"/></td><td><input class="form-input request-days" type="number" value='+split[1]+' name="Inicio_Bloqueo[]"/></td><td><input class="form-input request-days" type="number" value='+split[2]+' name="Termino_Bloqueo[]"/></td></tr>');
        }
        if(seccion == 5 && Number(split[0])>0){ // embarazada
          $("#mytable7 tbody").append('<tr><td><input class="form-input rut" type="number" value='+split[0]+' name="ID_Embarazada[]"/><td></tr>');
        }
      }
    }

}

function addFuncionario() {
  $("#mytable tbody").append('<tr><td><input class="rut form-input" type="number" name="ID[]" /></td><td><input class="form-input name" type="text"  name="name[]"/></td><td><input class="form-input lastname" type="text" name="lastname[]"/></td><td><label><input type="checkbox" value="1" name="lunes[]" checked/><span class="label-text"></span></label></td><td><label><input type="checkbox" value="1" name="martes[]" checked/><span class="label-text"></span></label></td><td><label><input type="checkbox" value="1" name="miercoles[]" checked/><span class="label-text"></span></label></td><td><label><input type="checkbox" value="1" name="jueves[]" checked/><span class="label-text"></span></label></td><td><label><input type="checkbox" value="1" name="viernes[]" checked/><span class="label-text"></span></label></td><td><label><input type="checkbox" value="1" name="sabado[]" checked/><span class="label-text"></span></label></td><td><label><input type="checkbox" value="1" name="domingo[]" checked/><span class="label-text"></span></label></td><td><select class="form-control form-control-sm" name="turno_preferido[]" id="turno_preferido[]"><option value="3">Tercer turno</option><option value="4">Cuarto turno</option></select></td><td><select class="form-control-prioridad form-control-prioridad pull-right" name="prioriedad[]" id="prioriedad[]"><option value="1">1</option><option value="2" >2</option><option value="3">3</option><option value="4">4</option><option value="5" selected>5</option><option value="6" >6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></td></tr>');
}

function addEmbarazada() {
    $("#mytable7 tbody").append('<input class="form-input rut" type="number" value='+'0'+' name="ID_Embarazada[]"/></td>');
     
}

function addBloqueo() {
  $("#mytable6 tbody").append('<tr><td><input class="form-input rut" type="number" value='+'0'+' name="ID_Bloqueo[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Inicio_Bloqueo[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Termino_Bloqueo[]"/></td></tr>');
}

function addLuto(){
  $("#mytable5 tbody").append('<tr><td><input class="form-input rut" type="number" value='+'0'+' name="ID_Luto[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Inicio_Luto[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Termino_Luto[]"/></td></tr>');
}

function addLicencia(){
  $("#mytable4 tbody").append('<tr><td><input class="form-input rut" type="number" value='+'0'+' name="ID_Licencia[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Inicio_Licencia[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Fin_Licencia[]"/></td></tr>');
}

function addVacaciones (){
  $("#mytable3 tbody").append('<tr><td><input class="form-input rut" type="number" value='+'0'+' name="ID_Vacaciones[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Inicio_Vacaciones[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Fin_Vacaciones[]"/></td></tr>');
}

function addDiaLibre (){
  $("#mytable2 tbody").append('<tr><td><input class="form-input rut" type="number" value='+'0'+' name="ID_Libre[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Libre1[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Libre2[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Libre3[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Libre4[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Libre5[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Libre6[]"/></td><td><input class="form-input request-days" type="number" value='+'0'+' name="Libre7[]"/></td>/tr>');
}
