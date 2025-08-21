<?php

namespace App\Exports\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class OrderFilterExport implements FromCollection,WithHeadings
{
    use Exportable;

    public $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection(){
        return $this->data;
    }

    public function headings(): array
    {
        $header_staff = [
            'Order id',
            'Order number',
            'Ship rate',
            'Tracking number',
            'Provider',
            'Partner code',
            'Post code',
            'Receiver name',
            'Address',
            'weight',
            'width',
            'height',
            'length',
            'ITEM',
            'Date created',
        ];

        $header_user = [
            'Order id',
            'Order number',
            'Tracking number',
            'Provider',
            'Partner code',
            'Post code',
            'Receiver name',
            'Address',
            'weight',
            'width',
            'height',
            'length',
            'ITEM',
            'Date created',
        ];

        if (auth()->user()->role == 2) {
            return $header_user;
        }
        return $header_staff;
    }
}
