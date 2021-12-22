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
namespace Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary as CreditSummarySummary;
use Aheadworks\CreditLimit\Model\CreditSummary;

/**
 * Class Collection
 *
 * @see \Aheadworks\CreditLimit\Model\ResourceModel\Customer\Collection used as base collection
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel\CreditSummary
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(CreditSummary::class, CreditSummarySummary::class);
    }
}
