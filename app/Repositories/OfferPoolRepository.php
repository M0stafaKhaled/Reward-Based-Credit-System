<?php
namespace App\Repositories;

use App\Models\OfferPool;

class OfferPoolRepository implements OfferPoolRepositoryInterface
{
    public function all()
    {
        return OfferPool::all();
    }

    public function find($id)
    {
        return OfferPool::find($id);
    }

    public function create(array $data)
    {
        return OfferPool::create($data);
    }

    public function update($id, array $data)
    {
        $offer = OfferPool::findOrFail($id);
        $offer->update($data);
        return $offer;
    }

    public function delete($id)
    {
        $offer = OfferPool::find($id);
        if (!$offer) {
            return false;
        }
        return $offer->delete();
    }

    public function getActiveOffers()
    {
        return OfferPool::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('offer_start')->orWhere('offer_start', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('offer_end')->orWhere('offer_end', '>=', now());
            })
            ->get();
    }
} 