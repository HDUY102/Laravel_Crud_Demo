<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    protected BaseRepository $repository;

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $data = $this->repository->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách thành công',
            'data' => $data
        ]);
    }

    public function show(mixed $id): JsonResponse
    {
        $item = $this->repository->find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bản ghi'], 404);
        }

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy(mixed $id): JsonResponse
    {
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Xóa thất bại'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Xóa thành công']);
    }
}