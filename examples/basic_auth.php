<?php

require_once __DIR__ . '/../src/DreamCommerce/Component/ShopAppstore/vendor/autoload.php';

$shop = new \DreamCommerce\Component\ShopAppstore\Model\BasicAuthShop([
    'uri' => 'https://shoper.local',
    'username' => 'test',
    'password' => 'test'
]);

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\Order();
$result = $resource->find($shop, 1);
var_dump($result->getExternalId(), (string)$result->getShop()->getUri(), $result->order_id);