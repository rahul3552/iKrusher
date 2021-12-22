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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Source\Transaction;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class EntityType
 *
 * @package Aheadworks\CreditLimit\Model\Source\Transaction
 */
class EntityType implements ArrayInterface
{
    /**#@+
     * Entity type values
     */
    const ORDER_ID = 'order_id';
    const CREDIT_MEMO_ID = 'credit_memo_id';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ORDER_ID,
                'label' => __('Order ID')
            ],
            [
                'value' => self::CREDIT_MEMO_ID,
                'label' => __('Credit Memo ID')
            ]
        ];
    }
}
