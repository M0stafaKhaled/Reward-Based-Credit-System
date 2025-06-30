<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponseService
{
    /**
     * Format a paginated response to match the structure expected by tests
     *
     * @param mixed $data
     * @return JsonResponse
     */
    public function paginatedResponse($data): JsonResponse
    {
        if ($data instanceof ResourceCollection && method_exists($data->resource, 'toArray')) {
            // Get the paginator from the resource collection
            $paginator = $data->resource;

            // Transform the items through the resource
            $formattedItems = $data->collection->map(function ($item) {
                return $item instanceof JsonResource ? $item->toArray(request()) : (array)$item;
            })->all();

            // Return a response with the structure expected by the tests
            return response()->json([
                'data' => $formattedItems,
                'current_page' => $paginator->currentPage(),
                'first_page_url' => $paginator->url(1),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'last_page_url' => $paginator->url($paginator->lastPage()),
                'links' => $paginator->linkCollection()->toArray(),
                'next_page_url' => $paginator->nextPageUrl(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]);
        }

        // If not a resource collection, just return data
        return response()->json(['data' => $data]);
    }

    /**
     * Format a single resource response
     *
     * @param JsonResource|null $resource
     * @param int $statusCode
     * @return JsonResponse
     */
    public function singleResourceResponse(?JsonResource $resource, int $statusCode = 200): JsonResponse
    {
        if (!$resource) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        // Return resource data directly at the root level (not wrapped in data)
        return response()->json($resource->toArray(request()), $statusCode);
    }

    /**
     * Format a collection response with a data wrapper
     *
     * @param mixed $collection
     * @param int $statusCode
     * @return JsonResponse
     */
    public function collectionResponse($collection, int $statusCode = 200): JsonResponse
    {
        // Format items to ensure they're properly serialized
        $formattedItems = [];

        if ($collection instanceof ResourceCollection) {
            foreach ($collection->collection as $item) {
                $formattedItems[] = $item instanceof JsonResource ? $item->toArray(request()) :
                    ($item instanceof \ArrayAccess ? $item->toArray() : (array)$item);
            }
        } else {
            // Handle model collections or arrays
            foreach (collect($collection) as $item) {
                if ($item instanceof JsonResource) {
                    $formattedItems[] = $item->toArray(request());
                } elseif (is_object($item) && method_exists($item, 'toArray')) {
                    // If it has a toArray method (like Eloquent models)
                    $formattedItems[] = $item->toArray();
                } else {
                    // Plain array or other object
                    $formattedItems[] = (array)$item;
                }
            }
        }

        // Return with the proper data wrapper structure expected by tests
        return response()->json(['data' => $formattedItems], $statusCode);
    }

    /**
     * Format a response for collections of model objects (suggestions/trending)
     *
     * @param mixed $models Collection of model objects
     * @param int $statusCode
     * @return JsonResponse
     */
    public function modelCollectionResponse($models, int $statusCode = 200): JsonResponse
    {
        $formattedItems = [];

        foreach ($models as $model) {
            // Convert to array to ensure all fields are accessible
            if (is_object($model) && method_exists($model, 'toArray')) {
                $modelArray = $model->toArray();
            } else {
                $modelArray = (array) $model;
            }

            // Ensure all required fields are present
            $formattedItems[] = [
                'id' => $modelArray['id'] ?? $model->id ?? null,
                'name' => $modelArray['name'] ?? $model->name ?? null,
                'description' => $modelArray['description'] ?? $model->description ?? null,
                'category' => $modelArray['category'] ?? $model->category ?? null,
                'price_in_points' => $modelArray['price_in_points'] ?? $model->price_in_points ?? null,
                'is_active' => $modelArray['is_active'] ?? $model->is_active ?? null,
                'created_at' => $modelArray['created_at'] ?? ($model->created_at ? $model->created_at : null),
                'updated_at' => $modelArray['updated_at'] ?? ($model->updated_at ? $model->updated_at : null),
            ];
        }

        return response()->json(['data' => $formattedItems], $statusCode);
    }

    /**
     * Format a response for product redemption
     *
     * @param mixed $product
     * @param bool $success
     * @param int $statusCode
     * @return JsonResponse
     */
    public function redeemResponse($product, bool $success = true, int $statusCode = 200): JsonResponse
    {
        // Format product data to ensure it has all required fields
        $productData = $product;

        if ($product instanceof JsonResource) {
            $productData = $product->toArray(request());
        } elseif (is_object($product) && method_exists($product, 'toArray')) {
            $productData = $product->toArray();
        } elseif (is_object($product)) {
            // Format model object directly to ensure all fields are present
            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'category' => $product->category,
                'price_in_points' => $product->price_in_points,
                'is_active' => $product->is_active,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        }

        return response()->json([
            'success' => $success,
            'product' => [
                'data' => $productData
            ]
        ], $statusCode);
    }
}
