<?php

namespace CashBerry\UnifiedBusClient;

use App\Service\UnifiedBusClient\Factory;
use CashBerry\UnifiedBusClient\Request\RequestPayload;
use CashBerry\UnifiedBusClient\Response\ResponsePayload;
use GuzzleHttp\Exception\RequestException;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Log\LoggerInterface;

class Client
{
    private const LOGGER_GROUP = 'unified_bus_client';

    public function __construct(string $busEndpoint, LoggerInterface $logger, $factory)
    {
        $this->busEndpoint = $busEndpoint;
        $this->logger = $logger;
        $this->factory = $factory;
    }

    private function getReponseByServiceName($serviceName): ResponsePayload
    {
        $methodName = 'response' . str_replace('-', '', ucwords($serviceName, '-'));

        if (!method_exists($this->factory,  $methodName)) {
            throw new \Exception('method ' . $methodName . 'doesn`t exist');
        }

        return $this->factory->$methodName();
    }

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

            $response = $this->deserialize((string)$responseFromBus->getBody(), $serviceName);
            $response->uuid = $uuid;

            $this->logger->info(self::LOGGER_GROUP, [
                'request' => [
                    'uuid' => $uuid,
                    'createdAt' => $createdAt,
                    'request' => $request->getBody()
                ],
                'response' => $response
            ]);

            return $response;

        } catch (RequestException $exception) {
            if ($exception->hasResponse()) {
                $response = $this->deserialize((string)$exception->getResponse()->getBody(), $serviceName);
                $response->uuid = $uuid;

                $this->logger->error(self::LOGGER_GROUP, [
                    'request' => [
                        'uuid' => $uuid,
                        'createdAt' => $createdAt,
                        'request' => $request->getBody()
                    ],
                    'response' => $response,
                    'code' => $exception->getCode()
                ]);

                return $response;
            }
        } catch (\Exception $exception) {
            $response->error = $exception->getMessage();
            $response->code = $exception->getCode();
            $this->logger->error(self::LOGGER_GROUP, [
                'request' => [
                    'uuid' => $uuid,
                    'createdAt' => $createdAt,
                    'request' => $request->getBody
                ],
                'code' => $exception->getCode()
            ]);

            return $response;
        }
    }


    private function deserialize(string $jsonString, string $serviceName)
    {
        $jsonDecoder = new JsonDecoder();
        $response = $jsonDecoder->decode($jsonString, Response::class);
        unset($response->details);
        $responseObject = $this->getReponseByServiceName($serviceName);
        $response->payload = $jsonDecoder->decode($jsonString, get_class($responseObject), 'details');

        return $response;
    }
}
