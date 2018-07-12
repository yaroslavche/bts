<?php

namespace Bitshares\Object\Protocol;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class Account extends BaseObject
{
    const SPACE_ID = 1;
    const TYPE_ID = 2;

    protected $id; // "1.2.397379"
    protected $membership_expiration_date; // "1970-01-01T00:00:00"
    protected $registrar; // ""1.2.96393""
    protected $referrer; // ""1.2.364369""
    protected $lifetime_referrer; // ""1.2.364369""
    protected $network_fee_percentage; // "2000"
    protected $lifetime_referrer_fee_percentage; // "3000"
    protected $referrer_rewards_percentage; // "6000"
    protected $name; // ""sova-wallet""
    protected $owner; // "{#2833 ▶}"
    protected $active; // "{#2834 ▶}"
    protected $options; // "{#2835 ▶}"
    protected $statistics; // ""2.6.397379""
    protected $whitelisting_accounts; // "[]"
    protected $blacklisting_accounts; // "[]"
    protected $whitelisted_accounts; // "[]"
    protected $blacklisted_accounts; // "[]"
    protected $owner_special_authority; // "array:2 [▶]"
    protected $active_special_authority; // "array:2 [▶]"
    protected $top_n_control_flags; // "0"

    public function __toString()
    {
        return $this->name;
    }

    protected function relatedObjects(): iterable
    {
        yield $this->registrar;
        yield $this->referrer;
        yield $this->lifetime_referrer;
        yield $this->statistics;
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        $this->registrar = $pool->get($this->registrar);
        $this->referrer = $pool->get($this->referrer);
        $this->lifetime_referrer = $pool->get($this->lifetime_referrer);
        $this->statistics = $pool->get($this->statistics);
    }
}
