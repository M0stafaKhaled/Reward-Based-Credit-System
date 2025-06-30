<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credit_package_id',
        'credits',
        'price',
        'reward_points',
        'status',
    ];

    public function creditPackage()
    {
        return $this->belongsTo(\App\Models\CreditPackage::class);
    }
} 