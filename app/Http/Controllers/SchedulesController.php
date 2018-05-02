<?php

namespace App\Http\Controllers;
use Storage;

use App\Events\CsvWasReceived;
use App\Service;
use App\Schedule;
use App\File;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreFileRequest;

class SchedulesController extends Controller
{
    public function __construct(){
      $this->middleware('auth');
      $this->middleware('roles:autor', ['only' => ['create','store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(){
       if(auth()->user()->role->name === 'autor'){
         $schedules = Schedule::where('user_id',auth()->user()->id)->with(['file'=> function($query){
           $query->where('period','mensual');
         }])->orderBy('id','DESC')->get();

       }elseif (auth()->user()->hasRoles(['moderador','admin'])){
         $schedules = Schedule::with(['file' => function($query){
           $query->where('period','mensual');
         }])->whereHas('user',function($query){
           $query->where('hospital_id',auth()->user()->hospital->id);
         })->orderBy('id','DESC')->get();
       }
         return view('manageSchedules.index.table', compact('schedules'));
     }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manageSchedules.create.form');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(StoreFileRequest $request){
	//dd($request);
       //Step 1
        $dateH = date("H");
        $datei = date("i");
        $dates = date("s");
        $root_path = base_path()."/storage/app/";

       $file = $request->file('file');
       $name = $dateH."_".$datei."_".$dates.'_'.$file->getClientOriginalName();
       $default_storage = 'public/'.auth()->user()->hospital->country->id.'/'.auth()->user()->hospital->id.'/'.'1'.'/'.date("Y").'/'.date("n").'/'.date("j"); // verificar 1
       $input = Storage::putFileAs($default_storage.'/input', $file, $name);

        $something = event(new CsvWasReceived($root_path.$input, $request));

       //Step 2
       $message = system('cd '.$root_path.'public && ./scheduler settings '.$root_path.$input.' feriados', $return);
       $hour = $dateH.$datei;
       if($return === 0){
         $personal_path = $root_path.$default_storage.'/output/'.$hour.'/personal.csv';
         $general_path = $root_path.$default_storage.'/output/'.$hour.'/general.csv';
         //Step 3
         $fila = 1;
         if ( ($personal = @fopen($personal_path, 'r')) && ($general = @fopen($general_path, 'r')) ) {

           $schedule = new Schedule;
           $schedule->user_id = auth()->user()->id;
           $schedule->service_id = 1;
           $schedule->save();
           //Read personalCSV
           while (($personal_data = fgetcsv($personal, 1000, ';')) !== FALSE){
             $data = array();
             $numero = count($personal_data);
             $fila++;
             for ($c=0; $c < $numero; $c++){
               array_push($data, $personal_data[$c]);
             }
             $next = 0;
             for($i=0; $i<6;$i++){
               $fileBD = new File;
               $fileBD->schedule_id = $schedule->id;
               $fileBD->receiver = $data[0];
               $fileBD->name_receiver = $data[1];
               $fileBD->lastname_receiver = $data[2];
               $fileBD->file_name = $data[3+$next];
               $fileBD->local_path = $data[4+$next];
               $fileBD->file_io = 1;
               $fileBD->period = 'null';
               $fileBD->storage_path = substr($data[4+$next],strlen($root_path.'public/'));
               $fileBD->save();
               $next = $next+2;
             }
           }
           fclose($personal);
           //Read generalCSV
           $general_row = 0;
           while (($general_data = fgetcsv($general, 1000, ';')) !== FALSE) {
             $data = array();
             $numero = count($general_data);
             $general_row++;
             for ($c=0; $c < $numero; $c++){
               array_push($data, $general_data[$c]);
             }
               $fileBD = new File;
               $fileBD->schedule_id = $schedule->id;
               $fileBD->receiver = auth()->user()->id;
               $fileBD->name_receiver = auth()->user()->name;
               $fileBD->lastname_receiver = auth()->user()->lastname;
               $fileBD->file_name = $data[0];
               $fileBD->local_path = $data[1];
               $fileBD->file_io = 1;
               if ($general_row === 1) {
                 $fileBD->period = 'Mensual';
               }else{
                 $fileBD->period = 'Semanal';
               }
               $fileBD->storage_path = substr($data[1],strlen($root_path.'public/'));
               $fileBD->save();
             }
             fclose($general);

         }else{
           return redirect('planificar/create')->with('danger', 'No se encontraron los archivos output');
         }
         return redirect('planificar/create')->with('info', $schedule->id);
       }else{
         return redirect('planificar/create')->with('danger', $message);
       }
       //fin funcion
     }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function show($id){
       $schedule = Schedule::findOrFail($id);
       $this->authorize('edit', $schedule);
       $files = File::where('schedule_id', '=', $schedule->id)->get();
       return view('manageSchedules.show.table', compact('schedule','files'));
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return abort(404);
    }
}
