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

namespace DreamCommerce\Component\ShopAppstore\Api;

use ArrayObject;
use DreamCommerce\Component\Common\Http\ClientInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;

abstract class Resource implements ResourceInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ShopInterface $shop, ...$args): ArrayObject
    {
        // TODO: Implement get() method.
    }

    /**
     * {@inheritdoc}
     */
    public function head(ShopInterface $shop, ...$args): ArrayObject
    {
        // TODO: Implement head() method.
    }

    /**
     * {@inheritdoc}
     */
    public function post(ShopInterface $shop, array $data): int
    {
        // TODO: Implement post() method.
    }

    /**
     * {@inheritdoc}
     */
    public function put(ShopInterface $shop, int $id, array $data): void
    {
        // TODO: Implement put() method.
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ShopInterface $shop, int $id): void
    {
        // TODO: Implement delete() method.
    }
}