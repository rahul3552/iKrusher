<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Shipment\Shipment;

/**
 * Class for shipment creation.
 */
class Create
{
    const SOURCE_SHIPMENT_ID = 'source_shipment_id';
    const TRACKING = 'tracking';
    const TRACKNUMBER = 'trackNumber';

    public $orderObject;
    public $customSalesOrder;
    public $customShipment;
    public $shipmentItems;
    public $targetShipmentId;
    public $sourceShipmentId;
    public $customSalesOrderId;
    public $erpOrderStatus;
    public $baseHelperData;
    public $logger;
    public $eventManager;
    public $shipmentFactory;
    public $trackRepository;
    public $shipmentRepo;
    public $shipmentTrack;
    public $shipmentCmtFactory;
    public $shipmentCmtRepo;
    public $shipmentMgmt;
    public $shipOrder;
    public $shipmentItemObj;
    public $shipTrackCreate;
    public $validate;
    public $abstractDataPersistence;
    public $shipmentObject = null;
    public $validateFields = [
        'targetId'=>'i95dev_shipment_order_shipmentid',
        'targetOrderId'=>'i95dev_empty_targetOrderId',
    ];
    public $stringData = [];
    public $entityCode = '';

    /**
     *
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Helper\Data $baseHelperData
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param \I95DevConnect\MessageQueue\Model\SalesShipmentFactory $customShipment
     * @param \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $erpOrderStatus
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \Magento\Sales\Api\Data\ShipmentInterfaceFactory $shipmentFactory
     * @param \Magento\Sales\Api\ShipmentTrackRepositoryInterfaceFactory $trackRepository
     * @param \Magento\Sales\Api\ShipmentRepositoryInterfaceFactory $shipmentRepo
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory $shipmentTrack
     * @param \Magento\Sales\Api\ShipmentManagementInterfaceFactory $shipmentMgmt
     * @param \Magento\Sales\Api\ShipmentCommentRepositoryInterfaceFactory $shipmentCmtRepo
     * @param \Magento\Sales\Api\Data\ShipmentCommentInterfaceFactory $shipmentCmtFactory
     * @param \Magento\Sales\Api\ShipOrderInterfaceFactory $shipOrder
     * @param \Magento\Sales\Api\Data\ShipmentItemCreationInterfaceFactory $shipmentItemObj
     * @param \Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory $shipTrackCreate
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\ValidateFactory $validate
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Helper\Data $baseHelperData,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        \I95DevConnect\MessageQueue\Model\SalesShipmentFactory $customShipment,
        \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $erpOrderStatus,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \Magento\Sales\Api\Data\ShipmentInterfaceFactory $shipmentFactory,
        \Magento\Sales\Api\ShipmentTrackRepositoryInterfaceFactory $trackRepository,
        \Magento\Sales\Api\ShipmentRepositoryInterfaceFactory $shipmentRepo,
        \Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory $shipmentTrack,
        \Magento\Sales\Api\ShipmentManagementInterfaceFactory $shipmentMgmt,
        \Magento\Sales\Api\ShipmentCommentRepositoryInterfaceFactory $shipmentCmtRepo,
        \Magento\Sales\Api\Data\ShipmentCommentInterfaceFactory $shipmentCmtFactory,
        \Magento\Sales\Api\ShipOrderInterfaceFactory $shipOrder,
        \Magento\Sales\Api\Data\ShipmentItemCreationInterfaceFactory $shipmentItemObj,
        \Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory $shipTrackCreate,
        \I95DevConnect\MessageQueue\Model\DataPersistence\ValidateFactory $validate,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
    ) {
        $this->customShipment = $customShipment;
        $this->customSalesOrder = $customSalesOrder;
        $this->erpOrderStatus = $erpOrderStatus;
        $this->baseHelperData = $baseHelperData;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
        $this->shipmentFactory = $shipmentFactory;
        $this->trackRepository = $trackRepository;
        $this->shipmentRepo = $shipmentRepo;
        $this->shipmentTrack = $shipmentTrack;
        $this->shipmentCmtFactory = $shipmentCmtFactory;
        $this->shipmentCmtRepo = $shipmentCmtRepo;
        $this->shipmentMgmt = $shipmentMgmt;
        $this->shipOrder = $shipOrder;
        $this->shipmentItemObj = $shipmentItemObj;
        $this->shipTrackCreate = $shipTrackCreate;
        $this->validate = $validate;
        $this->abstractDataPersistence = $abstractDataPersistence;
    }

    /**
     * Create Shipment.
     *
     * @param array $stringData
     * @param string $entityCode
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @updatedBy Arushi Bansal
     */
    public function createShipment($stringData, $entityCode)
    {
        $this->stringData = $stringData;
        $this->entityCode = $entityCode;
        try {
            $this->validateData();

            $itemsIds = [];
            $parentItemIds = [];
            foreach ($this->orderObject->getItems() as $itemObject) {
                $itemsIds[$itemObject->getProductId()] = $itemObject->getItemId();
                /** @updatedBy Debashis S. Gopal. Validating shipment item sku irrespective of their case **/
                $productMapArray[strtolower($itemObject->getSku())] = $itemObject->getProductId();
                $parentItem = $itemObject->getParentItem();
                if (isset($parentItem)) {
                    $parentItemIds[$itemObject->getItemId()] = $itemObject->getParentItemId();
                }
            }

            $shipmentItemsData = [];
            $shipmentItemEntity = $this->baseHelperData->getValueFromArray("shipmentItemEntity", $this->stringData);

            foreach ($shipmentItemEntity as $shipmentData) {
                /** @updatedBy Debashis S. Gopal. Validating shipment item sku irrespective of their case **/
                $erpSku = strtolower($shipmentData['orderItemId']);
                $productId = $this->validateShipmentItem($productMapArray, $erpSku, $shipmentData);

                $itemId = $itemsIds[$productId];
                $itemId = $this->setItemId($parentItemIds, $itemId);
                if (isset($shipmentData['qty'])) {
                    if (isset($shipmentItemsData[$itemId]['qty']) && $shipmentItemsData[$itemId]['qty'] > 0) {
                        $shipmentItemsData[$itemId]['qty'] += $shipmentData['qty'];
                    } else {
                        $shipmentItemsData[$itemId]['qty'] = $shipmentData['qty'];
                    }
                } else {
                    $shipmentItemsData[$itemId]['qty'] = 0;
                }
            }

            $this->setShipmentItemsQtyAsZero($shipmentItemsData, $itemsIds);

            $this->shipmentItems = $shipmentItemsData;
            $result = $this->doShipment();

            $aftereventname = 'erpconnect_messagequeuetomagento_aftersave_' . $this->entityCode;
            $this->eventManager->dispatch($aftereventname, ['currentObject' => $this]);

            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                "Record Synced Successfully",
                $result
            );
        } catch (\Exception $ex) {
            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }
    }

    /**
     * @param $parentItemIds
     * @param $itemId
     * @return mixed
     */
    public function setItemId($parentItemIds, $itemId)
    {
        if (isset($parentItemIds[$itemId])) {
            $parentItemId = $parentItemIds[$itemId];
            if ($parentItemId > 0) {
                $itemId = $parentItemId;
            }
        }

        return $itemId;
    }
    /**
     * @param $shipmentItemsData
     * @param $itemsIds
     */
    public function setShipmentItemsQtyAsZero(&$shipmentItemsData, $itemsIds)
    {
        foreach ($itemsIds as $itemId) {
            if (!key_exists($itemId, $shipmentItemsData)) {
                $shipmentItemsData[$itemId]['qty'] = 0;
            }
        }
    }

    /**
     * @param $productMapArray
     * @param $erpSku
     * @param $shipmentData
     * @return mixed
     * @return mixed
     */
    public function validateShipmentItem($productMapArray, $erpSku, $shipmentData)
    {
        if (isset($productMapArray[$erpSku])) {
            return $productMapArray[$erpSku];
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('i95dev_shipment_invalid_item %1', $shipmentData['orderItemId'])
            );
        }
    }

    /**
     * Do shipment of an order
     * @updatedBy Arushi Bansal
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function doShipment()
    {
        $customShipmentCollection = $this->checkShipmentAlreadySync();

        if ($customShipmentCollection->getSize() > 0) {
            // if same shipment reqest come again then update tracking number
            $customShipmentData = $customShipmentCollection->getData();
            $customShipmentData = $customShipmentData[0];
            $source_shipment_id = $customShipmentData[self::SOURCE_SHIPMENT_ID];

            $this->updateTrackingNumber($customShipmentData);

            return $source_shipment_id;
        } else {
            // for new shipment request
            $parentSimpleItems = $this->erpOrderStatus->getParentSimpleItems($this->orderObject);
            $shipQty = $this->prepareShipQty($parentSimpleItems);
            $shippedOrderedQty = $shipQty["shippedOrderedQty"];
            $shippedQty = $shipQty["shippedQty"];

            if ($shippedOrderedQty != $shippedQty) {
                $shipTrack = $this->setShipmentTrackDetails();

                $shipmentItemsDetails = $this->setShipmentItemsDetails();

                $beforeeventname = 'erpconnect_messagequeuetomagento_beforesave_' . $this->entityCode;
                $this->eventManager->dispatch($beforeeventname, ['currentObject' => $this]);

                $this->baseHelperData->unsetGlobalValue('i95_observer_skip');
                $this->baseHelperData->setGlobalValue('i95_observer_skip', true);

                $shipmentResponse = $this->shipOrder->create()->execute(
                    $this->orderObject->getEntityId(),
                    $shipmentItemsDetails,
                    false,
                    false,
                    null,
                    $shipTrack
                );

                if (!is_numeric($shipmentResponse)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__("shipment_not_synced"));
                }

                $this->shipmentObject = $this->getShipment($shipmentResponse);
                return $this->processAfterShipmentResponse($this->shipmentObject);
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_shipment_fullyshipped'));
            }
        }
    }

    /**
     * @param $shipmentObject
     * @return mixed
     * @throws \Exception
     */
    public function processAfterShipmentResponse($shipmentObject)
    {
        if ($shipmentObject) {
            $this->saveCustomI95DevShipment($shipmentObject->getIncrementId(), $this->targetShipmentId);
            $this->erpOrderStatus->updateCustomOrderStatus(
                $this->customSalesOrderId,
                $this->orderObject->getEntityId()
            );

            if ($this->baseHelperData->isEmailNotifyEnable('shipment')
                && $this->sendEmail($shipmentObject->getEntityId())) {
                $this->addComment($shipmentObject);
            }
            return $shipmentObject->getIncrementId();
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('shipment_not_synced'));
        }
    }

    /**
     * @param $parentSimpleItems
     * @return array
     */
    public function prepareShipQty($parentSimpleItems)
    {
        $shippedOrderedQty = 0;
        $shippedQty = 0;
        foreach ($this->orderObject->getItems() as $item) {
            $nonShippedTypes = ['virtual', 'downloadable'];
            if (!in_array($item->getProductType(), $nonShippedTypes)
                && !in_array($item->getItemId(), $parentSimpleItems)) {
                $shippedOrderedQty += $item->getQtyOrdered();
                $shippedQty += $item->getQtyShipped();
            }
        }
        return ['shippedOrderedQty' => $shippedOrderedQty, 'shippedQty' => $shippedQty];
    }

    /**
     * @return array
     */
    public function setShipmentItemsDetails()
    {
        $shipmentItemsData = [];
        foreach ($this->shipmentItems as $shipmentItemKey => $shipmentItemval) {
            $shipItem = $this->shipmentItemObj->create();
            $shipItem->setOrderItemId($shipmentItemKey);
            $shipItem->setQty($shipmentItemval['qty']);
            $shipmentItemsData[] = $shipItem;
        }

        return $shipmentItemsData;
    }
    /**
     * @return array
     */
    public function setShipmentTrackDetails()
    {
        $shipTrackCreate = [];
        $trackingNo = '';
        if (isset($this->stringData[self::TRACKING][0][self::TRACKNUMBER])) {
            $trackingNo = $this->stringData[self::TRACKING][0][self::TRACKNUMBER];
        }
        if (isset($this->stringData[self::TRACKING]) && $this->stringData[self::TRACKING] != ''
            && $trackingNo != '') {
            $trackingDetails = $this->stringData[self::TRACKING];
        } else {
            return $shipTrackCreate;
        }

        foreach ($trackingDetails as $trackingData) {
            $trackNumber = isset($trackingData[self::TRACKNUMBER]) ? $trackingData[self::TRACKNUMBER] : '';
            $data = $this->shipTrackCreate->create();
            if (isset($trackingData['carrier']) && !empty($trackingData['carrier'])) {
                $carrier = $trackingData['carrier'];
            } else {
                $carrier = 'custom';
            }

            $title = (isset($trackingData['title']) && !empty($trackingData['title'])) ? $trackingData['title'] : '';
            $data->setCarrierCode(strtolower($carrier));
            $data->setTitle($title);
            $data->setTrackNumber($trackNumber);
            $shipTrackCreate[] = $data;
        }

        return $shipTrackCreate;
    }

    /**
     * function responsible to save data in custom invoice table
     *
     * @param $shipmentId
     * @param $targetShipmentId
     * @param null $customShipmentObj
     *
     * @throws \Exception
     * @createdBy Arushi Bansal
     */
    public function saveCustomI95DevShipment($shipmentId, $targetShipmentId, $customShipmentObj = null)
    {
        try {
            if (!isset($customShipmentObj)) {
                $customShipmentData = $this->customShipment->create();
            } else {
                $customShipmentData = $customShipmentObj;
            }
            $customShipmentData->setSourceShipmentId($shipmentId);
            $customShipmentData->setTargetShipmentId($targetShipmentId);
            $customShipmentData->setUpdatedDt($this->baseHelperData->date->gmtDate());
            $customShipmentData->setUpdateBy('ERP');
            $customShipmentData->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->create()->createLog(
                __METHOD__,
                $e->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::INFO,
                'info'
            );
        }
    }

    /**
     * Validate shipment Date from ERP
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData()
    {
        $this->validate = $this->validate->create();
        $this->validate->validateFields = $this->validateFields;
        if ($this->validate->validateData($this->stringData)) {
            $this->targetShipmentId = $this->baseHelperData->getValueFromArray("targetId", $this->stringData);

            $targetOrderId = $this->baseHelperData->getValueFromArray("targetOrderId", $this->stringData);
            /*@FIX - CLOUD-551*/
            $customOrderDataCollection = $this->getCustomOrderByTargetId($targetOrderId);
            $this->customSalesOrderId = $customOrderDataCollection->getData()[0]['source_order_id'];
            $this->orderObject = $this->getOrder($this->customSalesOrderId);
            if (!$this->orderObject) {
                throw new \Magento\Framework\Exception\LocalizedException(__("order_not_exist"));
            }

            $shipmentItemEntity = $this->baseHelperData->getValueFromArray("shipmentItemEntity", $this->stringData);
            if (!is_array($shipmentItemEntity) || count($shipmentItemEntity) == 0) {
                throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_shipment_itemNotExist'));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__("validation_error"));
        }
        return true;
    }
    /**
     * @param $customShipmentData
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateTrackingNumber($customShipmentData)
    {
        try {
            /* Code for deleting existing tracking number */
            $shipmentData = $this->shipmentFactory->create()->loadByIncrementId(
                $customShipmentData["source_shipment_id"]
            );
            $shipmentDetails = $shipmentData->getTracks();
            if (!empty($shipmentDetails)) {
                foreach ($shipmentDetails as $shipmentDetail) {
                    $shipmentTrackData = $this->shipmentTrack->create();
                    $shipmentTrackData->load($shipmentDetail['entity_id'])->delete();
                }
            }

            $trackingData = $this->baseHelperData->getValueFromArray(self::TRACKING, $this->stringData);
            if (!empty($trackingData)) {
                foreach ($trackingData as $trackrow) {
                    $this->addTrackingDetails($trackrow, $shipmentData);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__("track_number_unkown_error"));
        }
    }

    /**
     * @param $trackrow
     * @param $shipmentData
     */
    public function addTrackingDetails($trackrow, $shipmentData)
    {
        if (isset($trackrow[self::TRACKNUMBER])) {
            /* Add tracking number to shipment */
            $carrier = (isset($trackrow['carrier']) && !empty($trackrow['carrier'])) ? $trackrow['carrier'] : 'custom';
            $title = (isset($trackrow['title']) && !empty($trackrow['title'])) ? $trackrow['title'] : '';
            $shipmentTrackData = $this->shipmentTrack->create();
            $shipmentTrackData->setOrderId($shipmentData->getOrderId());
            $shipmentTrackData->setParentId($shipmentData->getEntityId());
            $shipmentTrackData->setNumber($trackrow[self::TRACKNUMBER]);
            $shipmentTrackData->setCarrierCode(strtolower($carrier));
            $shipmentTrackData->setTitle($title);

            $this->trackRepository->create()->save($shipmentTrackData);
        }
    }

    /**
     * verify if invoice is already synced
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     * @createdBy Arushi Bansal
     */
    public function checkShipmentAlreadySync()
    {
        $customShipmentData = $this->customShipment->create()->getCollection();

        $customShipmentData->addFieldToSelect(self::SOURCE_SHIPMENT_ID)
            ->addFieldToFilter('target_shipment_id', $this->targetShipmentId);
        $customShipmentData->getSelect()->limit(1);

        return $customShipmentData;
    }

    /**
     * get order details on basis of target id
     *
     * @param string $targetOrderId
     *
     * @return customOrderDataCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Arushi bansal
     */
    public function getCustomOrderByTargetId($targetOrderId)
    {
        $customOrderDataCollection = $this->customSalesOrder->create()->getCollection()
            ->addFieldToSelect('source_order_id')
            ->addFieldToFilter('target_order_id', $targetOrderId)
            ->setOrder('id', 'DESC');
        $customOrderDataCollection->getSelect()->limit(1);

        if ($customOrderDataCollection->getSize() == 0) {
            throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_not_exists"));
        }

        return $customOrderDataCollection;
    }

    /**
     * Fetch order by given order id
     * @param string $orderId
     * @return array|null
     */
    public function getOrder($orderId)
    {
        return $this->erpOrderStatus->getOrderByIncrementId($orderId);
    }

    /**
     * Send email to customer once shipment done
     * @param int $id
     * @return bool
     */
    public function sendEmail($id)
    {
        $email_sent = $this->shipmentMgmt->create()->notify($id);

        if ($email_sent) {
            return $email_sent;
        } else {
            $this->logger->create()->createLog(
                __METHOD__,
                "There was some issue in sending shipment email (Shipment id :-" . $id . ")",
                \I95DevConnect\MessageQueue\Api\LoggerInterface::INFO,
                'info'
            );
            return false;
        }
    }

    /**
     * Fetch shipment by id
     * @param int $id
     * @return array|null
     */
    public function getShipment($id)
    {
        $result = $this->shipmentRepo->create()->get($id);

        if ($result instanceof \Magento\Sales\Api\Data\ShipmentInterface) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Adds comment to given shipment
     *
     * @param $shipmentObject
     *
     * @return array|null
     */
    public function addComment($shipmentObject)
    {
        $shipmentCmt = $this->shipmentCmtFactory->create();
        $shipmentCmt->setComment("Notified customer about Shipment " . $shipmentObject->getIncrementId());
        $shipmentCmt->setIsCustomerNotified(1);
        $shipmentCmt->setIsVisibleOnFront(0);
        $shipmentCmt->setParentId($shipmentObject->getEntityId());
        $shipmentCmt->setEntityId($shipmentObject->getEntityId());
        $result = $this->shipmentCmtRepo->create()->save($shipmentCmt);

        if ($result instanceof \Magento\Sales\Api\Data\ShipmentCommentInterface) {
            return $result;
        } else {
            $id = $shipmentObject->getEntityId();
            $this->logger->create()->createLog(
                __METHOD__,
                "There was some issue in sending shipment comments (Shipment id :-" . $id . ")",
                \I95DevConnect\MessageQueue\Api\LoggerInterface::INFO,
                'info'
            );
            return null;
        }
    }
}
