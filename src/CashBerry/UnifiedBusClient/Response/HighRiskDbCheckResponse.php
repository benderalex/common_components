<?php

namespace CashBerry\UnifiedBusClient\Response;

class HighRiskDbCheckResponse implements ResponsePayload
{
    public $msg;

    public $code;

    public $is_pep;

    public $is_sanctions;

}
