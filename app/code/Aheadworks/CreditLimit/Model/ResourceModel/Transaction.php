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
namespace Aheadworks\CreditLimit\Model\ResourceModel;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;

/**
 * Class Transaction
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel
 */
class Transaction extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_cl_transaction';

    /**
     * Transaction entity table
     */
    const TRANSACTION_ENTITY_TABLE = 'aw_cl_transaction_entity';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TransactionInterface::ID);
    }
}
