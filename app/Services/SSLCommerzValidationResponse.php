<?php

namespace App\Services;

class SSLCommerzValidationResponse
{
    public readonly string $apiConnect;
    public readonly int    $noOfTransFound;

    /** @var SSLCommerzTransactionElement[] */
    public readonly array $elements;

    public readonly string $errorMessage;

    public function __construct(array $body)
    {
        $this->apiConnect     = (string) ($body['APIConnect']        ?? '');
        $this->noOfTransFound = (int)    ($body['no_of_trans_found'] ?? 0);
        $this->errorMessage   = '';

        $this->elements = collect($body['element'] ?? [])
            ->map(fn(array $el) => new SSLCommerzTransactionElement($el))
            ->all();
    }

    public static function failed(string $reason): self
    {
        $instance               = new self([]);
        // Override readonly via clone trick for error state
        return new class($reason) extends SSLCommerzValidationResponse {
            public function __construct(string $reason)
            {
                $this->apiConnect     = SSLCommerzService::API_FAILED;
                $this->noOfTransFound = 0;
                $this->elements       = [];
                $this->errorMessage   = $reason;
            }
        };
    }

    public function isConnected(): bool
    {
        return $this->apiConnect === SSLCommerzService::API_DONE;
    }

    public function isAuthFailed(): bool
    {
        return $this->apiConnect === SSLCommerzService::API_FAILED;
    }

    public function isInactive(): bool
    {
        return $this->apiConnect === SSLCommerzService::API_INACTIVE;
    }

    public function isInvalidRequest(): bool
    {
        return $this->apiConnect === SSLCommerzService::API_INVALID_REQUEST;
    }

    /**
     * Return the first VALID or VALIDATED element.
     */
    public function firstSuccessful(): ?SSLCommerzTransactionElement
    {
        foreach ($this->elements as $element) {
            if ($element->isSuccessful()) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Return all VALID or VALIDATED elements.
     *
     * @return SSLCommerzTransactionElement[]
     */
    public function successfulElements(): array
    {
        return array_values(
            array_filter($this->elements, fn($el) => $el->isSuccessful())
        );
    }
}
