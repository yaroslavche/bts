<?php

namespace Bitshares\Object\Protocol;

use \Bitshares\Bitshares;

class OperationHelper
{
    const TRANSFER = 0; // 81CA80
    const LIMIT_ORDER_CREATE = 1; // 6BBCD7
    const LIMIT_ORDER_CANCEL = 2; // E9C842
    const CALL_ORDER_UPDATE = 3; // E96562
    const FILL_ORDER_VIRTUAL = 4; // 008000
    const ACCOUNT_CREATE = 5; // CCCCCC
    const ACCOUNT_UPDATE = 6; // FF007F
    const ACCOUNT_WHITELIST = 7; // FB8817
    const ACCOUNT_UPGRADE = 8; // 552AFF
    const ACCOUNT_TRANSFER = 9; // AA2AFF
    const ASSET_CREATE = 10; // D400FF
    const ASSET_UPDATE = 11; // 0000FF
    const ASSET_UPDATE_BITASSET = 12; // AA7FFF
    const ASSET_UPDATE_FEED_PRODUCERS = 13; // 2A7FFF
    const ASSET_ISSUE = 14; // 7FAAFF
    const ASSET_RESERVE = 15; // 55FF7F
    const ASSET_FUND_FEE_POOL = 16; // 55FF7F
    const ASSET_SETTLE = 17; // FFFFAA
    const ASSET_GLOBAL_SETTLE = 18; // FFFF7F
    const ASSET_PUBLISH_FEED = 19; // FF2A55
    const WITNESS_UPDATE = 20; // FFAA7F
    const PROPOSAL_CREATE = 21; // FFAA55
    const PROPOSAL_UPDATE = 22; // FF7F55
    const PROPOSAL_DELETE = 23; // FF552A
    const WITHDRAW_PERMISSION_CREATE = 24; // FF00AA
    const WITHDRAW_PERMISSION = 25; // FF00FF
    const WITHDRAW_PERMISSION_CLAIM = 26; // FF0055
    const WITHDRAW_PERMISSION_DELETE = 27; // 37B68Cc
    const COMITEE_MEMBER_CREATE = 28; // 37B68C
    const COMITEE_MEMBER_UPDATE = 29; // 6712E7
    const COMITEE_MEMBER_UPDATE_GLOBAL_PARAMETERS = 30; // B637B6
    const VESTING_BALANCE_CREATE = 31; // A5A5A5
    const VESTING_BALANCE_WITHDRAW = 32; // 696969
    const WORKER_CREATE = 33; // 0F0F0F
    const CUSTOM = 34; // 0DB762
    const ASSERT = 35; // FFFFFF
    const BALANCE_CLAIM = 36; // 939314
    const OVERRIDE_TRANSFER = 37; // 8D0DB7
    const TRANSFER_TO_BLIND = 38; // C4EFC4
    const BLIND_TRANSFER = 39; // F29DF2
    const TRANSFER_FROM_BLIND = 40; // 9D9DF2
    const ASSET_SETTLE_CANCEL = 41; // 4ECEF8
    const ASSET_CLAIM_FEES = 42; // F8794E
    const FBA_DISTRIBUTE = 43; //8808B2

    private $typeId;
    private $typeAlias;
    private $typeTitle;
    private $data;
    private $description;
    private $bitshares;

    public function __construct(Bitshares $bitshares, int $typeId, $data)
    {
        $this->bitshares = $bitshares;
        $this->typeId = $typeId;
        $this->data = $data;
        $this->typeAlias = static::getOperationTypeAlias($this->typeId);
        $this->typeTitle = ucwords(str_replace('_', ' ', strtolower($this->typeAlias)));
        $this->description = $this->getDescriptionString();
    }

    public function getTypeAlias(?bool $lowercase = false) : string
    {
        return $lowercase ? strtolower($this->typeAlias) : $this->typeAlias;
    }

    public function getTypeId() : int
    {
        return $this->typeId;
    }

