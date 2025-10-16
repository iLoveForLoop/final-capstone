<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SemaphoreService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('semaphore.api_key');
        $this->apiUrl = config('semaphore.api_url');
    }

    public function send($number, $message)
    {
        $response = Http::asForm()->post($this->apiUrl, [
            'apikey'   => $this->apiKey,
            'number'   => $number,
            'message'  => $message,
            'sendername' => 'Eventory'
        ]);

        return $response->json();
    }
}