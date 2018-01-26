<?php

/*
 * This file is part of the DreamCommerce Shop AppStore package.
 *
 * (c) DreamCommerce
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Model\Shop;

use Doctrine\Common\Collections\Collection;
use DreamCommerce\Component\ShopAppstore\Model\ShopDependTrait;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use Doctrine\Common\Collections\ArrayCollection;

class Metafield extends ShopItem implements MetafieldInterface
{
    use ShopDependTrait;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $object;

    /**
     * @var Collection|MetafieldValueInterface[]
     */
    private $values;

    /**
     * @var string
     */
    private $type;

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    /**
     * Set metafieldKey
     *
     * @param string $metafieldKey
     *
     * @return Metafield
     */
    public function setMetafieldKey(string $metafieldKey)
    {
        $this->metafieldKey = $metafieldKey;

        return $this;
    }

    /**
     * Get metafieldKey
     *
     * @return string
     */
    public function getMetafieldKey()
    {
        return $this->metafieldKey;
    }

    /**
     * Set namespace
     *
     * @param string $namespace
     *
     * @return Metafield
     */
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Get namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Metafield
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set object
     *
     * @param string|null $object
     *
     * @return Metafield
     */
    public function setObject($object=null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param MetafieldValue $metafieldValues
     * @return $this
     */
    public function addMetafieldValue(MetafieldValue $metafieldValues)
    {
        $this->tryToSetTypeByClassName(get_class($metafieldValues));

        if (!$this->hasMetafieldValue($metafieldValues)) {
            $this->metafieldValues->set($metafieldValues->getId(), $metafieldValues);
        }

        return $this;
    }

    public function hasMetafieldValue(MetafieldValue $metafieldValue)
    {
        return $this->metafieldValues->offsetExists($metafieldValue->getId());
    }

    /**
     * Remove MetafieldValue
     *
     * @param MetafieldValue $metafield
     */
    public function removeMetafieldValue(MetafieldValue $metafield)
    {
        $this->metafieldValues->removeElement($metafield);
    }

    /**
     * Get MetafieldValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMetafieldValues(): Collection
    {
        return $this->metafieldValues;
    }

    /**
     * @param ShopInterface $shop
     * @return $this
     */
    public function setShop(ShopInterface $shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * @return ShopInterface
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @return string
     * @throws MetafieldTypeException
     */
    public function getType()
    {
        if (!isset($this->type) || empty($this->type)) {
            throw new MetafieldTypeException('Metafield type cannot be empty when Metafield object is set to MetafieldValue Object');
        }

        return $this->type;
    }

    /**
     * @param string $type
     * @throws MetafieldTypeException
     */
    public function setType(string $type)
    {
        
        if (isset($this->type) && !empty($this->type) && $this->type !== $type) {
            throw new MetafieldTypeException('You can not change metafield type');
        }

        $this->type = $type;
    }

    /**
     * @param $className
     * @throws MetafieldTypeException
     */
    private function tryToSetTypeByClassName($className)
    {
        $types = array_flip(MetafieldValue::getMap());

        if (!isset($types[$className])) {
            throw new MetafieldTypeException('Metafield type class is not supported');
        }

        //the same type
        if ((isset($this->type) || !empty($this->type)) && $this->type == $types[$className]) {
            return;
        }


        $this->setType($types[$className]);
    }
}
