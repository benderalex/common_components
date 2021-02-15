<?php

namespace CashBerry\UnifiedBusClient;

/**
 * Class Response
 * @package CashBerry\UnifiedBusClient
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
    private $createdAt;

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
     * Response constructor.
     */
    public function __construct()
    {
        $this->createdAt = time();
    }
}
