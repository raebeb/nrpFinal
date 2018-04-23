<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required|max:41|regex:/^[\pL\s\-]+$/u',
            'lastname' => 'required|max:41|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|email|unique:users,email,'.$this->route('usuario'),
            'password' => 'nullable|min:6|max:20|confirmed',
            'password_confirmation' => 'nullable|min:6|max:20',
            'status' => 'boolean',
            'rol' => 'in:1,2,3',
        ];
    }
}
