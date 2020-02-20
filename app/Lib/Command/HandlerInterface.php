<?php

namespace App\Lib\Command;

use App\Lib\Command\Command;

interface HandlerInterface
{
    /**
     * Handles a command.
     *
     * @param \App\Lib\Command\Command $command
     * @return mixed
     */
    public function handle($command);
}
