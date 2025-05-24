<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = User::findOrFail(Auth::id())->orders()->with('products')->get();
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'error' => 'User not found',
            ], 404);
        }
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
        } catch (ValidationException $error) {
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 422);
        }
    }

    public function show($order_id)
    {
        try {
            $order = Order::with('products')->findOrFail($order_id);
            Gate::authorize('accessOrder', $order);
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'error' => 'Order not found',
            ], 404);
        }
    }

    public function update(Request $request, $order_id)
    {
        try {
            $validated = $request->validate([
                'products' => 'sometimes|array',
                'products.*.id' => 'required_with:products|exists:products,id',
                'products.*.quantity' => 'required_with:products|integer|min:1',
            ]);
            if (isset($validated['products'])) {
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
        } catch (ValidationException $error) {
            return response()->json([
                'success' => false,
                'error' => $error->getMessage(),
            ], 422);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'error' => 'Order not found',
            ], 404);
        }
    }

    public function destroy($order_id)
    {
        try {
            $order = Order::findOrFail($order_id);
            Gate::authorize('accessOrder', $order);
            $order->delete();
            return response()->json(['success' => true]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'error' => 'Order not found',
            ], 404);
        }
    }
}
