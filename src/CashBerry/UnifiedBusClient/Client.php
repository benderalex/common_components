<?php

namespace CashBerry\UnifiedBusClient;

use CashBerry\UnifiedBusClient\Request\RequestPayload;
use CashBerry\UnifiedBusClient\Response\ResponsePayload;
use GuzzleHttp\Exception\RequestException;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Log\LoggerInterface;

/**
 * Class Client
 * @package CashBerry\UnifiedBusClient
 */
class Client
{
    private const LOGGER_GROUP = 'unified_bus_client';
    /**
     * @var string
     */
    private $busEndpoint;

    private $factory;

    private $logger;

    /**
     * Client constructor.
     * @param string $busEndpoint
     * @param LoggerInterface $logger
     * @param $factory
     */
    public function __construct(string $busEndpoint, LoggerInterface $logger, $factory)
    {
        $this->busEndpoint = $busEndpoint;
        $this->logger = $logger;
        $this->factory = $factory;
    }

    /**
     * @param $serviceName
     * @return ResponsePayload
     * @throws \Exception
     */
    private function getResponseByServiceName(string $serviceName): ResponsePayload
    {
        $methodName = 'response' . str_replace('_', '', ucwords($serviceName, '-'));

        if (!method_exists($this->factory,  $methodName)) {
            throw new \Exception("method " . $methodName . "doesn't exist");
        }

        return $this->factory->$methodName();
    }

    /**
     * @param string $uuid
     * @param int $createdAt
     * @param string $serviceName
     * @param RequestPayload $request
     * @return Response
     */
    public function sendRequest(string $uuid, int $createdAt, string $serviceName, RequestPayload $request): Response
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
                            'payload' => $request->getBody(),
                            'requestId' => $uuid
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
            }
        } catch (\Exception $exception) {
            $response->error = $exception->getMessage();
            $response->code = $exception->getCode();
            $this->logger->error(self::LOGGER_GROUP, [
                'request' => [
                    'uuid' => $uuid,
                    'createdAt' => $createdAt,
                    'request' => $request->getBody()
                ],
                'code' => $exception->getCode()
            ]);
        }

        return $response;
    }

    /**
     * @param string $jsonString
     * @param string $serviceName
     * @return mixed
     * @throws \Exception
     */
    private function deserialize(string $jsonString, string $serviceName)
    {
        $jsonDecoder = new JsonDecoder();
        $response = $jsonDecoder->decode($jsonString, Response::class);
        unset($response->details);
        $responseObject = $this->getResponseByServiceName($serviceName);
        $response->payload = $jsonDecoder->decode($jsonString, get_class($responseObject), 'details');

        return $response;
    }
}
