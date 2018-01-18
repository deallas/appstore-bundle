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

use ArrayObject;

class DataContainer extends ArrayObject
{
    /**
     * @param array $data
     */
    public function fromArray(array $data): void
    {
        foreach($data as $k => $v) {
            $this->$k = $v;
        }
    }
}