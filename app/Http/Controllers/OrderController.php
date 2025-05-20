<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::with('products')->get();
        } catch (\Exception $error) {
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 500);
        }
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 422);
        }
        try {
            $order = Order::create(['user_id' => $validated['user_id']]);
            $products = collect($validated['products'])->mapWithKeys(function ($item) {
                return [$item['id'] => ['quantity' => $item['quantity']]];
            });
            $order->products()->attach($products);
        } catch (\Exception $error) {
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 500);
        }
        return response()->json([
            'success' => true,
            'data' => $order->load('products')
        ], 201);
    }

    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'data' => $order->load('products')
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'products' => 'sometimes|array',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
        ]);

        if (isset($validated['products'])) {
            $products = collect($validated['products'])->mapWithKeys(function ($item) {
                return [$item['id'] => ['quantity' => $item['quantity']]];
            });

            $order->products()->sync($products);
        }

        return response()->json([
            'success' => true,
            'data' => $order->load('products')
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([
            'success' => true,
        ], 204);
    }
}
