<?php

namespace App\Jobs;

use Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendWhatsappMessage implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;
    protected $number;
    protected $message;
    protected $media;
    public function __construct($number, $message, $media = null)
    {
        $this->number = $number;
        $this->message = $message;
        $this->media = $media;
    }

    public function handle(): void
    {
        static $lastRun = null;

        if ($lastRun) {
            sleep(10);
        }
        $lastRun = now();
        
        try {

            Http::get('https://nextsms.co.in/api/whatsapp/send', [
                'receiver' => $this->number,
                'msgtext' => $this->message,
                'token' => config('services.whatsapp.token'),
                'mediaUrl' => $this->media,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message to {$this->number}: " . $e->getMessage());
        }
    }
}
