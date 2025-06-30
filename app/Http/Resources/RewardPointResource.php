<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RewardPointResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'points' => $this->points,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 