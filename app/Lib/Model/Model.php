<?php

namespace App\Lib\Model;

use App\Lib\DateTime\Format;

class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var string
     */
    protected $dateFormat = Format::DEFAULT;
}
