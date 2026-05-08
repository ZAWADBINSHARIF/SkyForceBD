<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BulkSMSBDService
{
    private string $url      = 'http://bulksmsbd.net/api/smsapi';
    private string $apiKey;
    private string $senderId;

    public function __construct()
    {
        $this->apiKey   = config('services.bulksmsbd.api_key');
        $this->senderId = config('services.bulksmsbd.sender_id');
    }

    // ── Public API ────────────────────────────────────────────────

    /**
     * Send the same message to one or many numbers.
     *
     * @param  array  $numbers  Single number or array of numbers
     * @param  string        $message
     * @return BulkSMSBDResponse
     */
    public function send(array $numbers, string $message): BulkSMSBDResponse
    {
        $joined = implode(',', normalize_phone_numbers($numbers));
        
        $payload = [
            'api_key'  => $this->apiKey,
            'senderid' => $this->senderId,
            'number'   => $joined,
            'message'  => $message,
        ];

        return $this->dispatch($payload);
    }

    // ── Internals ─────────────────────────────────────────────────

    private function dispatch(array $payload): BulkSMSBDResponse
    {
        try {
            $raw = Http::asForm()
                ->withOptions(['verify' => false])
                ->timeout(15)
                ->post($this->url, $payload);

            $body = $raw->json();

            Log::debug('BulkSMSBD response', $body ?? ['raw' => $raw->body()]);

            return new BulkSMSBDResponse($body ?? []);
        } catch (\Throwable $e) {
            Log::error('BulkSMSBD request failed', ['error' => $e->getMessage()]);

            return BulkSMSBDResponse::failed($e->getMessage());
        }
    }
}
