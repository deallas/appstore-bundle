<?php

require_once __DIR__ . '/../src/DreamCommerce/Component/ShopAppstore/vendor/autoload.php';

$shop = new \DreamCommerce\Component\ShopAppstore\Model\BasicAuthShop([
    'uri' => 'https://kotarb.builder.dreamcommerce.com',
    'username' => 'test',
    'password' => 'test'
]);

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\Order();
$result = $resource->find($shop, 1);
var_dump($result->getExternalId(), (string)$result->getShop()->getUri(), $result->order_id, $result->billing_address->firstname);

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\ApplicationConfig();
$result = $resource->fetch($shop);

var_dump((string)$result->getShop()->getUri(), $result->shop_url, $result->shop_name);