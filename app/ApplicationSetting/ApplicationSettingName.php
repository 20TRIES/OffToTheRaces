<?php

namespace App\ApplicationSetting;

use App\Lib\Enum\DefinesEnumerationsInterface;

class ApplicationSettingName implements DefinesEnumerationsInterface
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
