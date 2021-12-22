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

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterfaceFactory;

/**
 * Class TransactionParametersFactory
 *
 * @package Aheadworks\CreditLimit\Model\Transaction
 */
class TransactionParametersFactory
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TransactionParametersInterfaceFactory
     */
    private $transactionParametersFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param TransactionParametersInterfaceFactory $transactionParametersFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        TransactionParametersInterfaceFactory $transactionParametersFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->transactionParametersFactory = $transactionParametersFactory;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return TransactionParametersInterface
     */
    public function create(array $data = [])
    {
        $transactionParameters = $this->transactionParametersFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $transactionParameters,
            $data,
            TransactionParametersInterface::class
        );
        return $transactionParameters;
    }
}
