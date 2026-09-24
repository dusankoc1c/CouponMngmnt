<?php

namespace App\Services;
class CouponImportService
{
    public function sanitizeAndValidateInput(array $row, int $columnOfset) : array
    {
        $code = trim($row[1 + $columnOfset] ?? '');
        $receiverName = trim($row[2 + $columnOfset] ?? '');
        $receiverEmail = trim($row[3 + $columnOfset] ?? '');
        $amountRaw = trim($row[4 + $columnOfset] ?? '');
        $sendDateRaw = trim($row[5 + $columnOfset] ?? '');
        $statusRaw = trim($row[6 + $columnOfset] ?? '');
        $createdAtRaw = trim($row[7 + $columnOfset] ?? '');
        $expiresAtRaw = trim($row[8 + $columnOfset] ?? '');

        if ($code === '') {
            return $this->neispravanRed('Kod kupona je obavezan');
        }

        if (!is_numeric($amountRaw)) {
            return $this->neispravanRed('Iznos nije broj');
        }

        $amount = (float) $amountRaw;

        if ($amount < 0) {
            return $this->neispravanRed('Iznos ne sme biti negativan');
        }

        if ($receiverEmail !== '' && !filter_var($receiverEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->neispravanRed('Email primaoca nije validan');
        }

        return [
            'valid' => true,
            'reason' => null,
            'data' => [
                'code' => $code,
                'receiver_name' => $receiverName === '' ? null : $receiverName,
                'receiver_email' => $receiverEmail === '' ? null : $receiverEmail,
                'discount_amount' => $amount,
                'send_date' => $sendDateRaw === '' ? null : $sendDateRaw,
                'is_used' => strtolower($statusRaw) === 'used',
                'created_at_override' => $createdAtRaw,
                'expires_at_raw' => $expiresAtRaw === '' ? null : $expiresAtRaw,
            ],
        ];
    }

    private function neispravanRed(string $message) : array
    {
        return [
            'valid' => false,
            'reason' => $message,
            'data' => null,
        ];
    }
}
