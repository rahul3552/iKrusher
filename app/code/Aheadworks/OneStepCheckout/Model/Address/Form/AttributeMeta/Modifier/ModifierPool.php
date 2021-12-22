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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\CountryId;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\City;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\Fax;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\Firstname;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\Lastname;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\Region;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\RegionId;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\VatId;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ModifierPool
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier
 */
class ModifierPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     * phpcs:disable Magento2.PHP.LiteralNamespaces.LiteralClassUsage
     */
    private $modifiers = [
        'country_id' => CountryId::class,
        'city' => City::class,
        'prefix' => 'Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\Prefix',
        'suffix' => 'Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\Suffix',
        'fax' => Fax::class,
        'firstname' => Firstname::class,
        'lastname' => Lastname::class,
        'region' => Region::class,
        'region_id' => RegionId::class,
        'vat_id' => VatId::class
    ];

    /**
     * @var ModifierInterface[]
     */
    private $modifierInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $modifiers
     */
    public function __construct(ObjectManagerInterface $objectManager, $modifiers = [])
    {
        $this->objectManager = $objectManager;
        $this->modifiers = array_merge($this->modifiers, $modifiers);
    }

    /**
     * Get modifier
     *
     * @param string $attributeCode
     * @return ModifierInterface|null
     */
    public function getModifier($attributeCode)
    {
        if (!isset($this->modifierInstances[$attributeCode])) {
            $className = isset($this->modifiers[$attributeCode])
                ? $this->modifiers[$attributeCode]
                : DefaultModifier::class;
            $this->modifierInstances[$attributeCode] = $this->objectManager->create($className);
        }
        return $this->modifierInstances[$attributeCode];
    }
}
