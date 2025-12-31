<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateCompanyRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        $id = isset($request->id) ? $request->id : 0;
        return [
            'company_name' => 'required|max:255|min:3|unique:companies,name,' . $id,
            'company_email' => 'required|email|max:255|unique:companies,email,' . $id,
            'company_cin' => 'required|max:255',
            'subscriptions_period' => 'required|max:255',
        ];
    }
}
