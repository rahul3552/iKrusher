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
namespace Aheadworks\CreditLimit\Model\Transaction;

use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CompositeBuilder
 *
 * @package Aheadworks\CreditLimit\Model\Transaction
 */
class CompositeBuilder
{
    /**
     * @var TransactionInterfaceFactory
     */
    private $transactionFactory;

    /**
     * @var TransactionBuilderInterface[]
     */
    private $builders;

    /**
     * @param TransactionInterfaceFactory $transactionFactory
     * @param TransactionBuilderInterface[] $builders
     */
    public function __construct(
        TransactionInterfaceFactory $transactionFactory,
        $builders = []
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->builders = $builders;
    }

    /**
     * Build transaction using provided params
     *
     * @param TransactionParametersInterface $params
     * @return TransactionInterface
     * @throws LocalizedException
     */
    public function build(TransactionParametersInterface $params)
    {
        /** @var TransactionInterface $transaction */
        $transaction = $this->transactionFactory->create();
        foreach ($this->builders as $builder) {
            if ($builder->checkIsValid($params)) {
                $builder->build($transaction, $params);
            }
        }

        return $transaction;
    }
}
