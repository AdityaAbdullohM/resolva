<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JdoodleService
{
    protected string $endpoint = 'https://api.jdoodle.com/v1/execute';

    public function execute(string $script, string $language = 'php', string $versionIndex = '0'): array
    {
        $payload = [
            'clientId' => env('JDOODLE_CLIENT_ID'),
            'clientSecret' => env('JDOODLE_CLIENT_SECRET'),
            'script' => $script,
            'language' => $language,
            'versionIndex' => $versionIndex,
        ];

        $response = Http::post($this->endpoint, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }
}
