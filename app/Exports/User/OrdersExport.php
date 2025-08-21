<?php

namespace App\Exports\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;


class OrdersExport implements FromCollection, WithHeadings
{
    use Exportable;

    public $orders = [];

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        $response = [];

        foreach ($this->orders as $order) {
            $orderProducts = $order->orderProducts ?? [];

            foreach ($orderProducts as $orderProduct) {
                $response[] = [
                    'id' => $order->id,

                    'name' => $order->addressTo->name ?? '',
                    'city' => $order->addressTo->city ?? '',
                    'company' => $order->addressTo->company ?? '',
                    "country" => $order->addressTo->country ?? '',
                    "email" => $order->addressTo->email ?? '',
                    "phone" => $order->addressTo->phone ?? '',
                    "state" => $order->addressTo->province ?? '',
                    'street1' => $order->addressTo->street1 ?? '',
                    'street2' => $order->addressTo->street2 ?? '',
                    'street3' => $order->addressTo->street3 ?? '',
                    "zip" => $order->addressTo->zip ?? '',

                    'product_name' => $orderProduct->product->name ?? '',
                    'product_quantity' => $orderProduct->quantity ?? '',
                ];
            }
        }

        return collect($response);
    }

    public function headings(): array
    {
        return [
            'id', 'recipient_name', 'recipient_city', 'recipient_company', 'recipient_country',
            'recipient_email', 'recipient_phone', 'recipient_state', 'recipient_street1', 'recipient_street2', 'recipient_street3', 'recipient_zip',
            'product_name', 'product_quantity'
        ];
    }
}
