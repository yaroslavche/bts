<?php

namespace Bitshares\Object;

use Bitshares\Api;

class ObjectPool
{
    // private $objects;
    public $objects;

    private $api;

    public function __construct(Api $api)
    {
        $this->objects = [];
        $this->api = $api;
    }

    public function add(string $id, \stdClass $data = null)
    {
        if (!array_key_exists($id, $this->objects)) {
            // if(!ObjectFactory::isValidId($id) || !in_array(substr($id, 0, 2), ['1.', '2.'])) return;
            $this->objects[$id] = ObjectFactory::create($id);
            if (!is_null($data)) {
                $this->objects[$id]->setData($data);
            }
        }
        return $this->objects[$id];
    }

    public function load()
    {
        $ids = [];
        foreach ($this->objects as $object) {
            if (is_null($object) || $object->isLoaded()) {
                continue;
            }
            $ids[] = $object->id;
        }
        if (empty($ids)) {
            return;
        }
        $objects = $this->api->get_objects($ids);
        foreach ($objects as $object) {
            if (is_null($object)) {
                continue;
            }
            if ($this->objects[$object->id]) {
                $this->objects[$object->id]->setData($object);
            }
        }
        foreach ($this->objects as $index => $object) {
            if (!is_null($object) && !$object->isLoaded()) {
                $this->objects[$index] = null;
            }
        }
    }

    public function get(?string $id)
    {
        if (is_null($id)) {
            return null;
        }
        if (array_key_exists($id, $this->objects)) {
            if (!is_null($this->objects[$id]) && !$this->objects[$id]->isLoaded()) {
                $this->load();
            }
            return $this->objects[$id];
        }
    }

    public function exists(string $id)
    {
        return array_key_exists($id, $this->objects);
    }
}
