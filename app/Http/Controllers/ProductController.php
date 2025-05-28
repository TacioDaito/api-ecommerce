<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);
        $product = Product::create($validated);
        return response()->json([
            'success' => true,
            'data' => $product
        ], 201);
    }

    public function show($product_id)
    {
        $product = Product::with('orders')->findOrFail($product_id);
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function update(Request $request, $product_id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
        ]);
        $product = Product::findOrFail($product_id);
        $product->update($validated);
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function destroy($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->delete();
        return response()->json(['success' => true], 204);
    }
}
