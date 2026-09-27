<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InvoiceScanController extends Controller
{
    public function scanInvoice(Request $request)
    {
        $request->validate([
            'invoice' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $imagePath = $request->file('invoice')->getRealPath();
        $base64Image = base64_encode(file_get_contents($imagePath));

        // OpenAI Vision API (বা Google Vision API) কল করা
        $apiKey = env('OPENAI_API_KEY'); // আপনার API Key env-তে থাকবে

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Extract details from this invoice image and return JSON with keys: cust_name, purchase_date, cust_address, cust_pincode, cust_phone, product_details, serial_no. Return ONLY valid JSON, no markdown code block.'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => 'data:image/jpeg;base64,' . $base64Image
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 300
        ]);

        $result = $response->json();
        $jsonText = $result['choices'][0]['message']['content'] ?? '{}';

        return response()->json(json_decode($jsonText, true));
    }
}