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
namespace Aheadworks\CreditLimit\Test\Unit\Model\Transaction;

use Aheadworks\CreditLimit\Model\Transaction\TransactionParametersFactory;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterfaceFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for TransactionParametersFactory
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model\Transaction
 */
class TransactionParametersFactoryTest extends TestCase
{
    /**
     * @var TransactionParametersFactory
     */
    private $model;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var TransactionParametersInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionParametersFactoryMock;

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->transactionParametersFactoryMock = $this->createPartialMock(
            TransactionParametersInterfaceFactory::class,
            ['create']
        );

        $this->model = $objectManager->getObject(
            TransactionParametersFactory::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'transactionParametersFactory' => $this->transactionParametersFactoryMock
            ]
        );
    }

    /**
     * Test for create method
     *
     * @throws \ReflectionException
     */
    public function testCreate()
    {
        $data = [
            TransactionParametersInterface::CUSTOMER_ID => 1,
            TransactionParametersInterface::AMOUNT => 20
        ];

        $transactionParametersMock = $this->createMock(TransactionParametersInterface::class);
        $this->transactionParametersFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transactionParametersMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($transactionParametersMock, $data, TransactionParametersInterface::class);

        $this->assertSame($transactionParametersMock, $this->model->create($data));
    }
}
