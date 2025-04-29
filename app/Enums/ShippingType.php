<?php

namespace App\Enums;


enum ShippingType: string 
{
    case LCL = 'lcl';
    case FCL = 'fcl';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LCL => 'LCL',
            self::FCL => 'FCL',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::LCL => 'success',
            self::FCL => 'primary',
        };
    }
}
