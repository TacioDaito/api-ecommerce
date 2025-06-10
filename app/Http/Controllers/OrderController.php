<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\ShowOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Requests\Order\DestroyOrderRequest;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $orders = $user->role->name === 'admin'
            ? Order::with('products')->get()
            : $user->orders()->with('products')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = Order::create(['user_id' => Auth::id()]);
        $products = $this->mapProducts($request->validated('products'));
        $order->products()->attach($products);

        return response()->json([
            'success' => true,
            'data' => $order->refresh()->load('products')
        ], 201);
    }

    public function show(ShowOrderRequest $request, Order $order): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $order->load('products')
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $products = $this->mapProducts($request->validated('products'));
        $order->products()->sync($products);

        return response()->json([
            'success' => true,
            'data' => $order->refresh()->load('products')
        ]);
    }

    public function destroy(DestroyOrderRequest $request, Order $order): JsonResponse
    {
        $order->delete();

        return response()->json(['success' => true]);
    }

    private function mapProducts(array $products): array
    {
        return collect($products)->mapWithKeys(function ($item) {
            return [$item['id'] => ['quantity' => $item['quantity']]];
        })->toArray();
    }
}