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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Plugin\Model\EnterpriseGiftCard;

use Magento\Framework\DataObject;

/**
 * Class ProductOptionProcessorPlugin
 *
 * @package Aheadworks\QuickOrder\Plugin\Model\EnterpriseGiftCard
 */
class ProductOptionProcessorPlugin
{
    /**
     * Convert extra buy request params to product option
     *
     * @param \Magento\GiftCard\Model\ProductOptionProcessor $subject
     * @param array $resultArray
     * @param DataObject $buyRequest
     * @return array
     */
    public function afterConvertToProductOption($subject, $resultArray, $buyRequest)
    {
        if ($buyRequest->getCustomGiftcardAmount()
            && $buyRequest->getAwQuickOrder()
            && isset($resultArray['giftcard_item_option'])
        ) {
            /** @var \Magento\GiftCard\Api\Data\GiftCardOptionInterface $giftCardOption */
            $giftCardOption = $resultArray['giftcard_item_option'];
            $giftCardOption->setCustomGiftcardAmount($buyRequest->getCustomGiftcardAmount());
            $resultArray['giftcard_item_option'] = $giftCardOption;
        }

        return $resultArray;
    }
}
