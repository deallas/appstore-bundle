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

namespace DreamCommerce\Component\ShopAppstore\Model;

class DataContainer implements DataContainerInterface
{
    use ShopDependTrait;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $changedKeys = [];

    /**
     * @param ShopInterface|null $shop
     * @param array $data
     */
    public function __construct(ShopInterface $shop = null, array $data = [])
    {
        $this->shop = $shop;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): void
    {
        $this->changedKeys = [];
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiffData(): array
    {
        $diff = array_intersect_key($this->data, array_flip($this->changedKeys));
        foreach($this->data as $k => $v) {
            if($this->data[$k] instanceof DataContainerInterface) {
                $partDiff = $this->data[$k]->getDiffData();
                if(!empty($partDiff)) {
                    $diff[$k] = $partDiff;
                }
            }
        }

        return $diff;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(string $field)
    {
        return $this->$field;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if(!isset($this->data[$name])) {
            return null;
        }

        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        if(!in_array($name, $this->changedKeys)) {
            $this->changedKeys[] = $name;
        }

        $this->data[$name] = $value;
    }
}