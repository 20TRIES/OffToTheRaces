<?php

namespace App\Lib\Enum\Http\Request;

use App\Lib\Enum\DefinesEnumerationsInterface;

/**
 * A class of constants for commonly used header names.
 */
class Header implements DefinesEnumerationsInterface
{
    /**
     * @var string
     */
    const IF_MATCH = 'If-Match';

    /**
     * @return array<string>
     */
    public function getEnumerations(): array
    {
        return [
            static::IF_MATCH,
        ];
    }
}
