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
namespace Aheadworks\Ctq\Model\Source\Quote;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\Ctq\Model\Source\Quote
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Quote statuses
     */
    const PENDING_SELLER_REVIEW = 'pending_seller_review';
    const DECLINED_BY_SELLER = 'declined_by_seller';
    const PENDING_BUYER_REVIEW = 'pending_buyer_review';
    const DECLINED_BY_BUYER = 'declined_by_buyer';
    const ORDERED = 'ordered';
    const EXPIRED = 'expired';
    const ACCEPTED = 'accepted';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PENDING_SELLER_REVIEW, 'label' => __('Pending Seller Review')],
            ['value' => self::DECLINED_BY_SELLER, 'label' => __('Declined by Seller')],
            ['value' => self::PENDING_BUYER_REVIEW, 'label' => __('Pending Buyer Review')],
            ['value' => self::DECLINED_BY_BUYER, 'label' => __('Declined by Buyer')],
            ['value' => self::ORDERED, 'label' => __('Ordered')],
            ['value' => self::EXPIRED, 'label' => __('Expired')],
            ['value' => self::ACCEPTED, 'label' => __('Accepted')]
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->toOptionArray() as $optionItem) {
            $options[$optionItem['value']] = $optionItem['label'];
        }
        return $options;
    }
}
