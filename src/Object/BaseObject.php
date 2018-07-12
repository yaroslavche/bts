<?php

namespace Bitshares\Object;

use Bitshares\Bitshares;

class BaseObject
{
    const SPACE_ID = 0;
    const TYPE_ID = 0;

    private $loaded = false;

    /**
     * Atom regex replace
     * \+\"(.+)\": (.+)
     * protected $1; // $2
     */

    /**
     * @see http://docs.bitshares.org/development/blockchain/objects.html
     */
    public function __construct($data = null)
    {
        $this->setData($data);
    }

    public function __toString()
    {
        return get_class($this);
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    // for twig?
    public function __call($method, $args)
    {
        if (property_exists($this, $method)) {
            return $this->$method;
        }
        throw new \Exception(sprintf('Undefined method "%s"', $method));
    }

    public function setData($data)
    {
        if (gettype($data) === 'string' && ObjectFactory::isValidId($data)) {
            $this->id = $data;
            return;
        }
        if (!is_iterable($data) && !($data instanceof \stdClass)) {
            return;
        }
        foreach ($data as $property => $value) {
            $this->$property = $value;
        }
        $this->loaded = true;
        // beforeLoad, afterLoad, beforeCreate, afterCreate?
        $this->onLoad();
    }

    public function isLoaded() : bool
    {
        return $this->loaded;
    }

    public function getRelatedObjectsIds() : iterable
    {
        if (!$this->loaded) {
            return [];
        }
        $ids = [];
        foreach ($this->relatedObjects() as $relatedObject) {
            if (gettype($relatedObject) === 'string' && ObjectFactory::isValidId($relatedObject)) {
                $ids[] = $relatedObject;
            }
        }
        return $ids;
    }

    public function assignRelated(ObjectPool $pool)
    {
        if (!$this->loaded) {
            return;
        }
        $this->assignRelatedFromPool($pool);
    }

    /**
     * child class custom methods
     */

    /**
     * after loading data into object
     * @return
     */
    protected function onLoad()
    {
    }

    /**
     * related objects id after loading main data
     * @return Iterable
     */
    protected function relatedObjects(): iterable
    {
        return [];
    }

    /**
     * assignRelatedFromPool
     *
     * @param  ObjectPool $pool
     * @return
     */
    protected function assignRelatedFromPool(ObjectPool $pool)
    {
    }
}
