<?php

namespace App\Http\Response\Error;

use Illuminate\Contracts\Translation\Translator;

class ErrorBuilder
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Builds an error from a given code.
     *
     * @param string $errorCode
     * @param array  $data [$data=[]]
     * @return Error
     */
    public function build(string $errorCode, array $data = [])
    {
        return new Error($errorCode, $this->translator->get(sprintf('errors.%s', $errorCode), $data));
    }
}
