<?php

namespace App\ApplicationSetting\Http;

use App\Lib\Enum\DefinesEnumerationsInterface;

class Etag implements DefinesEnumerationsInterface
{
    /**
     * @var string
     */
    const TIME = 'time';

    /**
     * @return array<string>
     */
    public function getEnumerations(): array
    {
        return [
            static::TIME,
        ];
    }
}
