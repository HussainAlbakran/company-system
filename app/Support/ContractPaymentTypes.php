<?php

namespace App\Support;

class ContractPaymentTypes
{
    public const FULL = 'full';

    public const INSTALLMENTS_2 = 'installments_2';

    public const INSTALLMENTS_3 = 'installments_3';

    public const INSTALLMENTS_4 = 'installments_4';

    public const INSTALLMENTS_5 = 'installments_5';

    public const INSTALLMENTS_6 = 'installments_6';

    public const GOVERNMENT = 'government';

    /** @deprecated legacy value */
    public const INSTALLMENTS_LEGACY = 'installments';

    public const ALL = [
        self::FULL,
        self::INSTALLMENTS_2,
        self::INSTALLMENTS_3,
        self::INSTALLMENTS_4,
        self::INSTALLMENTS_5,
        self::INSTALLMENTS_6,
        self::GOVERNMENT,
    ];

    public static function installmentCountFor(string $paymentType): ?int
    {
        return match ($paymentType) {
            self::FULL => 1,
            self::INSTALLMENTS_LEGACY, self::INSTALLMENTS_2 => 2,
            self::INSTALLMENTS_3 => 3,
            self::INSTALLMENTS_4 => 4,
            self::INSTALLMENTS_5 => 5,
            self::INSTALLMENTS_6 => 6,
            self::GOVERNMENT => null,
            default => null,
        };
    }

    public static function isInstallmentType(string $paymentType): bool
    {
        return in_array($paymentType, [
            self::INSTALLMENTS_LEGACY,
            self::INSTALLMENTS_2,
            self::INSTALLMENTS_3,
            self::INSTALLMENTS_4,
            self::INSTALLMENTS_5,
            self::INSTALLMENTS_6,
        ], true);
    }

    public static function labelKey(string $paymentType): string
    {
        return match ($paymentType) {
            self::FULL => 'contracts.payment_full',
            self::INSTALLMENTS_LEGACY, self::INSTALLMENTS_2 => 'contracts.payment_installments_2',
            self::INSTALLMENTS_3 => 'contracts.payment_installments_3',
            self::INSTALLMENTS_4 => 'contracts.payment_installments_4',
            self::INSTALLMENTS_5 => 'contracts.payment_installments_5',
            self::INSTALLMENTS_6 => 'contracts.payment_installments_6',
            self::GOVERNMENT => 'contracts.payment_government',
            default => 'contracts.payment_unknown',
        };
    }
}
