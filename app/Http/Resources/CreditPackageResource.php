<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditPackageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'credits' => $this->credits,
            'price' => $this->price,
            'points' => $this->reward_points,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
