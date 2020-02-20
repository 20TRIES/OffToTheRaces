<?php

namespace App\Model\ApplicationSetting;

use Illuminate\Database\Eloquent\Model;

class ApplicationSettingModel extends Model
{
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
    protected $primaryKey = 'id';

    /**
     * Gets the id of a setting.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->getAttribute('id');
    }

    /**
     * Gets the value of a setting.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->getAttribute('value');
    }
}
