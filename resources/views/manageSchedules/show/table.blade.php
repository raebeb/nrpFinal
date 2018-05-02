@extends('layouts.app')
@section('title', 'Planificación')
@section('content')
<div class="col-lg-12 content-panel">
    <div class="card">
        <div class="card-header">
            <i class="material-icons menu-icon align-text-bottom">history</i> @yield('title')
        </div>
        <div class="card-body align-items-center">
          <div class="table-responsive">
              <table id="csv-history" class="display table  table-hover" cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th>Nombre </th>
                              <th>Apellido</th>
                              <th>Rut</th>
                              <th>Archivo</th>
                          </tr>
                      </thead>
                      <tbody>
                        @foreach($files as $file)
                          <tr>
                              <td>{{ $file->name_receiver }}</td>
                              <td>{{ $file->lastname_receiver }}</td>
                              <td>{{ $file->receiver}}</td>
                              <td><a href="/storage/{{$file->storage_path}}"><i class="material-icons menu-icon align-text-bottom">file_download</i>{{ $file->file_name }}</a></td>
                          </tr>
                        @endforeach
                      </tbody>
                  </table>
              </div>
        </div>
    </div>
</div>
@stop
