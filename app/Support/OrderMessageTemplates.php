<?php

namespace App\Support;

use App\Models\Order;
use Carbon\Carbon;


class OrderMessageTemplates
{
    public static function all(): array
    {
        return [
            'order_request' => 'Thank you for requesting order on our website. Order id: #{order_number}. Our agent will call you soon.',

            'confirmed' => 'Order Confirmed: Your order #{order_number} has been confirmed. We will start processing it. Total amount: {total_price} BDT.',

            'china_received' => 'China Warehouse Update: Your order #{order_number} has arrived at China warehouse. We are preparing shipment.',

            'shipped' => 'Good news! Order #{order_number} has been shipped from China. Amount: {total_price} BDT.',

            'bd_received' => 'Your parcel #{order_number} has reached Bangladesh warehouse and is being processed.',

            'out_for_delivery' => 'Your order #{order_number} is out for delivery. Please stay available.',

            'delivered' => 'Order #{order_number} delivered successfully. Thank you for shopping with us!',

            'cancelled' => 'Order #{order_number} has been cancelled. For support contact us.',
        ];
    }

    public static function parseOrderMessage(string $message, Order $order): string
    {
        $formatDate = fn($date) =>
        $date
            ? Carbon::parse($date)
            ->timezone('Asia/Dhaka')
            ->format('d-m-Y')
            : '-';

        $products = collect($order->products ?? [])
            ->map(fn($p) => array_key_exists('name', $p) ? $p['name'] . ' x' . $p['quantity'] : '')
            ->implode(', ');

        return str_replace(
            [
                '{customer_name}',
                '{order_number}',
                '{order_status}',
                '{delivery_status}',
                '{work_process}',
                '{shipment_type}',

                '{order_receive_date}',
                '{order_place_date}',
                '{delivery_date}',

                '{total_price}',
                '{shipping_charge}',
                '{advance_payment}',
                '{due_payment}',
                '{product_weight}',

                '{products}'
            ],
            [
                $order->customer_name,
                $order->order_number_short_code,
                $order->order_status?->getLabel() ?? $order->order_status,
                $order->delivery_status?->getLabel() ?? $order->delivery_status,
                $order->work_process?->getLabel() ?? $order->work_process,
                $order->shipment_type?->getLabel() ?? $order->shipment_type,

                $formatDate($order->order_receive_date),
                $formatDate($order->order_place_date),
                $formatDate($order->delivery_date),

                $order->total_price,
                $order->shipping_charge,
                $order->advance_payment,
                $order->due_payment,
                $order->product_weight,

                $products
            ],
            $message
        );
    }
}
