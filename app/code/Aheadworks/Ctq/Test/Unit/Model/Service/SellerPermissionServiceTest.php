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
namespace Aheadworks\Ctq\Test\Unit\Model\Service;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsInterface;
use Aheadworks\Ctq\Model\Service\BuyerPermissionService;
use Aheadworks\Ctq\Model\Service\SellerPermissionService;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class SellerPermissionServiceTest
 * @package Aheadworks\Ctq\Test\Unit\Model\Service
 */
class SellerPermissionServiceTest extends TestCase
{
    /**
     * @var SellerPermissionService
     */
    private $model;

    /**
     * @var RestrictionsPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statusRestrictionsPoolMock;

    /**
     * @var QuoteRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->statusRestrictionsPoolMock = $this->createPartialMock(RestrictionsPool::class, ['getRestrictions']);
        $this->quoteRepositoryMock = $this->getMockForAbstractClass(QuoteRepositoryInterface::class);

        $this->model = $objectManager->getObject(
            BuyerPermissionService::class,
            [
                'statusRestrictionsPool' => $this->statusRestrictionsPoolMock,
                'quoteRepository' => $this->quoteRepositoryMock
            ]
        );
    }

    /**
     * Test canBuyQuote method
     *
     * @param array $nextAvailableStatuses
     * @param bool $expected
     * @dataProvider canBuyQuoteDataProvider
     */
    public function testCanBuyQuote($nextAvailableStatuses, $expected)
    {
        $quoteId = 1;
        $quoteStatus = Status::PENDING_BUYER_REVIEW;

        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $this->quoteRepositoryMock
            ->method('get')
            ->with($quoteId)
            ->willReturn($quoteMock);

        $quoteMock
            ->method('getStatus')
            ->willReturn($quoteStatus);

        $restrictionsMock = $this->getMockForAbstractClass(RestrictionsInterface::class);
        $restrictionsMock
            ->method('getNextAvailableStatuses')
            ->willReturn($nextAvailableStatuses);

        $this->statusRestrictionsPoolMock
            ->method('getRestrictions')
            ->with($quoteStatus)
            ->willReturn($restrictionsMock);

        $this->assertEquals($expected, $this->model->canBuyQuote($quoteId));
    }

    /**
     * Data provider for tests
     *
     * @return array
     */
    public function canBuyQuoteDataProvider()
    {
        return [
            [[Status::ORDERED], true],
            [[], false],
        ];
    }
}
