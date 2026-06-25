<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLabels;

enum ProjectType: string
{
    use HasLabels;

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
}
