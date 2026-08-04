<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/products - Lấy danh sách sản phẩm (Phân trang 10 mục)
    */
    public function index(): JsonResponse
    {
        $products = Product::lastest()->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Get list products successfully',
            'data' => $products
        ], 200);
    }

    /**
     * POST /api/products
    */
    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ],201);
    }

    /**
     * GET /api/products/{id}
    */
    public function show(string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product){
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Get product successfully',
            'data' => $product
        ], 200);
    }

    /**
     * PUT /api/products/{id}
    */
    public function update(ProductRequest $request, string $id): JsonResponse
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ],404);
        }

        $product->update($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
