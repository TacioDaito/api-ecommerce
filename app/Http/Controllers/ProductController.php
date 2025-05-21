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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
            ]);
            $product = Product::create($validated);
            return response()->json([
                'success' => true,
                'data' => $product
            ], 201);
        } catch (ValidationException $error) {
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 422);
        }
    }

    public function show($product_id)
    {
        try {
            $product = Product::with('orders')->findOrFail($product_id);
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found',
            ], 404);
        }
    }

    public function update(Request $request, $product_id)
    {
        try {
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
        } catch (ValidationException $error) {
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 422);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found',
            ], 404);
        }
    }

    public function destroy($product_id)
    {
        try {
            $product = Product::findOrFail($product_id);
            $product->delete();
            return response()->json(['success' => true], 204);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found',
            ], 404);
        }
    }
}
