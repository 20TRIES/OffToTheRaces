<?php

namespace App\Command\Handler;

use App\Command\Command;

interface HandlerInterface
{
    /**
     * Handles a command.
     *
     * @param Command $command
     * @return mixed
     */
    public function handle($command);
}
