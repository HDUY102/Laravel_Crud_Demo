<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    use AuthorizesRequests;
    
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): JsonResponse // Thêm Request $request
    {
        $this->authorize('product.view');

        $perPage = $request->get('per_page', 10);
        $products = $this->repository->paginate($perPage);
        
        return response()->json([
            'success' => true, 
            'data' => $products
        ]);
    }

    /**
     * POST /api/products
    */
    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Xử lý lưu file ảnh nếu có tải lên
        if ($request->hasFile('image')) {
            // Tải file lên storage/app/public/products
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = $this->repository->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * PUT /api/products/{id}
    */
    public function update(ProductRequest $request, string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $data = $request->validated();

        // Xử lý cập nhật file ảnh
        if ($request->hasFile('image')) {
            // Xóa file ảnh cũ nếu tồn tại trong storage
            // if ($product->image && Storage::disk('public')->exists($product->image)) {
            //     Storage::disk('public')->delete($product->image);
            // }
            $this->deleteProductImage($product->image);
            // Lưu file ảnh mới
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            // Giữ lại ảnh cũ nếu lần update này không tải ảnh mới
            unset($data['image']);
        }

        $this->repository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ], 200);
    }

    /**
     * DELETE /api/products/{id}
    */
    public function destroy(mixed $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // Xóa file ảnh trong storage khi xóa sản phẩm
        $this->deleteProductImage($product->image);

        $this->repository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ], 200);
    }

    private function deleteProductImage(?string $imagePath): void
    {
        if (!$imagePath) {
            return;
        }

        // Loại bỏ các tiền tố thừa nếu có (như /storage/ hay storage/)
        $relativePath = ltrim(str_replace('/storage/', '', $imagePath), '/');
        $relativePath = ltrim(str_replace('storage/', '', $relativePath), '/');

        // Thực hiện kiểm tra và xóa file trên disk 'public'
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
