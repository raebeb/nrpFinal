@extends('layouts.app')
@section('title', 'Gestionar usuarios')
@section('content')
<div class="col-12 content-panel">
<div class="card">
    <div class="card-header">
        <i class="material-icons menu-icon align-text-bottom">supervisor_account</i> @yield('title')
    </div>
    <div class="card-body">
      @if( session()->has('info') )
      <div class="alert alert-success" role="alert">
        {{ session('info') }}
      </div>
      @endif
        <div class="table-responsive">
            <table id="manage_users" class="display table table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>E-mail</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>
                            @if( $user->status === 1)
                                <i style="color: #dc3545;" class="material-icons">lock</i>
                            @else
                                <i style="color: #28a745;" class="material-icons">lock_open</i>
                            @endif
                        </td>
                        <td>{{ $user->name  .' '. $user->lastname }}</td>
                        <td>{{ $user->role->display_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><a href="{{ route('usuarios.edit', $user->id) }}"><i class="material-icons">mode_edit</i></a></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><a href="{{ route('usuarios.create') }}"><i class="material-icons align-text-bottom">add_circle</i> Agregar usuario</a> </th>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</div>
@stop
