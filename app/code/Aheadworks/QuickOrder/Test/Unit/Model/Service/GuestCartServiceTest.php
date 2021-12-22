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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Aheadworks\QuickOrder\Api\CartManagementInterface;
use Aheadworks\QuickOrder\Model\Service\GuestCartService;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;

/**
 * Unit test for GuestCartService
 *
 * @package Aheadworks\QuickOrder\Test\Unit\Model\Service
 */
class GuestCartServiceTest extends TestCase
{
    /**
     * @var GuestCartService
     */
    private $service;

    /**
     * @var QuoteIdMaskFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteIdMaskFactoryMock;

    /**
     * @var CartManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartManagementMock;

    /**
     * Init mocks for tests
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->quoteIdMaskFactoryMock = $this->quoteIdMaskFactoryMock = $this->createPartialMock(
            QuoteIdMaskFactory::class,
            ['create']
        );
        $this->cartManagementMock = $this->createMock(CartManagementInterface::class);
        $this->service = $objectManager->getObject(
            GuestCartService::class,
            [
                'quoteIdMaskFactory' => $this->quoteIdMaskFactoryMock,
                'cartManagement' => $this->cartManagementMock
            ]
        );
    }

    /**
     * Test addListToCart method
     *
     * @throws \Exception
     */
    public function testAddListToCart()
    {
        $quoteIdMaskMock = $this->getMockBuilder(QuoteIdMask::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQuoteId', 'getMaskedId', 'load', 'save', 'setQuoteId'])
            ->getMock();
        $maskedCartId = 'maskedCartId2';
        $listId = 2;
        $cartId = 1;
        $quoteIdMaskMock->expects($this->once())->method('load')->with($maskedCartId, 'masked_id')->willReturnSelf();
        $quoteIdMaskMock->expects($this->once())->method('getQuoteId')->willReturn($cartId);
        $this->quoteIdMaskFactoryMock->expects($this->once())->method('create')->willReturn($quoteIdMaskMock);
        $operationResultMock = $this->createMock(OperationResultInterface::class);
        $this->cartManagementMock->expects($this->once())
            ->method('addListToCart')
            ->with($listId, $cartId)
            ->willReturn($operationResultMock);

        $this->assertEquals($operationResultMock, $this->service->addListToCart($listId, $maskedCartId));
    }
}
