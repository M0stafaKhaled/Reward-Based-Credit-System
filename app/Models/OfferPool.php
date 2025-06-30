<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferPool extends Model
{
    use HasFactory;

    protected $table = 'offer_pool';

    protected $fillable = [
        'product_id',
        'is_active',
        'offer_start',
        'offer_end',
    ];
} 