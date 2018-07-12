<?php

namespace Bitshares\Object\Implementation;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class Transaction extends BaseObject
{
    const SPACE_ID = 2;
    const TYPE_ID = 7;

    protected $trx; // {#178 ▶}
    protected $trx_id; // "da520e3c4ec809da8d302a91676322f2a1c22b45"

    protected function relatedObjects(): iterable
    {
        return [];
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
    }
}
