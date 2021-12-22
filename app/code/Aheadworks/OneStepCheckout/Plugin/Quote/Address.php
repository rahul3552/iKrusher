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
namespace Aheadworks\OneStepCheckout\Plugin\Quote;

use Magento\Quote\Api\Data\AddressInterface;

/**
 * Class Address
 * @package Aheadworks\OneStepCheckout\Plugin\Quote
 */
class Address
{
    /**
     * @param AddressInterface $subject
     * @param \Closure $proceed
     * @param string|string[] $street
     * @return null|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSetStreet(AddressInterface $subject, \Closure $proceed, $street)
    {
        return empty($street) ? $proceed('') : $proceed($street);
    }
}
