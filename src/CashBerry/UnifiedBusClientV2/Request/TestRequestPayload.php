<?php

namespace CashBerry\UnifiedBusClientV2\Request;

/**
 * Class TestRequestPayload
 */
class TestRequestPayload extends AbstractRequestPayload
{
    public $intProperty = 1;

    public $strProperty = 'string';

    public $arrayProperty = [1,3,4];

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return 'testService';
    }
}
