<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectType: string
{
    case Infrastructure = 'infrastructure';
    case Energie = 'energie';
    case Amenagement = 'amenagement';
    case Environnement = 'environnement';
    case Numerique = 'numerique';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Infrastructure => 'Infrastructure',
            self::Energie => 'Énergie',
            self::Amenagement => 'Aménagement',
            self::Environnement => 'Environnement',
            self::Numerique => 'Numérique',
            self::Autre => 'Autre',
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
}
