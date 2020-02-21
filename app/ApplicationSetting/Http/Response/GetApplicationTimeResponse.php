<?php

namespace App\ApplicationSetting\Http\Response;

use App\ApplicationSetting\Exception\NegativeTimeException;
use App\ApplicationSetting\Http\Etag;
use Illuminate\Http\JsonResponse;

class GetApplicationTimeResponse extends JsonResponse
{
    /**
     * @param int $time
     * @throws NegativeTimeException
     */
    public function __construct(int $time)
    {
        if ($time < 0) {
            throw new NegativeTimeException(sprintf('Times cannot be negative but a time of "%s" was provided', $time));
        }
        parent::__construct(['time' => ['current' => $time]]);
        $this->setEtag(Etag::TIME);
    }

    /**
     * Gets the application time set for a response.
     *
     * @return int
     */
    public function getApplicationTime(): int
    {
        return $this->getData(true)['time']['current'];
    }
}
