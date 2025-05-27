<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = User::findOrFail(Auth::id())->orders()
        ->with('products')->get();
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        Gate::authorize('createOrder', [Order::class, $validated['user_id']]);
        $order = Order::create(['user_id' => $validated['user_id']]);
        $products = collect($validated['products'])->mapWithKeys(function ($item) {
            return [$item['id'] => ['quantity' => $item['quantity']]];
        });
        $order->products()->attach($products);
        return response()->json([
            'success' => true,
            'data' => $order->load('products')
        ], 201);
    }

    public function show($order_id): JsonResponse
    {
        $order = Order::with('products')->findOrFail($order_id);
        Gate::authorize('accessOrder', $order);
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function update(Request $request, $order_id): JsonResponse
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
        ]);
        $products = collect($validated['products'])->mapWithKeys(function ($item) {
            return [$item['id'] => ['quantity' => $item['quantity']]];
        });
        $order = Order::findOrFail($order_id);
        Gate::authorize('accessOrder', $order);
        $order->products()->sync($products);
        return response()->json([
            'success' => true,
            'data' => $order->load('products')
        ]);
    }

    public function destroy($order_id): JsonResponse
    {
        $order = Order::findOrFail($order_id);
        Gate::authorize('accessOrder', $order);
        $order->delete();
        return response()->json(['success' => true]);
    }
}
