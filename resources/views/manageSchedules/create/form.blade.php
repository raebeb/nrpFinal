@extends('layouts.app')
@section('title', 'Planificar')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/editCsvForm.css') }}">
@stop

@section('content')
<div class="col-lg-12 content-panel">
    <div class="card">
        <div class="card-header">
            <i class="material-icons menu-icon align-text-bottom">event</i> @yield('title')
        </div>
        <div class="card-body">
            @if($errors->all())
                <div class="alert alert-danger" role="alert">
                    <ul>
                    @foreach($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif
            @if(session()->has('info'))
            <div class="alert alert-success text-center" role="alert">
              Se ha generado una nueva planificación. <a href="{{ route('planificar.show', session('info'))}}">Ver Planificación</a>
            </div>
            @elseif(session()->has('danger'))
            <div class="alert alert-danger text-center" role="alert">
              {{session('danger')}}
            </div>
            @endif

            {!!$errors->first('file', '<div class="alert alert-danger" role="alert">:message</div>')!!}
            
            <form name="csvCreate" method="POST" action="{{ route('planificar.store') }}" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <div class="form-group form-inline justify-content-center">
                    <input type="file" name="file" class="form-control mb-2" accept = '.csv' required>
                    <div id="edit-csv-button"></div>
                </div>

            <!-- collapse button -->
                
                
                <div class="collapse" id="editCSV">
                  <div class="card-body">
                    <!-- kevin code -->
                    @include('manageSchedules.create.edit')
                    <!-- end kevin code -->
                  </div>
                </div>

            <!-- end collapse button-->
            <div class="text-center">
                <button type="submit" class="btn btn-primary mb-2 col-3">Subir CSV</button>
            </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script src="{{ asset('js/d3.v3.min.js') }}"></script>
<script src="{{ asset('js/editCsvForm.js') }}"></script>
@stop
