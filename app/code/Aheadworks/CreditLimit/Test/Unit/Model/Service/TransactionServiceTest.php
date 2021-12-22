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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Test\Unit\Model\Service;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Service\TransactionService;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\TransactionRepositoryInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as TransactionResource;
use Aheadworks\CreditLimit\Model\Transaction\CompositeBuilder as TransactionCompositeBuilder;
use Aheadworks\CreditLimit\Model\Customer\Notifier;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for TransactionService
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model\Service
 */
class TransactionServiceTest extends TestCase
{
    /**
     * @var TransactionService
     */
    private $model;

    /**
     * @var TransactionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionRepositoryMock;

    /**
     * @var TransactionResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionResourceMock;

    /**
     * @var TransactionCompositeBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionCompositeBuilderMock;

    /**
     * @var Notifier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $notifierMock;

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->transactionRepositoryMock = $this->createMock(TransactionRepositoryInterface::class);
        $this->transactionResourceMock = $this->createMock(TransactionResource::class);
        $this->transactionCompositeBuilderMock = $this->createMock(TransactionCompositeBuilder::class);
        $this->notifierMock = $this->createMock(Notifier::class);

        $this->model = $objectManager->getObject(
            TransactionService::class,
            [
                'transactionRepository' => $this->transactionRepositoryMock,
                'transactionResource' => $this->transactionResourceMock,
                'transactionCompositeBuilder' => $this->transactionCompositeBuilderMock,
                'notifier' => $this->notifierMock
            ]
        );
    }

    /**
     * Test for createTransaction method
     */
    public function testCreateTransaction()
    {
        $this->transactionResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $transactionParamsMock = $this->createMock(TransactionParametersInterface::class);
        $transactionMock = $this->createMock(TransactionInterface::class);
        $this->transactionCompositeBuilderMock->expects($this->once())
            ->method('build')
            ->with($transactionParamsMock)
            ->willReturn($transactionMock);
        $this->transactionRepositoryMock->expects($this->once())
            ->method('save')
            ->with($transactionMock)
            ->willReturn($transactionMock);
        $this->transactionResourceMock->expects($this->once())
            ->method('commit')
            ->willReturnSelf();
        $customerId = 10;
        $transactionParamsMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $this->notifierMock->expects($this->once())
            ->method('notify')
            ->with($customerId, $transactionMock)
            ->willReturn(true);

        $this->assertSame($transactionMock, $this->model->createTransaction($transactionParamsMock));
    }

    /**
     * Test createTransaction method with exception.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Amount is required
     */
    public function testCreateTransactionOnException()
    {
        $this->transactionResourceMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturnSelf();
        $transactionParamsMock = $this->createMock(TransactionParametersInterface::class);
        $this->transactionCompositeBuilderMock->expects($this->once())
            ->method('build')
            ->with($transactionParamsMock)
            ->willThrowException(new \InvalidArgumentException(__('Amount is required')));
        $this->transactionResourceMock->expects($this->once())
            ->method('rollBack')
            ->willReturnSelf();

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->model->createTransaction($transactionParamsMock);
    }
}
