<?php

namespace pammel\SimpleWordpressCms;

class Error
{
    const TYPE_CRAWLER = 'crawler';
    const TYPE_API = 'api';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $code;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Error
    {
        $this->type = $type;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Error
    {
        $this->message = $message;
        return $this;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): Error
    {
        $this->code = $code;
        return $this;
    }

}