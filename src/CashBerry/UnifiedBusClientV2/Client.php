<?php

namespace CashBerry\UnifiedBusClientV2;

use Exception;
use CashBerry\UnifiedBusClientV2\Request\AbstractRequestPayload;
use CashBerry\UnifiedBusClientV2\Response\Response;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

/**
 * Class Client
 */
class Client
{
    private const LOGGER_GROUP = 'bus_client';

    /**
     * @var Response[]
     */
    private static $testResponses = [];

    /**
     * @var string
     */
    private $busEndpoint;

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * Client constructor.
     *
     * @param string $busEndpoint
     * @param LoggerInterface|null $logger
     */
    public function __construct(string $busEndpoint, ?LoggerInterface $logger = null)
    {
        $this->busEndpoint = $busEndpoint;
        $this->logger = $logger;
    }

    /**
     * @param Response $response
     */
    public static function addTestResponse(Response $response): void
    {
        self::$testResponses[] = $response;
    }

    /**
     * @param string $uuid
     * @param AbstractRequestPayload $request
     *
     * @return Response
     * @throws GuzzleException
     */
    public function sendRequest(string $uuid, AbstractRequestPayload $request): Response
    {
        if (count(self::$testResponses) > 0) {
            if ($this->logger !== null) {
                $this->logger->info('Test response was returner');
            }

            return array_shift(self::$testResponses);
        }

        $response = new Response($uuid);
        try {
            $client = new HttpClient();
            $responseFromBus = $client->request(
                'POST',
                $this->busEndpoint,
                [
                    'json' =>
                        [
                            'serviceName' => $request->getServiceName(),
                            'payload' => $request->toArray(),
                            'requestId' => $uuid,
                        ],
                ]
            );

            $response = $this->getResponse((string)$responseFromBus->getBody(), $uuid);
            if ($this->logger !== null) {
                $this->logger->info(self::LOGGER_GROUP,
                    [
                        'request' => [
                            'uuid' => $uuid,
                            'createdAt' => time(),
                            'request' => $request->toArray(),
                        ],
                        'response' => $response,
                    ]
                );
            }
        } catch (RequestException $exception) {
            if ($exception->hasResponse()) {
                $response = $this->getResponse((string)$exception->getResponse()->getBody(), $uuid);
            }
        } catch (Exception $exception) {
            $response->error = $exception->getMessage();
            $response->code = $exception->getCode();
        }

        return $response;
    }

    /**
     * @param string $jsonString
     * @param string $uuid
     *
     * @return Response
     */
    private function getResponse(string $jsonString, string $uuid): Response
    {
        $payload = json_decode($jsonString, true);
        /**
         * "status": "success",
         * "error": "",
         * "details": {
         * "access_token": "test-access-token"
         * }
         */

        $response = new Response($uuid);

        $response->payload = $payload['details'] ?? [];
        $response->status = $payload['status'] ?: 'error';
        $response->error = $payload['error'] ?: '';

        return $response;
    }
}
