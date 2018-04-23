@extends('layouts.app')
@section('title', 'Editar usuario')
@section('content')
<div class="col-12 content-panel">
<div class="card">
    <div class="card-header">
        <i class="material-icons menu-icon align-text-bottom">supervisor_account</i> @yield('title') {{ $user->name}}
    </div>
    <div class="card-body">
      @if( session()->has('info') )
      <div class="alert alert-success" role="alert">
        {{ session('info') }}
      </div>
      @endif
      <form method="POST" action="{{ route('usuarios.update', $user->id) }}" >
        {!! method_field('PUT') !!}
        {!! csrf_field() !!}
          <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid':''}}" name="name" id="name" value="{{ $user->name }}">
            {!! $errors->first('name','<div class="invalid-feedback">:message</div>') !!}
          </div>
          <div class="form-group">
              <label for="lastname">Apellido</label>
              <input type="lastname" name="lastname" class="form-control {{ $errors->has('lastname') ? 'is-invalid':''}}" id="lastname" value="{{ $user->lastname }}">
              {!! $errors->first('lastname','<div class="invalid-feedback">:message</div>') !!}
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid':''}}" name="email" id="email" value="{{ $user->email }}">
            {!! $errors->first('email','<div class="invalid-feedback">:message</div>') !!}
          </div>
          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña">
            <small class="text-muted">La contraseña debe contener más de 6 caracteres</small>
            {!! $errors->first('password','<div class="invalid-feedback">:message</div>') !!}
          </div>
          <div class="form-group">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirmar ontraseña">
            {!! $errors->first('password_confirmation','<div class="invalid-feedback">:message</div>') !!}
          </div>

          @if(auth()->user()->isAdmin() && auth()->user()->id != $user->id)
            @include('manageUsers.edit.admin')
          @endif
          <input type="submit" class="btn btn-primary" value="Aplicar">
      </form>
    </div>
</div>
</div>
@stop
