<?php

namespace App\Lib\DateTime;

use App\Lib\Enum\DefinesEnumerationsInterface;

/**
 * A set of constants that define the date formats used within an application.
 */
class Format implements DefinesEnumerationsInterface
{
    /**
     * @var string
     */
    const DEFAULT = 'Y-m-d H:i:s';

    /**
     * @return array<string>
     */
    public function getEnumerations(): array
    {
        return [
            static::DEFAULT,
        ];
    }
}
