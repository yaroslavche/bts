<?php

namespace Bitshares;

use Bitshares\WebSocketClient;
use Bitshares\Object\ObjectFactory;
use Bitshares\Object\ObjectPool;
use Bitshares\Object\Protocol\Asset;

final class Bitshares
{
    /**
     * @var WebSocketClient
     */
    private $client;

    /**
     * @var ObjectPool
     */
    private $objectPool;

    /**
     * associative array of pairs $apiName (string) => $api (Api)
     * @var array
     */
    private $api;

    /**
     * @var Bitshares\Object\Implementation\GlobalProperty
     */
    public $globalProperties;

    /**
     * @var Bitshares\Object\Implementation\DynamicGlobalProperty
     */
    public $dynamicGlobalProperties;

    /**
     * full: ['block', 'network_broadcast', 'database', 'history', 'network_node', 'crypto', 'asset', 'debug']
     * not supported: ['block', 'network_node', 'asset', 'debug']
     * @var array
     */
    private $availableApiNames = ['network_broadcast', 'database', 'history', 'crypto'];

    public function __construct(array $apis = ['all'])
    {
        $this->client = new WebSocketClient();

        /**
         * @see http://docs.bitshares.org/api/access.html
         * stateless API-0
         */
        $this->api['stateless'] = new Api($this->client, 0);
        // authencitated API-1
        $this->api['login'] = new Api($this->client, 1);
        $this->api['login']->login('', '');
        $this->initApi('database');
        // $callback = $this->api['database']->set_subscribe_callback(1, true);
        $this->objectPool = new ObjectPool($this->api['database']);

        // if ($apis === ['all']) {
        //     $apis = $this->availableApiNames;
        // }
        // foreach ($apis as $apiName) {
        //     $this->initApi($apiName);
        // }

        // // global properties
        // // get_global_properties() method or object 2.0.0
        // // current blockchain data - dynamicGlobalProperties. Or object 2.1.0
        $this->getObjects(['2.0.0', '2.1.0', '2.3.0']);
        $this->globalProperties = $this->objectPool->get('2.0.0');
        $this->dynamicGlobalProperties = $this->objectPool->get('2.1.0');
    }

    public function getConnectedServer() : ?string
    {
        return $this->client->connectedUrl;
    }

    public function login(string $user = '', string $password = '')
    {
        $result = $this->loginApi->login($user, $password);
        return $result;
    }

    public function initApi(string $apiName)
    {
        if (array_key_exists($apiName, $this->api)) {
            return;
        }
        if (!in_array($apiName, $this->availableApiNames)) {
            throw new \Exception(sprintf('Trying enable unknown API "%s"', $apiName));
        }

        // http://docs.bitshares.org/api/access.html#_CPPv2N8graphene3app9login_api10enable_apiERK6string
        // not working?
        // $this->loginApi->enable_api($name);

        $apiId = $this->loginApi->$apiName();
        $this->api[$apiName] = new Api($this->client, $apiId);
    }

    public function __get($property)
    {
        // getter for (new Bitshares\Bitshares())->${$apiName . 'Api'}
        // mb slow?
        if (substr($property, -3) === 'Api') {
            $apiName = substr($property, 0, -3);
            if (array_key_exists($apiName, $this->api)) {
                return $this->api[$apiName];
            }
            throw new \Exception(sprintf('Unknown API "%s"', $apiName));
        }
        throw new \Exception(sprintf('Unknown property "%s"', $property));
    }

    public function getObject(string $objectId)
    {
        if (!$this->objectPool->exists($objectId)) {
            $this->objectPool->add($objectId);
        }
        $object = $this->objectPool->get($objectId);
        if (is_null($object)) {
            return null;
        }
        $relatedIds = $object->getRelatedObjectsIds();
        foreach ($relatedIds as $relObjectId) {
            if (!$this->objectPool->exists($relObjectId)) {
                $this->objectPool->add($relObjectId);
            }
        }
        $this->objectPool->load();
        $object->assignRelated($this->objectPool);
        return $object;
    }

    public function getObjects(array $objectIds)
    {
        if (empty($objectIds)) {
            return [];
        }
        $objects = [];
        // add id to pool
        foreach ($objectIds as $objectId) {
            if (ObjectFactory::isValidId($objectId) && !$this->objectPool->exists($objectId)) {
                $this->objectPool->add($objectId);
            }
        }
        // load all not loaded objects
        $this->objectPool->load();
        // get related objects ids
        foreach ($objectIds as $objectId) {
            if ($this->objectPool->exists($objectId)) {
                $objects[$objectId] = $object = $this->objectPool->get($objectId);
                if (is_null($object)) {
                    continue;
                }
                $relatedIds = $object->getRelatedObjectsIds();
                foreach ($relatedIds as $relObjectId) {
                    if (!$this->objectPool->exists($relObjectId)) {
                        $this->objectPool->add($relObjectId);
                    }
                }
            }
        }
        // and refresh pool again
        $this->objectPool->load();
        // assign related objects
        foreach ($objectIds as $objectId) {
            if ($this->objectPool->exists($objectId)) {
                $object = $objects[$objectId];
                if (is_null($object)) {
                    continue;
                }
                $object->assignRelated($this->objectPool);
            }
        }
        return $objects;
    }

    public function getBlock(int $blockNum)
    {
        $blockData = $this->api['stateless']->get_block($blockNum);
        // $block = ObjectFactory::createBlock($blockData);
        dump($blockData);
    }

    public function getAllAssets()
    {
        $assets = [];
        // dump(count($this->objectPool->objects));
        $assetsBatch = $this->getAssetsList('AAAAA', 100);
        // dump(count($this->objectPool->objects));
        $assets = array_merge($assets, $assetsBatch);
        while (count($assetsBatch) === 100) {
            $assetsBatch = $this->getAssetsList($assetsBatch[99]->symbol, 100);
            $assets = array_merge($assets, $assetsBatch);
        }
        // dump(count($this->objectPool->objects));
        foreach ($assets as $index => $asset) {
            if (get_class($asset) !== Asset::class) {
                unset($assets[$index]);
                continue;
            }
            $relatedIds = $asset->getRelatedObjectsIds();
            foreach ($relatedIds as $relObjectId) {
                if (!$this->objectPool->exists($relObjectId)) {
                    $this->objectPool->add($relObjectId);
                }
            }
        }
        // dump(count($this->objectPool->objects));
        $this->objectPool->load();
        foreach ($assets as $asset) {
            $asset->assignRelated($this->objectPool);
        }
        // dump($this);
        return $assets;
    }

    private function getAssetsList(string $from, int $count) : array
    {
        $assets = $this->api['database']->list_assets($from, $count);
        foreach ($assets as &$asset) {
            $assetId = $asset->id;
            if (!$this->objectPool->exists($assetId)) {
                $asset = $this->objectPool->add($assetId, $asset);
            }
        }
        unset($asset);
        return $assets;
    }
}
