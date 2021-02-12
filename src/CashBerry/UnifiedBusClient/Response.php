<?php

namespace CashBerry\UnifiedBusClient;

final class Response
{
    public $uuid;
    
    private $createdAt;

    public $payload;

    public $status;

    public $error;

    public function __construct()
    {
        $this->createdAt = time();
    }
}
