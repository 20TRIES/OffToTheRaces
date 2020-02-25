<?php

namespace App\Race;

use App\Horse\HorseModel;
use App\Horse\HorseModelCollection;
use App\Performance\RaceHorsePerformanceModel;
use App\Race\Exception\InvalidRaceLengthException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class RaceModel extends Model
{
    /**
     * @var string
     */
    const ATTRIBUTE_ID = 'id';

    /**
     * @var string
     */
    const ATTRIBUTE_SHORT_NAME = 'short_name';

    /**
     * @var string
     */
    const ATTRIBUTE_NAME = 'name';

    /**
     * @var string
     */
    const ATTRIBUTE_FINISHED_AT = 'finished_at';

    /**
     * @var string
     */
    const ATTRIBUTE_LENGTH = 'length';

    /**
     * @var string
     */
    const TABLE = 'races';

    /**
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * @var string
     */
    protected $table = self::TABLE;

    /**
     * @var string
     */
    protected $primaryKey = self::ATTRIBUTE_ID;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [self::ATTRIBUTE_FINISHED_AT];

    /**
     * Gets the relationship between a race and the horses that participated in it.
     *
     * @return BelongsToMany
     */
    public function horses()
    {
        return $this
            ->belongsToMany(
                HorseModel::class,
                RaceHorsePerformanceModel::TABLE,
                RaceHorsePerformanceModel::ATTRIBUTE_RACE_ID,
                RaceHorsePerformanceModel::ATTRIBUTE_HORSE_ID
            )
            ->using(RaceHorsePerformanceModel::class)
            ->withPivot([
                RaceHorsePerformanceModel::ATTRIBUTE_SECONDS_TO_FINISH
            ]);
    }

    /**
     * Gets the horses that run a race.
     *
     * @return HorseModelCollection<HorseModel>
     */
    public function getHorses(): HorseModelCollection
    {
        return $this->getRelation('horses');
    }

    /**
     * Gets the id of a race.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->getAttribute(static::ATTRIBUTE_ID);
    }

    /**
     * Gets the short name of a race.
     *
     * @return string
     */
    public function getShortName(): string
    {
        return $this->getAttribute(static::ATTRIBUTE_SHORT_NAME);
    }

    /**
     * Initializes the short name of a race.
     *
     * @return self
     */
    public function initializeShortName(): self
    {
        return $this->setAttribute(static::ATTRIBUTE_SHORT_NAME, Str::orderedUuid()->toString());
    }

    /**
     * Gets the name of a race.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getAttribute(static::ATTRIBUTE_NAME);
    }

    /**
     * Sets the name of a race.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        return $this->setAttribute(static::ATTRIBUTE_NAME, $name);
    }

    /**
     * Gets the date time for the time that a race finishes.
     *
     * @return Carbon|null
     */
    public function getFinishedAt(): ?Carbon
    {
        return $this->getAttribute(static::ATTRIBUTE_FINISHED_AT);
    }

    /**
     * Sets the date time for the time that a race finishes.
     *
     * @param Carbon $finishedAt
     * @return $this
     */
    public function setFinishedAt(Carbon $finishedAt): SELF
    {
        return $this->setAttribute(static::ATTRIBUTE_FINISHED_AT, $finishedAt);
    }

    /**
     * Gets the length of a race in meters.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->getAttribute(static::ATTRIBUTE_LENGTH) ?? 0;
    }

    /**
     * Sets the length of a race in meters.
     *
     * @param int $length
     * @return self
     * @throws InvalidRaceLengthException
     */
    public function setLength(int $length): self
    {
        if ($length < 1) {
            throw new InvalidRaceLengthException();
        }
        return $this->setAttribute(static::ATTRIBUTE_LENGTH, $length);
    }

    /**
     * Calculates the start time of a race.
     *
     * @return Carbon
     */
    public function calculateStartTime(): Carbon
    {
        $timesToFinish = [];
        foreach ($this->getHorses() as $horse) {
            assert($horse instanceof HorseModel);
            $timesToFinish[] = $horse->getPerformance()->getTimeToFinish();
        }
        return $this->getFinishedAt()->subSeconds(max($timesToFinish));
    }
}
