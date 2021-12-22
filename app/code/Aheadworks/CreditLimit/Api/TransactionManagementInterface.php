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
namespace Aheadworks\CreditLimit\Api;

/**
 * Interface TransactionManagementInterface
 * @api
 */
interface TransactionManagementInterface
{
    /**
     * Create transaction
     *
     * List of params:
     * customer_id - required
     * action - required
     * amount - depends on action
     * amount_currency - depends on action
     * used_currency - depends on action
     * credit_limit - depends on action
     * other params are optional
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface $params
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createTransaction(\Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface $params);
}
