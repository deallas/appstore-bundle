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

namespace DreamCommerce\Component\ShopAppstore\Api\Authenticator;

use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\Common\Http\ClientInterface as HttpClientInterface;
use DreamCommerce\Component\ShopAppstore\Model\OAuthShopInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\ShopTransitions;
use SM\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class OAuthAuthenticator extends BearerAuthenticator
{
    /**
     * @var FactoryInterface
     */
    private $shopStateMachineFactory;

    /**
     * @var ObjectManager
     */
    private $shopObjectManager;

    /**
     * @param HttpClientInterface $httpClient
     * @param ObjectManager|null $tokenObjectManager
     * @param ObjectManager|null $shopObjectManager
     * @param FactoryInterface|null $shopStateMachineFactory
     */
    public function __construct(HttpClientInterface $httpClient,
                                ObjectManager $tokenObjectManager = null,
                                ObjectManager $shopObjectManager = null,
                                FactoryInterface $shopStateMachineFactory = null
    ) {
        $this->shopObjectManager = $shopObjectManager;
        $this->shopStateMachineFactory = $shopStateMachineFactory;

        parent::__construct($httpClient, $tokenObjectManager);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(ShopInterface $shop): void
    {
        /** @var OAuthShopInterface $shop */
        Assert::isInstanceOf($shop, OAuthShopInterface::class);

        $application = $shop->getApplication();
        $shopUri = $shop->getUri();

        $query = [
            'grant_type' => 'authorization_code'
        ];

        $params = [
            'code' => $shop->getAuthCode()
        ];

        $authUri = $shopUri
            ->withPath($shopUri->getPath() . '/webapi/rest/oauth/token')
            ->withQuery('?' . http_build_query($query, '', '&'));

        $request = $this->httpClient->createRequest(
            'post',
            $authUri,
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode($application->getAppId() . ':' . $application->getAppSecret())
            ],
            http_build_query($params, '', '&')
        );

        $this->handleRequest($request, $shop);
        $shop->setAuthCode(null);

        if($this->shopStateMachineFactory !== null) {
            $stateMachine = $this->shopStateMachineFactory->get($shop, ShopTransitions::GRAPH);
            $stateMachine->apply(ShopTransitions::TRANSITION_INSTALL);
        }

        if($this->shopObjectManager !== null) {
            $this->shopObjectManager->persist($shop);
            $this->shopObjectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(ShopInterface $shop): void
    {
        /** @var OAuthShopInterface $shop */
        Assert::isInstanceOf($shop, OAuthShopInterface::class);

        $application = $shop->getApplication();
        $shopUri = $shop->getUri();

        $query = [
            'grant_type' => 'refresh_token'
        ];

        $params = [
            'client_id' => $application->getAppId(),
            'client_secret' => $application->getAppSecret()
        ];

        $refreshUri = $shopUri
            ->withPath($shopUri->getPath() . '/webapi/rest/oauth/token')
            ->withQuery('?' . http_build_query($query, '', '&'));

        $request = $this->httpClient->createRequest(
            'post',
            $refreshUri,
            [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            http_build_query($params, '', '&')
        );

        $this->handleRequest($request, $shop);
    }
}