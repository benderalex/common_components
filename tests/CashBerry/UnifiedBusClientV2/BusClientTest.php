<?php

namespace CashBerry\UnifiedBusClientV2;

use CashBerry\UnifiedBusClientV2\Request\TestRequestPayload;
use CashBerry\UnifiedBusClientV2\Response\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class BusClientTest
 */
class BusClientTest extends TestCase
{
    public function testSendRequest(): void
    {
        $request = new TestRequestPayload();

        $uuid = '9dc18d5a-ca2e-4ef0-a0be-e267e205b60d';
        $testResponse = new Response($uuid);
        $testResponse->status = 200;
        $testResponse->payload = 'success';

        Client::addTestResponse($testResponse);
        $client = new Client('test');
        $response = $client->sendRequest($uuid, $request);

        self::assertStringContainsString($testResponse->payload, $response->payload);
        self::assertStringContainsString($testResponse->uuid, $response->uuid);
    }
}
