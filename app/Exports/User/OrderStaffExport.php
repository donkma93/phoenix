<?php

namespace App\Exports\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\ToteHistory;


class OrderStaffExport implements FromCollection, WithHeadings
{
    use Exportable;

    public $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $response = [];

        // 'b.number_order as rtsInTote', 
        //     'c.number_order as rtsFulfill',
        //     'd.number_order as dtPending', 
        //     'e.number_order as dtInTote', 
        //     'f.number_order as dtFulfill',
        foreach ($this->data as $data) {
            $response[] = [
                'email' => $data->email,
                'rtsPending' => $data->rtsPending ?? "0",
                'dtPending' => $data->dtPending ?? "0",
                'rtsInTote' => $data->rtsInTote ?? "0",
                "dtInTote" => $data->dtInTote ?? "0",
                "rtsFulfill" => $data->rtsFulfill ?? "0",
                "dtFulfill" => $data->dtFulfill ?? "0",
            ];
        }

        return collect($response);
    }

    public function headings(): array
    {
        return [
            'Email', 'Pending(ready to ship)', 'Pending(due today)', 
            'In tote(ready to ship)', 'In tote(due today)',
            'Fulfill(ready to ship)', 'Fulfill(due today)'
        ];
    }
}
