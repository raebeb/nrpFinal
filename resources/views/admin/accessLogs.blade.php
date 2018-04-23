@extends('../layouts.app')
@section('title', 'Log acceso')
@section('content')
<div class="col-12 content-panel">
    <div class="card">
        <div class="card-header">
            <i class="material-icons menu-icon align-text-bottom">description</i> @yield('title')
        </div>
        <div class="card-body align-items-center">
                <div class="table-responsive">
                        <table id="access_log" class="display table  table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($accessLogs as $access)
                                    <tr>
                                        <td>{{ $access->id }}</td>
                                        <td><a href="{{route('usuarios.edit', $access->user->id )}}">{{ $access->user->name .' '. $access->user->lastname }}</a></td>
                                        <td>{{ $access->login }}</td>
                                        <td>{{ $access->ip_address }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
        </div>
    </div>
</div>
@stop
