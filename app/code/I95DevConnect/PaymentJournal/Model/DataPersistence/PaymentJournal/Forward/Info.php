<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentJournal
 */

namespace I95DevConnect\PaymentJournal\Model\DataPersistence\PaymentJournal\Forward;

/**
 * Class For Send Payment Journal Info To ERP
 */
class Info
{

    const TARGETINVOICEID = "target_invoice_id";
    const SOURCEINVOICEID = "source_invoice_id";
    const SOURCEORDERID= "source_order_id";
    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    public $eventManager;

    /**
     * @var \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory
     */
    public $paymentJournalFactory;
    
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    public $orderRepository;
    
    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    public $invoiceRepository;
    
    public $fieldMapInfo = [
        'sourcePaymentJournalId' => 'id',
        'targetInvoiceId' => self::TARGETINVOICEID,
        'sourceInvoiceId' => self::SOURCEINVOICEID,
        'sourceOrderId' => self::SOURCEORDERID,
        'invoiceAmount' => 'invoice_amount',
    ];

    /**
     * Info constructor.
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory $paymentJournalFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\PaymentJournal\Model\PaymentJournalFactory $paymentJournalFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->dataHelper = $dataHelper;
        $this->eventManager = $eventManager;
        $this->paymentJournalFactory = $paymentJournalFactory;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Get Payment Journal Info
     * @param type $journalId
     * @return array
     * @author Hrusikesh Manna
     */
    public function getInfo($journalId)
    {
        $data = $this->getpaymentJournalDetails($journalId);
        $this->InfoData = $this->dataHelper->prepareInfoArray($this->fieldMapInfo, $data);
        $event = "erpconnect_forward_paymenyJournalInfo";
        $this->eventManager->dispatch($event, ['paymentJournal' => $this]);
        return $this->InfoData;
    }
    
    /**
     * Get Payment Journal Details By Id
     * @param type $id
     * @return array
     * @author Hrusikesh Manna
     */
    public function getpaymentJournalDetails($id)
    {
        $model = $this->paymentJournalFactory->create();
        $details = $model->load($id)->getData();
        $orderIncrementId = $this->getOrderIncrementId($details[self::SOURCEORDERID]);
        $invoiceDetails = $this->getInvoiceDetails($details[self::SOURCEINVOICEID]);
        return [
            'id'=>$details['id'],
            self::TARGETINVOICEID=>$details[self::TARGETINVOICEID],
            self::SOURCEINVOICEID=>$invoiceDetails->getIncrementId(),
            self::SOURCEORDERID=>$orderIncrementId,
            'invoice_amount'=>$invoiceDetails->getGrandTotal()
        ];
    }
    
    /**
     * Get order increment id
     * @param type $orderId
     * @return integer
     * @author Hrusikesh Manna
     */
    public function getOrderIncrementId($orderId)
    {
        $orderDetails = $this->orderRepository->get($orderId);
        return $orderDetails->getIncrementId();
    }
    
    /**
     * Get invoice details by Id
     * @param type $invoiceId
     * @return object
     * @author Hrusikesh Manna
     */
    public function getInvoiceDetails($invoiceId)
    {
        return $this->invoiceRepository->get($invoiceId);
    }
}
