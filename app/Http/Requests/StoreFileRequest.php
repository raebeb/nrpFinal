<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'file' => 'required|file|mimes:csv,txt',
            'ID.*' => 'required|integer',
            'name.*' => 'required|alpha|max:41',
            'lastname.*' => 'required|alpha|max:41',
            'lunes.*' => 'required|integer',
            'martes.*' => 'required|integer',
            'miercoles.*' => 'required|integer',
            'jueves.*' => 'required|integer',
            'viernes.*' => 'required|integer',
            'sabado.*' => 'required|integer',
            'domingo.*' => 'required|integer',
            'turno_preferido.*' => 'required|integer',
            'prioriedad.*' => 'required|integer',
            'ID_Solicitudes.*' => 'integer',
            'Dias_Solicitados.*' => 'integer',
            'ID_Vacaciones.*' => 'integer',
            'Inicio_Vacaciones.*' => 'integer',
            'Termino_Vacaciones.*' => 'integer',
            'ID_Licencias.*' => 'integer',
            'Inicio_Licencias.*' => 'integer',
            'Termino_Licencias.*' => 'integer',
            'ID_Luto.*' => 'integer',
            'Inicio_Luto.*' => 'integer',
            'Termino_Luto.*' => 'integer',
            'ID_Bloqueo.*' => 'integer',
            'Inicio_Bloqueo.*' => 'integer',
            'Termino_Bloqueo.*' => 'integer',
            'ID_Embarazada.*' => 'integer'
        ];
    }

    public function messages(){
      return [
        'file.mimes' => 'El :attribute debe ser de formato: csv'
      ];
    }
}
