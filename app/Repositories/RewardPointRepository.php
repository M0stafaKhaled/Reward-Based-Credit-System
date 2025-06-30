<?php
namespace App\Repositories;

use App\Models\RewardPoint;

class RewardPointRepository implements RewardPointRepositoryInterface
{
    public function all()
    {
        return RewardPoint::all();
    }

    public function find($id)
    {
        return RewardPoint::find($id);
    }

    public function create(array $data)
    {
        return RewardPoint::create($data);
    }

    public function update($id, array $data)
    {
        $rewardPoint = RewardPoint::findOrFail($id);
        $rewardPoint->update($data);
        return $rewardPoint;
    }

    public function delete($id)
    {
        return RewardPoint::destroy($id);
    }
} 