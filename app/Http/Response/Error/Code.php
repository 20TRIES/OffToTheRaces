<?php

namespace App\Http\Response\Error;

use App\Lib\Enum\DefinesEnumerationsInterface;

class Code implements DefinesEnumerationsInterface
{
    /**
     * @var string
     */
    const MAX_ACTIVE_RACES_REACHED = 'RACE0000';

    /**
     * @return array<string>
     */
    public function getEnumerations(): array
    {
        return [
            static::MAX_ACTIVE_RACES_REACHED,
        ];
    }
}
