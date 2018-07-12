<?php

namespace Bitshares\Object\Implementation;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class GlobalProperty extends BaseObject
{
    const SPACE_ID = 2;
    const TYPE_ID = 0;

    protected $id; // "2.0.0"
    protected $parameters; // {#106 ▶}
    protected $next_available_vote_id; // 365
    protected $active_committee_members; // array:11 [▶]
    protected $active_witnesses; // array:21 [▶]

    protected function relatedObjects(): iterable
    {
        foreach ($this->active_committee_members as $committeeId) {
            yield $committeeId;
        }
        foreach ($this->active_witnesses as $witnessId) {
            yield $witnessId;
        }
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        if ($this->active_committee_members) {
            foreach ($this->active_committee_members as &$committeeId) {
                $committeeId = $pool->get($committeeId);
            }
            unset($committeeId);
        }
        if ($this->active_witnesses) {
            foreach ($this->active_witnesses as &$witnessId) {
                $witnessId = $pool->get($witnessId);
            }
            unset($witnessId);
        }
    }
}
