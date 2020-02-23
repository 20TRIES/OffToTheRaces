<?php

namespace App\Race;

use App\Horse\HorseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RacePerformanceModel extends Model
{
    /**
     * @var string
     */
    const ATTRIBUTE_HORSE_ID = 'horse_id';

    /**
     * @var string
     */
    const ATTRIBUTE_RACE_ID = 'race_id';

    /**
     * @var string
     */
    const ATTRIBUTE_TIME_TO_FINISH = 'time_to_finish';

    /**
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * @var string
     */
    protected $table = 'race_performance';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Creates a race performance from a given horse and race.
     *
     * @param HorseModel $horse
     * @param RaceModel $race
     * @return self
     */
    public static function createFromHorseAndRace(HorseModel $horse, RaceModel $race): self
    {
        $instance = new static();
        $instance->setRaceId($race->getId());
        $instance->setHorseId($horse->getId());
        $instance->setTimeToFinish($horse->calculateTimeToRunGivenDistance($race->getLength()));
        return $instance;
    }

    /**
     * Gets the relationship between a race performance and the race that was run.
     *
     * @return HasOne
     */
    public function race()
    {
        return $this->hasOne(RaceModel::class);
    }

    /**
     * Gets the relationship between a race performance model and the horse that it describes.
     *
     * @return HasOne
     */
    public function horse()
    {
        return $this->hasOne(HorseModel::class);
    }

    /**
     * Gets the time take in seconds for a horse to finish a race.
     *
     * @return int
     */
    public function getTimeToFinish(): int
    {
        return $this->getAttribute(static::ATTRIBUTE_TIME_TO_FINISH);
    }

    /**
     * Sets the time taken for a horse to finish a race.
     *
     * @param int $seconds
     * @return self
     */
    public function setTimeToFinish(int $seconds): self
    {
        return $this->setAttribute(static::ATTRIBUTE_TIME_TO_FINISH, $seconds);
    }

    /**
     * Sets the id of the race that a horse run in.
     *
     * @param int $id
     * @return self
     */
    public function setRaceId(int $id): self
    {
        return $this->setAttribute(static::ATTRIBUTE_RACE_ID, $id);
    }

    /**
     * Sets the id of the horse that run a race.
     *
     * @param int $id
     * @return self
     */
    public function setHorseId(int $id): self
    {
        return $this->setAttribute(static::ATTRIBUTE_HORSE_ID, $id);
    }
}
