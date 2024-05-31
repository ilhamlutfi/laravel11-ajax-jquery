<?php

namespace App\Service;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductService
{
    public function create(array $data)
    {
        $data['uuid'] = Str::uuid();
        $data['slug'] = Str::slug($data['name']);
        return Product::create($data);
    }

    public function getByUid(string $id)
    {
        return Product::where('uuid', $id)->firstOrFail();
    }

    public function update(array $data, string $id)
    {
        $data['uuid'] = Str::uuid();
        $data['slug'] = Str::slug($data['name']);
        return Product::where('uuid', $id)->update($data);
    }

    public function delete(string $id)
    {
        $product = Product::where('uuid', $id)->firstOrFail();

        if ($product->image) {
            Storage::disk('public')->delete('images/' . $product->image);
        }

        return $product->delete();
    }

    public function getDatatable()
    {
        $product = Product::latest()->get();

        return DataTables::of($product)
            ->addIndexColumn()
            ->editColumn('image', function ($row) {
                return '<div class="text-center">
                            <a href="' . asset('storage/images/' . $row->image) . '">
                                <img src="' . asset('storage/images/' . $row->image) . '" alt="Product Image" class="img-fluid">
                            </a>
                        </div>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="text-center">
                        <button class="btn btn-sm btn-success" onclick="editModal(this)" data-id="' . $row->uuid . '">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteModal(this)" data-id="' . $row->uuid . '">Delete</button>
                    </div>';
            })
            ->rawColumns(['image', 'action'])
            ->make();
    }
}
