<div class="col-lg-4 content-panel">
    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <i class="material-icons login-profile">account_circle</i>
                <h4>Saludos
                @if(auth()->check())
                    {{ auth()->user()->name }}
                    </h4>
                    <strong>{{ auth()->user()->role->display_name }}</strong>
                    <p>Institución: {{ auth()->user()->hospital->name }}</p>
                    <p></p>
                @endif
                <form action="{{ route('logout') }}" method="post">
                    {{csrf_field()}}
                    <button type="submit" class="dropdown-item"><i class="material-icons menu-icon align-text-bottom">power_settings_new</i> Desconectar</button>
                </form>
            </div>
        </div>
    </div>
</div>
