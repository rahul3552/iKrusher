<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Model\DataPersistence\PaymentJournal\Forward;

/**
 * Class For Set Payment Journal Response From ERP
 */
class Response
{

    /**
     * @var \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory
     */
    public $paymentJournalFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;

    /**
     * @var \I95DevConnect\MessageQueue\Model\AbstractDataPersistence
     */
    public $abstractDataPersistence;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    public $eventManager;

    /**
     * Response constructor.
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory $paymentJournalFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     * @param \Magento\Framework\Event\Manager $eventManager
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory $paymentJournalFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence,
        \Magento\Framework\Event\Manager $eventManager
    ) {
        $this->logger = $logger;
        $this->paymentJournalFactory = $paymentJournalFactory;
        $this->date = $date;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->eventManager = $eventManager;
    }

    /**
     * Set receipt Id in Payment Journal
     * @param $requestString
     * @param $entityCode
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @author Hrusikesh Manna
     */
    public function getResponse($requestString, $entityCode)
    {
        try {
            $paymentJournal = $this->paymentJournalFactory->create()->load($requestString['sourceId']);
            $paymentJournal->setReceiptId($requestString['targetId']);
            $paymentJournal->setUpdatedDt($this->date->gmtDate());
            $paymentJournal->save();
            $paymentJournalResponseEvent = "erpconnect_forward_paymentjournalresponse";
            $this->eventManager->dispatch($paymentJournalResponseEvent, ['currentObject' => $requestString]);
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                __("Response send successfully"),
                $paymentJournal
            );
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __("Some error occured in response sync -- ".$entityCode),
                null
            );
        }
    }
}
