<?php

namespace Bitshares\Object\Protocol;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class Asset extends BaseObject
{
    const SPACE_ID = 1;
    const TYPE_ID = 3;

    protected $id; // "1.3.0"
    protected $symbol; // "BTS"
    protected $precision; // 5
    protected $issuer; // "1.2.3"
    protected $options; // {#21 ▶}
    protected $dynamic_asset_data_id; // "2.3.0"
    protected $bitasset_data_id; // default: null
    protected $buyback_account; // default: null

    public function __toString()
    {
        return $this->symbol;
    }

    protected function relatedObjects(): iterable
    {
        yield $this->issuer;
        yield $this->dynamic_asset_data_id;
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        $this->issuer = $pool->get($this->issuer);
        $this->dynamic_asset_data_id = $pool->get($this->dynamic_asset_data_id);
    }
}
