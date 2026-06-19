<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicStoreController extends Controller
{
    /**
     * Show a user's public storefront
     */
    public function show(string $slug)
    {
        $user = User::where('store_slug', $slug)->firstOrFail();

        $settings = $user->store_settings ? json_decode($user->store_settings, true) : [];
        $showImages = $settings['show_images'] ?? true;

        $products = Product::where('user_id', $user->id)
            ->where('is_active', true)
            ->select(['id', 'name', 'description', 'price', 'image', 'sku', 'stock_quantity'])
            ->orderBy('name')
            ->get();

        return view('store.public', compact('user', 'products', 'showImages', 'settings'));
    }

    /**
     * Handle incoming order from public store
     */
    public function storeOrder(Request $request, string $slug)
    {
        $user = User::where('store_slug', $slug)->firstOrFail();

        $data = $request->validate([
            'customer_name'  => 'required|string|max:150',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:150',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|integer|min:1',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Build receipt data
        $items = [];
        $total = 0;
        foreach ($data['items'] as $item) {
            $product = Product::where('id', $item['id'])
                ->where('user_id', $user->id)
                ->first();
            if (!$product) continue;

            $subtotal = $product->price * $item['qty'];
            $total += $subtotal;
            $items[] = [
                'name'     => $product->name,
                'price'    => $product->price,
                'qty'      => $item['qty'],
                'subtotal' => $subtotal,
            ];
        }

        if (empty($items)) {
            return response()->json(['message' => 'No valid products in order'], 422);
        }

        // Store order as JSON in user's store_settings.orders or create a simple table
        $orders = $settings['orders'] ?? [];
        $order = [
            'id'              => 'ORD-' . strtoupper(Str::random(8)),
            'customer_name'   => $data['customer_name'],
            'customer_phone'  => $data['customer_phone'],
            'customer_email'  => $data['customer_email'] ?? null,
            'items'           => $items,
            'total'           => $total,
            'notes'           => $data['notes'] ?? null,
            'status'          => 'pending',
            'created_at'      => now()->toDateTimeString(),
        ];

        $settings = $user->store_settings ? json_decode($user->store_settings, true) : [];
        $orders = $settings['orders'] ?? [];
        $orders[] = $order;
        $settings['orders'] = $orders;
        $user->store_settings = json_encode($settings);
        $user->save();

        return response()->json([
            'message' => 'Order placed successfully!',
            'order_id' => $order['id'],
            'total' => $total,
        ]);
    }

    /**
     * Generate or regenerate store slug
     */
    public function generateSlug(Request $request)
    {
        $user = auth()->user();
        $base = Str::slug($request->business_name ?? $user->business_name ?? $user->name);
        if (!$base) $base = 'store';

        $slug = $base;
        $counter = 1;
        while (User::where('store_slug', $slug)->where('id', '!=', $user->id)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        $user->store_slug = $slug;
        $user->save();

        return response()->json(['slug' => $slug, 'url' => url('/store/' . $slug)]);
    }

    /**
     * Update store settings
     */
    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        $settings = $user->store_settings ? json_decode($user->store_settings, true) : [];

        if ($request->has('show_images')) {
            $settings['show_images'] = (bool) $request->show_images;
        }
        if ($request->has('store_title')) {
            $settings['store_title'] = $request->store_title;
        }
        if ($request->has('store_description')) {
            $settings['store_description'] = $request->store_description;
        }

        $user->store_settings = json_encode($settings);
        $user->save();

        return response()->json(['message' => 'Store settings updated', 'settings' => $settings]);
    }
}
