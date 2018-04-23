@extends('layouts.template')
@section('title', 'Restablecer contraseña')
@section('body')
<div class="col-md-12 col-lg-4 my-auto">
  <div class="card">
<div class="card-header">@yield('title')</div>
<div class="card-body">
  <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
      {{ csrf_field() }}
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }} col-12">
        <div class="input-group">
          <label for="email" class="col-md-12 control-label">E-mail</label>
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="material-icons login-input">perm_identity</i></span>
          </div>
          <input id="email" type="email" class="form-control {{ $errors->has('email') ? 'is-invalid':''}}" name="email" value="{{ $email or old('email') }}" required autofocus>
          @if ($errors->has('email'))
            <div class="invalid-feedback">
              {{ $errors->first('email') }}
            </div>
          @endif
        </div>
      </div>

      <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} col-12">
        <div class="input-group">
          <label for="password" class="col-md-12 control-label">Nueva contraseña</label>
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="material-icons login-input">lock_outline</i></span>
          </div>
          <input id="password" type="password" class="form-control {{ $errors->has('password') ? 'is-invalid':''}}" name="password" required>
          <small class="text-muted">La contraseña debe contener más de 6 caracteres</small>
          @if ($errors->has('password'))
            <div class="invalid-feedback">
              {{ $errors->first('password') }}
            </div>
          @endif
        </div>
      </div>

      <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }} col-12">
        <div class="input-group">
          <label for="password_confirmation" class="col-md-12 control-label">Confirmar nueva contraseña</label>
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="material-icons login-input">lock_outline</i></span>
          </div>
          <input id="password_confirmation" type="password" class="form-control {{ $errors->has('password') ? 'is-invalid':''}}" name="password_confirmation" required>
          @if ($errors->has('password_confirmation'))
            <div class="invalid-feedback">
              {{ $errors->first('password_confirmation') }}
            </div>
          @endif
        </div>
      </div>

      <div class="form-group">
          <div class="col-md-12 text-center">
              <button type="submit" class="btn btn-primary">
                  Cambiar contraseña
              </button>
          </div>
      </div>
  </form>
</div>
</div>
</div>
@stop
