<?php

namespace App\Horse;

use App\Lib\Model\Model;
use App\Performance\RaceHorsePerformanceModel;
use App\Race\RaceModel;
use Faker\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class HorseModel extends Model
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
    const ATTRIBUTE_BASE_SPEED = 'base_speed';

    /**
     * @var string
     */
    const ATTRIBUTE_SPEED_STAT = 'speed_stat';

    /**
     * @var string
     */
    const ATTRIBUTE_STRENGTH_STAT = 'strength_stat';

    /**
     * @var string
     */
    const ATTRIBUTE_ENDURANCE_STAT = 'endurance_stat';

    /**
     * @var string
     */
    const TABLE = 'horses';

    /**
     * @var int
     */
    const DEFAULT_BASE_SPEED = 5;

    /**
     * @var int
     */
    const ENDURANCE_DISTANCE_MULTIPLIER = 100;

    /**
     * @var int
     */
    const DEFAULT_JOKEY_SPEED_IMPACT = -5;

    /**
     * @var int
     */
    const STRENGTH_MULTIPLIER = 8;

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
     * Creates a new horse from default values.
     *
     * @return self
     */
    public static function createFromDefaults(): self
    {
        $horse = new static();
        $horse->initializeShortName();
        $horse->initializeName();
        $horse->initializeBaseSpeed();
        $horse->initializeSpeedStat();
        $horse->initializeStrengthStat();
        $horse->initializeEnduranceStat();
        return $horse;
    }

    /**
     * @param  array<HorseModel> $models
     * @return Collection
     */
    public function newCollection(array $models = [])
    {
        return new HorseModelCollection($models);
    }

    /**
     * Gets the relationship between a horse and its races.
     *
     * @return BelongsToMany
     */
    public function races()
    {
        return $this->belongsToMany(RaceModel::class, RaceHorsePerformanceModel::TABLE, 'horse_id', 'race_id')->using(RaceHorsePerformanceModel::class);
    }

    /**
     * Gets the race horse performance if "pivot" relation has been loaded.
     *
     * @return RaceHorsePerformanceModel|null
     */
    public function getPerformance()
    {
        return $this->getRelation('pivot');
    }

    /**
     * Gets the id of a horse.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->getAttribute(static::ATTRIBUTE_ID);
    }

    /**
     * Sets the id of a model.
     *
     * @param int $value
     * @return self
     */
    public function setId(int $value): self
    {
        return $this->setAttribute(static::ATTRIBUTE_ID, $value);
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
     * Sets the short name of a model.
     *
     * @param string $shortName
     * @return self
     */
    public function setShortName(string $shortName): self
    {
        return $this->setAttribute(static::ATTRIBUTE_SHORT_NAME, $shortName);
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
     * Initializes a horses name with a randomly generated name.
     *
     * @return $this
     */
    public function initializeName(): self
    {
        return $this->setName(HorseNameGenerator::generate());
    }

    /**
     * Gets the base speed of a horse.
     *
     * @return float
     */
    public function getBaseSpeed(): float
    {
        return $this->getAttribute(static::ATTRIBUTE_BASE_SPEED) ?? 0.0;
    }

    /**
     * Sets the base speed of a model.
     *
     * @param float $value
     * @return self
     */
    public function setBaseSpeed(float $value): self
    {
        return $this->setAttribute(static::ATTRIBUTE_BASE_SPEED, $value);
    }

    /**
     * Initializes a horse's base speed.
     *
     * @return self
     */
    public function initializeBaseSpeed(): self
    {
        return $this->setAttribute(static::ATTRIBUTE_BASE_SPEED, static::DEFAULT_BASE_SPEED);
    }

    /**
     * Generates a random stat value.
     *
     * @return float
     */
    private function generateRandomStat()
    {
        return (float) (Factory::create())->randomFloat(2, 0, 10);
    }

    /**
     * Gets the speed stat for a horse.
     *
     * @return float
     */
    public function getSpeedStat(): float
    {
        return $this->getAttribute(static::ATTRIBUTE_SPEED_STAT) ?? 0.0;
    }

    /**
     * Sets the speed stat for a model.
     *
     * @param float $value
     * @return self
     */
    public function setSpeedStat(float $value): self
    {
        return $this->setAttribute(static::ATTRIBUTE_SPEED_STAT, $value);
    }

    /**
     * Initializes a horse's speed stat.
     *
     * @return self
     */
    public function initializeSpeedStat(): self
    {
        return $this->setSpeedStat($this->generateRandomStat());
    }

    /**
     * Gets the strength stat for a horse.
     *
     * @return float
     */
    public function getStrengthStat(): float
    {
        return $this->getAttribute(static::ATTRIBUTE_STRENGTH_STAT) ?? 0.0;
    }

    /**
     * Sets the strength stat for a model.
     *
     * @param float $value
     * @return self
     */
    public function setStrengthStat(float $value): self
    {
        return $this->setAttribute(static::ATTRIBUTE_STRENGTH_STAT, $value);
    }

    /**
     * Initializes a horse's strength stat.
     *
     * @return self
     */
    public function initializeStrengthStat(): self
    {
        return $this->setStrengthStat($this->generateRandomStat());
    }

    /**
     * Gets the endurance stat for a horse.
     *
     * @return float
     */
    public function getEnduranceStat(): float
    {
        return $this->getAttribute(static::ATTRIBUTE_ENDURANCE_STAT) ?? 0.0;
    }

    /**
     * Sets the endurance stat for a model.
     *
     * @param float $value
     * @return self
     */
    public function setEnduranceStat(float $value): self
    {
        return $this->setAttribute(static::ATTRIBUTE_ENDURANCE_STAT, $value);
    }

    /**
     * Initializes a horse's endurance stat.
     *
     * @return self
     */
    public function initializeEnduranceStat(): self
    {
        return $this->setEnduranceStat($this->generateRandomStat());
    }

    /**
     * Gets the calculated speed of a horse.
     *
     * @return float
     */
    public function getUnladenSpeed(): float
    {
        return $this->getBaseSpeed() + $this->getSpeedStat();
    }

    /**
     * Gets the number of meters that a horse can run at it's top speed before being effected by the weight of a jockey.
     *
     * @return float
     */
    public function getUnladenMeters(): float
    {
        return $this->getEnduranceStat() * static::ENDURANCE_DISTANCE_MULTIPLIER;
    }

    /**
     * Gets a multiplier that can be used to adjust the default jockey impact taking into account a horses strength stat.
     *
     * @return float
     */
    public function getJokeyImpactStrengthMultiplier(): float
    {
        return ($this->getStrengthStat() * static::STRENGTH_MULTIPLIER) / 100;
    }

    /**
     * Gets the impact on a horses speed caused by a jokey (meters per second).
     *
     * @return float
     */
    public function getJokeySpeedImpact(): float
    {
        return static::DEFAULT_JOKEY_SPEED_IMPACT * $this->getJokeyImpactStrengthMultiplier();
    }

    /**
     * @return float
     */
    public function getLadenSpeed(): float
    {
        return $this->getUnladenSpeed() - $this->getJokeySpeedImpact();
    }

    /**
     * Calculates the number of seconds that it would take a horse to run a given distance.
     *
     * @param float $meters
     * @return float
     */
    public function calculateSecondsToRunGivenDistance(float $meters): float
    {
        $metersRunningUnhindered = $this->getUnladenMeters();
        $secondsRunningUnhindered = min($meters, $metersRunningUnhindered) / $this->getUnladenSpeed();
        $metersRunningHindered = max($meters - $metersRunningUnhindered, 0);
        $secondsRunningHindered = $metersRunningHindered / $this->getLadenSpeed();
        return $secondsRunningUnhindered + $secondsRunningHindered;
    }

    /**
     * Gets the number of seconds that a horse is able to run at its unladen speed.
     *
     * @return float
     */
    public function getSecondsAbleToRunAtUnladenSpeed(): float
    {
        return $this->getUnladenMeters() / $this->getUnladenSpeed();
    }

    /**
     * Calculates the number of meters that a horse would cover in a given number of seconds.
     *
     * @param int $seconds
     * @return float
     */
    public function calculateMetersCoverableInNSeconds(int $seconds): float
    {
        $unladenSpeed = $this->getUnladenSpeed();
        $secondsRunUnladen = min($this->getSecondsAbleToRunAtUnladenSpeed(), $seconds);
        $distanceCoveredUnladen = $unladenSpeed * $secondsRunUnladen;
        $secondsRunLaden = $seconds - $secondsRunUnladen;
        $distanceCoveredLaden = $this->getLadenSpeed() * $secondsRunLaden;
        return $distanceCoveredUnladen + $distanceCoveredLaden;
    }

    /**
     * Gets a horses average speed over a given distance.
     *
     * @param float $meters
     * @return float
     */
    public function calculateAverageSpeedOverNMeters(float $meters): float
    {
        return $meters / $this->calculateSecondsToRunGivenDistance($meters);
    }
}
