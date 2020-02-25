<?php

namespace App\Lib\Model;

use App\Lib\DateTime\Format;

class Pivot extends \Illuminate\Database\Eloquent\Relations\Pivot
{
    /**
     * @var string
     */
    protected $dateFormat = Format::DEFAULT;
}
