<?php

namespace App\ApplicationSetting\Response;

use App\ApplicationSetting\Exception\NegativeTimeException;
use Illuminate\Http\JsonResponse;

class GetTimeResponse extends JsonResponse
{
    /**
     * @var string
     */
    const ETAG_TIME = 'time';

    /**
     * @param int $time
     * @throws \App\ApplicationSetting\Time\Exception\\App\ApplicationSetting\Exception\NegativeTimeException
     */
    public function __construct(int $time)
    {
        if ($time < 0) {
            throw new NegativeTimeException(sprintf('Times cannot be negative but a time of "%s" was provided', $time));
        }
        parent::__construct(['time' => ['current' => $time]]);
        $this->setEtag(static::ETAG_TIME);
    }
}