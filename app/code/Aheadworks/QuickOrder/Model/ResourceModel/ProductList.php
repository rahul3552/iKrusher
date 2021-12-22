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
namespace Aheadworks\QuickOrder\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;

/**
 * Class ProductList
 *
 * @package Aheadworks\QuickOrder\Model\ResourceModel
 */
class ProductList extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_qo_product_list';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, ProductListInterface::LIST_ID);
    }

    /**
     * Retrieve list ID by customer ID
     *
     * @param int $customerId
     * @return int|false
     * @throws LocalizedException
     */
    public function getListIdByCustomerId($customerId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where(ProductListInterface::CUSTOMER_ID . ' = :' . ProductListInterface::CUSTOMER_ID);

        return $connection->fetchOne($select, [ProductListInterface::CUSTOMER_ID => $customerId]);
    }
}
