<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Test\Unit\Model\Magento;

use Aheadworks\Ctq\Model\Magento\ModuleUser\UserRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\User\Api\Data\UserInterfaceFactory;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class UserRepositoryTest
 * @package Aheadworks\Ctq\Test\Unit\Model\Magento
 */
class UserRepositoryTest extends TestCase
{
    /**
     * @var UserResource|PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var UserInterfaceFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $userFactoryMock;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * Init mocks for tests
     *
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->createMock(UserResource::class);
        $this->userFactoryMock = $this->createPartialMock(
            UserInterfaceFactory::class,
            ['create']
        );

        $this->repository = $objectManager->getObject(
            UserRepository::class,
            [
                'resource' => $this->resourceMock,
                'userFactory' => $this->userFactoryMock
            ]
        );
    }

    /**
     * Test getById method
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \ReflectionException
     */
    public function testGetById()
    {
        $userId = 1;
        $userMock= $this->createMock(User::class);

        $this->userFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($userMock);
        $this->resourceMock
            ->expects($this->once())
            ->method('load');
        $userMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($userId);

        $this->assertEquals($userMock, $this->repository->getById($userId));
    }

    /**
     * Test getById method on exception
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \ReflectionException
     */
    public function testGetByIdOnException()
    {
        $userId = 1;
        $failUserId = 0;
        $userMock= $this->createMock(User::class);

        $this->userFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($userMock);
        $this->resourceMock
            ->expects($this->once())
            ->method('load');
        $userMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($failUserId);

        $this->expectException(NoSuchEntityException::class);

        $this->repository->getById($userId);
    }
}
