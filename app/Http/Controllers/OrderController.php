<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreOrderRequest;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        if (Auth::user()->role->name === 'admin') {
            $orders = Order::with('products')->get();
        } else {
            $orders = User::findOrFail(Auth::id())->orders()
            ->with('products')->get();
        }
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = Order::create(['user_id' => $request->user_id]);
        $products = collect($request->products)->mapWithKeys(function ($item) {
            return [$item['id'] => ['quantity' => $item['quantity']]];
        });
        $order->products()->attach($products);
        return response()->json([
            'success' => true,
            'data' => $order->load('products')
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        Gate::authorize('accessOrder', $order);
        $orders = $order->load('products');
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
        ]);
        $products = collect($validated['products'])->mapWithKeys(function ($item) {
            return [$item['id'] => ['quantity' => $item['quantity']]];
        });
        Gate::authorize('accessOrder', $order);
        $order->products()->sync($products);
        return response()->json([
            'success' => true,
            'data' => $order->load('products')
        ]);
    }

    public function destroy(Order $order): JsonResponse
    {
        Gate::authorize('accessOrder', $order);
        $order->delete();
        return response()->json(['success' => true]);
    }
}
