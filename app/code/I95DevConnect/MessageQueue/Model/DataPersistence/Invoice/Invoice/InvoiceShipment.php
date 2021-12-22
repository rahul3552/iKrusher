<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Invoice\Invoice;

/**
 * Class for Invoice Shipment
 */
class InvoiceShipment
{
    const SKIPOBS = "i95_observer_skip";
    /**
     *
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    public $shipmentFactory;

    /**
     *
     * @var \Magento\Sales\Model\Order\Email\Sender\ShipmentSender
     */
    public $shipmentSender;
    public $customShipment;
    public $magentoDbTransactionModel;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $baseHelperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $baseHelperData
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
     * @param \I95DevConnect\MessageQueue\Model\SalesShipmentFactory $customShipment
     * @param \Magento\Framework\DB\Transaction $magentoDbTransactionModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $baseHelperData,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \I95DevConnect\MessageQueue\Model\SalesShipmentFactory $customShipment,
        \Magento\Framework\DB\Transaction $magentoDbTransactionModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->baseHelperData = $baseHelperData;
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentSender = $shipmentSender;
        $this->customShipment = $customShipment;
        $this->magentoDbTransactionModel = $magentoDbTransactionModel;
        $this->storeManager = $storeManager;
    }

    /**
     * Checks if shipment exists or not
     *
     * @param type $order
     * @param string $targetInvoiceId
     *
     * @throws \Exception
     */
    public function checkForShipment($order, $targetInvoiceId)
    {
        $shipmentItems = [];
        $shipmentTrackerDetails = [];
        if ((!$order->canInvoice()) && $order->canShip()) {
            foreach ($order->getAllItems() as $orderItem) {
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                $shipmentItems[$orderItem->getId()] = $qtyShipped;
            }

                $shipment = $this->shipmentFactory;
                $shipmentObject = $shipment->create(
                    $order,
                    $shipmentItems,
                    $shipmentTrackerDetails
                );
                $shipmentObject->getOrder()->setCustomerNoteNotify(true);
                $shipmentObject->getOrder()->setIsInProcess(true);
                $shipmentObject->register();
                $transactionSave = $this->magentoDbTransactionModel;
                $transactionSave->addObject($shipmentObject);
                $transactionSave->addObject($shipmentObject->getOrder());
                $this->baseHelperData->unsetGlobalValue(self::SKIPOBS);
                $this->baseHelperData->setGlobalValue(self::SKIPOBS, true);
                $transactionSave->save();
                /**
                 * to send for customer
                 */
                $isEnabled = $this->baseHelperData->getscopeConfig(
                    'i95dev_messagequeue/I95DevConnect_notifications/email_notifications',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    $this->storeManager->getDefaultStoreView()->getWebsiteId()
                );
                $enabled = preg_split('/\,/', $isEnabled);
            if (in_array('shipment', $enabled)) {
                $this->shipmentSender->send($shipmentObject);
            }
                $order->setHistoryEntityName('shipment');
                $order->addStatusHistoryComment(
                    __(
                        'Notified customer about Shipment #%1.',
                        $shipmentObject->getIncrementId()
                    ),
                    false
                )->setIsCustomerNotified(true)->save();
                $customShipmentData = $this->customShipment->create();
                $customShipmentData->setTargetShipmentId($targetInvoiceId);
                $customShipmentData->setSourceShipmentId($shipmentObject->getIncrementId());
                $customShipmentData->setCreatedDt($this->baseHelperData->date->gmtDate());
                $customShipmentData->setUpdatedDt($this->baseHelperData->date->gmtDate());
                $customShipmentData->setupdateBy('ERP');
                $this->baseHelperData->unsetGlobalValue(self::SKIPOBS);
                $this->baseHelperData->setGlobalValue(self::SKIPOBS, true);
                $customShipmentData->save();
                $this->baseHelperData->unsetGlobalValue(self::SKIPOBS);
        }
    }
}
