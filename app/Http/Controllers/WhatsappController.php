<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsappMessage;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SalesItems;
use App\Models\ServiceCall;
use Barryvdh\DomPDF\Facade\Pdf;
use Http;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function index()
    {
        // $name = "John Doe";
        // $phone = "919593045397";
        // $message = "Hii " . $name . ". Your order has been placed successfully. Your order ID is 1 We will contact you soon. Thank you for shopping with us! Team IconComputer";
        // $number = $phone;
        // $imageUrl = null; // Optional media URL
        // $response = SendWhatsappMessage::dispatch($number, $message, $imageUrl);

        // dd($response);

        $page_title = "Bulk WhatsApp Message";
        return view('admin.whatsapp.bulk-whatsapp')->with(compact('page_title'));

    }
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Upload image
        $mediaUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time() . '.' . $file->extension();
            $file->move(public_path('whatsapp_media'), $name);
            $mediaUrl = asset('whatsapp_media/' . $name);
        }

        $message = $request->message;

        // Collect phone numbers
        $customers = Customer::pluck('phone');

        foreach ($customers as $index => $phone) {

            try {
                // 👇 FORMAT EACH NUMBER
                $formattedNumber = $this->formatWhatsAppNumber($phone);

                SendWhatsappMessage::dispatch(
                    $formattedNumber,
                    $message,
                    $mediaUrl
                );

            } catch (\Exception $e) {
                \Log::error("Invalid phone number skipped: {$phone}");
            }
        }

        return back()->with('success', 'Bulk WhatsApp messages are being processed!');
    }


    public function sendWhatsAppInvoice($id)
    {
        try {
            $invoice = Sale::with('customer')->findOrFail($id);

            $phone = $invoice->customer->wpnumber ?? $invoice->customer->phone;
            if (!$phone) {
                return response()->json(['error' => 'Customer phone number not found'], 404);
            }

            $products = SalesItems::where('sale_id', '=', $id)->with('product')->get();
            $phone = $this->formatWhatsAppNumber($phone);
            $amount_in_words = $this->numberToWordsIndian($invoice->total);

            // ----- Generate PDF -----
            $pdf = Pdf::loadView('partials.pdf', compact('invoice', 'products', 'amount_in_words'))
                ->setPaper('a4');

            // 2. Create directory if not exists
            $directory = public_path('invoice_pdf');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            // 3. PDF filename (timestamp)
            $filename = 'invoice_' . time() . '_' . $id . '.pdf';
            $filePath = $directory . '/' . $filename;

            // 4. Save PDF to public/invoice_pdf
            file_put_contents($filePath, $pdf->output());

            // ----- WhatsApp API -----
            $msg = "Hello {$invoice->customer->name}, here is your invoice.\n\n*ICONCOMPUTER*\nThank You";
            $mediaUrl = asset('invoice_pdf/' . $filename);

            $response = Http::get('https://nextsms.co.in/api/whatsapp/send', [
                'receiver' => $phone,
                'msgtext' => $msg,
                'token' => config('services.whatsapp.token'),
                'mediaUrl' => $mediaUrl,
            ]);

            // Optional: delete PDF after sending 
            // We'll keep it for 1 minute to ensure the API server has time to download it, 
            // but we'll add a cleanup step here for OLD files (older than 1 hour)
            $this->cleanupOldInvoices($directory);

            return response()->json([
                'status' => $response->successful() ? 'success' : 'error',
                'api_response' => $response->body(),
                'message' => $response->successful() ? "Invoice sent to WhatsApp" : "Failed to send WhatsApp message"
            ]);

        } catch (\Exception $e) {
            \Log::error("WhatsApp Invoice Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete invoice PDFs older than 1 hour to save disk space
     */
    private function cleanupOldInvoices($directory)
    {
        $files = glob($directory . '/*.pdf');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file) >= 3600)) { // 1 hour
                unlink($file);
            }
        }
    }

    public function sendWhatsappService($id)
    {
        $service=ServiceCall::findOrFail($id);
        $phone=$this->formatWhatsAppNumber($service->phone);
        $name=$service->name;
        $admin_remark=$service->remark->admin_remark;
        $company_remark=$service->remark->company;
        $caseId=$service->remark->case_id;
        $mediaUrl=asset('storage/' . $service->remark->image);
        $message=$msg = "Hello *$service->name*,\n\n- $company_remark\n- $admin_remark\n\n Your case id is - $caseId\n\nThank You\n*ICONCOMPUTER*.";
        $response = Http::get('https://nextsms.co.in/api/whatsapp/send', [
            'receiver' => $phone,
            'msgtext' => $message,
            'mediaUrl' => $mediaUrl,
            'token' => config('services.whatsapp.token'),
        ]);

        return redirect()->back()->with('success', 'WhatsApp message sent successfully.');
    }


    public function formatWhatsAppNumber($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // Remove leading 0 if present (e.g., 091 -> 91)
        $phone = ltrim($phone, '0');

        // If number starts with 91 and is 12 digits total → perfect
        if (preg_match('/^91\d{10}$/', $phone)) {
            return $phone; // Already correct: 919876543210
        }

        // If it's a 10-digit Indian number without country code → add 91
        if (preg_match('/^\d{10}$/', $phone)) {
            return '91' . $phone; // e.g., 9876543210 → 919876543210
        }

        // If it starts with +91 (we already removed +), it will be caught above
        // Fallback: try to extract last 10 digits and prepend 91
        if (strlen($phone) > 10) {
            $phone = substr($phone, -10); // take last 10 digits
            return '91' . $phone;
        }

        throw new \Exception("Invalid Indian phone number: {$phone}");
    }
    
    public function sendRewardPointWhatsapp($mobile)
{
    try {

        $reward = \App\Models\RewardPoint::where(
            'mobile',
            $mobile
        )->first();

        if (!$reward) {

            return response()->json([
                'status' => 'error',
                'message' => 'Reward customer not found'
            ]);
        }

        $phone =
            $this->formatWhatsAppNumber(
                $reward->mobile
            );

$message =
"প্রিয় {$reward->customer_name}! 🙏

ICON COMPUTER-এর পক্ষ থেকে আপনাকে শুভেচ্ছা!

আমাদের দোকান থেকে আগের কেনাকাটার জন্য আপনি আকর্ষণীয় রিওয়ার্ড পয়েন্ট অর্জন করেছেন।🎉

👉 মোবাইল নম্বর: {$reward->mobile}

💰 বর্তমান রিওয়ার্ড ব্যালেন্স : {$reward->balance}

🎁 (১ পয়েন্ট = ১ টাকা)

আপনার এই জমানো রিওয়ার্ড পয়েন্টগুলো ব্যবহার করে পরবর্তী কেনাকাটায় বিশেষ ছাড় বা সুবিধা উপভোগ করুন। 

দেরি না করে আজই অর্ডার করুন! https://iconcomputer.in এ এই ওয়েবসাইট অথবা দোকানে গিয়ে রিওয়ার্ড মাইনাস করে নিতে পারেন । 

📞 হেল্পলাইন: 8670569446";

        $response = Http::get(
            'https://nextsms.co.in/api/whatsapp/send',
            [

                'receiver' => $phone,

                'msgtext' => $message,

                'token' =>
                    config('services.whatsapp.token'),
            ]
        );

        return response()->json([

            'status' =>
                $response->successful()
                    ? 'success'
                    : 'error',

            'message' =>
                $response->successful()
                    ? 'Reward Point Message Sent Successfully'
                    : 'Failed To Send WhatsApp Message',
        ]);

    } catch (\Exception $e) {

        \Log::error(
            "Reward WhatsApp Error: " .
            $e->getMessage()
        );

        return response()->json([

            'status' => 'error',

            'message' =>
                'Something went wrong'
        ]);
    }
}

    public function numberToWordsIndian($number)
    {
        $no = floor($number);
        $fraction = round(($number - $no) * 100);

        $words = array(
            '0' => '',
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three',
            '4' => 'Four',
            '5' => 'Five',
            '6' => 'Six',
            '7' => 'Seven',
            '8' => 'Eight',
            '9' => 'Nine',
            '10' => 'Ten',
            '11' => 'Eleven',
            '12' => 'Twelve',
            '13' => 'Thirteen',
            '14' => 'Fourteen',
            '15' => 'Fifteen',
            '16' => 'Sixteen',
            '17' => 'Seventeen',
            '18' => 'Eighteen',
            '19' => 'Nineteen',
            '20' => 'Twenty',
            '30' => 'Thirty',
            '40' => 'Forty',
            '50' => 'Fifty',
            '60' => 'Sixty',
            '70' => 'Seventy',
            '80' => 'Eighty',
            '90' => 'Ninety'
        );

        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');

        $str = array();
        $i = 0;

        while ($no > 0) {
            $divider = ($i == 1) ? 10 : 100;
            $current = $no % $divider;
            $no = floor($no / $divider);

            if ($current) {
                $plural = ($current > 9) ? '' : '';
                $hundred = ($i == 1 && !empty($str)) ? ' and ' : '';
                if ($current < 21) {
                    $str[] = $words[$current] . " " . $digits[$i] . $plural . $hundred;
                } else {
                    $str[] = $words[floor($current / 10) * 10] . " " . $words[$current % 10] . " " . $digits[$i] . $plural;
                }
            } else {
                $str[] = null;
            }
            $i += ($i == 1) ? 1 : 1;
        }

        $result = implode(' ', array_reverse($str));

        if ($fraction > 0) {
            $result .= " and " . $words[$fraction / 10 * 10] . " " . $words[$fraction % 10] . " Paise";
        }

        return trim($result) . " Only";
    }

}
