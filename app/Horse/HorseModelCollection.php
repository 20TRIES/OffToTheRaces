<?php

namespace App\Horse;

use Illuminate\Database\Eloquent\Collection;

class HorseModelCollection extends Collection
{
    /**
     * Sorts a collection by the distance that they would cover over a given number of seconds.
     *
     * @param int $secondsIntoRace
     * @param int $options [$options=SORT_REGULAR]
     * @param bool $descending [$descending=false]
     * @return HorseModelCollection
     */
    public function sortByDistanceCoveredAfterNSeconds(int $secondsIntoRace, $options = SORT_REGULAR, $descending = true)
    {
        return $this->sortBy(function (HorseModel $horse) use ($secondsIntoRace) {
            return $secondsIntoRace ? $horse->calculateMetersCoverableInNSeconds($secondsIntoRace) : 0;
        }, $options, $descending);
    }
}
