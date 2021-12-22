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
namespace Aheadworks\OneStepCheckout\Test\Unit\Model\Address\Form\AttributeMeta;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierInterface;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier
 */
class ModifierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Modifier
     */
    private $modifier;

    /**
     * @var ModifierPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $modifierPoolMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->modifierPoolMock = $this->getMockBuilder(ModifierPool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getModifier'])
            ->getMock();
        $this->modifier = $objectManager->getObject(
            Modifier::class,
            ['modifierPool' => $this->modifierPoolMock]
        );
    }

    public function testModify()
    {
        $addressType = 'shipping';
        $attributeCode = 'firstname';
        $metadata = ['label' => 'First Name'];
        $modifiedMetadata = ['label' => 'First Name changed'];

        $modifierMock = $this->getMockForAbstractClass(ModifierInterface::class);

        $this->modifierPoolMock->expects($this->once())
            ->method('getModifier')
            ->with($attributeCode)
            ->willReturn($modifierMock);
        $modifierMock->expects($this->once())
            ->method('modify')
            ->with($metadata, $addressType)
            ->willReturn($modifiedMetadata);

        $this->assertEquals(
            $modifiedMetadata,
            $this->modifier->modify($attributeCode, $metadata, $addressType)
        );
    }
}
