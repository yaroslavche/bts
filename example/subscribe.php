<?php

Error_Reporting(-1);
ini_set('display_errors', 1);

session_save_path("/tmp");
session_start();

require __DIR__ . '/../vendor/autoload.php';

use Bitshares\Object\ObjectFactory;
use Bitshares\Bitshares;

$objects = [];
foreach ($_POST['subscriptionData'] as $object) {
    $objectId = $object['id'];
    if (substr($objectId, 0, 4) !== '2.9.') {
        continue;
    }
    $objects[] = $objectId;
}
if (empty($objects)) {
    return '';
}
$service = new Bitshares();
var_dump($objects);
$transactions = $service->getObjects($objects);


$operations = $_SESSION['operations'] ?? [];
foreach ($transactions as $transactionId => $transactionHistoryObject) {
    $helper = $transactionHistoryObject->operation_id->helper;
    $alias = $helper->getTypeAlias();
    if (!array_key_exists($alias, $operations)) {
        $operations[$alias] = $transactionHistoryObject->operation_id;
    }
}
if ($_SESSION['operations'] !== $operations) {
    $_SESSION['operations'] = $operations;
}
