<?php

namespace App\Http\Controllers;
use App\File;
use App\Hospital;
use App\Schedule;
use Illuminate\Http\Request;

class HomeController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
      if(auth()->user()->hasRoles(['autor'])){
        $schedules = Schedule::where('user_id',auth()->user()->id)->with(['file' => function($query){
            $query->where('period','=','mensual');
        }])->orderBy('created_at','DESC')->limit(5)->get();
      }elseif (auth()->user()->hasRoles(['moderador','admin'])) {
        $schedules = Schedule::whereHas('user',function($query){
          $query->where('hospital_id', auth()->user()->hospital->id);
        })->with(['file'=>function($query){
          $query->where('period','mensual');
        }])->orderBy('id','DESC')->limit(5)->get();
      }
      return view('home',compact('schedules'));
    }
}
