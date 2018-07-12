<?php

namespace Bitshares\Object\Implementation;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class AccountTransactionHistory extends BaseObject
{
    const SPACE_ID = 2;
    const TYPE_ID = 9;

    protected $id; // "2.9.168125434"
    protected $account; // "1.2.785035"
    protected $operation_id; // "1.11.161273199"
    protected $sequence; // 167
    protected $next; // "2.9.168125076"

    protected function relatedObjects(): iterable
    {
        yield $this->account;
        yield $this->operation_id;
        yield $this->next;
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        if ($this->account) {
            $this->account = $pool->get($this->account);
        }
        if ($this->operation_id) {
            $this->operation_id = $pool->get($this->operation_id);
        }
        if ($this->next) {
            $this->next = $pool->get($this->next);
        }
    }
}
