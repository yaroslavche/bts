<?php

namespace Bitshares\Object\Protocol;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class CommitteeMember extends BaseObject
{
    const SPACE_ID = 1;
    const TYPE_ID = 5;

    protected $id; // "1.5.15"
    protected $committee_member_account; // "1.2.121"
    protected $vote_id; // "0:85"
    protected $total_votes; // "71558252259307"
    protected $url; // "transwiser.com"

    protected function relatedObjects(): iterable
    {
        yield $this->committee_member_account;
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        $this->committee_member_account = $pool->get($this->committee_member_account);
    }
}
