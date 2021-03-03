<?php

namespace CashBerry\UnifiedBusClient\Request;

interface RequestPayload
{
    public function getBody(): array;
}