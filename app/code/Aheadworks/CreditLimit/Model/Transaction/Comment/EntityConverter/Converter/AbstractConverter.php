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
namespace Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Converter;

use Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\ConverterInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionEntityInterfaceFactory;

/**
 * Class Creditmemo
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter\Converter
 */
abstract class AbstractConverter implements ConverterInterface
{
    /**
     * @var TransactionEntityInterfaceFactory
     */
    protected $transactionEntityFactory;

    /**
     * @param TransactionEntityInterfaceFactory $transactionEntityFactory
     */
    public function __construct(
        TransactionEntityInterfaceFactory $transactionEntityFactory
    ) {
        $this->transactionEntityFactory = $transactionEntityFactory;
    }

    /**
     * @inheritdoc
     */
    abstract public function convertToTransactionEntity($object);
}
