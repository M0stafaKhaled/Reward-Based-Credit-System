<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditPackageRequest extends FormRequest
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
            'credits' => 'required|integer',
            'price' => 'required|numeric',
            'reward_points' => 'required|integer',
            'is_active' => 'boolean',
        ];
    }
} 