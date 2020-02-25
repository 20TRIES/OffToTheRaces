<?php

namespace App\ApplicationSetting\Http\Response;

use App\ApplicationSetting\Http\Etag;
use App\Lib\DateTime\Format;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class GetApplicationTimeResponse extends JsonResponse
{
    /**
     * @param Carbon $time
     */
    public function __construct(Carbon $time)
    {
        parent::__construct(['time' => ['current' => $time->format(Format::DEFAULT)]]);
        $this->setEtag(Etag::TIME);
    }

    /**
     * Gets the application time set for a response.
     *
     * @return string
     */
    public function getApplicationTime(): string
    {
        return $this->getData(true)['time']['current'];
    }
}
