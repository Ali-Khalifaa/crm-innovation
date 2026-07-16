<?php

namespace Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Helpers\ApiResponse;
use Modules\Products\Http\Requests\StoreProductRequest;
use Modules\Products\Http\Requests\UpdateProductRequest;
use Modules\Products\Http\Resources\ProductResource;
use Modules\Products\Models\Product;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Product::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (request()->has('is_active')) {
            $query->where('is_active', filter_var(request('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($type = request('type')) {
            $query->where('type', $type);
        }

        $products = $query->latest()->paginate(20);

        return ApiResponse::paginated($products, ProductResource::class);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return ApiResponse::success(new ProductResource($product), __('crm.created'), 201);
    }

    public function show(Product $product): JsonResponse
    {
        return ApiResponse::success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return ApiResponse::success(new ProductResource($product));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }

    public function dropdown(): JsonResponse
    {
        $products = Product::where('is_active', true)
            ->select('id', 'name', 'unit_price', 'currency', 'type')
            ->orderBy('name')
            ->get();

        return ApiResponse::success($products);
    }
}
