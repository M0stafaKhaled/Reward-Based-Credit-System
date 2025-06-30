<?php
namespace App\Repositories;

use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function all()
    {
        return Product::all();
    }

    public function find($id)
    {
        return Product::find($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        return Product::destroy($id);
    }

    public function search($query, $category = null, $paginate = 20)
    {
        $q = Product::query()->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('category', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%")
                  ;
            });
        if ($category) {
            $q->where('category', $category);
        }
        return $q->paginate($paginate);
    }

    public function allPaginated($perPage = 15)
    {
        return Product::paginate($perPage);
    }

    public function getTrending($category = null, $limit = 10)
    {
        $q = Product::query()->where('is_active', true);
        if ($category) {
            $q->where('category', $category);
        }
        // Placeholder: trending = most recent
        return $q->orderByDesc('created_at')->take($limit)->get();
    }
} 