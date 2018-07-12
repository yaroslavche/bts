<?php

namespace Bitshares\Object\Protocol;

use Bitshares\Object\BaseObject;
use Bitshares\Object\ObjectPool;

class OperationHistory extends BaseObject
{
    const SPACE_ID = 1;
    const TYPE_ID = 11;

    protected $id; // "1.11.161274166"
    protected $op; // array:2 [▶]
    protected $result; // array:2 [▶]
    protected $block_num; // 25590752
    protected $trx_in_block; // 5
    protected $op_in_trx; // 0
    protected $virtual_op; // 21336

    public $helper;

    protected function relatedObjects(): iterable
    {
        // yield $this->block_num;

        if ($this->result[0] === 1) {
            yield $this->result[1];
        }

        switch ($this->helper->getTypeId()) {
            case OperationHistoryHelper::TRANSFER:
                yield $this->op[1]->from;
                yield $this->op[1]->to;
                yield $this->op[1]->amount->asset_id;
                break;

            default:
                // dump(sprintf('implement history operation type %d!', $this->helper->getTypeId()));
                break;
        }
    }

    protected function assignRelatedFromPool(ObjectPool $pool)
    {
        if ($this->result[0] === 1) {
            $this->result[1] = $pool->get($this->result[1]);
        }
        
        switch ($this->helper->getTypeId()) {
            case OperationHistoryHelper::TRANSFER:
                $this->op[1]->from = $pool->get($this->op[1]->from);
                $this->op[1]->to = $pool->get($this->op[1]->to);
                $this->op[1]->amount->asset_id = $pool->get($this->op[1]->amount->asset_id);
                break;

            default:
                // dump(sprintf('implement history operation type %d!', $this->helper->getTypeId()));
                break;
        }
    }

    protected function onLoad()
    {
        $this->helper = new OperationHistoryHelper($this);
    }
}
