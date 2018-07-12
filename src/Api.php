<?php

namespace Bitshares;

class Api
{
    /**
     * API ID
     * @var int
     */
    private $apiId = 0;

    /**
     * @var WebSocketClient
     */
    private $client;

    /**
     * @var array
     */
    private $queryLog;

    public function __construct(WebSocketClient $client, int $apiId = 0)
    {
        $this->client = $client;
        $this->apiId = $apiId;
    }

    public function __call(string $method, array $args)
    {
        if (!method_exists($this, $method)) {
            $this->queryLog[] = ['method' => $method, 'parameters' => $args];
            $response = $this->client->call($this->apiId, $method, $args);
            if ($response->id) {
                $queryId = $response->id;
                if (isset($response->result)) {
                    return $response->result;
                }
                if (isset($response->error)) {
                    trigger_error('Api call failed', E_USER_NOTICE);
                    // throw new \Exception(sprintf('Error in api call: "%s"', $response->error->message), $response->error->code);
                }
                return $response;
            }
        }
    }
}
