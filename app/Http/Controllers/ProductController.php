<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Requests\ProductRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
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

        $data['uuid'] = Str::uuid();
        $data['slug'] = Str::slug($data['name']);
        Product::create($data);

        return response()->json(['title' => 'Good Job', 'text' => 'Product created successfully', 'icon' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            return response()->json([
                'data' => Product::where('uuid', $id)->firstOrFail()
            ]);
        } catch (Exception $th) {
            //throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            $data['uuid'] = Str::uuid();
            $data['slug'] = Str::slug($data['name']);
            Product::where('uuid', $id)->update($data);

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
        $product = Product::where('uuid', $id)->firstOrFail();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function serversideTable(Request $request)
    {
        $product = Product::get();

        return DataTables::of($product)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
            return '<div class="text-center">
                        <button class="btn btn-sm btn-success" onclick="editModal(this)" data-id="' . $row->uuid . '">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteModal(this)" data-id="' . $row->uuid . '">Delete</button>
                    </div>';
        })
        ->make();
    }
}
