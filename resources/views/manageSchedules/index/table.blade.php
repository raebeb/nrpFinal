@extends('layouts.app')
@section('title', 'Historial CSV')
@section('content')
<div class="col-lg-12 content-panel">
    <div class="card">
        <div class="card-header">
            <i class="material-icons menu-icon align-text-bottom">history</i> @yield('title')
        </div>
        <div class="card-body align-items-center">
          <div class="table-responsive">
            @if(auth()->user()->hasRoles(['autor']))
              <table id="csv-history" class="display table  table-hover" cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th>Archivo</th>
                              <th>Autor</th>
                              <th>Fecha de creación</th>
                              <th>Mensual</th>
                          </tr>
                      </thead>
                      <tbody>
                        @foreach($schedules as $schedule)
                          <tr>
                              <td><a href="{{ route('planificar.show', $schedule->id)}}">Ver Planificación</a></td>
                              <td>{{ $schedule->user->name.' '.$schedule->user->lastname}}</td>
                              <td>{{ $schedule->created_at }}</td>
                              <td><a href="/storage{{$schedule->file[0]->storage_path}}"><i class="material-icons menu-icon align-text-bottom">file_download</i>Descargar</a></td>
                          </tr>
                        @endforeach
                      </tbody>
                  </table>
              @endif
              @if(auth()->user()->hasRoles(['moderador','admin']))
                <table id="csv-history" class="display table  table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Archivo</th>
                                <th>Autor</th>
                                <th>Fecha de creación</th>
                                <th>Servicio</th>
                                <th>Mensual</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach($schedules as $schedule)
                            <tr>
                                <td><a href="{{ route('planificar.show', $schedule->id)}}">Ver Planificación</a></td>
                                <td><a href="{{ route('usuarios.edit', $schedule->user->id) }}">{{ $schedule->user->name.' '.$schedule->user->lastname}}</a></td>
                                <td>{{ $schedule->created_at }}</td>
                                <td>{{ $schedule->service->name }}</td>
                                <td><a href="/storage{{$schedule->file[0]->storage_path}}"><i class="material-icons menu-icon align-text-bottom">file_download</i>Descargar</a></td>
                            </tr>
                          @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
