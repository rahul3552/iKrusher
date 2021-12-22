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
namespace Aheadworks\OneStepCheckout\Plugin\Checkout;

use Magento\Checkout\Api\TotalsInformationManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Quote\Api\Data\TotalsInterface;

/**
 * Class TotalsInformationPlugin
 * @package Aheadworks\OneStepCheckout\Plugin\Checkout
 */
class TotalsInformationPlugin
{
    /**
     * Quote repository.
     *
     * @var cartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param TotalsInformationManagementInterface $subject
     * @param \Closure $proceed
     * @param string $cartId
     * @param TotalsInformationInterface $addressInformation
     * @return TotalsInterface
     */
    public function aroundCalculate(
        TotalsInformationManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        TotalsInformationInterface $addressInformation
    ) {
        $result = $proceed($cartId, $addressInformation);
        try {
            $quote = $this->cartRepository->get($cartId);
            $quote->save();
        } catch (\Exception $e) {
            return $result;
        }
        return $result;
    }
}
