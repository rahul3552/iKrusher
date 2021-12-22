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
namespace Aheadworks\OneStepCheckout\Model\ResourceModel\Report;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AbandonedCheckout
 * @package Aheadworks\OneStepCheckout\Model\ResourceModel\Report
 */
class AbandonedCheckout extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_osc_report_abandoned_checkouts_index', 'index_id');
    }

    /**
     * Fetch min available date
     *
     * @return string
     * @throws LocalizedException
     */
    public function fetchMinDate()
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['main_table' => $this->getMainTable()],
                ['min' => new \Zend_Db_Expr('MIN(period)')]
            );
        return $connection->fetchOne($select);
    }
}
