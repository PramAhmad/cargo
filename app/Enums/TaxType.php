<?php

namespace App\Enums;

enum TaxType : string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Percentage',
            self::FIXED => 'Fixed',
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'percentage',
            self::FIXED => 'fixed',
        };
    }
}
