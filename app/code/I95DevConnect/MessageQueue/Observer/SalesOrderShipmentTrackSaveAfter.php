<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Observer class for sales order shipment track after save
 */
class SalesOrderShipmentTrackSaveAfter implements ObserverInterface
{

    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $data;

    /**
     * @var customSalesOrder
     */
    public $customSalesShipment;

    /**
     *
     * @var date
     */
    public $date;

    /**
     * @var salesShipment
     */
    public $salesShipment;

    public $request;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $data
     * @param \I95DevConnect\MessageQueue\Model\SalesShipment $customSalesShipment
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Sales\Model\Order\Shipment $salesShipment
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $data,
        \I95DevConnect\MessageQueue\Model\SalesShipment $customSalesShipment,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Sales\Model\Order\Shipment $salesShipment,
        Http $request
    ) {
        $this->data = $data;
        $this->customSalesShipment = $customSalesShipment;
        $this->date = $date;
        $this->salesShipment = $salesShipment;
        $this->request = $request;
    }

    /**
     * Save i95Dev Custom attributes.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $is_enabled = $this->data->isEnabled();
        if (!$is_enabled) {
            return;
        }
        if ($this->data->getGlobalValue('i95_observer_skip') || $this->request->getParam('isI95DevRestReq') == 'true') {
            return;
        }
        try {
            $shipment = $observer->getEvent()->getDataObject();
            $shipmentId = $shipment->getParentId();
            $sales_shipment = $this->salesShipment->load($shipmentId);
            $incrementId = $sales_shipment->getIncrementId();
            $shipmentModel = $this->customSalesShipment;
            $customShipmentData = $shipmentModel->getCollection()
                    ->addFieldToSelect('id')
                    ->addFieldToFilter('source_shipment_id', $incrementId);
            $customShipmentData->getSelect()->limit(1);
            $customShipmentData = $customShipmentData->getData();
            $customShipmentId = (isset($customShipmentData[0]['id']) ? $customShipmentData[0]['id'] : '');
            if ($customShipmentId !== null) {
                $customShipment = $shipmentModel->load($customShipmentId);
                $customShipment->setUpdatedDt($this->date->gmtDate());
                $customShipment->setUpdateBy('Magento');
                $customShipment->save();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->data->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
    }
}
