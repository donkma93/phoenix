<?php

namespace App\Exports\User;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class SkuExport implements FromCollection, WithHeadings
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
        return [
            'id',
            'product_name',
            'sku',
        ];
    }
}
