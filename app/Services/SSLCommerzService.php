<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzService
{
    private string $storeId;
    private string $storePassword;
    private string $validationUrl;

    const STATUS_VALID     = 'VALID';
    const STATUS_VALIDATED = 'VALIDATED';
    const STATUS_PENDING   = 'PENDING';
    const STATUS_FAILED    = 'FAILED';

    const API_DONE            = 'DONE';
    const API_FAILED          = 'FAILED';
    const API_INACTIVE        = 'INACTIVE';
    const API_INVALID_REQUEST = 'INVALID_REQUEST';

    public function __construct()
    {
        $this->storeId       = config('sslcommerz.apiCredentials.store_id', '');
        $this->storePassword = config('sslcommerz.apiCredentials.store_password', '');
        $this->validationUrl = config('sslcommerz.apiDomain') . config('sslcommerz.apiUrl.transaction_status');
    }

    // ── Public API ────────────────────────────────────────────────

    /**
     * Validate a transaction by tran_id and return a typed response.
     */
    public function validateTransaction(string $tranId): SSLCommerzValidationResponse
    {
        try {
            $response = Http::timeout(300)
                ->get($this->validationUrl, [
                    'tran_id'      => $tranId,
                    'store_id'     => $this->storeId,
                    'store_passwd' => $this->storePassword,
                    'format'       => 'json',
                ]);

            $body = $response->json();

            Log::debug('SSLCommerz validation response', [
                'tran_id' => $tranId,
                'body'    => $body,
            ]);

            return new SSLCommerzValidationResponse($body ?? []);
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validation request failed', [
                'tran_id' => $tranId,
                'error'   => $e->getMessage(),
            ]);

            return SSLCommerzValidationResponse::failed($e->getMessage());
        }
    }

    /**
     * Validate and return only the first successful transaction element.
     * Returns null if no valid/validated transaction is found.
     */
    public function getValidTransaction(string $tranId): ?SSLCommerzTransactionElement
    {
        $response = $this->validateTransaction($tranId);

        if (! $response->isConnected()) {
            return null;
        }

        return $response->firstSuccessful();
    }
}
