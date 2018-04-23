<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:500" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <title>NRP - Iniciar sesión</title>
</head>
<body class="background">
    <div class="container login-panel">
        <div class="row h-100 justify-content-center">
            <div class="col-md-12 col-lg-4 my-auto">
                @if(session()->has('form'))
                    <div class="alert alert-success" role="alert">
                        {{ session('form') }}
                    </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        <div class="text-center"><i class="material-icons login-profile">account_circle</i></div>
                        @if($errors->has('email') || $errors->has('password'))
                            <div class="alert alert-danger" role="alert">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                        @if( session()->has('info') )
                        <div class="alert alert-danger" role="alert">
                            {{ session('info') }}
                        </div>
                        @endif
                        <form method="POST" action="{{ route('login') }}">
                            {!! csrf_field() !!}
                            <div class="form-group col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="material-icons login-input">perm_identity</i></span>
                                    </div>
                                    <input type="text" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid':''}}" value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="material-icons login-input">lock_outline</i></span>
                                    </div>
                                    <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid':''}}" required>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="remember" class="custom-control-input" id="remember">
                                    <label class="custom-control-label" for="remember">Recordar usuario</label>
                                </div>
                            </div>

                            <div class="form-group col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                            </div>
                        </form>
                        <div class="text-center"><a href="{{ route('password.request') }}">¿Olvidó su contraseña?</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
