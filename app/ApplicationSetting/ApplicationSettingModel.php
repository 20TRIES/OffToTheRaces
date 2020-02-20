<?php

namespace App\ApplicationSetting;

use Illuminate\Database\Eloquent\Model;

class ApplicationSettingModel extends Model
{
    /**
     * @var string
     */
    const ATTRIBUTE_ID = 'id';

    /**
     * @var string
     */
    const ATTRIBUTE_VALUE = 'value';

    /**
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * @var string
     */
    protected $table = 'application_settings';

    /**
     * @var string
     */
    protected $primaryKey = self::ATTRIBUTE_ID;

    /**
     * Gets the id of a setting.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->getAttribute(static::ATTRIBUTE_ID);
    }

    /**
     * Sets the id of a setting.
     *
     * @param mixed $value
     * @return $this
     */
    public function setId($value): self
    {
        $this->setAttribute(static::ATTRIBUTE_ID, $value);
        return $this;
    }

    /**
     * Gets the value of a setting.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->getAttribute(static::ATTRIBUTE_VALUE);
    }

    /**
     * Sets the value of a setting.
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->setAttribute(static::ATTRIBUTE_VALUE, $value);
        return $this;
    }
}
