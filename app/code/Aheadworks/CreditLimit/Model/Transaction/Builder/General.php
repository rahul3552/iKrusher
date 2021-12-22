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
namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;

/**
 * Class General
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class General extends AbstractBuilder implements TransactionBuilderInterface
{
    /**
     * @inheritdoc
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        if (!$params->getCustomerId()) {
            throw new \InvalidArgumentException(__('Customer ID is required'));
        }
        if (!$params->getAction()) {
            throw new \InvalidArgumentException(__('Transaction action is required'));
        }
        if (!in_array($params->getAction(), $this->transactionActionSource->getAllActions())) {
            throw new \InvalidArgumentException(__('Transaction action type is not allowed'));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params)
    {
        $transaction->setAction($params->getAction());
    }
}
