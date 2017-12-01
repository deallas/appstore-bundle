<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Billing;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\Common\Exception\NotDefinedException;
use DreamCommerce\Component\Common\Factory\UriFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Billing\Payload;
use DreamCommerce\Component\ShopAppstore\Billing\Resolver\MessageResolverInterface;
use DreamCommerce\Component\ShopAppstore\Factory\ShopFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Repository\ShopRepositoryInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class Dispatcher extends ServiceRegistry implements DispatcherInterface
{
    /**
     * @var array
     */
    private $availableActions = [
        self::ACTION_BILLING_INSTALL,
        self::ACTION_BILLING_SUBSCRIPTION,
        self::ACTION_INSTALL,
        self::ACTION_UNINSTALL,
        self::ACTION_UPGRADE
    ];

    /**
     * @var array
     */
    private $mapActionToClass = [
        self::ACTION_BILLING_INSTALL => Payload\BillingInstall::class,
        self::ACTION_BILLING_SUBSCRIPTION => Payload\BillingSubscription::class,
        self::ACTION_INSTALL => Payload\Install::class,
        self::ACTION_UNINSTALL => Payload\Uninstall::class,
        self::ACTION_UPGRADE => Payload\Upgrade::class
    ];

    /**
     * @var ServiceRegistryInterface
     */
    private $applicationRegistry;

    /**
     * @var ShopRepositoryInterface
     */
    private $shopRepository;

    /**
     * @var ShopFactoryInterface
     */
    private $shopFactory;

    /**
     * @var ObjectManager
     */
    private $shopObjectManager;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * @param ServiceRegistryInterface $applicationRegistry
     * @param ShopRepositoryInterface $shopRepository
     * @param ShopFactoryInterface $shopFactory
     * @param ObjectManager $shopObjectManager
     * @param UriFactoryInterface $uriFactory
     */
    public function __construct(ServiceRegistryInterface $applicationRegistry, ShopRepositoryInterface $shopRepository,
                                ShopFactoryInterface $shopFactory, ObjectManager $shopObjectManager,
                                UriFactoryInterface $uriFactory)
    {
        $this->applicationRegistry = $applicationRegistry;
        $this->shopRepository = $shopRepository;
        $this->shopFactory = $shopFactory;
        $this->shopObjectManager = $shopObjectManager;
        $this->uriFactory = $uriFactory;

        parent::__construct(MessageResolverInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(ServerRequestInterface $serverRequest): void
    {
        if($serverRequest->getMethod() !== 'POST') {
            return;
        }

        $params = $serverRequest->getParsedBody();
        $this->verifyRequirement($params);

        /** @var ApplicationInterface $application */
        $application = $this->applicationRegistry->get($params['application_code']);
        $this->verifyPayload($application, $params);

        /** @var UriInterface $shopUri */
        $shopUri = $this->uriFactory->createNewByUriString($params['shop_url']);

        $shop = $this->shopRepository->findOneByNameAndApplication($params['shop'], $application);
        if($shop === null) {
            $shop = $this->shopFactory->createNewByApplicationAndUri($application, $shopUri);
        } else {
            $shop->setUri($shopUri);
        }

        /** @var MessageResolverInterface $resolver */
        $resolver = $this->get($params['action']);
        $resolver->resolve($this->getPayloadByAction($application, $shop, $params));

        $this->shopObjectManager->persist($shop);
        $this->shopObjectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier): bool
    {
        if(!in_array($identifier, $this->availableActions)) {
            throw new InvalidArgumentException('Action "' . $identifier . '" is not supported');
        }

        return parent::has($identifier);
    }

    /**
     * @param array $params
     * @throws NotDefinedException
     */
    private function verifyRequirement(array $params): void
    {
        $requiredParams = [ 'action', 'shop', 'shop_url', 'hash', 'timestamp', 'application_code' ];

        if(isset($params['action'])) {
            switch ($params['action']) {
                case self::ACTION_BILLING_SUBSCRIPTION:
                    $requiredParams[] = 'subscription_end_time';
                    break;
                case self::ACTION_INSTALL:
                    $requiredParams[] = 'application_version';
                    $requiredParams[] = 'auth_code';
                    break;
                case self::ACTION_UPGRADE:
                    $requiredParams[] = 'application_version';
                    break;
            }
        }

        foreach($requiredParams as $requiredParam) {
            if (!isset($params[$requiredParam])) {
                throw NotDefinedException::forParameter($requiredParam);
            }
        }
    }

    public function verifyPayload(ApplicationInterface $application, array $params): void
    {
        $providedHash = $params['hash'];
        unset($params['hash']);

        // sort params
        ksort($params);

        $processedPayload = "";
        foreach($params as $k => $v){
            $processedPayload .= '&'.$k.'='.$v;
        }
        $processedPayload = substr($processedPayload, 1);

        $computedHash = hash_hmac('sha512', $processedPayload, $application->getAppstoreSecret());
        if((string)$computedHash !== (string)$providedHash) {
            // TODO throw exception
        }
    }

    /**
     * @param ApplicationInterface $application
     * @param ShopInterface $shop
     * @param array $params
     * @return Payload\Message
     */
    private function getPayloadByAction(ApplicationInterface $application, ShopInterface $shop, array $params): Payload\Message
    {
        $messageClass = $this->mapActionToClass[$params['action']];
        $dateTime = new DateTime($params['timestamp'], self::TIMEZONE);

        unset($params['timestamp']);
        unset($params['action']);
        unset($params['shop']);
        unset($params['shop_url']);
        unset($params['application_code']);

        return new $messageClass(
            $shop,
            $application,
            $dateTime,
            $params
        );
    }
}