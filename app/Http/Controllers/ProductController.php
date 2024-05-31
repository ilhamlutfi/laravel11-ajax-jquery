<?php

namespace App\Http\Controllers;

use Exception;
use App\Service\ImageService;
use App\Service\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService, private ImageService $imageService){}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('products.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $uploadImg = $this->imageService->uploadImg($data);
            $data['image'] = $uploadImg;

            $this->productService->create($data);

            return response()->json(['title' => 'Good Job', 'text' => 'Product created successfully', 'icon' => 'success']);
        } catch (Exception $error) {
            return response()->json(['title' => 'Error', 'text' => $error->getMessage(), 'icon' => 'error']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            return response()->json([
                'data' => $this->productService->getByUid($id),
            ]);
        } catch (Exception $th) {
            //throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $data = $request->validated();

        $getImage = $this->productService->getByUid($id);

        try {
            $uploadImg = $this->imageService->uploadImg($data, $getImage->image);
            $data['image'] = $uploadImg;

            $this->productService->update($data, $id);

            return response()->json(['title' => 'Good Job', 'text' => 'Product updated successfully', 'icon' => 'success']);
        } catch (Exception $error) {
            return response()->json(['title' => 'Error', 'text' => $error->getMessage(), 'icon' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->productService->delete($id);

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function serversideTable(): JsonResponse
    {
        return $this->productService->getDatatable();
    }
}
