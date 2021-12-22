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
namespace Aheadworks\OneStepCheckout\Test\Unit\Model;

use Aheadworks\OneStepCheckout\Model\CheckoutSectionsManagement;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\Data\ShippingMethodInterface;

/**
 * Test for \Aheadworks\OneStepCheckout\Model\CheckoutSectionsManagement
 */
class CheckoutSectionsManagementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CheckoutSectionsManagement
     */
    private $sectionManagement;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->sectionManagement = $objectManager->getObject(CheckoutSectionsManagement::class);
    }

    /**
     * @param ShippingMethodInterface[]|\PHPUnit_Framework_MockObject_MockObject $allMethodsMocks
     * @param string $currentMethod
     * @param string $defaultMethod
     * @param string $result
     * @dataProvider resolveShippingMethodDataProvider
     */
    public function testResolveShippingMethod($allMethodsMocks, $currentMethod, $defaultMethod, $result)
    {
        $class = new \ReflectionClass($this->sectionManagement);
        $method = $class->getMethod('resolveShippingMethod');
        $method->setAccessible(true);

        $this->assertEquals(
            $result,
            $method->invokeArgs($this->sectionManagement, [$allMethodsMocks, $currentMethod, $defaultMethod])
        );
    }

    /**
     * Create shipping method mock
     *
     * @param string $carrierCode
     * @param string $methodCode
     * @return ShippingMethodInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createShippingMethodMock($carrierCode, $methodCode)
    {
        $shippingMethodMock = $this->getMockForAbstractClass(ShippingMethodInterface::class);
        $shippingMethodMock->expects($this->any())
            ->method('getCarrierCode')
            ->willReturn($carrierCode);
        $shippingMethodMock->expects($this->any())
            ->method('getMethodCode')
            ->willReturn($methodCode);
        return $shippingMethodMock;
    }

    /**
     * @return array
     */
    public function resolveShippingMethodDataProvider()
    {
        return [
            [
                [
                    $this->createShippingMethodMock('flatrate', 'flatrate'),
                    $this->createShippingMethodMock('tablerate', 'bestway')
                ],
                null,
                null,
                null
            ],
            [
                [
                    $this->createShippingMethodMock('flatrate', 'flatrate'),
                    $this->createShippingMethodMock('tablerate', 'bestway')
                ],
                'flatrate_flatrate',
                null,
                null
            ],
            [
                [
                    $this->createShippingMethodMock('flatrate', 'flatrate'),
                    $this->createShippingMethodMock('tablerate', 'bestway')
                ],
                'freeshipping_freeshipping',
                null,
                null
            ],
            [
                [
                    $this->createShippingMethodMock('flatrate', 'flatrate'),
                    $this->createShippingMethodMock('tablerate', 'bestway')
                ],
                'freeshipping_freeshipping',
                'flatrate_flatrate',
                'flatrate_flatrate'
            ],
            [
                [$this->createShippingMethodMock('flatrate', 'flatrate')],
                'freeshipping_freeshipping',
                null,
                'flatrate_flatrate'
            ],
            [
                [$this->createShippingMethodMock('flatrate', 'flatrate')],
                null,
                null,
                'flatrate_flatrate'
            ],
            [
                [$this->createShippingMethodMock('flatrate', 'flatrate')],
                'flatrate_flatrate',
                null,
                null
            ],
            [
                [
                    $this->createShippingMethodMock('flatrate', 'flatrate'),
                    $this->createShippingMethodMock('tablerate', 'bestway')
                ],
                null,
                'tablerate_bestway',
                'tablerate_bestway'
            ],
            [
                [
                    $this->createShippingMethodMock('flatrate', 'flatrate'),
                    $this->createShippingMethodMock('tablerate', 'bestway')
                ],
                null,
                'freeshipping_freeshipping',
                null
            ]
        ];
    }
}
