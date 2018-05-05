<?php

namespace App\Listeners;

use App\Events\CsvWasReceived;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StorageCsv
{
    /**
     * Handle the event.
     *
     * @param  CsvWasReceived  $event
     * @return void
     */
    public function handle(CsvWasReceived $event)
    {
    $respuestas=fopen($event->path,"w");
    fwrite($respuestas,"sep=;\n");
    fwrite($respuestas,"# Esto es un comentario. Comentarios comienzan con # \n");
    fwrite($respuestas,"# Dias de la semana = LUN, MAR, MIE, JUE, VIE, SAB, o DOM \n\n");
    fwrite($respuestas,"LISTADO_DE_ENFERMERAS\n");
    fwrite($respuestas,"# FuncionarioID, Nombre, Dias de la semana que trabaja (0 no trabajo, 1 si trabaja. Orden: lunes a domingo), Tipo de turno (Solo turno x, cualquier turno 0), Prioridad\n");
    fwrite($respuestas,"ID;Nombre;Apellido;LUN;MAR;MIE;JUE;VIE;SAB;DOM;Turno\n");
    $i = 0;$y=0;

    for($i = 0; $i < sizeof($event->request->input('ID')) ; $i++){
        fwrite($respuestas,$event->request->input('ID')[$i].";".$event->request->input('name')[$i].";".$event->request->input('lastname')[$i].";");

        $lunes = $event->request->input('checkbox-lunes-'.$event->request->input('ID')[$i]);
        $martes = $event->request->input('checkbox-martes-'.$event->request->input('ID')[$i]);
        $miercoles = $event->request->input('checkbox-miercoles-'.$event->request->input('ID')[$i]);
        $jueves = $event->request->input('checkbox-jueves-'.$event->request->input('ID')[$i]);
        $viernes = $event->request->input('checkbox-viernes-'.$event->request->input('ID')[$i]);
        $sabado = $event->request->input('checkbox-sabado-'.$event->request->input('ID')[$i]);
        $domingo = $event->request->input('checkbox-domingo-'.$event->request->input('ID')[$i]);

        if( isset($lunes)){
            fwrite($respuestas, '1' . ';');
        }else{
            fwrite($respuestas, '0' . ';');
        }

        if( isset($martes)){
            fwrite($respuestas, '1' . ';');
        }else{
            fwrite($respuestas, '0' . ';');
        }
        if( isset($miercoles)){
            fwrite($respuestas, '1' . ';');
        }else{
            fwrite($respuestas, '0' . ';');
        }
        if( isset($jueves)){
            fwrite($respuestas, '1' . ';');
        }else{
            fwrite($respuestas, '0' . ';');
        }
        if( isset($viernes)){
            fwrite($respuestas, '1' . ';');
        }else{
            fwrite($respuestas, '0' . ';');
        }
        if( isset($sabado)){
            fwrite($respuestas, '1' . ';');
        }else{
            fwrite($respuestas, '0' . ';');
        }
        if( isset($domingo)){
            fwrite($respuestas, '1' . ';');
        }else{
            fwrite($respuestas, '0' . ';');
        }
                fwrite($respuestas,$event->request->input('turno_preferido')[$i].";");

                fwrite($respuestas,$event->request->input('prioriedad')[$i]."\n");

    }
    fwrite($respuestas,"\n");
    fwrite($respuestas,"DIAS_LIBRES_POR_SOLICITUD_APROBADA\n");
    fwrite($respuestas,"# FuncionarioID, Dia del mes libre (separados por punto y coma)\n");
    for($i = 0; $i < sizeof($event->request->input('ID_Solicitudes')) ; $i++){
        fwrite($respuestas,$event->request->input('ID_Solicitudes')[$i]);
        for ($y = 1; $y < 8 ; $y++){
            if($event->request->input('Dias_Solicitados'.$y)[$i]>0){
                fwrite($respuestas,";".$event->request->input('Dias_Solicitados'.$y)[$i]);
            }
        }
        fwrite($respuestas,"\n");
    }
    fwrite($respuestas,"\nDIAS_DE_VACACIONES_POR_FUNCIONARIO\n# FuncionarioID, Dia inicial (0 si inicio se da en mes anterior), Dia termino (0 si termino se da en mes posterior)\n");
    for($i = 0; $i < sizeof($event->request->input('ID_Vacaciones')) ; $i++){
        if($event->request->input('ID_Vacaciones')[$i]>0){
            fwrite($respuestas,$event->request->input('ID_Vacaciones')[$i].";".$event->request->input('Inicio_Vacaciones')[$i].";".$event->request->input('Termino_Vacaciones')[$i]."\n");
        }
    }
    fwrite($respuestas,"\nDIAS_LIBRES_POR_RAZONES_MEDICAS
# FuncionarioID, Dia inicial (0 si inicio se da en mes anterior), Dia termino (0 si termino se da en mes posterior)\n");
    for($i = 0; $i < sizeof($event->request->input('ID_Licencias')) ; $i++){
        if($event->request->input('ID_Licencias')[$i]>0){
            fwrite($respuestas,$event->request->input('ID_Licencias')[$i].";".$event->request->input('Inicio_Licencias')[$i].";".$event->request->input('Termino_Licencias')[$i]."\n");
        }
    }

    fwrite($respuestas,"\nDIAS_LIBRES_POR_FALLECIMIENTO_DE_FAMILIAR
# FuncionarioID, Dia inicial (0 si inicio se da en mes anterior), Dia termino (0 si termino se da en mes posterior)\n");
    for($i = 0; $i < sizeof($event->request->input('ID_Luto')) ; $i++){
        if($event->request->input('ID_Luto')[$i]>0){
            fwrite($respuestas,$event->request->input('ID_Luto')[$i].";".$event->request->input('Inicio_Luto')[$i].";".$event->request->input('Termino_Luto')[$i]."\n");
        }
    }

fwrite($respuestas,"\nPERIODO_DE_BLOQUEO_DE_FUNCIONARIO
# FuncionarioID, Dia inicial (0 si inicio se da en mes anterior), Dia termino (0 si termino se da en mes posterior)\n");
if($event->request->has('ID_Bloqueo'))
{
    for($i = 0; $i < sizeof($event->request->input('ID_Bloqueo')) ; $i++){
        if($event->request->input('ID_Bloqueo')[$i]>0){
            fwrite($respuestas,$event->request->input('ID_Bloqueo')[$i].";".$event->request->input('Inicio_Bloqueo')[$i].";".$event->request->input('Termino_Bloqueo')[$i]."\n");
        }
    }
}
    fwrite($respuestas,"\nFUNCIONARIA_EMBARAZADA
# FuncionarioID de aquella funcionaria que se encuentra embarazada\n");
    if($event->request->has('ID_Embarazada'))
    {
            for($i = 0; $i < sizeof($event->request->input('ID_Embarazada')) ; $i++){
                if($event->request->input('ID_Embarazada')[$i]>0){
                fwrite($respuestas,$event->request->input('ID_Embarazada')[$i]."\n");
                }
            }
    }
    }
}



