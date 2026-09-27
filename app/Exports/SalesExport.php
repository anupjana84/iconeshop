<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Sale::with([
            'customer',
            'items.product.brand',
            'items.product.category'
        ])->get()->map(function ($sale) {

            // ✅ Category
            $categories = $sale->items->map(function ($item) {
                return $item->product->category->name ?? 'N/A';
            })->unique()->implode("\n");

            // ✅ Product (Brand)
            $products = $sale->items->map(function ($item) {
                return $item->product->brand->name ?? 'N/A';
            })->unique()->implode("\n");

            // ✅ Model
            $models = $sale->items->map(function ($item) {
                return $item->product->model ?? '';
            })->implode("\n");

            return [
                'Customer Name' => $sale->customer->name ?? 'Cash',
                'Mobile' => $sale->customer->phone ?? '',
                'Invoice Number' => $sale->invoice_number,
                'Category' => $categories,
                'Product' => $products,
                'Model' => $models,
                'Sale Date' => optional($sale->created_at)->format('d-m-Y'),
                'Total Amount' => $sale->total,
                'GST' => $sale->gst == 'yes' ? 'Yes' : 'No',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Mobile',
            'Invoice Number',
            'Category',
            'Product',
            'Model',
            'Sale Date',
            'Total Amount',
            'GST'
        ];
    }
}