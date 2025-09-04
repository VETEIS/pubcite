<?php

namespace App\Enums;

enum SignatureStatus: string
{
    case PENDING = 'pending';
    case SIGNED = 'signed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::SIGNED => 'Signed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::SIGNED => 'green',
        };
    }
}
