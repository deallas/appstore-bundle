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

namespace DreamCommerce\Component\ShopAppstore\Api\Authentication;

use DreamCommerce\Component\Common\Http\ClientInterface as HttpClientInterface;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\TokenInterface;
use Webmozart\Assert\Assert;

final class OAuthAuthenticator implements AuthenticatorInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(ShopInterface $shop, bool $force = false): TokenInterface
    {
        /** @var OAuthShopInterface $shop */
        Assert::isInstanceOf($shop, OAuthShopInterface::class);

        $uri = $shop->getUri()->getPath() . '/oauth/token';
        $response = $this->httpClient->request(
            'POST',
            $uri,
            [
                'query' => [
                    'code' => $shop->getAuthCode()
                ]
            ]
        );

    }
}