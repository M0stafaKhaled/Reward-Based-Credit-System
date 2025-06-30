<?php
namespace App\Repositories;

use App\Models\Purchase;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    public function all()
    {
        return Purchase::all();
    }

    public function find($id)
    {
        return Purchase::find($id);
    }

    public function create(array $data)
    {
        return Purchase::create($data);
    }

    public function update($id, array $data)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->update($data);
        return $purchase;
    }

    public function delete($id)
    {
        return Purchase::destroy($id);
    }
} 