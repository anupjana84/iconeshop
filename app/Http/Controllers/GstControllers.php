<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\GstReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
require_once app_path('Lib/SimpleXLSXGen.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GstControllers extends Controller
{
    public function gstReports()
    {
        $page_title = 'GST Reports';
        $reports = GstReport::latest()->get();
        return view('admin.reports.gstReturn', compact('page_title', 'reports'));
    }

    public function downloadReport($id)
    {
        $report = GstReport::findOrFail($id);
        $fullPath = public_path($report->file_path);

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('fail', 'Report file does not exist on server.');
        }

        return response()->download($fullPath, basename($report->file_path), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'gst_type' => 'nullable|in:yes,no,all',
        ]);

        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();
        $gstType = $request->input('gst_type', 'all');
        $placeOfSupply = "19-West Bengal";

        $applyGstFilter = function ($query) use ($gstType) {
            if ($gstType === 'yes') {
                $query->where(function ($q) {
                    $q->where('gst', 'yes')->orWhere('gst', '1');
                });
            } elseif ($gstType === 'no') {
                $query->where(function ($q) {
                    $q->where('gst', 'no')->orWhere('gst', '0')->orWhereNull('gst');
                });
            }
        };

        // =====================================================================
        // SHEET 1: B2B (Registered Sales)
        // =====================================================================
        $b2bData = [['GSTIN/UIN of Recipient', 'Receiver Name', 'Invoice Number', 'Invoice date', 'Invoice Value', 'Place Of Supply', 'Reverse Charge', 'Applicable % of Tax Rate', 'Invoice Type', 'E-Commerce GSTIN', 'Rate', 'Taxable Value', 'Cess Amount']];
        $b2bSalesQuery = Sale::with(['customer', 'items.product.category'])
            ->whereHas('customer', function($q) { 
                $q->whereNotNull('gst_number')->where('gst_number', '!=', ''); 
            })
            ->whereBetween('created_at', [$fromDate, $toDate]);

        $applyGstFilter($b2bSalesQuery);
        $b2bSales = $b2bSalesQuery->get();

        foreach ($b2bSales as $sale) {
            $invoiceAgg = [];
            $invoiceTotalFromItems = 0;

            foreach ($sale->items as $item) {
                // Strictly use gst from sales_items table as discussed
                $rate = $item->gst ?? 0; 
                $itemTotal = $item->total;
                $taxable = $itemTotal / (1 + ($rate / 100));
                
                if (!isset($invoiceAgg[$rate])) {
                    $invoiceAgg[$rate] = 0;
                }
                $invoiceAgg[$rate] += $taxable;
                $invoiceTotalFromItems += $itemTotal;
            }

            foreach ($invoiceAgg as $rate => $sumTaxable) {
                $b2bData[] = [
                    $sale->customer->gst_number,
                    '',
                    $sale->invoice_number ?? $sale->invoice_no,
                    Carbon::parse($sale->created_at)->format('d-M-y'),
                    number_format($invoiceTotalFromItems, 2, '.', ''), // Invoice Value from items to be 100% consistent
                    $placeOfSupply,
                    'N', ' ', 'Regular B2B', ' ',
                    $rate,
                    number_format($sumTaxable, 2, '.', ''),
                    0
                ];
            }
        }

        // =====================================================================
        // SHEET 2: B2CS (Unregistered Sales - Small)
        // =====================================================================
        $b2csData = [['Type', 'Place Of Supply', 'Rate', 'Applicable % of Tax Rate', 'Taxable Value', 'Cess Amount', 'E-Commerce GSTIN']];
        // Logic: Include all sales that do not have a GSTIN (Unregistered or No-customer Sales)
        $b2csSalesQuery = Sale::with(['customer', 'items.product.category'])
            ->where(function($q) {
                $q->whereDoesntHave('customer') // No customer record
                  ->orWhereHas('customer', function($sub) {
                      $sub->whereNull('gst_number')->orWhere('gst_number', ''); // Customer has no/empty GSTIN
                  });
            })
            ->whereBetween('created_at', [$fromDate, $toDate]);

        $applyGstFilter($b2csSalesQuery);
        $b2csSales = $b2csSalesQuery->get();

        $b2csAgg = [];
        foreach ($b2csSales as $sale) {
            foreach ($sale->items as $item) {
                $rate = $item->gst ?? 0;
                $itemTotal = $item->total;
                $taxable = $itemTotal / (1 + ($rate / 100));
                
                $key = $placeOfSupply . '_' . $rate;
                if (!isset($b2csAgg[$key])) {
                    $b2csAgg[$key] = [
                        'type' => 'OE', 
                        'pos' => $placeOfSupply, 
                        'rate' => $rate, 
                        'tax_rate_pct' => ' ', 
                        'taxable_sum' => 0, 
                        'cess' => 0, 
                        'ecom' => ' '
                    ];
                }
                $b2csAgg[$key]['taxable_sum'] += $taxable;
            }
        }
        foreach ($b2csAgg as $row) {
            $b2csData[] = [
                $row['type'],
                $row['pos'],
                $row['rate'],
                $row['tax_rate_pct'],
                number_format($row['taxable_sum'], 2, '.', ''),
                $row['cess'],
                $row['ecom']
            ];
        }

        // =====================================================================
        // SHEET 3: CDNR (Registered Credit/Debit Notes)
        // =====================================================================
        $cdnrData = [['GSTIN/UIN of Recipient', 'Receiver Name', 'Note Number', 'Note Date', 'Note Type', 'Place Of Supply', 'Reverse Charge', 'Note Supply Type', 'Note Value', 'Applicable % of Tax Rate', 'Rate', 'Taxable Value', 'Cess Amount']];
        $cdnrReturns = \App\Models\SaleReturnMaster::with(['sale.customer', 'details.salesItem', 'details.product.category'])
            ->whereHas('sale.customer', function($q) { 
                $q->whereNotNull('gst_number')->where('gst_number', '!=', ''); 
            })
            ->whereBetween('return_date', [$fromDate, $toDate])
            ->get();

        foreach ($cdnrReturns as $ret) {
            $noteAgg = [];
            $noteTotalValue = 0;
            
            foreach ($ret->details as $det) {
                // Strictly pull rate from the original sale item for perfection
                $rate = $det->salesItem->gst ?? ($det->product->category->gst ?? 0);
                $itemTotal = $det->total;
                $taxable = $itemTotal / (1 + ($rate / 100));
                
                if (!isset($noteAgg[$rate])) {
                    $noteAgg[$rate] = 0;
                }
                $noteAgg[$rate] += $taxable;
                $noteTotalValue += $itemTotal;
            }

            foreach ($noteAgg as $rate => $sumTaxable) {
                $cdnrData[] = [
                    $ret->sale->customer->gst_number,
                    '', // Receiver Name
                    'CN-' . $ret->id,
                    Carbon::parse($ret->return_date)->format('d-M-y'),
                    'C', // Note Type: Credit Note
                    $placeOfSupply,
                    'N', // Reverse Charge
                    'Regular B2B', // Note Supply Type
                    number_format($noteTotalValue, 2, '.', ''),
                    ' ', // Applicable % of Tax Rate
                    $rate,
                    number_format($sumTaxable, 2, '.', ''),
                    0
                ];
            }
        }

        // =====================================================================
        // SHEET 4: CDNUR (Unregistered Credit/Debit Notes)
        // =====================================================================
        $cdnurData = [['UR Type', 'Note Number', 'Note Date', 'Note Type', 'Place Of Supply', 'Note Value', 'Applicable % of Tax Rate', 'Rate', 'Taxable Value', 'Cess Amount']];
        // Includes returns from customers without GSTIN OR missing customer entries
        $cdnurReturns = \App\Models\SaleReturnMaster::with(['sale.customer', 'details.salesItem', 'details.product.category'])
            ->where(function($q) {
                $q->whereDoesntHave('sale.customer') 
                  ->orWhereHas('sale.customer', function($sub) {
                      $sub->whereNull('gst_number')->orWhere('gst_number', '');
                  });
            })
            ->whereBetween('return_date', [$fromDate, $toDate])
            ->get();

        foreach ($cdnurReturns as $ret) {
            $noteAgg = [];
            $noteTotalValue = 0;

            foreach ($ret->details as $det) {
                // Strictly pull rate from original sale item
                $rate = $det->salesItem->gst ?? ($det->product->category->gst ?? 0);
                $itemTotal = $det->total;
                $taxable = $itemTotal / (1 + ($rate / 100));

                if (!isset($noteAgg[$rate])) {
                    $noteAgg[$rate] = 0;
                }
                $noteAgg[$rate] += $taxable;
                $noteTotalValue += $itemTotal;
            }

            foreach ($noteAgg as $rate => $sumTaxable) {
                $cdnurData[] = [
                    'B2CL', // UR Type: Defaulting to B2CL for sample compatibility
                    'CN-' . $ret->id,
                    Carbon::parse($ret->return_date)->format('d-M-y'),
                    'C', // Note Type: Credit Note
                    $placeOfSupply,
                    number_format($noteTotalValue, 2, '.', ''),
                    ' ', // Applicable % of Tax Rate
                    $rate,
                    number_format($sumTaxable, 2, '.', ''),
                    0
                ];
            }
        }

        // =====================================================================
        // SHEET 5: HSN(B2B) (Registered Sales HSN Summary)
        // =====================================================================
        $hsnB2B = [['HSN', 'Description', 'UQC', 'Total Quantity', 'Total Value', 'Taxable Value', 'Integrated Tax Amount', 'Central Tax Amount', 'State/UT Tax Amount', 'Cess Amount', 'Rate']];
        $hsnB2BAgg = [];
        foreach ($b2bSales as $sale) {
            foreach ($sale->items as $item) {
                $hsn = $item->product->category->hsn_code ?? 'NA';
                $rate = $item->gst ?? 0;
                
                $qty = (float)($item->quantity ?? 0);
                $retQty = (float)($item->return_quantity ?? 0);
                $netQty = $qty - $retQty;
                
                if ($netQty <= 0) continue; 

                $unitPrice = $item->price; 
                $netTotal = (float)($netQty * $unitPrice);
                $taxable = $netTotal / (1 + ($rate / 100));
                $taxAmt = $netTotal - $taxable;
                
                $key = $hsn . '_' . $rate;
                if (!isset($hsnB2BAgg[$key])) {
                    $hsnB2BAgg[$key] = ['hsn' => $hsn, 'desc' => '', 'uqc' => 'NOS', 'qty' => 0, 'total_val' => 0, 'taxable_val' => 0, 'igst' => 0, 'cgst' => 0, 'sgst' => 0, 'cess' => 0, 'rate' => $rate];
                }
                $hsnB2BAgg[$key]['qty'] += $netQty;
                $hsnB2BAgg[$key]['total_val'] += $netTotal;
                $hsnB2BAgg[$key]['taxable_val'] += $taxable;
                $hsnB2BAgg[$key]['cgst'] += $taxAmt / 2;
                $hsnB2BAgg[$key]['sgst'] += $taxAmt / 2;
            }
        }
        foreach ($hsnB2BAgg as $row) {
            $hsnB2B[] = [$row['hsn'], $row['desc'], $row['uqc'], $row['qty'], number_format($row['total_val'], 2, '.', ''), number_format($row['taxable_val'], 2, '.', ''), number_format($row['igst'], 2, '.', ''), number_format($row['cgst'], 2, '.', ''), number_format($row['sgst'], 2, '.', ''), $row['cess'], $row['rate']];
        }

        // =====================================================================
        // SHEET 6: HSN(B2C) (Unregistered Sales HSN Summary)
        // =====================================================================
        $hsnB2C = [['HSN', 'Description', 'UQC', 'Total Quantity', 'Total Value', 'Taxable Value', 'Integrated Tax Amount', 'Central Tax Amount', 'State/UT Tax Amount', 'Cess Amount', 'Rate']];
        $hsnB2CAgg = [];
        // Uses $b2csSales which already includes Unregistered and Ghost Sales
        foreach ($b2csSales as $sale) {
            foreach ($sale->items as $item) {
                $hsn = $item->product->category->hsn_code ?? 'NA';
                $rate = $item->gst ?? 0;
                
                $qty = (float)($item->quantity ?? 0);
                $retQty = (float)($item->return_quantity ?? 0);
                $netQty = $qty - $retQty;
                
                if ($netQty <= 0) continue;

                $unitPrice = $item->price;
                $netTotal = (float)($netQty * $unitPrice);
                $taxable = $netTotal / (1 + ($rate / 100));
                $taxAmt = $netTotal - $taxable;

                $key = $hsn . '_' . $rate;
                if (!isset($hsnB2CAgg[$key])) {
                    $hsnB2CAgg[$key] = ['hsn' => $hsn, 'desc' => '', 'uqc' => 'NOS', 'qty' => 0, 'total_val' => 0, 'taxable_val' => 0, 'igst' => 0, 'cgst' => 0, 'sgst' => 0, 'cess' => 0, 'rate' => $rate];
                }
                $hsnB2CAgg[$key]['qty'] += $netQty;
                $hsnB2CAgg[$key]['total_val'] += $netTotal;
                $hsnB2CAgg[$key]['taxable_val'] += $taxable;
                $hsnB2CAgg[$key]['cgst'] += $taxAmt / 2;
                $hsnB2CAgg[$key]['sgst'] += $taxAmt / 2;
            }
        }
        foreach ($hsnB2CAgg as $row) {
            $hsnB2C[] = [$row['hsn'], $row['desc'], $row['uqc'], $row['qty'], number_format($row['total_val'], 2, '.', ''), number_format($row['taxable_val'], 2, '.', ''), number_format($row['igst'], 2, '.', ''), number_format($row['cgst'], 2, '.', ''), number_format($row['sgst'], 2, '.', ''), $row['cess'], $row['rate']];
        }

        // =====================================================================
        // SHEET 7: DOCS (Document Summary)
        // =====================================================================
        $docsData = [['Nature of Document', 'Sr. No. From', 'Sr. No. To', 'Total Number', 'Cancelled']];
        
        // 1. Invoices for outward supply
        $allSalesQuery = Sale::whereBetween('created_at', [$fromDate, $toDate]);
        $applyGstFilter($allSalesQuery);
        $allSales = $allSalesQuery->orderBy('id', 'asc')->get();

        if ($allSales->count() > 0) {
            $firstInv = $allSales->first()->invoice_number ?? $allSales->first()->invoice_no;
            $lastInv = $allSales->last()->invoice_number ?? $allSales->last()->invoice_no;
            $docsData[] = ['Invoices for outward supply', $firstInv, $lastInv, $allSales->count(), 0];
        } else {
            $docsData[] = ['Invoices for outward supply', '', '', 0, 0];
        }

        // 2. Invoices for inward supply from unregistered person
        $docsData[] = ['Invoices for inward supply from unregistered person', '', '', 0, 0];
        
        // 3. Revised Invoice
        $docsData[] = ['Revised Invoice', '', '', 0, 0];

        // 4. Debit Note
        $docsData[] = ['Debit Note', '', '', 0, 0];

        // 5. Credit Note
        $allReturns = \App\Models\SaleReturnMaster::whereBetween('return_date', [$fromDate, $toDate])
            ->orderBy('id', 'asc')
            ->get();
        if ($allReturns->count() > 0) {
            $firstRet = 'CN-' . $allReturns->first()->id;
            $lastRet = 'CN-' . $allReturns->last()->id;
            $docsData[] = ['Credit Note', $firstRet, $lastRet, $allReturns->count(), 0];
        } else {
            $docsData[] = ['Credit Note', '', '', 0, 0];
        }

        // 6. Receipt voucher
        $docsData[] = ['Receipt voucher', '', '', 0, 0];

        // 7. Payment Voucher
        $docsData[] = ['Payment Voucher', '', '', 0, 0];

        // 8. Refund voucher
        $docsData[] = ['Refund voucher', '', '', 0, 0];

        // 9. Delivery Challan for job work
        $docsData[] = ['Delivery Challan for job work', '', '', 0, 0];

        // 10. Delivery Challan for supply on approval
        $docsData[] = ['Delivery Challan for supply on approval', '', '', 0, 0];

        // 11. Delivery Challan in case of liquid gas
        $docsData[] = ['Delivery Challan in case of liquid gas', '', '', 0, 0];

        // 12. Delivery Challan in other cases
        $docsData[] = ['Delivery Challan in other cases', '', '', 0, 0];

        // =====================================================================
        // GENERATE FINAL EXCEL FILE
        // =====================================================================
        $typeLabel = $gstType === 'yes' ? 'With_GST' : ($gstType === 'no' ? 'No_GST' : 'All_Sales');
        $fileName = 'GST_Return_' . $typeLabel . '_' . Carbon::parse($fromDate)->format('M_Y') . '_' . time() . '.xlsx';
        $filePath = 'gst/' . $fileName;
        
        if (!file_exists(public_path('gst'))) {
            mkdir(public_path('gst'), 0777, true);
        }

        $spreadsheet = new Spreadsheet();

        // Sheet 1: b2b
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('b2b');
        $sheet1->fromArray($b2bData, null, 'A1');

        // Sheet 2: b2cs
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('b2cs');
        $sheet2->fromArray($b2csData, null, 'A1');

        // Sheet 3: cdnr
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('cdnr');
        $sheet3->fromArray($cdnrData, null, 'A1');

        // Sheet 4: cdnur
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('cdnur');
        $sheet4->fromArray($cdnurData, null, 'A1');

        // Sheet 5: hsn(b2b)
        $sheet5 = $spreadsheet->createSheet();
        $sheet5->setTitle('hsn(b2b)');
        $sheet5->fromArray($hsnB2B, null, 'A1');

        // Sheet 6: hsn(b2c)
        $sheet6 = $spreadsheet->createSheet();
        $sheet6->setTitle('hsn(b2c)');
        $sheet6->fromArray($hsnB2C, null, 'A1');

        // Sheet 7: docs
        $sheet7 = $spreadsheet->createSheet();
        $sheet7->setTitle('docs');
        $sheet7->fromArray($docsData, null, 'A1');

        $writer = new Xlsx($spreadsheet);
        $writer->save(public_path($filePath));

        GstReport::create([
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'file_path' => $filePath
        ]);

        return redirect()->back()->with('success', 'GST Return Generated Successfully!');
    }
}
