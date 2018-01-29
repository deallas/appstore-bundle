<?php

require_once __DIR__ . '/../src/DreamCommerce/Component/ShopAppstore/vendor/autoload.php';

$shop = new \DreamCommerce\Component\ShopAppstore\Model\BasicAuthShop([
    'uri' => 'http://shoper.local',
    'username' => 'test',
    'password' => 'test'
]);

/////////////////////////////

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\Order();
/** @var \DreamCommerce\Component\ShopAppstore\Model\ShopItemInterface $item */
$result = $resource->find($shop, 2);
var_dump($result->getExternalId(), (string)$result->getShop()->getUri(), $result->order_id, $result->billing_address->firstname);

/////////////////////////////

$criteria = \DreamCommerce\Component\ShopAppstore\Api\Criteria::create();
$criteria->where('paid > 0');

/** @var \DreamCommerce\Component\ShopAppstore\Model\ShopItemListInterface $result */
$list = $resource->findBy($shop, $criteria);

/** @var \DreamCommerce\Component\ShopAppstore\Model\ShopItemInterface $item */
foreach($list as $item) {
    var_dump($item->getExternalId());
}

/////////////////////////////

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\ApplicationConfig();
/** @var \DreamCommerce\Component\ShopAppstore\Model\ShopDataInterface $result */
$result = $resource->fetch($shop);

var_dump((string)$result->getShop()->getUri(), $result->shop_url, $result->shop_name);

///////////////////////////

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\Metafield();
/** @var \DreamCommerce\Component\ShopAppstore\Model\Shop\MetafieldInterface $result */
$metafield = $resource->insert($shop, [
    'type' => \DreamCommerce\Component\ShopAppstore\Model\Shop\MetafieldInterface::TYPE_FLOAT,
    'namespace' => 'DreamCommerce',
    'key' => 'global_key_' . time(),
    'description' => '... global description ...'
]);

$criteria = \DreamCommerce\Component\ShopAppstore\Api\Criteria::create();
$criteria->where('key = "global_key"');

$list = $resource->findBy($shop, $criteria);
var_dump($list[0]->namespace, $list[0]->key);

/////////////////////////////

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\MetafieldValue();
/** @var \DreamCommerce\Component\ShopAppstore\Model\Shop\MetafieldValueFloat $value */
$value = $resource->insert($shop, [
    'metafield_id' => $metafield->getExternalId(),
    'value' => 555.44
]);

///////////////////////////

$oResource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\Order();
$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\Metafield();

$metafield = $resource->insertByResource($oResource, $shop, [
    'type' => \DreamCommerce\Component\ShopAppstore\Model\Shop\MetafieldInterface::TYPE_STRING,
    'namespace' => 'DreamCommerce',
    'key' => 'order_key_' . time(),
    'description' => '... description ...'
]);
var_dump($metafield->getExternalId());


$list = $resource->findByResource($oResource, $shop);
var_dump(count($list), $list[0]->namespace, $list[0]->key);

/////////////////////////////

$order = new \DreamCommerce\Component\ShopAppstore\Model\ShopItem($shop, [], 3);

$resource = new \DreamCommerce\Component\ShopAppstore\Api\Resource\MetafieldValue();
/** @var \DreamCommerce\Component\ShopAppstore\Model\Shop\MetafieldValueString $result */
$result = $resource->insertByMetafield($metafield, $order, '... value ...');

$list = $resource->findByMetafield($metafield);
var_dump(count($list), $list[0]->value, $list[0]->type);
