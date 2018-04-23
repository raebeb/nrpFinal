<div class="col-lg-8 content-panel">
    <div class="card">
        <div class="card-header">
            <i class="material-icons menu-icon align-text-bottom">event</i> Últimas planificaciones
        </div>
        <div class="card-body align-items-center">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Fecha de creación</th>
                            <th>Servicio</th>
                            <th>Mensual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          @if(auth()->user()->hasRoles(['autor']))
                            @foreach($schedules as $schedule)
                            <tr>
                              <td><a href="{{route('planificar.show', $schedule->id)}}">Ver Planificación</a></td>
                              <td>{{$schedule->created_at}}</td>
                              <td>{{$schedule->service->name}}</td>
                              <td><a href="/storage{{$schedule->file[0]->storage_path}}"><i class="material-icons menu-icon align-text-bottom">file_download</i>Descargar</a></td>
                            </tr>
                            @endforeach
                          @elseif(auth()->user()->hasRoles(['moderador','admin']))
                            @foreach($schedules as $schedule)
                            <tr>
                              <td><a href="{{route('planificar.show', $schedule->id)}}">Ver Planificación</a></td>
                              <td><a href="{{route('usuarios.edit', $schedule->user->id)}}">{{$schedule->user->name.' '.$schedule->user->lastname}}</a></td>
                              <td>{{$schedule->service->name}}</td>
                              <td><a href="/storage{{$schedule->file[0]->storage_path}}"><i class="material-icons menu-icon align-text-bottom">file_download</i>Descargar</a></td>
                            </tr>
                            @endforeach
                          @endif
                    </tbody>
                </table>

                <!-- test code -->
<div class="container">
   <div class="row">
     <div class="col-md-12">
     <!-- or use any other number .col-md- -->
         <div class="table-responsive">
             <div class="table">
             </div>
         </div>
     </div>
   </div>
</div>
                <!-- end test code -->




            </div>
        </div>
    </div>
</div>
