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
namespace Aheadworks\Ctq\Setup\Updater\Schema;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\ResourceModel\Comment;
use Aheadworks\Ctq\Model\ResourceModel\Quote;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class Updater
 *
 * @package Aheadworks\Ctq\Setup\Updater\Schema
 */
class Updater
{
    /**
     * Update for 1.3.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function update130(SchemaSetupInterface $setup)
    {
        $this->addOrderIdColumn($setup);

        return $this;
    }

    /**
     * Update for 1.4.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function update140(SchemaSetupInterface $setup)
    {
        $this->allowNullValueToOwnerIdColumn($setup);

        return $this;
    }

    /**
     * Add order id to quote table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addOrderIdColumn(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable(Quote::MAIN_TABLE_NAME);
        if (!$connection->tableColumnExists($tableName, QuoteInterface::ORDER_ID)) {
            $connection->addColumn(
                $tableName,
                QuoteInterface::ORDER_ID,
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'unsigned' => true,
                    'after' => QuoteInterface::CART_ID,
                    'comment' => 'Order ID'
                ]
            );

            $connection->addIndex(
                $tableName,
                $connection->getIndexName($tableName, [QuoteInterface::ORDER_ID]),
                [QuoteInterface::ORDER_ID]
            );
        }

        return $this;
    }

    private function allowNullValueToOwnerIdColumn(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable(Comment::MAIN_TABLE_NAME);
        if ($connection->tableColumnExists($tableName, CommentInterface::OWNER_ID)) {
            $connection->modifyColumn(
                $tableName,
                CommentInterface::OWNER_ID,
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'unsigned' => true,
                    'comment' => 'Owner Id'
                ]
            );
        }
    }
}
