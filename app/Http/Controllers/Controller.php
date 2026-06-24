<?php

declare(strict_types=1);

namespace App\Http\Controllers;

abstract class Controller
{
    private ?array $communesCache = null;

    /** @return array<int, string> */
    protected function communes(): array
    {
        if ($this->communesCache !== null) {
            return $this->communesCache;
        }

        $list = config('options.communes_haute_vienne', []);
        sort($list, SORT_STRING | SORT_FLAG_CASE);

        return $this->communesCache = $list;
    }
}
