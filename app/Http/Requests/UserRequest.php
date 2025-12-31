<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = isset( $this->user) ?  $this->user : 0;
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'pan_number' => ['required', 'string', 'max: 25'],
            'phone' => ['required', 'string','min:10','max:15'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'user_type' => ['required']
        ];

        if ($id === 0) {
            $rules['password'] = ['required','string', 'min:8', 'confirmed', 'max:20'];
        }else{
            $rules['password'] = ['nullable','string', 'min:8', 'confirmed', 'max:20'];

        }
        return $rules;
    }
}
