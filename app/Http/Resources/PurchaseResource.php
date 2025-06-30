<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'credit_package_id' => $this->credit_package_id,
            'credits' => $this->credits,
            'price' => $this->price,
            'reward_points' => $this->reward_points,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'credit_package' => $this->whenLoaded('creditPackage'),
        ];
    }
} 