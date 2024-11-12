<?php

namespace App\Services;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Storage;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function updateOrderDetails(Order $order, array $data)
    {
        $order->fill($data);

        if (isset($data['image_link'])) {
            $order->image_link = $this->handleImageUpload($data['image_link']);
        }

        $order->save();
        return $order;
    }

    public function updateOrderItems(Order $order, array $itemsData)
    {
        foreach ($itemsData['order_kind'] as $index => $orderKind) {
            $item = $order->items()->get()[$index];
            $item->update([
                'order_kind' => $orderKind,
                'order_fix_type' => $itemsData['order_fix_type'][$index],
                'quantity' => $itemsData['quantity'][$index],
                'gold_color' => $itemsData['gold_color'][$index],
            ]);
        }
    }

    private function handleImageUpload($image)
    {
        $path = $image->store('public/order_images');
        return str_replace('public/', '', $path);
    }
}