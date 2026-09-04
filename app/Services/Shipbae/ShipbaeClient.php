<?php

namespace App\Services\Shipbae;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShipbaeClient
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private int $timeout;

    public function __construct(?array $config = null)
    {
        $config = $config ?? config('services.shipbae', []);

        $this->baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');
        $this->clientId = (string) ($config['client_id'] ?? '');
        $this->clientSecret = (string) ($config['client_secret'] ?? '');
        $this->timeout = (int) ($config['timeout'] ?? 60);
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->clientId !== '' && $this->clientSecret !== '';
    }

    public function getAccessToken(bool $forceRefresh = false): ?string
    {
        if (!$this->isConfigured()) {
            Log::error('Shipbae credentials not configured');
            return null;
        }

        $cacheKey = 'shipbae_access_token_' . md5($this->baseUrl . '|' . $this->clientId);

        if (!$forceRefresh) {
            $cached = Cache::get($cacheKey);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        }

        $response = $this->request('POST', '/auth/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ], false);

        if (!$response['ok'] || !is_array($response['data'])) {
            Log::error('Shipbae auth failed', [
                'http_code' => $response['http_code'],
                'error' => $response['error'],
                'body' => $response['raw'],
            ]);
            return null;
        }

        $token = $response['data']['access_token'] ?? null;
        $expiresIn = (int) ($response['data']['expires_in'] ?? 0);

        if (!is_string($token) || $token === '') {
            Log::error('Shipbae auth response missing access_token', ['data' => $response['data']]);
            return null;
        }

        // Refresh a bit early so requests don't race expiry.
        $ttl = max(60, $expiresIn > 120 ? ($expiresIn - 120) : max(30, $expiresIn - 10));
        Cache::put($cacheKey, $token, $ttl);

        return $token;
    }

    public function getRates(array $shipmentPayload): array
    {
        return $this->authorizedRequest('POST', '/shipments/rates', ['shipment' => $shipmentPayload]);
    }

    public function createShipment(array $shipmentPayload): array
    {
        return $this->authorizedRequest('POST', '/shipments', ['shipment' => $shipmentPayload]);
    }

    public function getShipment(int $shipmentId): array
    {
        return $this->authorizedRequest('GET', '/shipments/' . $shipmentId);
    }

    public function trackShipment(int $shipmentId): array
    {
        return $this->authorizedRequest('GET', '/shipments/' . $shipmentId . '/track');
    }

    public function refundShipment(int $shipmentId): array
    {
        return $this->authorizedRequest('POST', '/shipments/' . $shipmentId . '/refund');
    }

    public function verifyAddress(array $address, ?string $provider = null): array
    {
        $body = ['address' => $address];
        if ($provider) {
            $body['address_verification_provider'] = $provider;
        }

        return $this->authorizedRequest('POST', '/addresses', $body);
    }

    private function authorizedRequest(string $method, string $path, ?array $body = null): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'ok' => false,
                'http_code' => 401,
                'data' => null,
                'raw' => null,
                'error' => 'Unable to obtain Shipbae access token',
            ];
        }

        $response = $this->request($method, $path, $body, true, $token);

        // One retry on expired/invalid token.
        if (in_array($response['http_code'], [401, 403], true)) {
            $token = $this->getAccessToken(true);
            if ($token) {
                $response = $this->request($method, $path, $body, true, $token);
            }
        }

        return $response;
    }

    private function request(string $method, string $path, ?array $body = null, bool $withAuth = false, ?string $token = null): array
    {
        if ($this->baseUrl === '') {
            return [
                'ok' => false,
                'http_code' => 500,
                'data' => null,
                'raw' => null,
                'error' => 'Shipbae base URL not configured',
            ];
        }

        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        if ($withAuth && $token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $curl = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($body !== null && strtoupper($method) !== 'GET') {
            $options[CURLOPT_POSTFIELDS] = json_encode($body);
        }

        curl_setopt_array($curl, $options);

        $raw = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curlError) {
            Log::error('Shipbae curl error', [
                'method' => $method,
                'path' => $path,
                'error' => $curlError,
            ]);

            return [
                'ok' => false,
                'http_code' => 500,
                'data' => null,
                'raw' => null,
                'error' => 'Network error: ' . $curlError,
            ];
        }

        $data = null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            }
        }

        $ok = $httpCode >= 200 && $httpCode < 300;
        $error = null;
        if (!$ok) {
            $error = $this->extractErrorMessage($data, $raw, $httpCode);
            Log::warning('Shipbae API error', [
                'method' => $method,
                'path' => $path,
                'http_code' => $httpCode,
                'error' => $error,
                'body' => is_string($raw) ? substr($raw, 0, 2000) : $raw,
            ]);
        }

        return [
            'ok' => $ok,
            'http_code' => $httpCode,
            'data' => $data,
            'raw' => $raw,
            'error' => $error,
        ];
    }

    private function extractErrorMessage($data, $raw, int $httpCode): string
    {
        if (is_array($data)) {
            if (!empty($data['message']) && is_string($data['message'])) {
                return $data['message'];
            }

            // Nested shape: {"error":{"name":"...","message":"[from_address.zip] ...","status":400}}
            if (!empty($data['error']) && is_array($data['error'])) {
                if (!empty($data['error']['message']) && is_string($data['error']['message'])) {
                    return $data['error']['message'];
                }
                if (!empty($data['error']['name']) && is_string($data['error']['name'])) {
                    return $data['error']['name'];
                }
            }

            if (!empty($data['error']) && is_string($data['error'])) {
                return $data['error'];
            }

            if (!empty($data['fields']) && is_array($data['fields'])) {
                $parts = [];
                foreach ($data['fields'] as $key => $value) {
                    if (is_array($value)) {
                        $parts[] = $key . ': ' . implode(', ', $value);
                    } elseif (is_string($value)) {
                        $parts[] = $key . ': ' . $value;
                    }
                }
                if ($parts) {
                    return implode('; ', $parts);
                }
            }

            if (!empty($data['errors']) && is_array($data['errors'])) {
                $parts = [];
                foreach ($data['errors'] as $key => $value) {
                    if (is_array($value)) {
                        $parts[] = $key . ': ' . implode(', ', $value);
                    } elseif (is_string($value)) {
                        $parts[] = $value;
                    }
                }
                if ($parts) {
                    return implode('; ', $parts);
                }
            }
        }

        // Last resort: try nested message inside raw JSON without dumping full body.
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $nested = $decoded['error']['message'] ?? ($decoded['message'] ?? null);
                if (is_string($nested) && $nested !== '') {
                    return $nested;
                }
            }

            return 'Shipbae API error (HTTP ' . $httpCode . '): ' . substr($raw, 0, 500);
        }

        return 'Shipbae API error (HTTP ' . $httpCode . ')';
    }
}
