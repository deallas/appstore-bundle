<?php

declare(strict_types=1);

namespace DreamCommerce\Component\ShopAppstore\Tests\Billing;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use DreamCommerce\Component\Common\Factory\UriFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Billing\Dispatcher;
use DreamCommerce\Component\ShopAppstore\Billing\DispatcherInterface;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\BillingInstall;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\BillingSubscription;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Install;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Message;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Uninstall;
use DreamCommerce\Component\ShopAppstore\Billing\Payload\Upgrade;
use DreamCommerce\Component\ShopAppstore\Billing\Resolver\MessageResolverInterface;
use DreamCommerce\Component\ShopAppstore\Factory\ShopFactoryInterface;
use DreamCommerce\Component\ShopAppstore\Model\ApplicationInterface;
use DreamCommerce\Component\ShopAppstore\Model\ShopInterface;
use DreamCommerce\Component\ShopAppstore\Repository\ShopRepositoryInterface;
use DreamCommerce\Component\ShopAppstore\Tests\Fixtures\Billing\ExampleResolver;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use stdClass;
use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Registry\ServiceRegistryInterface;

class DispatcherTest extends TestCase
{
    private const APPSTORE_SECRET   = 'APPSTORE_SECRET';
    private const APPLICATION_CODE  = 'APPLICATION_CODE';

    /**
     * @var array
     */
    private $payloads = [
        DispatcherInterface::ACTION_BILLING_INSTALL         => BillingInstall::class,
        DispatcherInterface::ACTION_BILLING_SUBSCRIPTION    => BillingSubscription::class,
        DispatcherInterface::ACTION_INSTALL                 => Install::class,
        DispatcherInterface::ACTION_UNINSTALL               => Uninstall::class,
        DispatcherInterface::ACTION_UPGRADE                 => Upgrade::class
    ];

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var MessageResolverInterface[]|MockObject[]
     */
    private $resolvers = [];

    /**
     * @var ServiceRegistryInterface|MockObject
     */
    private $applicationRegistry;

    /**
     * @var ShopRepositoryInterface|MockObject
     */
    private $shopRepository;

    /**
     * @var ShopFactoryInterface|MockObject
     */
    private $shopFactory;

    /**
     * @var ObjectManager|MockObject
     */
    private $shopObjectManager;

    /**
     * @var UriFactoryInterface|MockObject
     */
    private $uriFactory;

    public function setUp()
    {
        $this->applicationRegistry = $this->getMockBuilder(ServiceRegistryInterface::class)->getMock();
        $this->shopRepository = $this->getMockBuilder(ShopRepositoryInterface::class)->getMock();
        $this->shopFactory = $this->getMockBuilder(ShopFactoryInterface::class)->getMock();
        $this->shopObjectManager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $this->uriFactory = $this->getMockBuilder(UriFactoryInterface::class)->getMock();

        $this->dispatcher = new Dispatcher(
            $this->applicationRegistry,
            $this->shopRepository,
            $this->shopFactory,
            $this->shopObjectManager,
            $this->uriFactory
        );
    }

    public function testShouldImplements()
    {
        $this->assertInstanceOf(DispatcherInterface::class, $this->dispatcher);
    }

    /**
     * @dataProvider validResolvers
     *
     * @param string $action
     * @param string $className
     */
    public function testRegisterValidResolver(string $action , string $className)
    {
        $resolver = new $className();
        $this->dispatcher->register($action, $resolver);
        $this->assertSame($resolver, $this->dispatcher->get($action));
    }

    /**
     * @dataProvider invalidResolvers
     * @expectedException InvalidArgumentException
     *
     * @param string $action
     * @param string $className
     */
    public function testRegisterInvalidResolver(string $action, string $className)
    {
        $this->dispatcher->register($action, new $className);
    }

    /**
     * @expectedException \DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException
     * @expectedExceptionCode \DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException::CODE_INVALID_REQUEST_METHOD
     */
    public function testInvalidRequestMethodWhileDispatching()
    {
        $serverRequest = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
        $serverRequest->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $this->dispatcher->dispatch($serverRequest);
    }

    /**
     * @dataProvider invalidRequestParams
     * @expectedException \DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException
     * @expectedExceptionCode \DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException::CODE_UNFULFILLED_REQUIREMENTS
     *
     * @param array $params
     */
    public function testUnfulfilledRequirementsWhileDispatching(array $params = array())
    {
        $serverRequest = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
        $serverRequest->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');
        $serverRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($params);

        $this->dispatcher->dispatch($serverRequest);
    }

