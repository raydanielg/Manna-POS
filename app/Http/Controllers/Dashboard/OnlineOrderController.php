<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OnlineOrderController extends Controller
{
    /**
     * Display online orders from public store.
     */
    public function index()
    {
        $user = auth()->user();
        $settings = $user->store_settings ? json_decode($user->store_settings, true) : [];
        $orders = collect($settings['orders'] ?? [])
            ->sortByDesc('created_at')
            ->values()
            ->map(function ($order, $index) {
                $order['index'] = $index;
                return $order;
            });

        $stats = [
            'total'     => $orders->count(),
            'pending'   => $orders->where('status', 'pending')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'revenue'   => $orders->where('status', '!=', 'cancelled')->sum('total'),
        ];

        return view('dashboard.online-orders', compact('orders', 'stats'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $user = auth()->user();
        $settings = $user->store_settings ? json_decode($user->store_settings, true) : [];
        $orders = $settings['orders'] ?? [];

        $found = false;
        foreach ($orders as $key => $order) {
            if (($order['id'] ?? null) === $orderId) {
                $orders[$key]['status'] = $request->status;
                $orders[$key]['updated_at'] = now()->toDateTimeString();
                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $settings['orders'] = $orders;
        $user->store_settings = json_encode($settings);
        $user->save();

        Log::info('Online order status updated', [
            'user_id'   => $user->id,
            'order_id'  => $orderId,
            'status'    => $request->status,
        ]);

        return response()->json([
            'message' => 'Order status updated to ' . ucfirst($request->status),
        ]);
    }

    /**
     * Delete an order.
     */
    public function destroy($orderId)
    {
        $user = auth()->user();
        $settings = $user->store_settings ? json_decode($user->store_settings, true) : [];
        $orders = $settings['orders'] ?? [];

        $found = false;
        foreach ($orders as $key => $order) {
            if (($order['id'] ?? null) === $orderId) {
                unset($orders[$key]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $settings['orders'] = array_values($orders);
        $user->store_settings = json_encode($settings);
        $user->save();

        Log::info('Online order deleted', [
            'user_id'  => $user->id,
            'order_id' => $orderId,
        ]);

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
