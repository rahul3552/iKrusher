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
 * Interface CartItemManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface CartItemManagementInterface
{
    /**
     * Remove item from cart
     *
     * @param int $itemId
     * @param int $cartId
     * @return \Aheadworks\OneStepCheckout\Api\Data\CartItemUpdateDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function remove($itemId, $cartId);

    /**
     * Update cart item
     *
     * @param \Magento\Quote\Api\Data\TotalsItemInterface $item
     * @param int $cartId
     * @return \Aheadworks\OneStepCheckout\Api\Data\CartItemUpdateDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function update(\Magento\Quote\Api\Data\TotalsItemInterface $item, $cartId);
}
