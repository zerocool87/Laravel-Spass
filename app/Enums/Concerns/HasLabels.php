<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

trait HasLabels
{
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
