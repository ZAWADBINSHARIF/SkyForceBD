<?php

namespace App\Services;

class BulkSMSBDResponse
{
    public readonly int    $code;
    public readonly ?int   $messageId;
    public readonly string $successMessage;
    public readonly string $errorMessage;

    public function __construct(array $body)
    {
        $this->code           = (int)   ($body['response_code']   ?? 0);
        $this->messageId      =          $body['message_id']       ?? null;
        $this->successMessage = (string)($body['success_message'] ?? '');
        $this->errorMessage   = (string)($body['error_message']   ?? '');
    }

    public static function failed(string $reason): self
    {
        return new self([
            'response_code'   => 1005,
            'message_id'      => null,
            'success_message' => '',
            'error_message'   => $reason,
        ]);
    }

    public function successful(): bool
    {
        return $this->code === 202;
    }

    public function errorLabel(): string
    {
        return match ($this->code) {
            202  => 'SMS Submitted Successfully',
            1001 => 'Invalid number',
            1002 => 'Sender ID not correct or disabled',
            1003 => 'Required fields missing',
            1005 => 'Internal error',
            1006 => 'Balance validity not available',
            1007 => 'Insufficient balance',
            1011 => 'User ID not found',
            1012 => 'Masking SMS must be sent in Bengali',
            1013 => 'Sender ID has no gateway for this API key',
            1014 => 'Sender type name not found for this sender',
            1015 => 'Sender ID has no valid gateway',
            1016 => 'Active price info not found for sender type',
            1017 => 'Price info not found for sender type',
            1018 => 'Account owner is disabled',
            1019 => 'Sender type price is disabled',
            1020 => 'Parent account not found',
            1021 => 'Parent active price not found',
            1031 => 'Account not verified — contact administrator',
            1032 => 'IP not whitelisted',
            default => 'Unknown error (code ' . $this->code . ')',
        };
    }
}
