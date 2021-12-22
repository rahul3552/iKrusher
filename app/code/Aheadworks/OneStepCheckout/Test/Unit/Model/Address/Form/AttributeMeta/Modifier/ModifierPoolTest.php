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
namespace Aheadworks\OneStepCheckout\Test\Unit\Model\Address\Form\AttributeMeta\Modifier;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierInterface;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierPool;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierPool
 */
class ModifierPoolTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->objectManagerMock = $this->getMockForAbstractClass(ObjectManagerInterface::class);
    }

    public function testGetModifier()
    {
        $attributeCode = 'firstname';
        $modifierClassName = 'FirstNameModifier';

        /** @var ModifierPool $modifierPool */
        $modifierPool = $this->objectManager->getObject(
            ModifierPool::class,
            ['objectManager' => $this->objectManagerMock]
        );
        $modifierMock = $this->getMockForAbstractClass(ModifierInterface::class);

        $class = new \ReflectionClass($modifierPool);

        $modifiersProperty = $class->getProperty('modifiers');
        $modifiersProperty->setAccessible(true);
        $modifiersProperty->setValue($modifierPool, [$attributeCode => $modifierClassName]);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($modifierClassName)
            ->willReturn($modifierMock);

        $this->assertSame($modifierMock, $modifierPool->getModifier($attributeCode));
    }

    public function testGetModifierCustom()
    {
        $attributeCode = 'firstname';
        $modifierClassName = 'FirstNameCustomModifier';

        /** @var ModifierPool $modifierPool */
        $modifierPool = $this->objectManager->getObject(
            ModifierPool::class,
            [
                'objectManager' => $this->objectManagerMock,
                'modifiers' => [$attributeCode => $modifierClassName]
            ]
        );
        $modifierMock = $this->getMockForAbstractClass(ModifierInterface::class);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($modifierClassName)
            ->willReturn($modifierMock);

        $this->assertSame($modifierMock, $modifierPool->getModifier($attributeCode));
    }
}
