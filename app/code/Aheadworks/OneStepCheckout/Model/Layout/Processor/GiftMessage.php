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
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class GiftMessage
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class GiftMessage implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $giftMessageItemPath = 'components/checkout/children/cart-items/children/details/giftMessageRendererConfig';
        $paymentOptionsChildrenPath = 'components/checkout/children/payment-options/children/giftMessage';
        $giftMessageItemLayout = $this->arrayManager->get($giftMessageItemPath, $jsLayout);

        $giftMessageOrderLayout = $giftMessageItemLayout;
        $giftMessageItemLayout['level'] = 'item';

        $giftMessageOrderLayout['dataScope'] = 'giftMessage.order';
        $giftMessageOrderLayout['level'] = 'order';

        $jsLayout = $this->arrayManager->set($giftMessageItemPath, $jsLayout, $giftMessageItemLayout);
        $jsLayout = $this->arrayManager->set($paymentOptionsChildrenPath, $jsLayout, $giftMessageOrderLayout);

        return $jsLayout;
    }
}
