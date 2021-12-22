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
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface GuestCartItemOptionsManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @deprecated
 */
interface GuestCartItemOptionsManagementInterface
{
    /**
     * Update cart item options
     *
     * @param int $itemId
     * @param string $cartId
     * @param string $options
     * @return \Aheadworks\OneStepCheckout\Api\Data\CartItemOptionsDetailsInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($itemId, $cartId, $options);
}
