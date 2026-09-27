@extends('layouts.main')
@push('page_title')
    <title>Purchase Barcode</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            #printArea {
                width: 100%;
                display: grid !important;
                grid-template-columns: repeat(2, 48mm);
                /* 2 labels per row */
                justify-content: center;
                gap: 0;
            }

            .barcode-box {
                width: 48mm !important;
                /* half-page width */
                min-height: 30mm;
                padding: 2mm;
                margin: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                page-break-inside: avoid;
                border: 1px solid #000;
                /* remove optional */
            }

            .barcode-box p {
                font-size: 10px;
                margin: 1px 0;
                line-height: 1.1;
                text-align: center;
            }

            .barcode-img {
                width: 90%;
                height: auto;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Screen preview (optional) */
        .barcode-box {
            border: 1px solid #ddd;
            padding: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    </style>
@endpush

@section('content_page')
    <div>
        <div class="max-w-6xl mx-auto p-4">
            <!-- ✅ Print Button -->
            <div class="flex justify-end mb-4">
                <button onclick="printSection('printArea')"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow">
                    🖨️ Print Barcodes
                </button>
            </div>

            <!-- PRINT AREA -->
            <div id="printArea" class="print-container">
                @foreach ($purchase_item as $item)
                    @for ($j = 0; $j < $item->quantity; $j++)
                        <div class="barcode-box">
                            <p class="title">{{ $item->product->brand->name ?? '' }} -
                                {{ $item->product->category->name ?? '' }}</p>
                            <p class="title">{{ $item->product->model ?? '' }}</p>

                            <div
                                style="
        display:flex;
        flex-direction:column;
        align-items:center;
        background:white;
        padding:8px;
    ">
                                {!! DNS1D::getBarcodeSVG($item->product->code, 'C128', 1.0, 45, 'black') !!}
                                {{-- <p style="margin-top:2px; font-size:12px;">{{ $product->code }}</p> --}}
                            </div>
                        </div>
                    @endfor
                @endforeach
            </div>
        </div>

        <!-- PRINT SCRIPT -->
        <script>
            function printSection(divId) {
                let printContent = document.getElementById(divId).innerHTML;
                let originalContent = document.body.innerHTML;

                document.body.innerHTML = `
            <html>
            <head>
                <title>Print Barcode</title>
                <style>
                    body {
                        margin: 0;
                        padding: 0px;
                        font-family: Arial, sans-serif;
                    }

                    /* FLEX PRINTING — 2 PER ROW */
                    .print-container {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: space-between; /* Makes exactly 2 per row */
                        padding-top: 5px;
                        gap-row: 26px;               /* ⭐ Increase vertical gap here */
                    }

                    /* Barcode box */
                    .barcode-box {
                        width: 48%;                /* 2 per row */
                        
                        margin-bottom: 12px;       /* ⭐ Increase row gap here */
                        // border: 1px solid #000;
                        padding: 0px 4px;
                        text-align: center;
                        border-radius: 6px;
                        height: 22mm;
                    }

                    .title {
                        margin: 0;
                        padding: 0px;
                        font-size: 7px;
                        font-weight: bold;
                    }

                    .barcode-img {
                        display: block;
                        margin: 1px auto;
                        padding: 1px;
                        background: #fff;
                    }

                    .code {
                        margin: 0;
                        font-size: 8px;
                    }

                    @media print {
                        @page { 
                            size: auto;
                            margin: 0mm;
                        }
                        body { margin: 0; }
                    }
                </style>
            </head>
            <body>
                <div class="print-container">${printContent}</div>
            </body>
            </html>
            `;

                window.print();
                document.body.innerHTML = originalContent;
                location.reload();
            }
        </script>

    </div>
@endsection
