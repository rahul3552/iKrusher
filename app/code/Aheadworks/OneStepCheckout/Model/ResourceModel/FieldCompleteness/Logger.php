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
namespace Aheadworks\OneStepCheckout\Model\ResourceModel\FieldCompleteness;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\OneStepCheckout\Setup\InstallSchema;

/**
 * Class Logger
 * @package Aheadworks\OneStepCheckout\Model\ResourceModel\FieldCompleteness
 */
class Logger
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var string
     */
    private $table = 'aw_osc_checkout_data_completeness';

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Log data into database
     *
     * @param int $cartId
     * @param array $data
     * @return void
     */
    public function log($cartId, array $data)
    {
        $connection = $this->resource->getConnection(InstallSchema::CHECKOUT_CONNECTION_NAME);
        $tableName = $this->resource->getTableName($this->table, InstallSchema::CHECKOUT_CONNECTION_NAME);

        $connection->delete($tableName, ['quote_id = ?' => $cartId]);

        foreach ($data as &$item) {
            $item['quote_id'] = $cartId;
            if (!isset($item['scope'])) {
                $item['scope'] = null;
            }
        }

        $connection->insertMultiple($tableName, $data);
    }
}
