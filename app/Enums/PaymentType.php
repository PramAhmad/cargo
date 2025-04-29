<?php

namespace App\Enums;
use App\Traits\EnumToArray;

enum PaymentType: string
{
    use EnumToArray;

    case Transfer = 'transfer';
    case COD = 'cod';
    case Tempo = 'tempo';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Transfer => 'Transfer',
            self::COD => 'COD',
            self::Tempo => 'Tempo',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Transfer => 'success',
            self::COD => 'warning',
            self::Tempo => 'danger',
        };
    }
}
