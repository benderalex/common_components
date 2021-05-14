<?php

namespace CashBerry\UnifiedBusClientV2\Response;

/**
 * Class Response
 */
final class Response
{
    /**
     * @var string
     */
    public $uuid;

    /**
     * @var int
     */
    public $createdAt;

    /**
     * @var string
     */
    public $payload;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $error;

    /**
     * @var int
     */
    public $code;

    /**
     * Response constructor.
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->createdAt = time();
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error || $this->error === null || $this->status === 'error';
    }
}
