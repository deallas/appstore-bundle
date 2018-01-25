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

class Data implements DataInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $changedKeys = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
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
            if($this->data[$k] instanceof DataInterface) {
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
     * {@inheritdoc}
     */
    public function setFieldValue(string $field, $value): void
    {
        $this->$field = $value;
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