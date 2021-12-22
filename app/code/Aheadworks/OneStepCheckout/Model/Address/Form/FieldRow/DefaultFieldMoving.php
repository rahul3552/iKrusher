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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow;

/**
 * Class DefaultFieldMoving
 *
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow
 */
class DefaultFieldMoving
{
    /**
     * @var array
     */
    private $map = [
        'prefix' => false,
        'firstname' => true,
        'middlename' => true,
        'lastname' => true,
        'suffix' => true,
        'street' => [
            0 => false,
            1 => true,
            2 => true
        ],
        'city' => false,
        'country_id' => false,
        'region' => true,
        'postcode' => true,
        'company' => false,
        'telephone' => true,
        'fax' => true,
        'vat_id' => false,
    ];

    /**
     * Check if required to move field
     *
     * @param string $attributeCode
     * @param int|null $lineNumber
     * @return bool
     */
    public function get($attributeCode, $lineNumber = null)
    {
        if ($lineNumber === null) {
            return isset($this->map[$attributeCode])
                ? $this->map[$attributeCode]
                : false;
        } else {
            return isset($this->map[$attributeCode][$lineNumber])
                ? $this->map[$attributeCode][$lineNumber]
                : false;
        }
    }
}
