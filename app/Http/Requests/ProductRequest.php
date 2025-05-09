<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'title' => 'required|unique:products',
            'price_custom_label' => 'required',
            'description' => 'required',
            'images' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'title.unique' => 'Nama produk sudah digunakan'
        ];
    }
}
