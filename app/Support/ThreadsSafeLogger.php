<?php

namespace App\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class ThreadsSafeLogger
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function sanitizePayload(array $payload): array
    {
        $sensitiveKeys = [
            'access_token',
            'client_secret',
            'signed_request',
            'code',
            'token',
            'authorization',
        ];

        $sanitized = [];

        foreach ($payload as $key => $value) {
            if (is_string($key) && self::isSensitiveKey($key, $sensitiveKeys)) {
                $sanitized[$key] = self::maskToken(is_string($value) ? $value : '[redacted]');

                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = self::sanitizePayload($value);

                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    /**
     * @return array<string, mixed>
     */
    public static function summarizeMetaError(Response $response): array
    {
        $body = $response->json();

        if (! is_array($body)) {
            return [
                'status' => $response->status(),
                'body_preview' => Str::limit($response->body(), 200),
            ];
        }

        $error = $body['error'] ?? $body;

        return [
            'status' => $response->status(),
            'type' => is_array($error) ? ($error['type'] ?? null) : null,
            'code' => is_array($error) ? ($error['code'] ?? null) : null,
            'message' => is_array($error) ? ($error['message'] ?? null) : null,
            'fbtrace_id' => is_array($error) ? ($error['fbtrace_id'] ?? null) : null,
        ];
    }

    public static function maskToken(string $token): string
    {
        if ($token === '') {
            return '[empty]';
        }

        if (strlen($token) <= 8) {
            return '[redacted]';
        }

        return substr($token, 0, 4).'…'.substr($token, -4);
    }

    /**
     * @param  list<string>  $sensitiveKeys
     */
    protected static function isSensitiveKey(string $key, array $sensitiveKeys): bool
    {
        $normalized = Str::lower($key);

        foreach ($sensitiveKeys as $sensitiveKey) {
            if (Str::contains($normalized, Str::lower($sensitiveKey))) {
                return true;
            }
        }

        return false;
    }
}
