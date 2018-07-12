<?php

namespace Bitshares;

use WebSocket\Client;

class WebSocketClient
{
    const TIMEOUT = 1;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var int
     */
    private $queryId = 0;

    public $connectedUrl;

    public function __construct()
    {
        $this->connectToFullNode();
    }

    private function connectToFullNode()
    {
        // $nodes = $this->getNodes(); with priority order
        $nodes = [
            'wss://bitshares.openledger.info/ws',
            // 'wss://bitshares.dacplay.org:8089/ws',
            'wss://dexnode.net/ws',
            // 'ws://node.testnet.bitshares.eu:18092/ws',
            // 'wss://dele-puppy.com/ws',
        ];
        foreach ($nodes as $url) {
            try {
                $this->client = new Client($url, ['timeout' => static::TIMEOUT]);
                $ping = crc32($url . time());
                $this->client->send($ping, 'ping');
                $pingResult = (int)$this->client->receive();
                if ($pingResult === $ping) {
                    $this->connectedUrl = $url;
                    return true;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        throw new \Exception(sprintf('Failed to connect to %d nodes: [%s]', count($nodes), implode(', ', $nodes)));
    }

    public function call(int $apiId, string $method, array $args = [])
    {
        $this->queryId += 1;
        $json = json_encode([
            'jsonrpc' => '2.0',
            'method' => 'call',
            'params' => [
                $apiId,
                $method,
                $args
            ],
            'id' => $this->queryId,
        ]);
        $this->client->send($json);
        $result = json_decode($this->client->receive());
        // dump($this->queryId, $json, $result);
        return $result;
    }
}
