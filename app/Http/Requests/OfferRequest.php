<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
{
    public function authorize()
    {
        // You can add admin check here
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'is_active' => 'boolean',
            'offer_start' => 'nullable|date',
            'offer_end' => 'nullable|date',
        ];
    }
} 