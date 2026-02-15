<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // Filter by status
        if ($request->has('status')) {
            $status = OrderStatus::tryFrom($request->status);
            if ($status) {
                $query->where('status', $status);
            }
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range filter
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return OrderResource::collection($query->paginate(10));
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create order
            $order = Order::create([
                'user_id' => $request->user_id ?? auth()->id(),
                'status' => OrderStatus::PENDING,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'notes' => $request->notes,
            ]);

            // Create order items and update stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Create order item
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                ]);

                // Reduce stock
                $product->adjustStock(-$item['quantity']);
            }

            // Calculate total
            $order->calculateTotal();

            return new OrderResource($order->load(['user', 'items']));
        });
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $user = auth()->user();

        if ($order->user_id !== $user->id && !$user->hasAnyRole(['admin', 'manager'])) {
            return response()->json([
                'message' => 'You do not have permission to view this order.'
            ], 403);
        }

        return new OrderResource($order->load(['user', 'items.product']));
    }

    /**
     * Update the specified order.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();

        if (isset($data['status'])) {
            $status = OrderStatus::from($data['status']);

            switch ($status) {
                case OrderStatus::PROCESSING:
                    $order->markAsProcessing();
                    break;
                case OrderStatus::COMPLETED:
                    $order->markAsCompleted();
                    break;
                case OrderStatus::CANCELLED:
                    if (!$order->canBeCancelled()) {
                        return response()->json([
                            'message' => 'This order cannot be cancelled.'
                        ], 422);
                    }
                    $order->markAsCancelled();
                    foreach ($order->items()->with('product')->get() as $item) {
                        if ($item->product) {
                            $item->product->adjustStock($item->quantity);
                        }
                    }
                    break;
            }
            unset($data['status']);
        }

        if (!empty($data)) {
            $order->update($data);
            if (isset($data['tax']) || isset($data['discount'])) {
                $order->calculateTotal();
            }
        }

        return new OrderResource($order->fresh(['user', 'items']));
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        if ($order->status !== OrderStatus::PENDING) {
            return response()->json([
                'message' => 'Only pending orders can be deleted.'
            ], 422);
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items()->with('product')->get() as $item) {
                if ($item->product) {
                    $item->product->adjustStock($item->quantity);
                }
            }

            $order->delete();
        });

        return response()->noContent();
    }

    /**
     * Cancel an order.
     */
    public function cancel(Order $order)
    {
        if (!$order->canBeCancelled()) {
            return response()->json([
                'message' => 'Este pedido não pode ser cancelado.'
            ], 422);
        }

        DB::transaction(function () use ($order) {
            $order->markAsCancelled();

            // Restore stock
            foreach ($order->items()->with('product')->get() as $item) {
                if ($item->product) {
                    $item->product->adjustStock($item->quantity);
                }
            }
        });

        return new OrderResource($order->fresh(['user', 'items']));
    }

    /**
     * Get orders for the authenticated user.
     */
    public function myOrders(Request $request)
    {
        $query = Order::with(['items.product'])
            ->where('user_id', auth()->id());

        if ($request->has('status')) {
            $status = OrderStatus::tryFrom($request->status);
            if ($status) {
                $query->where('status', $status);
            }
        }

        $query->orderBy('created_at', 'desc');

        return OrderResource::collection($query->paginate(10));
    }
}

