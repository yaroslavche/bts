<?php

namespace Bitshares\Object\Implementation;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class DynamicGlobalProperty extends BaseObject
{
    const SPACE_ID = 2;
    const TYPE_ID = 1;

    protected $id; // "2.1.0"
    protected $head_block_number; // 25578161
    protected $head_block_id; // "01864ab17ae67e9d6f8be9d4195475d5a1113012"
    protected $time; // "2018-03-26T23:40:27"
    protected $current_witness; // "1.6.28"
    protected $next_maintenance_time; // "2018-03-27T00:00:00"
    protected $last_budget_time; // "2018-03-26T23:00:00"
    protected $witness_budget; // 39400000
    protected $accounts_registered_this_interval; // 39
    protected $recently_missed_count; // 0
    protected $current_aslot; // 25724366
    protected $recent_slots_filled; // "340282366920938463463374607431768211455"
    protected $dynamic_flags; // 0
    protected $last_irreversible_block_num; // 25578147

    protected function relatedObjects(): iterable
    {
        yield $this->current_witness;
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        $this->current_witness = $pool->get($this->current_witness);
    }
}
