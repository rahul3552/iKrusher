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
namespace Aheadworks\Ctq\Plugin\Model\Quote\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

/**
 * Class AwGiftCardPlugin
 *
 * @package Aheadworks\Ctq\Plugin\Model\Quote\Total
 */
class AwGiftCardPlugin
{
    /**
     * Adjust result to avoid the issue with non-existing extension attributes
     *
     * @param AbstractTotal $subject
     * @param array $result
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function afterFetch(
        AbstractTotal $subject,
        $result,
        Quote $quote,
        Total $total
    ) {
        if (!$result && $quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getAwCtqQuote()) {
            $amount = $total->getAwGiftcardAmount();
            if ($amount != 0) {
                $result = [
                    'code' => $subject->getCode(),
                    'aw_giftcard_codes' => [],
                    'title' => __('Gift Card'),
                    'value' => -$amount
                ];
            }
        }

        return $result;
    }
}
