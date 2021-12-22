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
namespace Aheadworks\CreditLimit\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order as SalesOrder;

/**
 * Class SaleCommand
 *
 * @package Aheadworks\CreditLimit\Gateway\Command
 */
class SaleCommand implements CommandInterface
{
    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @var CreditLimitManagementInterface
     */
    private $creditLimitManagement;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param ConfigInterface $configInterface
     * @param CreditLimitManagementInterface $creditLimitManagement
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ConfigInterface $configInterface,
        CreditLimitManagementInterface $creditLimitManagement,
        SubjectReader $subjectReader
    ) {
        $this->configInterface = $configInterface;
        $this->creditLimitManagement = $creditLimitManagement;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $paymentDataObject = $this->subjectReader->readPayment($commandSubject);
        $payment = $paymentDataObject->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException(__('Order payment is not provided.'));
        }

        /** @var OrderInterface $order */
        $order = $payment->getOrder();
        if ($order->getCustomerId()) {
            $this->creditLimitManagement->spendCreditBalanceOnOrder(
                $order->getCustomerId(),
                $order
            );
        }

        $configuredOrderStatus = $this->configInterface->getValue('order_status');
        if ($configuredOrderStatus != SalesOrder::STATE_PROCESSING) {
            $order->setState(SalesOrder::STATE_NEW);
            $payment->setSkipOrderProcessing(true);
        }
    }
}
