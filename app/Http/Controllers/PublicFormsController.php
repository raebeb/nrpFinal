<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicFormsController extends Controller
{	
	public function __construct(){
		$this->middleware('ValidatePublicFormUrl');
	}

    public function show($country, $hospital, $service, $form){
    	$url = ['country' => $country, 'hospital' => $hospital, 'service' => $service, 'form' => $form];
    	return view('manageForms.public.show', compact('url'));
    }

    public function store(Request $request){

    	$route = storage_path().'/app/public/kevin/';
    	$name = $request->input('name');
    	$lastname = $request->input('lastname');
    	$rut = $request->input('rut');
    	$days = $request->input('dias-solicitados');
    	$category = $request->input('categoria');
    	$daterange = $request->input('daterange');
    	$comment = $request->input('comentario');

    	//Kevin Code
		error_reporting(0);

		$aux=""; $o=0;$i=0;$h=0;$date="";$time="";
		//SECCION FUNCIONARIOS
			if(!is_file($route."funcionarios.csv")){
				fopen($route."funcionarios.csv","w");
				$funcionarios = fopen($route."funcionarios.csv", "r+");
				fwrite($funcionarios,"sep=;\n");
				fwrite($funcionarios,"# Esto es un comentario. Comentarios comienzan con # \n");
				fwrite($funcionarios,"# Dias de la semana = LUN, MAR, MIE, JUE, VIE, SAB, o DOM \n\n");
			    fwrite($funcionarios,"LISTADO_DE_ENFERMERAS\n");
			    fwrite($funcionarios,"# FuncionarioID, Nombre, Apellido, Dias de la semana que trabaja (0 no trabajo, 1 si trabaja. Orden: lunes a domingo), Tipo de turno (Solo turno x, cualquier turno 0), Prioridad\n");
			    fwrite($funcionarios,"ID;Nombre;Apellido;LUN;MAR;MIE;JUE;VIE;SAB;DOM;Turno\n");
			    fclose($funcionarios);
			}
			$funcionarios = fopen($route."funcionarios.csv", "r+");
				for($i=0;$i<6;$i++){
					$aux = fgets($funcionarios);
				}
				while(strlen($aux)>1){
					$aux = fgets($funcionarios);
				}
				$dias = array("lunes" => 1,"martes" => 1,"miercoles" => 1,"jueves" => 1,"viernes" => 1,"sabado" => 1,"domingo" => 1);
				if (isset($_POST['lunes'])){
					$dias["lunes"]=0;
				}
				if (isset($_POST['martes'])){
					$dias["martes"]=0;
				}
				if (isset($_POST['miercoles'])){
					$dias["miercoles"]=0;
				}
				if (isset($_POST['jueves'])){
					$dias["jueves"]=0;
				}
				if (isset($_POST['"viernes'])){
					$dias["viernes"]=0;
				}
				if (isset($_POST['sabado'])){
					$dias["sabado"]=0;
				}
				if (isset($_POST['domingo'])){
					$dias["domingo"]=0;
				}
				$contenido = "";
				$contenido = $_POST['rut'].";".$_POST['name'].";".$_POST['lastname'].";".$dias["lunes"].";".$dias["martes"].";".$dias["miercoles"].";".$dias["jueves"].";".$dias["viernes"].";".$dias["sabado"].";".$dias["domingo"].";"."0".";"."5"."\n";
				fwrite($funcionarios,$contenido);
				fclose($funcionarios);

		//SECCION DIAS SOLICITADOS
			if(!is_file($route."dias_solicitados.csv")){
					fopen($route."dias_solicitados.csv","w");
					$dias_solicitados = fopen($route."dias_solicitados.csv", "r+");
			        fwrite($dias_solicitados,"DIAS_LIBRES_POR_SOLICITUD_APROBADA\n");
			        fwrite($dias_solicitados,"# FuncionarioID, Dia del mes libre (separados por punto y coma)\n");
					fclose($dias_solicitados);
				}
				$dias_solicitados = fopen($route."dias_solicitados.csv", "r+");
					for($i=0;$i<1;$i++){
						$aux = fgets($dias_solicitados);
					}
					while(strlen($aux)>1){
						$aux = fgets($dias_solicitados);
					}

					$contenido = "";
					if(!strcmp($_POST['dias-solicitados'] ,"")==0 ) {
						$o=0;
						$h=0;
						$contenido=$contenido.$_POST['rut'];
						do{
							$time = "";
							for ($o=$o; $o<=($h+9);$o++){
								$time = $time.$_POST['dias-solicitados'][$o];
							}
							$o++;
							$h=$o;
							$date = date_create_from_format("d/m/Y", $time);
							$contenido = $contenido.";".date_format($date, "d");
							}while(!strcmp(($_POST['dias-solicitados'][$o]),"") == null );
					fwrite($dias_solicitados,$contenido."\n");
					}
					fclose($dias_solicitados);
		//SECCION VACACIONES
				if(!is_file($route."vacaciones.csv")){
					fopen($route."vacaciones.csv","w");
					$vacaciones = fopen($route."vacaciones.csv", "r+");
			        fwrite($vacaciones,"DIAS_DE_VACACIONES_POR_FUNCIONARIO\n");
			        fwrite($vacaciones,"# FuncionarioID, Dia inicial (0 si inicio se da en mes anterior), Dia termino (0 si termino se da en mes posterior)\n");
					fclose($vacaciones);
				}
				$vacaciones = fopen($route."vacaciones.csv", "r+");
					for($i=0;$i<1;$i++){
						$aux = fgets($vacaciones);
					}
					while(strlen($aux)>1){
						$aux = fgets($vacaciones);
					}
					$contenido="";
						if($_POST['categoria']==2){
							$o=0;
							$h=0;
							$contenido=$contenido.$_POST['rut'];
							$time = "";
							for ($o=$o; $o<=($h+9);$o++){
								$time = $time.$_POST['daterange'][$o];
							}
							$o++;
							$h=$o;
							$date = date_create_from_format("d/m/Y", $time);
							$contenido = $contenido.";".date_format($date, "d");
							$time = "";
							for ($o=$o; $o<=($h+9);$o++){
								$time = $time.$_POST['daterange'][$o];
							}
							$o++;
							$h=$o;
							$date = date_create_from_format("d/m/Y", $time);
							$contenido = $contenido.";".date_format($date, "d");
							fwrite($vacaciones,$contenido."\n");
						}
						fclose($vacaciones);

		//SECCION LICENCIAS
				if(!is_file($route."licencias.csv")){
					fopen($route."licencias.csv","w");
					$licencias = fopen($route."licencias.csv", "r+");
			        fwrite($licencias,"DIAS_LIBRES_POR_RAZONES_MEDICAS\n");
			        fwrite($licencias,"# FuncionarioID, Dia inicial (0 si inicio se da en mes anterior), Dia termino (0 si termino se da en mes posterior)\n");
					fclose($licencias);
				}
				$licencias = fopen($route."licencias.csv", "r+");
					for($i=0;$i<1;$i++){
						$aux = fgets($licencias);
					}
					while(strlen($aux)>1){
						$aux = fgets($licencias);
					}
					$contenido="";
						if($_POST['categoria']==3){
							$o=0;
							$h=0;
							$contenido=$contenido.$_POST['rut'];
							$time = "";
							for ($o=$o; $o<=($h+9);$o++){
								$time = $time.$_POST['daterange'][$o];
							}
							$o++;
							$h=$o;
							$date = date_create_from_format("d/m/Y", $time);
							$contenido = $contenido.";".date_format($date, "d");
							$time = "";
							for ($o=$o; $o<=($h+9);$o++){
								$time = $time.$_POST['daterange'][$o];
							}
							$o++;
							$h=$o;
							$date = date_create_from_format("d/m/Y", $time);
							$contenido = $contenido.";".date_format($date, "d");
							fwrite($licencias,$contenido."\n");
						}
						fclose($licencias);

		//SECCION LUTO
				if(!is_file($route."luto.csv")){
					fopen($route."luto.csv","w");
					$luto = fopen($route."luto.csv", "r+");
			        fwrite($luto,"DIAS_LIBRES_POR_FALLECIMIENTO_DE_FAMILIAR\n");
			       	fwrite($luto,"# FuncionarioID, Dia inicial (0 si inicio se da en mes anterior), Dia termino (0 si termino se da en mes posterior)\n");
					fclose($luto);
				}
				$luto = fopen($route."luto.csv", "r+");
					for($i=0;$i<1;$i++){
						$aux = fgets($luto);
					}
					while(strlen($aux)>1){
						$aux = fgets($luto);
					}
					$contenido="";
						if($_POST['categoria']==4){
							$o=0;
							$h=0;
							$contenido=$contenido.$_POST['rut'];
							$time = "";
							for ($o=$o; $o<=($h+9);$o++){
								$time = $time.$_POST['daterange'][$o];
							}
							$o++;
							$h=$o;
							$date = date_create_from_format("d/m/Y", $time);
							$contenido = $contenido.";".date_format($date, "d");
							$time = "";
							for ($o=$o; $o<=($h+9);$o++){
								$time = $time.$_POST['daterange'][$o];
							}
							$o++;
							$h=$o;
							$date = date_create_from_format("d/m/Y", $time);
							$contenido = $contenido.";".date_format($date, "d");
							fwrite($luto,$contenido."\n");
						}
						fclose($luto);

		//SECCION BLOQUEOS
				if(!is_file($route."bloqueos.csv")){
					fopen($route."bloqueos.csv","w");
					$bloqueos = fopen($route."bloqueos.csv", "r+");
			        fwrite($bloqueos,"PERIODO_DE_BLOQUEO_DE_FUNCIONARIO\n");
			        fwrite($bloqueos,"# FuncionarioID, Dia inicial (0 si inicio se da en mes anterior), Dia termino (0 si termino se da en mes posterior)\n");
					fclose($bloqueos);
				}
				$bloqueos = fopen($route."bloqueos.csv", "r+");
					for($i=0;$i<1;$i++){
						$aux = fgets($bloqueos);
					}
					while(strlen($aux)>1){
						$aux = fgets($bloqueos);
					}
					fclose($bloqueos);
		//SECCION EMBARAZADAS
				if(!is_file($route."embarazadas.csv")){
					fopen($route."embarazadas.csv","w");
					$embarazadas = fopen($route."embarazadas.csv", "r+");
			        fwrite($embarazadas,"FUNCIONARIA_EMBARAZADA\n");
			        fwrite($embarazadas,"# FuncionarioID de aquella funcionaria que se encuentra embarazada\n");
					fclose($embarazadas);
				}
				$embarazadas = fopen($route."embarazadas.csv", "r+");
					for($i=0;$i<1;$i++){
						$aux = fgets($embarazadas);
					}
					while(strlen($aux)>1){
						$aux = fgets($embarazadas);
					}
					fclose($embarazadas);
		//GENERACION ARCHIVO RESPUESTAS
			$contenido = "";
			$file = fopen($route."funcionarios.csv","r");
			while(!feof($file)){
				$contenido = $contenido.fgets($file);
			}
			$contenido=$contenido."\n";
			fclose($file);
			$file = fopen($route."dias_solicitados.csv","r");
			while(!feof($file)){
				$contenido = $contenido.fgets($file);
			}
			$contenido=$contenido."\n";
			fclose($file);
			$file = fopen($route."vacaciones.csv","r");
			while(!feof($file)){
				$contenido = $contenido.fgets($file);
			}
			$contenido=$contenido."\n";
			fclose($file);
			$file = fopen($route."licencias.csv","r");
			while(!feof($file)){
				$contenido = $contenido.fgets($file);
			}
			$contenido=$contenido."\n";
			fclose($file);
			$file = fopen($route."luto.csv","r");
			while(!feof($file)){
				$contenido = $contenido.fgets($file);
			}
			$contenido=$contenido."\n";
			fclose($file);
			$file = fopen($route."bloqueos.csv","r");
			while(!feof($file)){
				$contenido = $contenido.fgets($file);
			}
			$contenido=$contenido."\n";
			fclose($file);
			$file = fopen($route."embarazadas.csv","r");
			while(!feof($file)){
				$contenido = $contenido.fgets($file);
			}
			fclose($file);
			$file = fopen($route."Respuestas.csv", "w+");
			fwrite($file, $contenido);
			fclose($file);
    	//End Kevin Code







    	return redirect('login')->with('form', 'Su solicitud fue enviada correctamente');
    }
}
