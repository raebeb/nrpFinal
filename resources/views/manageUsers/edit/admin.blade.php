<div class="form-group">
      <label for="status">Estado</label>
      <select id="status" name="status" class="form-control {{ $user->isBlock() ? 'is-invalid':'is-valid' }}">
          <option value="1" @if($user->status) selected @endif>Bloqueado</option>
          <option value="0" @if(!$user->status) selected @endif>Desbloqueado</option>
      </select>
      {!! $errors->first('status','<div class="invalid-feedback">:message</div>') !!}
</div>
<div class="form-group">
      <label for="rol">Rol</label>
      <select id="rol" name="rol" class="form-control {{ $errors->has('rol') ? 'is-invalid':''}}">
        @foreach($roles as $role)
          @if($user->role->name === $role->name)
            <option value="{{$role->id}}" selected>{{$role->display_name}}</option>
          @else
            <option value="{{$role->id}}">{{$role->display_name}}</option>
          @endif
        @endforeach
      </select>
      {!! $errors->first('rol','<div class="invalid-feedback">:message</div>') !!}
</div>
