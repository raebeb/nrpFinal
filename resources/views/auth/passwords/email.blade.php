@extends('layouts.template')
@section('title','Restablecer contraseña')
@section('body')
<div class="col-md-12 col-lg-4 my-auto">
  <div class="card">
<div class="card-body">
  @if (session('status'))
      <div class="alert alert-success">
          {{ session('status') }}
      </div>
  @endif
  @if ($errors->has('email'))
      <div class="alert alert-danger">
          {{ $errors->first('email') }}
      </div>
  @endif
    <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}
        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }} col-12">
          <div class="input-group">
            <label for="email" class="col-md-12 control-label text-center">Ingresar dirección de email</label>
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="material-icons login-input">perm_identity</i></span>
            </div>
            <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid':''}}" value="{{ old('email') }}" placeholder="E-mail" required autofocus>
          </div>
        </div>

        <div class="form-group">
          <div class="form-group col-12 text-center">
            <button type="submit" class="btn btn-primary">
              Restablecer contraseña
            </button>
          </div>
        </div>
    </form>
</div>
</div>
</div>
@stop
