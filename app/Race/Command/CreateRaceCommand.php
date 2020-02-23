<?php

namespace App\Race\Command;

use App\Lib\Command\Command;
use App\Race\Command\Handler\CreateRaceHandler;

/**
 * @see CreateRaceHandler
 */
class CreateRaceCommand extends Command
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
