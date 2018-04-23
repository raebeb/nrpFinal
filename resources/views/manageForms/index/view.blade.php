@extends('layouts.app')
@section('title','Formularios')
@section('content')
<div class="col-lg-12 content-panel">
    <div class="card">
        <div class="card-header">
            <i class="material-icons menu-icon align-text-bottom">event</i> @yield('title')
        </div>
        <div class="card-body align-items-center">
            <div class="table-responsive">
                <table class="table table-hover">
				  <thead>
				    <tr>
				      <th scope="col">#</th>
				      <th scope="col">Formulario</th>
				      <th scope="col">Respuestas</th>
				      <th scope="col">Acción</th>
				    </tr>
				  </thead>
				  <tbody>
				    <tr>
				      <th scope="row">1</th>
				      <td><a href="/1/2/1/4/create">Ver formulario</a></td>
				      <td><a href="/storage/kevin/Respuestas.csv">Descargar Respuestas</a></td>
				      <td></td>
				    </tr>
				  </tbody>
				</table>
            </div>
        </div>
    </div>
</div>

@stop