    public function getTypeTitle() : string
    {
        return $this->typeTitle;
    }
    
    public function getFee() : string
    {
        if (!isset($this->data->fee->asset_id)) {
            return '0';
        }
        // TODO: REFACTOR!
        $feeAsset = $this->bitshares->getObject($this->data->fee->asset_id);
        $this->data->fee->asset_id = $feeAsset;
        return $this->amount($this->data->fee);
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    private function amount($amount)
    {
        if (isset($amount->amount) && isset($amount->asset_id) && $amount->asset_id instanceof Asset) {
            return sprintf('%.' . $amount->asset_id->precision . 'f %s', $amount->amount / pow(10, $amount->asset_id->precision), $amount->asset_id);
        }
        return '0';
    }

    protected function getDescriptionString()
    {
        switch ($this->typeId) {
            case static::TRANSFER:
                $objects = [
                    $this->data->amount->asset_id,
                    $this->data->from,
                    $this->data->to
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->amount->asset_id) === 'string') {
                    $this->data->amount->asset_id = $objects[$this->data->amount->asset_id];
                }
                if (gettype($this->data->from) === 'string') {
                    $this->data->from = $objects[$this->data->from];
                }
                if (gettype($this->data->to) === 'string') {
                    $this->data->to = $objects[$this->data->to];
                }
                $this->description = sprintf(
                    '%s sent %s to %s',
                    $this->data->from,
                    $this->amount($this->data->amount),
                    $this->data->to
                );
                break;
            case static::LIMIT_ORDER_CREATE:
                $objects = [
                    $this->data->seller,
                    $this->data->amount_to_sell->asset_id,
                    $this->data->min_to_receive->asset_id
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->amount_to_sell->asset_id) === 'string') {
                    $this->data->amount_to_sell->asset_id = $objects[$this->data->amount_to_sell->asset_id];
                }
                if (gettype($this->data->min_to_receive->asset_id) === 'string') {
                    $this->data->min_to_receive->asset_id = $objects[$this->data->min_to_receive->asset_id];
                }
                if (gettype($this->data->seller) === 'string') {
                    $this->data->seller = $objects[$this->data->seller];
                }
                $this->description = sprintf(
                    '%s place an order %s (min receive %s)',
                    $this->data->seller,
                    $this->amount($this->data->amount_to_sell),
                    $this->amount($this->data->min_to_receive)
                );
                break;
            case static::LIMIT_ORDER_CANCEL:
                $objects = [
                    $this->data->fee_paying_account
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->fee_paying_account) === 'string') {
                    $this->data->fee_paying_account = $objects[$this->data->fee_paying_account];
                }
                $this->description = sprintf(
                    '%s canceled order %s',
                    $this->data->fee_paying_account,
                    $this->data->order
                );
                break;
            case static::CALL_ORDER_UPDATE:
                $objects = [
                    $this->data->funding_account,
                    $this->data->delta_collateral->asset_id,
                    $this->data->delta_debt->asset_id
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->funding_account) === 'string') {
                    $this->data->funding_account = $objects[$this->data->funding_account];
                }
                if (gettype($this->data->delta_collateral->asset_id) === 'string') {
                    $this->data->delta_collateral->asset_id = $objects[$this->data->delta_collateral->asset_id];
                }
                if (gettype($this->data->delta_debt->asset_id) === 'string') {
                    $this->data->delta_debt->asset_id = $objects[$this->data->delta_debt->asset_id];
                }
                $this->description = sprintf(
                    '%s updated margin %s, %s',
                    $this->data->funding_account,
                    $this->amount($this->data->delta_collateral),
                    $this->amount($this->data->delta_debt)
                );
                break;
            case static::FILL_ORDER_VIRTUAL:
                // TODO: find this type. Not found in blocks [26085110, 26085610]
                $this->description = 'FILL ORDER VIRTUAL';
                break;
            case static::ACCOUNT_CREATE:
                $objects = [
                    $this->data->registrar
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->registrar) === 'string') {
                    $this->data->registrar = $objects[$this->data->registrar];
                }
                $this->description = sprintf(
                    'Registered %s (registrar %s)',
                    $this->data->name,
                    $this->data->registrar
                );
                break;
            case static::ACCOUNT_UPDATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ACCOUNT_UPDATE';
                break;
            case static::ACCOUNT_WHITELIST:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ACCOUNT_WHITELIST';
                break;
            case static::ACCOUNT_UPGRADE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ACCOUNT_UPGRADE';
                break;
            case static::ACCOUNT_TRANSFER:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ACCOUNT_TRANSFER';
                break;
            case static::ASSET_CREATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSET_CREATE';
                break;
            case static::ASSET_UPDATE:
                $objects = [
                    $this->data->issuer,
                    $this->data->asset_to_update,
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->issuer) === 'string') {
                    $this->data->issuer = $objects[$this->data->issuer];
                }
                if (gettype($this->data->asset_to_update) === 'string') {
                    $this->data->asset_to_update = $objects[$this->data->asset_to_update];
                }
                $this->description = sprintf(
                    'Update asset %s (issuer %s)',
                    $this->data->asset_to_update,
                    $this->data->issuer
                );
                break;
            case static::ASSET_UPDATE_BITASSET:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSET_UPDATE_BITASSET';
                break;
            case static::ASSET_UPDATE_FEED_PRODUCERS:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSET_UPDATE_FEED_PRODUCERS';
                break;
            case static::ASSET_ISSUE:
                $objects = [
                    $this->data->issuer,
                    $this->data->asset_to_issue->asset_id,
                    $this->data->issue_to_account,
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->issuer) === 'string') {
                    $this->data->issuer = $objects[$this->data->issuer];
                }
                if (gettype($this->data->asset_to_issue->asset_id) === 'string') {
                    $this->data->asset_to_issue->asset_id = $objects[$this->data->asset_to_issue->asset_id];
                }
                if (gettype($this->data->issue_to_account) === 'string') {
                    $this->data->issue_to_account = $objects[$this->data->issue_to_account];
                }
                $this->description = sprintf(
                    '%s issue %s to %s',
                    $this->data->issuer,
                    $this->amount($this->data->asset_to_issue),
                    $this->data->issue_to_account
                );
                break;
            case static::ASSET_RESERVE:
                $objects = [
                    $this->data->payer,
                    $this->data->amount_to_reserve->asset_id
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->payer) === 'string') {
                    $this->data->payer = $objects[$this->data->payer];
                }
                if (gettype($this->data->amount_to_reserve->asset_id) === 'string') {
                    $this->data->amount_to_reserve->asset_id = $objects[$this->data->amount_to_reserve->asset_id];
                }
                $this->description = sprintf(
                    '%s reserve %s',
                    $this->data->payer,
                    $this->amount($this->data->amount_to_reserve)
                );
                break;
            case static::ASSET_FUND_FEE_POOL:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSET_FUND_FEE_POOL';
                break;
            case static::ASSET_SETTLE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSET_SETTLE';
                break;
            case static::ASSET_GLOBAL_SETTLE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSET_GLOBAL_SETTLE';
                break;
            case static::ASSET_PUBLISH_FEED:
                $this->description = 'ASSET_PUBLISH_FEED';
                break;
            case static::WITNESS_UPDATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'WITNESS_UPDATE';
                break;
            case static::PROPOSAL_CREATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'PROPOSAL_CREATE';
                break;
            case static::PROPOSAL_UPDATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'PROPOSAL_UPDATE';
                break;
            case static::PROPOSAL_DELETE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'PROPOSAL_DELETE';
                break;
            case static::WITHDRAW_PERMISSION_CREATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'WITHDRAW_PERMISSION_CREATE';
                break;
            case static::WITHDRAW_PERMISSION:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'WITHDRAW_PERMISSION';
                break;
            case static::WITHDRAW_PERMISSION_CLAIM:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'WITHDRAW_PERMISSION_CLAIM';
                break;
            case static::WITHDRAW_PERMISSION_DELETE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'WITHDRAW_PERMISSION_DELETE';
                break;
            case static::COMITEE_MEMBER_CREATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'COMITEE_MEMBER_CREATE';
                break;
            case static::COMITEE_MEMBER_UPDATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'COMITEE_MEMBER_UPDATE';
                break;
            case static::COMITEE_MEMBER_UPDATE_GLOBAL_PARAMETERS:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'COMITEE_MEMBER_UPDATE_GLOBAL_PARAMETERS';
                break;
            case static::VESTING_BALANCE_CREATE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'VESTING_BALANCE_CREATE';
                break;
            case static::VESTING_BALANCE_WITHDRAW:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'VESTING_BALANCE_WITHDRAW';
                break;
            case static::WORKER_CREATE:
                $objects = [
                    $this->data->vesting_balance,
                    $this->data->owner,
                    $this->data->amount->asset_id
                ];
                $objects = $this->bitshares->getObjects($objects);
                if (gettype($this->data->vesting_balance) === 'string') {
                    $this->data->vesting_balance = $objects[$this->data->vesting_balance];
                }
                if (gettype($this->data->owner) === 'string') {
                    $this->data->owner = $objects[$this->data->owner];
                }
                if (gettype($this->data->amount->asset_id) === 'string') {
                    $this->data->amount->asset_id = $objects[$this->data->amount->asset_id];
                }
                $this->description = sprintf(
                    'Vesting balance %s (owner %s)',
                    // TODO: analyze and fix this
                    // $this->data->vesting_balance,
                    $this->amount($this->data->amount),
                    $this->data->owner
                );
                break;
            case static::CUSTOM:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'CUSTOM';
                break;
            case static::ASSERT:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSERT';
                break;
            case static::BALANCE_CLAIM:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'BALANCE_CLAIM';
                break;
            case static::OVERRIDE_TRANSFER:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'OVERRIDE_TRANSFER';
                break;
            case static::TRANSFER_TO_BLIND:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'TRANSFER_TO_BLIND';
                break;
            case static::BLIND_TRANSFER:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'BLIND_TRANSFER';
                break;
            case static::TRANSFER_FROM_BLIND:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'TRANSFER_FROM_BLIND';
                break;
            case static::ASSET_SETTLE_CANCEL:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSET_SETTLE_CANCEL';
                break;
            case static::ASSET_CLAIM_FEES:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'ASSET_CLAIM_FEES';
                break;
            case static::FBA_DISTRIBUTE:
                // TODO: not found in quick search in transactions for 50 blocks
                $this->description = 'FBA_DISTRIBUTE';
                break;
            default:
                throw new \Exception(sprintf('Unknown type "%d" for operation history object', $this->typeId));
                break;
        }
        return $this->description;
    }

    public function getColor() : string
    {
        $colors = [
            'TRANSFER' => '#81CA80',
            'LIMIT_ORDER_CREATE' => '#6BBCD7',
            'LIMIT_ORDER_CANCEL' => '#E9C842',
            'CALL_ORDER_UPDATE' => '#E96562',
            'FILL_ORDER_VIRTUAL' => '#008000',
            'ACCOUNT_CREATE' => '#CCCCCC',
            'ACCOUNT_UPDATE' => '#FF007F',
            'ACCOUNT_WHITELIST' => '#FB8817',
            'ACCOUNT_UPGRADE' => '#552AFF',
            'ACCOUNT_TRANSFER' => '#AA2AFF',
            'ASSET_CREATE' => '#D400FF',
            'ASSET_UPDATE' => '#0000FF',
            'ASSET_UPDATE_BITASSET' => '#AA7FFF',
            'ASSET_UPDATE_FEED_PRODUCERS' => '#2A7FFF',
            'ASSET_ISSUE' => '#7FAAFF',
            'ASSET_RESERVE' => '#55FF7F',
            'ASSET_FUND_FEE_POOL' => '#55FF7F',
            'ASSET_SETTLE' => '#FFFFAA',
            'ASSET_GLOBAL_SETTLE' => '#FFFF7F',
            'ASSET_PUBLISH_FEED' => '#FF2A55',
            'WITNESS_UPDATE' => '#FFAA7F',
            'PROPOSAL_CREATE' => '#FFAA55',
            'PROPOSAL_UPDATE' => '#FF7F55',
            'PROPOSAL_DELETE' => '#FF552A',
            'WITHDRAW_PERMISSION_CREATE' => '#FF00AA',
            'WITHDRAW_PERMISSION' => '#FF00FF',
            'WITHDRAW_PERMISSION_CLAIM' => '#FF0055',
            'WITHDRAW_PERMISSION_DELETE' => '#37B68Cc',
            'COMITEE_MEMBER_CREATE' => '#37B68C',
            'COMITEE_MEMBER_UPDATE' => '#6712E7',
            'COMITEE_MEMBER_UPDATE_GLOBAL_PARAMETERS' => '#B637B6',
            'VESTING_BALANCE_CREATE' => '#A5A5A5',
            'VESTING_BALANCE_WITHDRAW' => '#696969',
            'WORKER_CREATE' => '#0F0F0F',
            'CUSTOM' => '#0DB762',
            'ASSERT' => '#FFFFFF',
            'BALANCE_CLAIM' => '#939314',
            'OVERRIDE_TRANSFER' => '#8D0DB7',
            'TRANSFER_TO_BLIND' => '#C4EFC4',
            'BLIND_TRANSFER' => '#F29DF2',
            'TRANSFER_FROM_BLIND' => '#9D9DF2',
            'ASSET_SETTLE_CANCEL' => '#4ECEF8',
            'ASSET_CLAIM_FEES' => '#F8794E',
            'FBA_DISTRIBUTE' => '#8808B2'
        ];
        return $colors[$this->typeAlias];
    }

    public static function getOperationTypeAlias(int $typeId)
    {
        $typeAlias = 'UNKNOWN';
        switch ($typeId) {
            case static::TRANSFER:
                $typeAlias = 'TRANSFER';
                break;
            case static::LIMIT_ORDER_CREATE:
                $typeAlias = 'LIMIT_ORDER_CREATE';
                break;
            case static::LIMIT_ORDER_CANCEL:
                $typeAlias = 'LIMIT_ORDER_CANCEL';
                break;
            case static::CALL_ORDER_UPDATE:
                $typeAlias = 'CALL_ORDER_UPDATE';
                break;
            case static::FILL_ORDER_VIRTUAL:
                $typeAlias = 'FILL_ORDER_VIRTUAL';
                break;
            case static::ACCOUNT_CREATE:
                $typeAlias = 'ACCOUNT_CREATE';
                break;
            case static::ACCOUNT_UPDATE:
                $typeAlias = 'ACCOUNT_UPDATE';
                break;
            case static::ACCOUNT_WHITELIST:
                $typeAlias = 'ACCOUNT_WHITELIST';
                break;
            case static::ACCOUNT_UPGRADE:
                $typeAlias = 'ACCOUNT_UPGRADE';
                break;
            case static::ACCOUNT_TRANSFER:
                $typeAlias = 'ACCOUNT_TRANSFER';
                break;
            case static::ASSET_CREATE:
                $typeAlias = 'ASSET_CREATE';
                break;
            case static::ASSET_UPDATE:
                $typeAlias = 'ASSET_UPDATE';
                break;
            case static::ASSET_UPDATE_BITASSET:
                $typeAlias = 'ASSET_UPDATE_BITASSET';
                break;
            case static::ASSET_UPDATE_FEED_PRODUCERS:
                $typeAlias = 'ASSET_UPDATE_FEED_PRODUCERS';
                break;
            case static::ASSET_ISSUE:
                $typeAlias = 'ASSET_ISSUE';
                break;
            case static::ASSET_RESERVE:
                $typeAlias = 'ASSET_RESERVE';
                break;
            case static::ASSET_FUND_FEE_POOL:
                $typeAlias = 'ASSET_FUND_FEE_POOL';
                break;
            case static::ASSET_SETTLE:
                $typeAlias = 'ASSET_SETTLE';
                break;
            case static::ASSET_GLOBAL_SETTLE:
                $typeAlias = 'ASSET_GLOBAL_SETTLE';
                break;
            case static::ASSET_PUBLISH_FEED:
                $typeAlias = 'ASSET_PUBLISH_FEED';
                break;
            case static::WITNESS_UPDATE:
                $typeAlias = 'WITNESS_UPDATE';
                break;
            case static::PROPOSAL_CREATE:
                $typeAlias = 'PROPOSAL_CREATE';
                break;
            case static::PROPOSAL_UPDATE:
                $typeAlias = 'PROPOSAL_UPDATE';
                break;
            case static::PROPOSAL_DELETE:
                $typeAlias = 'PROPOSAL_DELETE';
                break;
            case static::WITHDRAW_PERMISSION_CREATE:
                $typeAlias = 'WITHDRAW_PERMISSION_CREATE';
                break;
            case static::WITHDRAW_PERMISSION:
                $typeAlias = 'WITHDRAW_PERMISSION';
                break;
            case static::WITHDRAW_PERMISSION_CLAIM:
                $typeAlias = 'WITHDRAW_PERMISSION_CLAIM';
                break;
            case static::WITHDRAW_PERMISSION_DELETE:
                $typeAlias = 'WITHDRAW_PERMISSION_DELETE';
                break;
            case static::COMITEE_MEMBER_CREATE:
                $typeAlias = 'COMITEE_MEMBER_CREATE';
                break;
            case static::COMITEE_MEMBER_UPDATE:
                $typeAlias = 'COMITEE_MEMBER_UPDATE';
                break;
            case static::COMITEE_MEMBER_UPDATE_GLOBAL_PARAMETERS:
                $typeAlias = 'COMITEE_MEMBER_UPDATE_GLOBAL_PARAMETERS';
                break;
            case static::VESTING_BALANCE_CREATE:
                $typeAlias = 'VESTING_BALANCE_CREATE';
                break;
            case static::VESTING_BALANCE_WITHDRAW:
                $typeAlias = 'VESTING_BALANCE_WITHDRAW';
                break;
            case static::WORKER_CREATE:
                $typeAlias = 'WORKER_CREATE';
                break;
            case static::CUSTOM:
                $typeAlias = 'CUSTOM';
                break;
            case static::ASSERT:
                $typeAlias = 'ASSERT';
                break;
            case static::BALANCE_CLAIM:
                $typeAlias = 'BALANCE_CLAIM';
                break;
            case static::OVERRIDE_TRANSFER:
                $typeAlias = 'OVERRIDE_TRANSFER';
                break;
            case static::TRANSFER_TO_BLIND:
                $typeAlias = 'TRANSFER_TO_BLIND';
                break;
            case static::BLIND_TRANSFER:
                $typeAlias = 'BLIND_TRANSFER';
                break;
            case static::TRANSFER_FROM_BLIND:
                $typeAlias = 'TRANSFER_FROM_BLIND';
                break;
            case static::ASSET_SETTLE_CANCEL:
                $typeAlias = 'ASSET_SETTLE_CANCEL';
                break;
            case static::ASSET_CLAIM_FEES:
                $typeAlias = 'ASSET_CLAIM_FEES';
                break;
            case static::FBA_DISTRIBUTE:
                $typeAlias = 'FBA_DISTRIBUTE';
                break;
            default:
                break;
        }
        return $typeAlias;
    }
}
