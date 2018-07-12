<?php

namespace Bitshares\Object\Protocol;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class VestingBalance extends BaseObject
{
    const SPACE_ID = 1;
    const TYPE_ID = 13;

    protected $id; // "1.13.2196"
    protected $owner; // "1.2.439676"
    protected $balance; // {#376 ▶}
    protected $policy; // array:2 [▶]

    public function __toString()
    {
        return $this->owner;
    }

    protected function relatedObjects(): iterable
    {
        yield $this->$owner;
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        $this->$owner = $pool->get($this->$owner);
    }
}
