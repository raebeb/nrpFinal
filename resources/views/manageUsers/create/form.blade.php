@extends('layouts.app')
@section('title', 'Agregar usuario')
@section('content')
<div class="col-12 content-panel">
<div class="card">
    <div class="card-header">
        <i class="material-icons menu-icon align-text-bottom">supervisor_account</i> @yield('title')
    </div>
    <div class="card-body">
<form method="POST" action="{{ route('usuarios.store') }}">
    {{ csrf_field() }}
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid':''}}" id="name" placeholder="John" value="{{ old('name') }}">
            {!! $errors->first('name','<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            <label for="lastname">Apellido</label>
            <input type="lastname" name="lastname" class="form-control {{ $errors->has('lastname') ? 'is-invalid':''}}" id="lastname" placeholder="Doe" value="{{ old('lastname') }}">
            {!! $errors->first('lastname','<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid':''}}" id="email" placeholder="Email" value="{{ old('email') }}">
            {!! $errors->first('email','<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid':''}}" id="password" placeholder="Contraseña">
            {!! $errors->first('password','<div class="invalid-feedback">:message</div>') !!}
            <small class="text-muted">La contraseña debe ser de al menos de 6 caracteres.</small>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid':''}}" id="password_confirmation" placeholder="Confirmar contraseña">
            {!! $errors->first('password_confirmation','<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            <label for="rol">Rol</label>
            <select id="rol" name="rol" class="form-control {{ $errors->has('rol') ? 'is-invalid':''}}">
              <option value="0" selected>Asignar un rol...</option>
              @foreach($roles as $rol)
                <option value="{{$rol->id}}">{{$rol->display_name}}</option>
              @endforeach

            </select>
            {!! $errors->first('rol','<div class="invalid-feedback">:message</div>') !!}
        </div>
    <button type="submit" class="btn btn-primary">Agregar</button>
</form>
    </div>
</div>
</div>
@stop
