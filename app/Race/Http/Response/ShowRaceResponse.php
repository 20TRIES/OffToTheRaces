<?php

namespace App\Race\Http\Response;

use App\Race\RaceModel;
use Illuminate\Http\JsonResponse;

class ShowRaceResponse extends JsonResponse
{
    /**
     * @param RaceModel $raceModel
     */
    public function __construct(RaceModel $raceModel)
    {
        $data = [
            'race' => [
                'id' => $raceModel->getShortName(),
                'name' => $raceModel->getName(),
            ],
        ];
        parent::__construct($data);
    }
}
