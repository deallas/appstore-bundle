<?php


namespace DreamCommerce\ShopAppstoreBundle\Utils\Webhook\Validator;


use DreamCommerce\ShopAppstoreBundle\Handler\Application;
use DreamCommerce\ShopAppstoreBundle\Model\ShopInterface;

interface AppValidatorInterface extends ValidatorInterface
{
    public function setShop(ShopInterface $shop);
    public function setConfig(Application $app, $webhookName);
}