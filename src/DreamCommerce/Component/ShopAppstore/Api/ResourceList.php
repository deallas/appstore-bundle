<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Api;

use ArrayObject;
use stdClass;

final class ResourceList extends ArrayObject
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $pages = 0;

    /**
     * @param array $array
     * @param int $flags
     */
    public function __construct($array = array(), $flags = parent::ARRAY_AS_PROPS)
    {
        $array = $this->transform($array);

        parent::__construct($array, $flags);
    }

    /**
     * @param int|null $count
     */
    public function setCount(?int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int|null $page
     */
    public function setPage(?int $page)
    {
        $this->page = $page;
    }

    /**
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @param int|null $count
     */
    public function setPageCount(?int $count)
    {
        $this->pages = $count;
    }

    /**
     * @return int|null
     */
    public function getPageCount(): ?int
    {
        return $this->pages;
    }

    /**
     * @param array|ArrayObject $array
     * @return ArrayObject
     */
    private function transform($array): ArrayObject
    {
        if(!$array instanceof ArrayObject) {
            if(is_array($array) || $array instanceof stdClass) {
                foreach($array as $k => $value){
                    $array[$k] = $this->transform($value);
                }
                $array = new ArrayObject($array, ArrayObject::ARRAY_AS_PROPS);
            }
        }

        return $array;
    }
}
