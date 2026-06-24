<?php

declare(strict_types=1);

namespace App\Enums;

enum ReunionStatus: string
{
    case Planifiee = 'planifiee';
    case Confirmee = 'confirmee';
    case Terminee = 'terminee';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::Planifiee => 'Planifiée',
            self::Confirmee => 'Confirmée',
            self::Terminee => 'Terminée',
            self::Annulee => 'Annulée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planifiee => 'blue',
            self::Confirmee => 'green',
            self::Terminee => 'gray',
            self::Annulee => 'red',
        };
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }

    public function hexColor(): string
    {
        return match ($this) {
            self::Planifiee => '#3b82f6',
            self::Confirmee => '#22c55e',
            self::Terminee => '#6b7280',
            self::Annulee => '#ef4444',
        };
    }
}
