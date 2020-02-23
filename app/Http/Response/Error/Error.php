<?php

namespace App\Http\Response\Error;

use JsonSerializable;

class Error implements JsonSerializable
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param string $code
     * @param string $message
     */
    public function __construct(string $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Gets the array representation of an error.
     *
     * @return array<string>
     */
    public function jsonSerialize()
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->getMessage()
        ];
    }
}
