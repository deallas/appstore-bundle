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

namespace DreamCommerce\Component\ShopAppstore\Api\Hydrator;

use DreamCommerce\Component\ShopAppstore\Api\ResourceInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HydratorInterface
{
    /**
     * @param ResourceInterface $resource
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function hydrate(ResourceInterface $resource, RequestInterface $request, ResponseInterface $response);
}