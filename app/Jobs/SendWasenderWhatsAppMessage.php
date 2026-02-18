<?php

namespace App\Jobs;

use App\Services\Integrations\Wasender\WasenderApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWasenderWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $to;
    public string $text;

    public function __construct(string $to, string $text)
    {
        $this->to = $to;
        $this->text = $text;
    }

    public function handle(WasenderApiService $api): void
    {
        $api->sendMessage($this->to, $this->text);
    }
}

