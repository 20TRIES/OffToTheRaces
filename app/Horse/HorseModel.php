<?php

namespace App\Horse;

use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
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
    protected $table = 'horses';

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
     * Gets the id of a horse.
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
     * Initializes a horse's speed stat.
     *
     * @return self
     */
    public function initializeSpeedStat(): self
    {
        return $this->setAttribute(static::ATTRIBUTE_SPEED_STAT, $this->generateRandomStat());
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
     * Initializes a horse's strength stat.
     *
     * @return self
     */
    public function initializeStrengthStat(): self
    {
        return $this->setAttribute(static::ATTRIBUTE_STRENGTH_STAT, $this->generateRandomStat());
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
     * Initializes a horse's endurance stat.
     *
     * @return self
     */
    public function initializeEnduranceStat(): self
    {
        return $this->setAttribute(static::ATTRIBUTE_ENDURANCE_STAT, $this->generateRandomStat());
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
     * @return int
     */
    public function calculateTimeToRunGivenDistance(float $meters): int
    {
        $metersRunningUnhindered = $this->getUnladenMeters();
        $secondsRunningUnhindered = min($meters, $metersRunningUnhindered) / $this->getUnladenSpeed();
        $metersRunningHindered = max($meters - $metersRunningUnhindered, 0);
        $secondsRunningHindered = $metersRunningHindered / $this->getLadenSpeed();
        return $secondsRunningUnhindered + $secondsRunningHindered;
    }
}
