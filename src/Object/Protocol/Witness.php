<?php

namespace Bitshares\Object\Protocol;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class Witness extends BaseObject
{
    const SPACE_ID = 1;
    const TYPE_ID = 6;

    protected $id; // "1.6.3"
    protected $witness_account; // "1.2.90742"
    protected $last_aslot; // 172345
    protected $signing_key; // "BTS6eTWdfBXvgmfY1VLSRc6iiFk4qMbmsLcLCEEbbLYJBgAYw3C7L"
    protected $pay_vb; // "1.13.8"
    protected $vote_id; // "1:0"
    protected $total_votes; // "4696225791800"
    protected $url; // ""
    protected $total_missed; // 982
    protected $last_confirmed_block_num; // 167184

    protected function relatedObjects(): iterable
    {
        yield $this->witness_account;
        yield $this->pay_vb;
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        if (gettype($this->witness_account) === 'string') {
            $this->witness_account = $pool->get($this->witness_account);
        }
        if (gettype($this->pay_vb) === 'string') {
            $this->pay_vb = $pool->get($this->pay_vb);
        }
        // $this->last_confirmed_block_num = $pool->get($this->statistics, Block::class);
    }

    protected function onLoad()
    {
        $this->url = filter_var($this->url, FILTER_VALIDATE_URL);
        // TODO: url without scheme
        // $url = parse_url($this->url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
    }
}
