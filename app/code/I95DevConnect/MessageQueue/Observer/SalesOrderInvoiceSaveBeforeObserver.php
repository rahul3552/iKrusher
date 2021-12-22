<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @createdBy vinayakrao.shetkar
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class for sales order invoice before save
 */
class SalesOrderInvoiceSaveBeforeObserver implements ObserverInterface
{

    public $baseHelperData;
    private $timezone;

    /**
     * SalesOrderInvoiceSaveBeforeObserver constructor.
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $baseHelperData
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $baseHelperData,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->baseHelperData = $baseHelperData;
        $this->timezone = $timezone;
    }

    /**
     * Save custom invoice
     * @param \Magento\Framework\Event\Observer $observer
     * @return SalesOrderInvoiceSaveBeforeObserver
     * @return SalesOrderInvoiceSaveBeforeObserver
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $erp_invoice_date = $this->baseHelperData->getGlobalValue('invoice_date');

            if ($erp_invoice_date !== '') {
                $invoice = $observer->getEvent()->getInvoice();
                $erp_invoice_date_correct = $this->timezone->convertConfigTimeToUtc($erp_invoice_date);
                $invoice->setCreatedAt($erp_invoice_date_correct);

                return $this;
            }
            $this->baseHelperData->unsetGlobalValue('invoice_date');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__("invoice_not_synced"));
        }
    }
}
