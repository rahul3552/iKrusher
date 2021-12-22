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
namespace Aheadworks\Ctq\Model\Metadata\Negotiation;

/**
 * Interface NegotiatedDiscountInterface
 *
 * @package Aheadworks\Ctq\Model\Metadata\Negotiation
 */
interface NegotiatedDiscountInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const DISCOUNT_TYPE = 'discount_type';
    const DISCOUNT_VALUE = 'discount_value';
    /**#@-*/

    /**
     * Get discount type
     *
     * @return string
     */
    public function getDiscountType();

    /**
     * Set discount type
     *
     * @param int $discountType
     * @return $this
     */
    public function setDiscountType($discountType);

    /**
     * Get discount value
     *
     * @return string|float
     */
    public function getDiscountValue();

    /**
     * Set discount value
     *
     * @param string|float $discountValue
     * @return $this
     */
    public function setDiscountValue($discountValue);
}
