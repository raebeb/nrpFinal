<?php

namespace App\Http\Controllers;

use App\AccessLog;
use App\User;
use Illuminate\Http\Request;

class AccessLogsController extends Controller
{
    public function __construct(){
      $this->middleware(['auth','roles:admin']);
    }

    public function index(){
      $accessLogs = AccessLog::whereHas('user', function($query){
        $query->where('hospital_id', '=', auth()->user()->hospital_id);
      })->with(['user'])->orderBy('id', 'desc')->get();
      return view('admin.accessLogs', compact('accessLogs'));
    }
}
