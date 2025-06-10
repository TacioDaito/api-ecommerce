<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());
        
        return response()->json([
            'success' => true,
            'data' => $product
        ], 201);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['success' => true], 204);
    }
}
