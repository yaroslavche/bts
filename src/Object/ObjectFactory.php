<?php

namespace Bitshares\Object;

class ObjectFactory
{
    const PROTOCOL_SPACE = 1;

    const BASE_OBJECT = 1; // 1.1.x base object
    const ACCOUNT_OBJECT = 2; // 1.2.x account object
    const ASSET_OBJECT = 3; // 1.3.x asset object
    const FORCE_SETTLEMENT_OBJECT = 4; // 1.4.x force settlement object
    const COMMITTEE_MEMBER_OBJECT = 5; // 1.5.x committee member object
    const WITNESS_OBJECT = 6; // 1.6.x witness object
    const LIMIT_ORDER_OBJECT = 7; // 1.7.x limit order object
    const CALL_ORDER_OBJECT = 8; // 1.8.x call order object
    const CUSTOM_OBJECT = 9; // 1.9.x custom object
    const PROPOSAL_OBJECT = 10; // 1.10.x proposal object
    const OPERATION_HISTORY_OBJECT = 11; // 1.11.x operation history object
    const WITHDRAW_PERMISSION_OBJECT = 12; // 12.1.x withdraw permission object
    const VESTING_BALANCE_OBJECT = 13; // 1.13.x vesting balance object
    const WORKER_OBJECT = 14; // 1.14.x worker object
    const BALANCE_OBJECT = 15; // 1.15.x balance object

    const IMPLEMENTATION_SPACE = 2;

    const GLOBAL_PROPERTY_OBJECT = 0; // 2.0.x global_property_object
    const DYNAMIC_GLOBAL_PROPERTY_OBJECT = 1; // 2.1.x dynamic_global_property_object
    const ASSET_DYNAMIC_DATA = 3; // 2.3.x asset_dynamic_data
    const ASSET_BITASSET_DATA = 4; // 2.4.x asset_bitasset_data
    const ACCOUNT_BALANCE_OBJECT = 5; // 2.5.x account_balance_object
    const ACCOUNT_STATISTICS_OBJECT = 6; // 2.6.x account_statistics_object
    const TRANSACTION_OBJECT = 7; // 2.7.x transaction_object
    const BLOCK_SUMMARY_OBJECT = 8; // 2.8.x block_summary_object
    const ACCOUNT_TRANSACTION_HISTORY_OBJECT = 9; // 2.9.x account_transaction_history_object
    const BLINDED_BALANCE_OBJECT = 10; // 2.10.x blinded_balance_object
    const CHAIN_PROPERTY_OBJECT = 11; // 2.11.x chain_property_object
    const WITNESS_SCHEDULE_OBJECT = 12; // 2.12.x witness_schedule_object
    const BUDGET_RECORD_OBJECT = 13; // 2.13.x budget_record_object
    const SPECIAL_AUTHORITY_OBJECT = 14; // 2.14.x special_authority_object

    const SPACE_5 = 5;

    const OBJECT_5_0 = 0;
    const OBJECT_5_1 = 1;

    private static $spaceId;
    private static $typeId;

    public static function create($data = null)
    {
        $objectId = null;
        if (gettype($data) === 'string') {
            $objectId = $data;
        } elseif (isset($data->id)) {
            $objectId = $data->id;
        }
        if (!static::isValidId($objectId)) {
            throw new \Exception(sprintf('Invalid object id "%s"', $objectId));
        }
        // for debug - default BaseObject. REMOVE!
        try {
            $objectClass = static::getClassName();
            if (!class_exists($objectClass)) {
                throw new \Exception(sprintf('Class "%s" not found.', $objectClass));
            }
        } catch (\Exception $e) {
            $objectClass = BaseObject::class;
        }
        return new $objectClass($data);
    }

    public static function isValidId(string $objectId) : bool
    {
        $matches = null;
        $checkResult = preg_match('/([\\d]+)\\.([\\d]+)\\.([\\d]+)/i', $objectId, $matches);
        $isValid = $checkResult === 1 && $matches[0] === $objectId && count($matches) === 4;
        if ($isValid) {
            static::$spaceId = $matches[1];
            static::$typeId = $matches[2];
            // $objectId = $matches[3];
        }
        unset($matches);
        return $isValid;
    }

    private static function getClassName()
    {
        switch (static::$spaceId) {
            case static::PROTOCOL_SPACE:
                switch (static::$typeId) {
                    case static::BASE_OBJECT:
                        return Protocol\Base::class;
                    case static::ACCOUNT_OBJECT:
                        return Protocol\Account::class;
                    case static::ASSET_OBJECT:
                        return Protocol\Asset::class;
                    case static::FORCE_SETTLEMENT_OBJECT:
                    case static::COMMITTEE_MEMBER_OBJECT:
                        return Protocol\CommitteeMember::class;
                    case static::WITNESS_OBJECT:
                        return Protocol\Witness::class;
                    case static::LIMIT_ORDER_OBJECT:
                    case static::CALL_ORDER_OBJECT:
                    case static::CUSTOM_OBJECT:
                    case static::PROPOSAL_OBJECT:
                    case static::OPERATION_HISTORY_OBJECT:
                        return Protocol\OperationHistory::class;
                    case static::WITHDRAW_PERMISSION_OBJECT:
                    case static::VESTING_BALANCE_OBJECT:
                    case static::WORKER_OBJECT:
                    case static::BALANCE_OBJECT:
                    default: throw new \Exception(sprintf('Unknown type %d for space %s', static::$typeId, 'PROTOCOL SPACE (1)'));
                }
                // no break. return or exception
            case static::IMPLEMENTATION_SPACE:
                switch (static::$typeId) {
                    case static::GLOBAL_PROPERTY_OBJECT:
                        return Implementation\GlobalProperty::class;
                    case static::DYNAMIC_GLOBAL_PROPERTY_OBJECT:
                        return Implementation\DynamicGlobalProperty::class;
                    case static::ASSET_DYNAMIC_DATA:
                    case static::ASSET_BITASSET_DATA:
                    case static::ACCOUNT_BALANCE_OBJECT:
                    case static::ACCOUNT_STATISTICS_OBJECT:
                    case static::TRANSACTION_OBJECT:
                        return Implementation\Transaction::class;
                    case static::BLOCK_SUMMARY_OBJECT:
                    case static::ACCOUNT_TRANSACTION_HISTORY_OBJECT:
                        return Implementation\AccountTransactionHistory::class;
                    case static::BLINDED_BALANCE_OBJECT:
                    case static::CHAIN_PROPERTY_OBJECT:
                    case static::WITNESS_SCHEDULE_OBJECT:
                    case static::BUDGET_RECORD_OBJECT:
                    case static::SPECIAL_AUTHORITY_OBJECT:
                    default: throw new \Exception(sprintf('Unknown type %d for space %s', static::$typeId, 'IMPLEMENTATION SPACE (2)'));
                }
                // no break. return or exception
            case static::SPACE_5:
                switch (static::$typeId) {
                    case static::OBJECT_5_0:
                    case static::OBJECT_5_1:
                    default: throw new \Exception(sprintf('Unknown type %d for space %s', static::$typeId, 'SPACE_5 (5)'));
                }
            default: throw new \Exception(sprintf('Unknown space %d', static::$spaceId));
        }
    }
}
