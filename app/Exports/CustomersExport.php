<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Customer::get()->map(function ($customer) {
            return [
                'Name' => $customer->name,
                'Address' => $customer->address,
                'Mobile' => $customer->phone,
                'WhatsApp' => $customer->wpnumber,
                'PIN Code' => $customer->pin,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Address',
            'Mobile Number',
            'WhatsApp Number',
            'PIN Code'
        ];
    }
}