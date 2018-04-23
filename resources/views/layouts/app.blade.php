<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:500" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dataTables.bootstrap4.min.css')}}">
    @yield('styles')
    <title>NRP - @yield('title')</title>
</head>
<body class="background-home">
      <div id="wrap">
        <nav class="navbar navbar-expand-lg navbar-dark bg-navbar">
                <div class="container">
                        <a class="navbar-brand" href="#">NRP</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                          <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarText">
                          <ul class="navbar-nav mr-auto">
                            <li class="nav-item {{ request()->is('home') ? 'active':''}}">
                              <a class="nav-link" href="{{ route('inicio') }}"><i class="material-icons menu-icon align-text-bottom">home</i> Inicio <span class="sr-only">(current)</span></a>
                            </li>
                            @if(auth()->check())
                                @if(auth()->user()->hasRoles(['autor',]))
                                  <li class="nav-item {{ request()->is('planificar/create') ? 'active':''}}">
                                    <a class="nav-link" href="{{ route('planificar.create') }}"><i class="material-icons menu-icon align-text-bottom">event</i> Planificar</a>
                                  </li>
                                  <!-- <li class="nav-item {{ request()->is('formularios') ? 'active':''}}">
                                    <a class="nav-link" href="{{ route('formularios.index') }}"><i class="material-icons menu-icon align-text-bottom">event</i> Formularios <span class="badge badge-dark">Inestable</span></a>
                                  </li> -->
                                @endif
                            @endif

                            @if(auth()->check())
                                @if(auth()->user()->hasRoles(['admin','moderador','autor']))
                                  <li class="nav-item {{ request()->is('planificar') ? 'active':''}}">
                                    <a class="nav-link" href="{{ route('planificar.index') }}"><i class="material-icons menu-icon align-text-bottom">history</i> Historial CSV</a>
                                  </li>
                                @endif
                            @endif
                            @if(auth()->check())
                                @if(auth()->user()->hasRoles(['admin',]))
                                    <li class="nav-item {{ request()->is('logs') ? 'active':''}}">
                                        <a class="nav-link" href="{{ route('logs')}}"><i class="material-icons menu-icon align-text-bottom">description</i> Log acceso</a>
                                    </li>
                                    <li class="nav-item {{ request()->is('usuarios*') ? 'active':''}}">
                                        <a class="nav-link" href="{{ route('usuarios.index') }}"><i class="material-icons menu-icon align-text-bottom">supervisor_account</i> Gestionar usuarios</a>
                                    </li>
                                @endif
                            @endif
                          </ul>
                          <ul class="nav navbar-nav navbar-right">
                            <span class="navbar-text">
                              <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="material-icons menu-icon align-text-bottom">account_circle</i>
                                  @if(auth()->check())
                                  {{ auth()->user()->name }}
                                  @endif
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                  <a class="dropdown-item" href="{{route('usuarios.edit', auth()->id())}}"><i class="material-icons menu-icon align-text-bottom">perm_identity</i> Mi cuenta</a>
                                  <div class="dropdown-divider"></div>
                                  <form action="{{ route('logout') }}" method="post">
                                    {{csrf_field()}}
                                    <button type="submit" class="dropdown-item"><i class="material-icons menu-icon align-text-bottom">power_settings_new</i> Desconectar</button>
                                  </form>
                                </div>
                              </li>
                            </span>
                          </ul>
                        </div>
                </div>
              </nav>

    <div class="container">
        <div class="row" id="content-inside">
            @yield('content')
        </div>
    </div>
    </div>
    <div id="footer">
      <div class="container text-center">
        <img src="{{ asset('img/logo-mutual.png') }}" height="30px;"> 
        <img src="{{ asset('img/logo-unab.png') }}" height="30px;">
      </div>
    </div>

    <script src="{{ asset('js/jquery-3.2.1.slim.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/javascript.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
