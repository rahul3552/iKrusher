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
namespace Aheadworks\OneStepCheckout\Model\Address\Attribute\Code;

/**
 * Class Resolver
 *
 * @package Aheadworks\OneStepCheckout\Model\Address\Attribute\Code
 */
class Resolver
{
    /**
     * @var array
     */
    private $fieldsDuplicationMap = [];

    /**
     * @param array $fieldsDuplicationMap
     */
    public function __construct(
        array $fieldsDuplicationMap = []
    ) {
        $this->fieldsDuplicationMap = $fieldsDuplicationMap;
    }

    /**
     * Get duplicated attribute code
     *
     * @param string $attributeCode
     * @return string|null
     */
    public function getDuplicatedAttributeCode($attributeCode)
    {
        if (isset($this->fieldsDuplicationMap[$attributeCode])) {
            return $this->fieldsDuplicationMap[$attributeCode];
        }
        $flippedMap = array_flip($this->fieldsDuplicationMap);
        if (isset($flippedMap[$attributeCode])) {
            return $flippedMap[$attributeCode];
        }
        return null;
    }
}
