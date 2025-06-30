<?php
namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function all();
    public function allPaginated($perPage = 15);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function search($query, $category = null, $paginate = 20);
    public function getTrending($category = null, $limit = 10);
} 