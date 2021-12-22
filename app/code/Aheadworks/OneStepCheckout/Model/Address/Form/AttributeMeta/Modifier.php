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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierPool;

/**
 * Class Modifier
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta
 */
class Modifier
{
    /**
     * @var ModifierPool
     */
    private $modifierPool;

    /**
     * @param ModifierPool $modifierPool
     */
    public function __construct(ModifierPool $modifierPool)
    {
        $this->modifierPool = $modifierPool;
    }

    /**
     * Modify attribute metadata
     *
     * @param string $attributeCode
     * @param array $metadata
     * @param string $addressType
     * @return array
     */
    public function modify($attributeCode, $metadata, $addressType)
    {
        $modifier = $this->modifierPool->getModifier($attributeCode);
        return $modifier->modify($metadata, $addressType);
    }
}
