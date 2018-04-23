@extends('layouts.template')
@section('title', 'Formulario')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css') }}">
@stop

@section('body')
<div class="col-md-12 col-lg-8 my-auto">
<div class="card" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="card-header">
        <i class="material-icons menu-icon align-text-bottom">description</i> @yield('title')
    </div>
    <div class="card-body align-items-center">
        <form method="POST" action="/{{ $url['country'].'/'.$url['hospital'].'/'.$url['service'].'/'.$url['form'] }}">
            <div class="form-row">
            	{{ csrf_field() }}
                <div class="form-group col-md-12">
                    <strong><label for="name">Nombre</label></strong>
                    <input type="text" class="form-control" placeholder="Jhon" name="name" id="name">
                </div>

                <div class="form-group col-md-12">
                    <strong><label for="name">Apellido</label></strong>
                    <input type="text" class="form-control" placeholder="Doe" name="lastname" id="lastname">
                </div>
                
                <div class="form-group col-md-12">
                    <strong><label for="rut">Rut</label></strong>
                    <input type="number" class="form-control" placeholder="11.111.111-1" name="rut" id="rut">
                    <small id="passwordHelpBlock" class="form-text text-muted">
						                       No debe contener puntos ni guion. si termina en k reemplazelo por un 1
					                  </small>
                </div>

                <div class="form-group date col-md-12">
                    <strong><label for="dias-solicitados">Ingrese día libre que desea solicitar</label></strong>
                    <input type="text" class="form-control" data-provide="datepicker" name="dias-solicitados" id="dias-solicitados">
                </div>

                <div class="form-group col-md-12">
                    <strong><label for="categoria">Si desea informar sobre algun incidente, seleccione una categoria</label></strong>
                    <select class="form-control" name="categoria" id="categoria">
                    <option value="1">Ninguno</option>
                    <option value="2">Vacaciones</option>
                    <option value="3">Licencia Medica</option>
                    <option value="4">Fallecimiento de Familiar</option>
                    </select>
                  </div>

                <div class="form-group col-md-12">
                  <strong><label for="daterange">Ingrese rango de fechas</label></strong>
                  <input type="text" class="form-control" name="daterange" id="daterange"/>
                </div>

                <strong><label>Ingrese los dias que NO puede trabajar</label></strong>
                <div class="form-group col-md-12">
                    <div class="form-check form-check-inline">
                      <label><input type="checkbox" name="lunes" id="lunes" value="1">Lunes</label>
                    </div>

                    <div class="form-check form-check-inline">
                      <label><input type="checkbox" id="martes" value="1">Martes</label>
                    </div>

                    <div class="form-check form-check-inline">
                      <label><input type="checkbox" id="miercoles" value="1">Miércoles</label>
                    </div>

                    <div class="form-check form-check-inline">
                      <label><input type="checkbox" id="jueves" value="1">Jueves</label>
                    </div>

                    <div class="form-check form-check-inline">
                      <label><input type="checkbox" id="viernes" value="1">Viernes</label>
                    </div>

                     <div class="form-check form-check-inline">
                      <label><input type="checkbox" id="sabado" value="1">Sábado</label>
                    </div>

                    <div class="form-check form-check-inline">
                      <label><input type="checkbox" id="domingo" value="1">Domingo</label>
                    </div>
                    <small id="testing" class="form-text text-muted">
                            Si puede trabajar todos los dias deje las casillas vacias
                    </small>
                  </div>

                <div class="form-group col-md-12">
                    <strong><label for="comentario">Aqui puede ingresar comentarios</label></strong>
                    <input type="text" class="form-control" name="comentario" id="comentario">
                    <small id="testing" class="form-text text-muted">
                        Razones de sus solicitudes, turno preferido, situacion de salud delicada, embarazo, etc.
                    </small>
                </div>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </div>

        </form>
    </div>
</div>
</div>
@stop

@section('scripts')
	<script src="{{ asset('js/jquery-3.2.1.slim.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
	<script src="{{ asset('js/public-forms.js') }}"></script>
	<script src="{{ asset('js/moment.js') }}"></script>
	<script src="{{ asset('js/daterangepicker.js') }}"></script>
@stop