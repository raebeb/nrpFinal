<?php

namespace App\Http\Controllers;

use Session;
use App\User;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;

class UsersController extends Controller
{
    public function __construct(){
      $this->middleware('auth');
      $this->middleware('roles:admin', ['except'=>['edit','update']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('hospital_id', auth()->user()->hospital_id)->with(['role'])->orderBy('id','DESC')->get();
        return view('manageUsers.index.table', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::select('id','display_name')->where('id','!=',0)->orderBy('id')->get();
        return view('manageUsers.create.form',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = new User;
        $user->name = ucwords(strtolower($request->input('name')));
        $user->lastname = ucwords(strtolower($request->input('lastname')));
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->status = "0";
        $user->role_id = $request->input('rol');
        $user->hospital_id = auth()->user()->hospital->id;
        $user->save();
        return redirect('usuarios')->with('info', 'Usuario agregado');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $user = User::findOrFail($id);
        $this->authorize($user);
        $roles = Role::where('id','!=',0)->orderBy('id')->get();
        return view('manageUsers.edit.form', compact('user','roles'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id){
        $user = User::findOrFail($id);
        $this->authorize($user);
        //Everybody
        $user->name = $request->input('name');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        if($request->filled('password') && $request->filled('password_confirmation')){
          $user->password = bcrypt($request->input('password'));
        }
        //Admin
        if(auth()->user()->isAdmin() && auth()->user()->id != $id){
          if($request->filled('status')){
            $user->status = (int) $request->input('status');
          }
          if($request->filled('rol')){
            $user->role_id = (int) $request->input('rol');
          }
        }
        $user->update();
        return back()->with('info', 'Usuario actualizado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        return abort(404);
    }
}
