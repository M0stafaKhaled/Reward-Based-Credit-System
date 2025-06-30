<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        // You can add admin check here
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'price_in_points' => 'required|integer',
            'is_active' => 'boolean',
        ];
    }
} 