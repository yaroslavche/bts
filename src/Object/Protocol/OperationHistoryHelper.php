<?php

namespace Bitshares\Object\Protocol;

class OperationHistoryHelper
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
    private $result;

    // public $result;

    public function __construct(OperationHistory $object)
    {
        $this->typeId = $object->op[0];
        $this->data = $object->op[1];
        $this->result = $object->result[0] === 1 ? $object->result[1] : false;
        $this->typeAlias = static::getOperationTypeAlias($this->typeId);
        $this->typeTitle = ucwords(str_replace('_', ' ', strtolower($this->typeAlias)));
        return $this;
    }

    public function getTypeAlias(?bool $lowercase = false) : string
    {
        if ($lowercase) {
            return strtolower($this->typeAlias);
        }
        return $this->typeAlias;
    }

    public function getProperty(string $property)
    {
        if (property_exists($this->data, $property)) {
            return $this->data->$property;
        }
    }

    public function getTypeId() : int
    {
        return $this->typeId;
    }

    public function getTypeTitle() : string
    {
        return $this->typeTitle;
    }

    public function getDescription()
    {
        switch ($this->typeId) {
            case static::TRANSFER:
                // $asset = $this->data->amount->asset_id instanceof Asset ? $this->data->amount->asset_id->id : $this->data->amount->asset_id;
                // $this->description = ' ' . $this->data->amount->amount . ' ' . $asset;
                $this->description = 'TRANSFER';
                break;
            case static::LIMIT_ORDER_CREATE:
                $this->description = 'LIMIT_ORDER_CREATE';
                break;
            case static::LIMIT_ORDER_CANCEL:
                $this->description = 'LIMIT_ORDER_CANCEL';
                break;
            case static::CALL_ORDER_UPDATE:
                $this->description = 'CALL_ORDER_UPDATE';
                break;
            case static::FILL_ORDER_VIRTUAL:
                $this->description = 'FILL_ORDER_VIRTUAL';
                break;
            case static::ACCOUNT_CREATE:
                $this->description = 'ACCOUNT_CREATE';
                break;
            case static::ACCOUNT_UPDATE:
                $this->description = 'ACCOUNT_UPDATE';
                break;
            case static::ACCOUNT_WHITELIST:
                $this->description = 'ACCOUNT_WHITELIST';
                break;
            case static::ACCOUNT_UPGRADE:
                $this->description = 'ACCOUNT_UPGRADE';
                break;
            case static::ACCOUNT_TRANSFER:
                $this->description = 'ACCOUNT_TRANSFER';
                break;
            case static::ASSET_CREATE:
                $this->description = 'ASSET_CREATE';
                break;
            case static::ASSET_UPDATE:
                $this->description = 'ASSET_UPDATE';
                break;
            case static::ASSET_UPDATE_BITASSET:
                $this->description = 'ASSET_UPDATE_BITASSET';
                break;
            case static::ASSET_UPDATE_FEED_PRODUCERS:
                $this->description = 'ASSET_UPDATE_FEED_PRODUCERS';
                break;
            case static::ASSET_ISSUE:
                $this->description = 'ASSET_ISSUE';
                break;
            case static::ASSET_RESERVE:
                $this->description = 'ASSET_RESERVE';
                break;
            case static::ASSET_FUND_FEE_POOL:
                $this->description = 'ASSET_FUND_FEE_POOL';
                break;
            case static::ASSET_SETTLE:
                $this->description = 'ASSET_SETTLE';
                break;
            case static::ASSET_GLOBAL_SETTLE:
                $this->description = 'ASSET_GLOBAL_SETTLE';
                break;
            case static::ASSET_PUBLISH_FEED:
                $this->description = 'ASSET_PUBLISH_FEED';
                break;
            case static::WITNESS_UPDATE:
                $this->description = 'WITNESS_UPDATE';
                break;
            case static::PROPOSAL_CREATE:
                $this->description = 'PROPOSAL_CREATE';
                break;
            case static::PROPOSAL_UPDATE:
                $this->description = 'PROPOSAL_UPDATE';
                break;
            case static::PROPOSAL_DELETE:
                $this->description = 'PROPOSAL_DELETE';
                break;
            case static::WITHDRAW_PERMISSION_CREATE:
                $this->description = 'WITHDRAW_PERMISSION_CREATE';
                break;
            case static::WITHDRAW_PERMISSION:
                $this->description = 'WITHDRAW_PERMISSION';
                break;
            case static::WITHDRAW_PERMISSION_CLAIM:
                $this->description = 'WITHDRAW_PERMISSION_CLAIM';
                break;
            case static::WITHDRAW_PERMISSION_DELETE:
                $this->description = 'WITHDRAW_PERMISSION_DELETE';
                break;
            case static::COMITEE_MEMBER_CREATE:
                $this->description = 'COMITEE_MEMBER_CREATE';
                break;
            case static::COMITEE_MEMBER_UPDATE:
                $this->description = 'COMITEE_MEMBER_UPDATE';
                break;
            case static::COMITEE_MEMBER_UPDATE_GLOBAL_PARAMETERS:
                $this->description = 'COMITEE_MEMBER_UPDATE_GLOBAL_PARAMETERS';
                break;
            case static::VESTING_BALANCE_CREATE:
                $this->description = 'VESTING_BALANCE_CREATE';
                break;
            case static::VESTING_BALANCE_WITHDRAW:
                $this->description = 'VESTING_BALANCE_WITHDRAW';
                break;
            case static::WORKER_CREATE:
                $this->description = 'WORKER_CREATE';
                break;
            case static::CUSTOM:
                $this->description = 'CUSTOM';
                break;
            case static::ASSERT:
                $this->description = 'ASSERT';
                break;
            case static::BALANCE_CLAIM:
                $this->description = 'BALANCE_CLAIM';
                break;
            case static::OVERRIDE_TRANSFER:
                $this->description = 'OVERRIDE_TRANSFER';
                break;
            case static::TRANSFER_TO_BLIND:
                $this->description = 'TRANSFER_TO_BLIND';
                break;
            case static::BLIND_TRANSFER:
                $this->description = 'BLIND_TRANSFER';
                break;
            case static::TRANSFER_FROM_BLIND:
                $this->description = 'TRANSFER_FROM_BLIND';
                break;
            case static::ASSET_SETTLE_CANCEL:
                $this->description = 'ASSET_SETTLE_CANCEL';
                break;
            case static::ASSET_CLAIM_FEES:
                $this->description = 'ASSET_CLAIM_FEES';
                break;
            case static::FBA_DISTRIBUTE:
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
