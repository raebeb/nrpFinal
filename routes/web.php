<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'session'], function(){

  Route::get('/home', 'HomeController@index')->name('inicio');
  Route::get('/logs', 'AccessLogsController@index')->name('logs');
  
  Route::resource('planificar','SchedulesController');
  Route::resource('usuarios', 'UsersController');

  Route::resource('formularios', 'FormsController');
  
});

// Public forms
Route::get('{country}/{hospital}/{service}/{form}/create','PublicFormsController@show')->name('publicforms.create');
Route::post('{country}/{hospital}/{service}/{form}','PublicFormsController@store')->name('publicforms.store');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
//Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
//Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

//Download
/*
Route::get('storage/{pais}/{hospital}/{servicio}/{anyo}/{mes}/{dia}/{io}/{fileInput?}/{hora?}/{file_responsable?}/{individual?}/{person?}/{file_receiver?}', function ($pais, $hospital, $servicio, $anyo, $mes, $dia, $io, $fileInput = null , $hora = null, $file_responsable = null, $individual = null) {
     if ($io === 'input') {
       $url = storage_path().'/app/public/'.$pais.'/'.$hospital.'/'.$servicio.'/'.$anyo.'/'.$mes.'/'.$dia.'/'.$io.'/'.$fileInput;
     }
     elseif ($io === 'output') {
       if (strpos($hora, '.csv') !== false) {
          $url = storage_path().'/app/public/'.$pais.'/'.$hospital.'/'.$servicio.'/'.$anyo.'/'.$mes.'/'.$dia.'/'.$io.'/'.$fileInput.'/'.$hora;
        }else {
          $url = storage_path().'/app/public/'.$pais.'/'.$hospital.'/'.$servicio.'/'.$anyo.'/'.$mes.'/'.$dia.'/'.$io.'/'.$fileInput.'/'.$hora.'/'.$file_responsable.'/'.$individual;
        }
     }
     //verificamos si el archivo existe y lo retornamos
     if (1)
     {
       return response()->download($url);
     }
     //si no se encuentra lanzamos un error 404.
     abort(404);
});
*/
