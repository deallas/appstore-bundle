<?php

require_once __DIR__ . '/../src/DreamCommerce/Component/ShopAppstore/vendor/autoload.php';

$shop = new \DreamCommerce\Component\ShopAppstore\Model\BasicAuthShop([
    'uri' => 'http://shoper.local',
    'username' => 'api',
    'password' => 'test'
]);

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\Order();
$result = $resource->get($shop);

var_dump($result);
