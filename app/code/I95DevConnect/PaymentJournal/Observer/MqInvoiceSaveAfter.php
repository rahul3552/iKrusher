<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Mq to Magento invoice save after observer
 */
class MqInvoiceSaveAfter implements ObserverInterface
{

    /**
     * @var \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory
     */
    public $paymentJournal;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    public $eventManager;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $baseHelperData;

    /**
     * MqInvoiceSaveAfter constructor.
     * @param \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory $paymentJournal
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \I95DevConnect\MessageQueue\Helper\Data $baseHelperData
     */
    public function __construct(
        \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory $paymentJournal,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \I95DevConnect\MessageQueue\Helper\Data $baseHelperData
    ) {
        $this->paymentJournal = $paymentJournal;
        $this->date = $date;
        $this->eventManager = $eventManager;
        $this->orderRepository = $orderRepository;
        $this->baseHelperData = $baseHelperData;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $currentObject = $observer->getEvent()->getData("currentObject");
            $orderId  = $currentObject->orderObject->getEntityId();
            $orderDetails = $this->orderRepository->get($orderId);
            $isOffline = $orderDetails->getPayment()->getMethodInstance()->isOffline();
            if ($isOffline) {
                return;
            }
            $this->baseHelperData->unsetGlobalValue('i95_observer_skip');
            $this->baseHelperData->setGlobalValue('i95_observer_skip', false);
            $paymentJournalFactory = $this->paymentJournal->create();
            $paymentJournalFactory->setSourceInvoiceId($currentObject->invoiceId);
            $paymentJournalFactory->setTargetInvoiceId($currentObject->targetInvoiceId);
            $paymentJournalFactory->setSourceOrderId($currentObject->orderObject->getId());
            $paymentJournalFactory->setCreatedDt($this->date->gmtDate());
            $paymentJournalFactory->setUpdatedDt($this->date->gmtDate());
            $paymentJournalFactory->save();
            $data = $this->paymentJournal->create()->load($paymentJournalFactory->getId());
            // Dispatch after save event
            $aftereventname = 'payment_journal_save_after';
            $this->eventManager->dispatch($aftereventname, ['data_object' => $data]);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
