<?php

namespace App\Services;

class SSLCommerzTransactionElement
{
    public readonly string  $valId;
    public readonly string  $status;
    public readonly string  $validatedOn;
    public readonly string  $tranDate;
    public readonly string  $tranId;
    public readonly float   $amount;
    public readonly float   $storeAmount;
    public readonly string  $bankTranId;
    public readonly string  $cardType;
    public readonly string  $cardNo;
    public readonly string  $cardBrand;
    public readonly string  $cardIssuer;
    public readonly string  $cardIssuerCountry;
    public readonly string  $cardIssuerCountryCode;
    public readonly string  $currency;
    public readonly string  $currencyType;
    public readonly float   $currencyAmount;
    public readonly float   $currencyRate;
    public readonly int     $emiInstalment;
    public readonly float   $emiAmount;
    public readonly float   $discountPercentage;
    public readonly string  $discountRemarks;
    public readonly string  $valueA;
    public readonly string  $valueB;
    public readonly string  $valueC;
    public readonly string  $valueD;
    public readonly int     $riskLevel;
    public readonly string  $riskTitle;
    public readonly string  $error;

    public function __construct(array $data)
    {
        $this->valId                 = (string) ($data['val_id']                    ?? '');
        $this->status                = (string) ($data['status']                    ?? '');
        $this->validatedOn           = (string) ($data['validated_on']              ?? '');
        $this->tranDate              = (string) ($data['tran_date']                 ?? '');
        $this->tranId                = (string) ($data['tran_id']                   ?? '');
        $this->amount                = (float)  ($data['amount']                    ?? 0);
        $this->storeAmount           = (float)  ($data['store_amount']              ?? 0);
        $this->bankTranId            = (string) ($data['bank_tran_id']              ?? '');
        $this->cardType              = (string) ($data['card_type']                 ?? '');
        $this->cardNo                = (string) ($data['card_no']                   ?? '');
        $this->cardBrand             = (string) ($data['card_brand']                ?? '');
        $this->cardIssuer            = (string) ($data['card_issuer']               ?? '');
        $this->cardIssuerCountry     = (string) ($data['card_issuer_country']       ?? '');
        $this->cardIssuerCountryCode = (string) ($data['card_issuer_country_code']  ?? '');
        $this->currency              = (string) ($data['currency']                  ?? '');
        $this->currencyType          = (string) ($data['currency_type']             ?? '');
        $this->currencyAmount        = (float)  ($data['currency_amount']           ?? 0);
        $this->currencyRate          = (float)  ($data['currency_rate']             ?? 0);
        $this->emiInstalment         = (int)    ($data['emi_instalment']            ?? 0);
        $this->emiAmount             = (float)  ($data['emi_amount']                ?? 0);
        $this->discountPercentage    = (float)  ($data['discount_percentage']       ?? 0);
        $this->discountRemarks       = (string) ($data['discount_remarks']          ?? '');
        $this->valueA                = (string) ($data['value_a']                   ?? '');
        $this->valueB                = (string) ($data['value_b']                   ?? '');
        $this->valueC                = (string) ($data['value_c']                   ?? '');
        $this->valueD                = (string) ($data['value_d']                   ?? '');
        $this->riskLevel             = (int)    ($data['risk_level']                ?? 0);
        $this->riskTitle             = (string) ($data['risk_title']                ?? '');
        $this->error                 = (string) ($data['error']                     ?? '');
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, [
            SSLCommerzService::STATUS_VALID,
            SSLCommerzService::STATUS_VALIDATED,
        ], strict: true);
    }

    public function isPending(): bool
    {
        return $this->status === SSLCommerzService::STATUS_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === SSLCommerzService::STATUS_FAILED;
    }

    public function isHighRisk(): bool
    {
        return $this->riskLevel === 1;
    }
}
