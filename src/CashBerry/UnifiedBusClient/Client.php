<?php

namespace CashBerry\UnifiedBusClient;

use CashBerry\UnifiedBusClient\Request\RequestPayload;
use CashBerry\UnifiedBusClient\Response\HighRiskDbCheckResponse;
use GuzzleHttp\Exception\RequestException;
use Karriere\JsonDecoder\JsonDecoder;


class Client
{
    private $busEndpoint = 'http://cash_bus:9501';

    public function sendRequest(string $uuid, int $createdAt, string $serviceName, RequestPayload $request)
    {
        $response = new Response();
        try {
            $client = new \GuzzleHttp\Client();
            $responseFromBus = $client->request(
                'POST',
                $this->busEndpoint,
                [
                    'json' =>
                        [
                            'serviceName' => $serviceName,
                            'payload' => $request->getBody()
                        ]
                ]
            );

            $stringResponse = (string)$responseFromBus->getBody();
            $response = $this->deserialize($stringResponse);
            $response->uuid = $uuid;

            return $response;

        } catch (RequestException $exception) {
            if ($exception->hasResponse()) {
                $response = $this->deserialize((string)$exception->getResponse()->getBody());
                $response->uuid = $uuid;

                return $response;
            }
        } catch (\Exception $exception) {
            $response->error = $exception->getMessage();
            $response->code = $exception->getCode();

            return $response;
        }
    }


    private function deserialize(string $jsonString)
    {
        $jsonDecoder = new JsonDecoder();
        $response = $jsonDecoder->decode($jsonString, Response::class);
        unset($response->details);
        $response->payload = $jsonDecoder->decode($jsonString, HighRiskDbCheckResponse::class, 'details');

        return $response;
    }
}
