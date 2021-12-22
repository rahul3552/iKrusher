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
namespace Aheadworks\Ctq\Model\ResourceModel;

use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class Quote
 * @package Aheadworks\Ctq\Model\ResourceModel
 */
class Quote extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_ctq_quote';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, QuoteInterface::ID);
    }

    /**
     * Get quote identifier by cart id
     *
     * @param int $cartId
     * @return int|false
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdByCartId($cartId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('cart_id = :cart_id');

        return $connection->fetchOne($select, ['cart_id' => $cartId]);
    }

    /**
     * Get quote identifier by order id
     *
     * @param int $orderId
     * @return int|false
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdByOrderId($orderId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('order_id = :order_id');

        return $connection->fetchOne($select, ['order_id' => $orderId]);
    }
}
