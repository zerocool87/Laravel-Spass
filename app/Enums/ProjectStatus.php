<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case Planifie = 'planifie';
    case EnCours = 'en_cours';
    case Termine = 'termine';
    case Suspendu = 'suspendu';
    case Annule = 'annule';

    public function label(): string
    {
        return match ($this) {
            self::Planifie => 'Planifié',
            self::EnCours => 'En cours',
            self::Termine => 'Terminé',
            self::Suspendu => 'Suspendu',
            self::Annule => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planifie => 'blue',
            self::EnCours => 'yellow',
            self::Termine => 'green',
            self::Suspendu => 'orange',
            self::Annule => 'red',
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
