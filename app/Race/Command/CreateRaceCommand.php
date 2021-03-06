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
     * @var string|null
     */
    private $name;

    /**
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