    /**
     * @expectedException \DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException
     * @expectedExceptionCode \DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException::CODE_NOT_EXIST_APPLICATION
     */
    public function testNotExistApplicationWhileDispatching()
    {
        $params = $this->getValidRequestParams(DispatcherInterface::ACTION_UNINSTALL);

        $serverRequest = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
        $serverRequest->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');
        $serverRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($params);

        $this->applicationRegistry->expects($this->once())
            ->method('get')
            ->willThrowException(new NonExistingServiceException('', '', array()));

        $this->dispatcher->dispatch($serverRequest);
    }

    /**
     * @expectedException \DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException
     * @expectedExceptionCode \DreamCommerce\Component\ShopAppstore\Exception\Billing\UnableDispatchException::CODE_INVALID_PAYLOAD_HASH
     */
    public function testInvalidHashWhileDispatching()
    {
        $params = $this->getValidRequestParams(DispatcherInterface::ACTION_UNINSTALL);
        $params['hash'] = '#';

        $serverRequest = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
        $serverRequest->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');
        $serverRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($params);

        $this->applicationRegistry->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->getMockBuilder(ApplicationInterface::class)->getMock()
            );

        $this->dispatcher->dispatch($serverRequest);
    }

    /**
     * @dataProvider validServerRequests
     *
     * @param ServerRequestInterface $serverRequest
     * @param string $action
     */
    public function testValidDispatch(ServerRequestInterface $serverRequest, string $action)
    {
        $this->registerResolvers();

        $application = $this->getMockBuilder(ApplicationInterface::class)->getMock();
        $application->expects($this->once())
            ->method('getAppstoreSecret')
            ->willReturn(self::APPSTORE_SECRET)
        ;

        $uri = $this->getMockBuilder(UriInterface::class)->getMock();

        $this->uriFactory->expects($this->once())
            ->method('createNewByUriString')
            ->will($this->returnCallback(function($uriString) use($serverRequest, $uri) {
                $params = $serverRequest->getParsedBody();
                $this->assertEquals($params['shop_url'], $uriString);

                return $uri;
            }));

        $this->applicationRegistry->expects($this->once())
            ->method('get')
            ->willReturn($application)
        ;

        $this->shopRepository->expects($this->once())
            ->method('findOneByNameAndApplication')
            ->willReturn(null)
        ;

        $shop = $this->getMockBuilder(ShopInterface::class)->getMock();

        $this->shopFactory->expects($this->once())
            ->method('createNewByApplicationAndUri')
            ->will($this->returnCallback(function($fApplication, $fUri) use($application, $uri, $shop) {
                $this->assertEquals($application, $fApplication);
                $this->assertEquals($uri, $fUri);

                return $shop;
            }));

        $this->resolvers[$action]->expects($this->once())
            ->method('resolve')
            ->will($this->returnCallback(function($payload) use($action) {
                /** @var Message $payload */
                $this->assertInstanceOf($this->payloads[$action], $payload);

                $timestamp = $payload->getTimestamp();
                $this->assertInstanceOf(DateTime::class, $timestamp);
                $this->assertEquals($timestamp->getTimezone()->getName(), DispatcherInterface::TIMEZONE);

                $this->assertInstanceOf(ShopInterface::class, $payload->getShop());
                $this->assertInstanceOf(ApplicationInterface::class, $payload->getApplication());

                if($action === DispatcherInterface::ACTION_BILLING_SUBSCRIPTION) {
                    $subscriptionTime = $payload->getSubscriptionEndTime();
                    $this->assertInstanceOf(DateTime::class, $subscriptionTime);
                    $this->assertEquals($subscriptionTime->getTimezone()->getName(), DispatcherInterface::TIMEZONE);
                } elseif(in_array($action, [ DispatcherInterface::ACTION_INSTALL, DispatcherInterface::ACTION_UPGRADE ])) {
                    $this->assertInternalType("int", $payload->getApplicationVersion());
                    if($action === DispatcherInterface::ACTION_INSTALL) {
                        $this->assertNotNull($payload->getAuthCode());
                    }
                }
            }));

        $this->shopObjectManager->expects($this->once())
            ->method('persist');
        $this->shopObjectManager->expects($this->once())
            ->method('flush');

        $this->dispatcher->dispatch($serverRequest);
    }

    public function testUpdateShopUrlWhileDispatch()
    {
        $action = DispatcherInterface::ACTION_UNINSTALL;
        $serverRequest = $this->getValidServerRequest($action);

        $resolver = $this->getMockBuilder(MessageResolverInterface::class)->getMock();
        $this->dispatcher->register($action, $resolver);

        $uri = $this->getMockBuilder(UriInterface::class)->getMock();

        $this->uriFactory->expects($this->once())
            ->method('createNewByUriString')
            ->will($this->returnCallback(function($uriString) use($serverRequest, $uri) {
                $params = $serverRequest->getParsedBody();
                $this->assertEquals($params['shop_url'], $uriString);

                return $uri;
            }));

        $shop = $this->getMockBuilder(ShopInterface::class)->getMock();
        $shop->expects($this->once())
            ->method('setUri')
            ->will($this->returnCallback(function($fUri) use($uri) {
                /** @var UriInterface $fUri */
                $this->assertInstanceOf(UriInterface::class, $fUri);
                $this->assertEquals($uri, $fUri);
            }));

        $application = $this->getMockBuilder(ApplicationInterface::class)->getMock();
        $application->expects($this->once())
            ->method('getAppstoreSecret')
            ->willReturn(self::APPSTORE_SECRET);

        $this->applicationRegistry->expects($this->once())
            ->method('get')
            ->willReturn($application)
        ;

        $this->shopRepository->expects($this->once())
            ->method('findOneByNameAndApplication')
            ->willReturn($shop)
        ;

        $this->shopObjectManager->expects($this->once())
            ->method('persist');
        $this->shopObjectManager->expects($this->once())
            ->method('flush');

        $this->dispatcher->dispatch($serverRequest);
    }

    /* --------------------------------------------------------------------- */

    public function validResolvers()
    {
        return [
            [ DispatcherInterface::ACTION_INSTALL, ExampleResolver::class ]
        ];
    }

    public function invalidResolvers()
    {
        return [
            [ DispatcherInterface::ACTION_INSTALL, stdClass::class ],
            [ 'test', ExampleResolver::class ],
            [ 'test', stdClass::class ]
        ];
    }

    public function validServerRequests()
    {
        $serverRequests = [];
        foreach(array_keys($this->payloads) as $action) {
            $serverRequests[] = [ $this->getValidServerRequest($action), $action ];
        }

        return $serverRequests;
    }

    public function invalidRequestParams()
    {
        $validParams = [
            'action' => DispatcherInterface::ACTION_UNINSTALL,
            'shop' => '12345',
            'shop_url' => 'http://example.com',
            'hash' => '#',
            'timestamp' => time(),
            'application_code' => md5((string)time())
        ];

        $params = [];

        // 1. parameters have not been sent

        $params[] = [ [] ];

        // 2. parameters are empty

        $params[] = [ array_fill_keys(array_keys($validParams), '') ];

        // 3. parameter "subscription_end_time" has not been sent for action "billing_subscription"

        $invalidParams = $validParams;
        $invalidParams['action'] = DispatcherInterface::ACTION_BILLING_SUBSCRIPTION;

        $params[] = [ $invalidParams ];

        // 4. parameter "application_version" has not been sent for action "install" and "upgrade"

        $invalidParams = $validParams;
        $invalidParams['action'] = DispatcherInterface::ACTION_INSTALL;

        $params[] = [ $invalidParams ];

        $invalidParams = $validParams;
        $invalidParams['action'] = DispatcherInterface::ACTION_UPGRADE;

        $params[] = [ $invalidParams ];

        return $params;
    }

    /* --------------------------------------------------------------------- */

    private function getValidServerRequest(string $action)
    {
        $serverRequest = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $serverRequest->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');
        $serverRequest->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(
                $this->getValidRequestParams($action)
            );

        return $serverRequest;
    }

    private function getValidRequestParams(string $action)
    {
        $dt = new DateTime();

        $params = [
            'action' => $action,
            'shop' => md5(uniqid((string)rand(), true)),
            'shop_url' => 'http://example.com',
            'timestamp' => $dt->format('Y-m-d H:i:s'),
            'application_code' => self::APPLICATION_CODE
        ];

        switch($action) {
            case DispatcherInterface::ACTION_INSTALL:
                $params['auth_code'] = 'AUTH_CODE';
                $params['application_version'] = (string)time();
                break;
            case DispatcherInterface::ACTION_UPGRADE:
                $params['application_version'] = (string)time();
                break;
            case DispatcherInterface::ACTION_BILLING_SUBSCRIPTION:
                $dt->add(new \DateInterval('P10D'));
                $params['subscription_end_time'] = $dt->format('Y-m-d H:i:s');
                break;
        }

        ksort($params);

        $processedPayload = "";
        foreach($params as $k => $v){
            $processedPayload .= '&'.$k.'='.$v;
        }
        $processedPayload = substr($processedPayload, 1);
        $params['hash'] = hash_hmac('sha512', $processedPayload, self::APPSTORE_SECRET);

        return $params;
    }

    private function registerResolvers()
    {
        foreach(array_keys($this->payloads) as $action) {
            $resolver = $this->getMockBuilder(MessageResolverInterface::class)->getMock();
            $this->dispatcher->register($action, $resolver);
            $this->resolvers[$action] = $resolver;
        }
    }
}