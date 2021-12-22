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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Test\Unit\Model\Layout\Processor\AddressAttributes;

use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultSortOrder;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\Configurator;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes\FieldRowsSorter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes\FieldRowsSorter
 */
class FieldRowsSorterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FieldRowsSorter
     */
    private $sorter;

    /**
     * @var Configurator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configuratorMock;

    /**
     * @var DefaultSortOrder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $defaultSortOrderMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->configuratorMock = $this->getMockBuilder(Configurator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomizationConfig'])
            ->getMock();
        $this->defaultSortOrderMock = $this->getMockBuilder(DefaultSortOrder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRowSortOrder'])
            ->getMock();
        $this->sorter = $objectManager->getObject(
            FieldRowsSorter::class,
            [
                'configurator' => $this->configuratorMock,
                'defaultSortOrder' => $this->defaultSortOrderMock
            ]
        );
    }

    public function testSortDefaultValues()
    {
        $rowId = 'row1';
        $addressType = 'shipping';
        $sortOrder = 10;

        $this->configuratorMock->expects($this->once())
            ->method('getCustomizationConfig')
            ->with($addressType)
            ->willReturn([]);
        $this->defaultSortOrderMock->expects($this->once())
            ->method('getRowSortOrder')
            ->with($rowId)
            ->willReturn($sortOrder);

        $this->assertEquals(
            [
                $rowId => [
                    'component' => 'uiComponent',
                    'config' => [],
                    'children' => [],
                    'sortOrder' => $sortOrder
                ]
            ],
            $this->sorter->sort(
                [
                    $rowId => [
                        'component' => 'uiComponent',
                        'config' => [],
                        'children' => [],
                        'sortOrder' => 2
                    ]
                ],
                $addressType
            )
        );
    }

    public function testSortConfigValues()
    {
        $rowId = 'row1';
        $addressType = 'shipping';
        $sortOrder = 10;

        $this->configuratorMock->expects($this->once())
            ->method('getCustomizationConfig')
            ->with($addressType)
            ->willReturn(
                [
                    'sort_orders' => [
                        $rowId => $sortOrder
                    ]
                ]
            );
        $this->defaultSortOrderMock->expects($this->never())
            ->method('getRowSortOrder');

        $this->assertEquals(
            [
                $rowId => [
                    'component' => 'uiComponent',
                    'config' => [],
                    'children' => [],
                    'sortOrder' => $sortOrder
                ]
            ],
            $this->sorter->sort(
                [
                    $rowId => [
                        'component' => 'uiComponent',
                        'config' => [],
                        'children' => [],
                        'sortOrder' => 2
                    ]
                ],
                $addressType
            )
        );
    }
}
