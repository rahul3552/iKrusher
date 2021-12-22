<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Invoice\Invoice;

/**
 * Class for creating invoice in magento
 */
class Create
{
    const INCREMENT_ID='increment_id';
    public $invoiceNotSynced ='invoice_not_synced';
    const ORDER_ITEM_ID ='order_item_id';
    const TARGETID ='targetId';
    const TARGETORDERID ='targetOrderId';
    const ORDERITEMID ='orderItemId';
    const CURRENTOBJECT='currentObject';
    const ENTITY_ID='entity_id';

    public $invoiceItems;
    public $customSalesOrderId;
    public $erpOrderStatus;
    public $invoiceShipment;
    public $logger;
    public $baseHelperData;
    public $customInvoice;
    public $customSalesOrder;
    public $orderObject;
    public $invoiceCollection;
    public $totalQtyOrdered;
    public $totalQtyInvoiced;
    public $postData = [];
    public $abstractDataPersistence;
    public $eventManager;
    public $invoiceRepository;
    public $invoiceHistory;
    public $invoiceItemHistory;

    public $invoiceMgmt;
    public $invoiceCmtRepo;
    public $invoiceCmtFactory;
    public $invoiceOrder;
    public $validate;
    public $invoiceItemObjFactory;
    public $validateFields = [
        self::TARGETID=>'i95dev_empty_targetInvoiceId',
        self::TARGETORDERID=>'i95dev_empty_targetOrderId',
    ];
    public $stringData;
    public $entityCode;

    public $targetInvoiceId;
    public $invoiceItemEntity = [];
    public $invoiceId;

    protected $connection;
    protected $resource;

    /**
     * @param \I95DevConnect\MessageQueue\Helper\Data $baseHelperData
     * @param \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $customInvoice
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $erpOrderStatus
     * @param InvoiceShipment $invoiceShipment
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Api\InvoiceRepositoryInterfaceFactory $invoiceRepository
     * @param \Magento\Sales\Api\InvoiceManagementInterfaceFactory $invoiceMgmt
     * @param \Magento\Sales\Api\InvoiceCommentRepositoryInterfaceFactory $invoiceCmtRepo
     * @param \Magento\Sales\Api\Data\InvoiceCommentInterfaceFactory $invoiceCmt
     * @param \Magento\Sales\Api\InvoiceOrderInterfaceFactory $invoiceOrder
     * @param \Magento\Sales\Api\Data\InvoiceItemCreationInterfaceFactory $invoiceItemObjFactory
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\ValidateFactory $validate
     * @param \I95DevConnect\MessageQueue\Model\I95DevInvoiceHistoryFactory $invoiceHistory
     * @param \I95DevConnect\MessageQueue\Model\I95DevInvoiceItemHistoryFactory $invoiceItemHistory
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $baseHelperData,
        \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $customInvoice,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $erpOrderStatus,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Invoice\Invoice\InvoiceShipment $invoiceShipment,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Model\AbstractDataPersistence $abstractDataPersistence,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Api\InvoiceRepositoryInterfaceFactory $invoiceRepository,
        \Magento\Sales\Api\InvoiceManagementInterfaceFactory $invoiceMgmt,
        \Magento\Sales\Api\InvoiceCommentRepositoryInterfaceFactory $invoiceCmtRepo,
        \Magento\Sales\Api\Data\InvoiceCommentInterfaceFactory $invoiceCmt,
        \Magento\Sales\Api\InvoiceOrderInterfaceFactory $invoiceOrder,
        \Magento\Sales\Api\Data\InvoiceItemCreationInterfaceFactory $invoiceItemObjFactory,
        \I95DevConnect\MessageQueue\Model\DataPersistence\ValidateFactory $validate,
        \I95DevConnect\MessageQueue\Model\I95DevInvoiceHistoryFactory $invoiceHistory,
        \I95DevConnect\MessageQueue\Model\I95DevInvoiceItemHistoryFactory $invoiceItemHistory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->baseHelperData = $baseHelperData;
        $this->customInvoice = $customInvoice;
        $this->customSalesOrder = $customSalesOrder;
        $this->erpOrderStatus = $erpOrderStatus;
        $this->invoiceShipment=$invoiceShipment;
        $this->logger = $logger;
        $this->abstractDataPersistence = $abstractDataPersistence;
        $this->eventManager = $eventManager;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceMgmt = $invoiceMgmt;
        $this->invoiceCmtRepo = $invoiceCmtRepo;
        $this->invoiceCmtFactory = $invoiceCmt;
        $this->invoiceOrder = $invoiceOrder;
        $this->validate = $validate;
        $this->invoiceItemObjFactory = $invoiceItemObjFactory;
        $this->invoiceHistory = $invoiceHistory;
        $this->invoiceItemHistory = $invoiceItemHistory;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    /**
     * Create Invoice.
     *
     * @param array $stringData
     * @param string $entityCode
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @updatedBy Arushi Bansal
     */
    public function createInvoice($stringData, $entityCode)
    {
        $this->stringData = $stringData;
        $this->entityCode = $entityCode;

        try {
            $this->validateData();
            $prepareData = $this->preparePostData();
            if (is_array($prepareData)) {
                $this->invoiceItems = $prepareData;
                $response = $this->doInvoice($entityCode);
                // Updated by Sravani Polu for mapping target invoice id for existing invoice in magento.
            } elseif (is_numeric($prepareData)) {
                $response = $prepareData;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__($this->invoiceNotSynced));
            }

            return $this->abstractDataPersistence->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                "Record Synced Successfully",
                $response
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
     * prepare invoice item data array
     * @return type
     * @createdBy Arushi Bansal
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function preparePostData()
    {
        $itemsIds = [];

        $this->invoiceCollection = $this->getInvoices($this->orderObject->getEntityId());

        $orderItems = $this->orderObject->getItems();
        $productTypeMapArray = [];
        foreach ($orderItems as $item) {
            if ($item->getProductType() != 'configurable') {
                $productTypeMapArray[$item->getId()] = $item->getProductType();
            }
        }

        $this->totalQtyInvoiced = 0;
        $invoiceCount = $this->setTotalQtyInvoiced($productTypeMapArray);

        $this->totalQtyOrdered  = $this->orderObject->getTotalQtyOrdered();
        if ($invoiceCount > 0 && $this->totalQtyInvoiced >= $this->totalQtyOrdered) {
            return $this->invoiceExists();
        }

        $parentItemIds = [];
        $productMapArray = [];
        foreach ($this->orderObject->getItems() as $item) {
            $itemsIds[$item->getProductId()] = $item->getItemId();
            /** @updatedBy Debashis S. Gopal. Validating shipment item sku irrespective of their case **/
            $productMapArray[strtolower($item->getSku())] = $item->getProductId();

            $product_id = $item->getParentItem();
            if (isset($product_id)) {
                $parentItemIds[$item->getItemId()] = $item->getParentItemId();
            }
        }

        $invoiceItemsArr = $this->prepareInvoiceItemsArr($itemsIds, $parentItemIds, $productMapArray);

        return $this->prepareItemArray($invoiceItemsArr);
    }

    /**
     * @param $productTypeMapArray
     * @return int|void
     * @return int|void
     */
    public function setTotalQtyInvoiced($productTypeMapArray)
    {
        if (!$this->invoiceCollection) {
            $invoiceCount = 0;
        } else {
            $invoiceCount = count($this->invoiceCollection);
            $invoice = $this->invoiceCollection->getItems();

            foreach ($invoice as $itemsInInvoice) {
                $itemsInInvoice = $itemsInInvoice->getItems();
                foreach ($itemsInInvoice as $invoiceItemsArr) {
                    if (isset($productTypeMapArray[$invoiceItemsArr->getOrderItemId()])) {
                        $this->totalQtyInvoiced = $this->totalQtyInvoiced + $invoiceItemsArr->getQty();
                    }
                }
            }
        }

        return $invoiceCount;
    }

    /**
     * @param $itemsIds
     * @param $parentItemIds
     * @param $productMapArray
     * @return mixed
     */
    public function prepareInvoiceItemsArr($itemsIds, $parentItemIds, $productMapArray)
    {
        $invoiceItemsArr = $this->prepareInvoiceItemByERPString(
            $this->invoiceItemEntity,
            $itemsIds,
            $parentItemIds,
            $productMapArray
        );
        foreach ($itemsIds as $itemId) {
            if (!key_exists($itemId, $invoiceItemsArr)) {
                $invoiceItemsArr[$itemId][self::ORDER_ITEM_ID] = $itemId;
                $invoiceItemsArr[$itemId]['qty'] = 0;
            }
        }

        return $invoiceItemsArr;
    }

    /**
     * prepare invoice items data on basis of erp data
     *
     * @param array $invoiceItemEntity
     * @param array $itemsIds
     * @param array $parentItemIds
     * @param $mapArr
     * @return array
     * @createdBy Arushi Bansal
     */
    public function prepareInvoiceItemByERPString($invoiceItemEntity, $itemsIds, $parentItemIds, $mapArr)
    {
        $productMapArray = $mapArr;
        $invoiceItemsArr = [];
        foreach ($invoiceItemEntity as $invoiceData) {
            $this->emptyFieldCheck(self::ORDERITEMID, $invoiceData, "i95dev_empty_invoiceOrderItemId");
            $this->emptyFieldCheck("qty", $invoiceData, "i95dev_empty_qtyToInvoice");

            /** @updatedBy Debashis S. Gopal. Validating shipment item sku irrespective of their case **/
            $erpSku = strtolower($invoiceData[self::ORDERITEMID]);
            if (isset($productMapArray[$erpSku])) {
                $productId = $productMapArray[$erpSku];
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('i95dev_invalid_invoice_item %1', $invoiceData[self::ORDERITEMID])
                );
            }

            $itemId = $itemsIds[$productId];
            if (isset($parentItemIds[$itemId])) {
                $parentItemId = $parentItemIds[$itemId];
                if ($parentItemId > 0) {
                    $itemId = $parentItemId;
                }
            }

            $invoiceItemsArr[$itemId][self::ORDER_ITEM_ID] = $itemId;
            // @updatedBy Subhan.  #79 cognitive complexity issue
            $this->prepareInvoiceItemArr($invoiceData, $invoiceItemsArr, $itemId);
        }

        return $invoiceItemsArr;
    }

    /**
     * @param $invoiceData
     * @param $invoiceItemsArr
     * @param $itemId
     * @return mixed
     */
    public function prepareInvoiceItemArr($invoiceData, &$invoiceItemsArr, $itemId)
    {
        if (isset($invoiceData['qty'])) {
            if (isset($invoiceItemsArr[$itemId]['qty']) && $invoiceItemsArr[$itemId]['qty'] > 0) {
                $invoiceItemsArr[$itemId]['qty'] += $invoiceData['qty'];
            } else {
                $invoiceItemsArr[$itemId]['qty'] = $invoiceData['qty'];
            }
        } else {
            $invoiceItemsArr[$itemId]['qty'] = 0;
        }

        return $invoiceItemsArr;
    }

    /**
     * check for internal field if they exists
     * @param string $fieldname
     * @param string $dataString
     * @param string $errormsg
     * @throws \Magento\Framework\Exception\LocalizedException
     * @createdBy Arushi Bansal
     */
    public function emptyFieldCheck($fieldname, $dataString, $errormsg)
    {

        if (!isset($dataString[$fieldname]) || $dataString[$fieldname] === "") {
            throw new \Magento\Framework\Exception\LocalizedException(__($errormsg));
        }
    }

    /**
     * prepare item array
     * @param array $invoiceItems
     * @return array
     * @createdBy Arushi Bansal
     */
    public function prepareItemArray($invoiceItems)
    {
        $invoiceItemObj = [];
        foreach ($invoiceItems as $items) {
            $item = $this->invoiceItemObjFactory->create();
            $item->setOrderItemId($items[self::ORDER_ITEM_ID]);
            $item->setQty($items['qty']);

            $invoiceItemObj[] = $item;
        }

        return $invoiceItemObj;
    }

    /**
     * method to do invoice for an existing order
     *
     * @param string $entityCode
     * @return \I95DevConnect\MessageQueue\Api\I95DevResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException\Exception|\Exception
     * @updatedBy Arushi Bansal
     */
    public function doInvoice($entityCode)
    {
        $component = $this->baseHelperData->getComponent();

        $beforeeventname = 'erpconnect_messagequeuetomagento_beforesave_' . $entityCode;
        $this->eventManager->dispatch($beforeeventname, [self::CURRENTOBJECT => $this]);

        $isCaptureAllowed = $this->baseHelperData->isCaptureInvoiceEnabled();
        $capture = (bool)$isCaptureAllowed;

        $this->baseHelperData->unsetGlobalValue('i95_observer_skip');
        $this->baseHelperData->setGlobalValue('i95_observer_skip', true);

        /** @updatedBy kavya.k. if created date comes from erp then set created date **/
        $invoiceCreatedDate = isset($this->stringData['createdDate']) ? $this->stringData['createdDate'] : '' ;

        $this->baseHelperData->unsetGlobalValue('invoice_date');
        $this->baseHelperData->setGlobalValue('invoice_date', $invoiceCreatedDate);

        $result = $this->invoiceOrder->create()->execute(
            $this->orderObject->getEntityId(),
            $capture,
            $this->invoiceItems,
            false,
            false
        );

        if (!is_numeric($result)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($this->invoiceNotSynced));
        }
        $invoiceData = $this->getInvoice($result);

        if ($invoiceData) {
            $invoicedQty = 0;

            foreach ($this->invoiceItemEntity as $invoiceItem) {
                $invoicedQty += $invoiceItem['qty'];
            }

            $this->saveCustomI95DevInvoice(
                $invoiceData->getIncrementId(),
                $invoicedQty,
                $this->targetInvoiceId,
                null
            );
            $this->saveI95DevInvoiceHistory();

            $this->erpOrderStatus->updateCustomOrderStatus(
                $this->customSalesOrderId,
                $this->orderObject->getEntityId()
            );

            if ($component == 'AX') {
                $this->invoiceShipment->checkForShipment($this->orderObject, $this->targetInvoiceId);
            }

            if ($this->baseHelperData->isEmailNotifyEnable('invoice') &&
                $this->sendEmail($invoiceData->getEntityId())) {
                $this->addComment($invoiceData);
            }

            // @Hrusikesh assigned invoice id to current object
            $this->invoiceId = $invoiceData->getId();

            $aftereventname = 'erpconnect_messagequeuetomagento_aftersave_' . $entityCode;
            $this->eventManager->dispatch($aftereventname, [self::CURRENTOBJECT => $this]);

            return $invoiceData->getIncrementId();
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__($this->invoiceNotSynced));
        }
    }

    /**
     * Validate invoice Data.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Arushi Bansal
     */
    public function validateData()
    {
        $this->validate = $this->validate->create();
        $this->validate->validateFields = $this->validateFields;
        if ($this->validate->validateData($this->stringData)) {
            $this->targetInvoiceId = $this->baseHelperData->getValueFromArray(self::TARGETID, $this->stringData);

            $customInvoiceObj = $this->customInvoice->create();
            $customInvoiceCollection = $customInvoiceObj->getCollection()
                ->addFieldToFilter('target_invoice_id', $this->targetInvoiceId);

            if ($customInvoiceCollection->getSize() > 0) {
                throw new \Magento\Framework\Exception\LocalizedException(__("invoice_already_sync"));
            }

            $targetOrderId = $this->baseHelperData->getValueFromArray(self::TARGETORDERID, $this->stringData);
            /*@FIX - CLOUD-551*/
            $customOrderDataCollection = $this->getCustomOrderByTargetId($targetOrderId);
            $this->customSalesOrderId = $customOrderDataCollection->getData()[0]['source_order_id'];
            $this->orderObject = $this->getOrder($this->customSalesOrderId);

            if (!$this->orderObject) {
                throw new \Magento\Framework\Exception\LocalizedException(__("order_not_exist"));
            }

            $this->invoiceItemEntity = $this->baseHelperData->getValueFromArray("invoiceItemEntity", $this->stringData);
            if ($this->invoiceItemEntity == "") {
                throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_invoice_itemNotExist'));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__("validation_error"));
        }
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
     * Check if invoice exists for an order or not
     *
     * @return string
     * @throws \Exception
     * @updatedBy Arushi Bansal
     */
    public function invoiceExists()
    {
        $invoice = $this->invoiceCollection->getData();
        $invoice_id = $invoice[0][self::INCREMENT_ID];
        $invoicedQty = 0;

        foreach ($this->invoiceItemEntity as $invoiceItem) {
            $invoicedQty += $invoiceItem['qty'];
        }

        $customInvoiceData = $this->customInvoice->create()
            ->load($invoice_id, 'source_invoice_id')->getData();
        if (!empty($customInvoiceData)) {
            $customInvoicId = $customInvoiceData['id'];
            $custInvoice = $this->customInvoice->create()->load($customInvoicId);
            $targetId = $custInvoice->getTargetInvoiceId() ? $custInvoice->getTargetInvoiceId() . ',' : "";
            $targetId = $targetId . $this->baseHelperData->getValueFromArray(
                self::TARGETID,
                $this->stringData
            );
            $invoicedQty += (is_numeric($custInvoice->getTargetInvoicedQty()))?$custInvoice->getTargetInvoicedQty():0;
            $customInvoiceObj = $custInvoice;
        } else {
            $targetId = $this->baseHelperData->getValueFromArray(
                self::TARGETID,
                $this->stringData
            );
            $customInvoiceObj = null;
        }

        //@Hrusikesh added before save event for custom invoice
        $this->invoiceId = $invoice[0][self::ENTITY_ID];
        $this->targetInvoiceId = $targetId;
        $beforeEvent = 'erpconnect_custominvoice_beforesave';
        $this->eventManager->dispatch($beforeEvent, [self::CURRENTOBJECT => $this]);
        $this->saveCustomI95DevInvoice($invoice_id, $invoicedQty, $targetId, $customInvoiceObj);
        $this->saveI95DevInvoiceHistory();
        $this->erpOrderStatus->updateCustomOrderStatus($this->customSalesOrderId, $this->orderObject->getEntityId());
        //@Hrusikesh added after save event for custom invoice
        $afterEvent = 'erpconnect_custominvoice_aftersave';
        $this->eventManager->dispatch($afterEvent, [self::CURRENTOBJECT => $this]);

        return $invoice_id;
    }

    /**
     * function responsible to save data in custom invoice table
     *
     * @param int $invoiceId
     * @param int $invoicedQty
     * @param int $targetInvoiceId
     * @param Object $customInvoiceObj
     *
     * @throws \Exception
     * @createdBy Arushi Bansal
     */
    public function saveCustomI95DevInvoice($invoiceId, $invoicedQty, $targetInvoiceId, $customInvoiceObj = null)
    {
        try {
            if (!isset($customInvoiceObj)) {
                $customInvoiceData = $this->customInvoice->create();
            } else {
                $customInvoiceData = $customInvoiceObj;
            }
            $customInvoiceData->setSourceInvoiceId($invoiceId);
            $customInvoiceData->setTargetInvoicedQty($invoicedQty);
            $customInvoiceData->setTargetInvoiceId($targetInvoiceId);
            $customInvoiceData->setUpdatedDt($this->baseHelperData->date->gmtDate());
            $customInvoiceData->setUpdateBy('ERP');
            $customInvoiceData->save();
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
     * Get order data by order id
     *
     * @param string $orderId
     * @return array
     * @updatedBy Arushi Bansal
     */
    public function getOrder($orderId)
    {
        return $this->erpOrderStatus->getOrderByIncrementId($orderId);
    }

    /**
     * Get Invoices of  given order id
     * @param string $orderId
     * @return \Magento\Sales\Api\Data\InvoiceSearchResultInterface|null
     * @updatedBy Arushi Bansal
     */
    public function getInvoices($orderId)
    {
        return $this->erpOrderStatus->getInvoices($orderId);
    }

    /**
     * Get Invoice by invoice id
     *
     * @param string $id
     *
     * @return array
     * @updatedBy Arushi Bansal
     */
    public function getInvoice($id)
    {
        $result = $this->invoiceRepository->create()->get($id);

        if ($result instanceof \Magento\Sales\Api\Data\InvoiceInterface) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Send invoice mail to customer
     *
     * @param string $id
     * @return string|null
     * @updatedBy Arushi Bansal
     */
    public function sendEmail($id)
    {
        $email_sent = $this->invoiceMgmt->create()->notify($id);

        if ($email_sent) {
            return $email_sent;
        } else {
            $this->logger->create()->createLog(
                __METHOD__,
                "There was some issue in sending invoice email (Invoice id :-"  . $id . ")",
                \I95DevConnect\MessageQueue\Api\LoggerInterface::INFO,
                'info'
            );
            return false;
        }
    }

    /**
     * Add comments to the given invoice
     *
     * @param $invoiceData
     *
     * @return string|null
     * @updatedBy Arushi Bansal
     */
    public function addComment($invoiceData)
    {

        $invoiceCmt = $this->invoiceCmtFactory->create();
        $invoiceCmt->setComment("Notified customer about invoice " . $invoiceData[self::INCREMENT_ID]);
        $invoiceCmt->setIsCustomerNotified(1);
        $invoiceCmt->setIsVisibleOnFront(0);
        $invoiceCmt->setParentId($invoiceData[self::ENTITY_ID]);
        $invoiceCmt->setEntityId($invoiceData[self::ENTITY_ID]);

        $result = $this->invoiceCmtRepo->create()->save($invoiceCmt);

        if ($result instanceof \Magento\Sales\Api\Data\InvoiceCommentInterface) {
            return $result;
        } else {
            $id = $invoiceData[self::INCREMENT_ID];
            $this->logger->create()->createLog(
                __METHOD__,
                "There was some issue in sending invoice comments (Invoice id :-"  . $id . ")",
                \I95DevConnect\MessageQueue\Api\LoggerInterface::INFO,
                'info'
            );
            return null;
        }
    }

    /**
     * @addedBy Subhan. To store invoice history and its items
     */
    public function saveI95DevInvoiceHistory()
    {
        $targtInvoiceId = $this->baseHelperData->getValueFromArray(
            self::TARGETID,
            $this->stringData
        );
        $targtOrderId = $this->baseHelperData->getValueFromArray(
            self::TARGETORDERID,
            $this->stringData
        );
        try {
            $invoiceHstry = $this->invoiceHistory->create();
            $invoiceHstry->setTargetInvoiceId($targtInvoiceId);
            $invoiceHstry->setTargetOrderId($targtOrderId);
            $invoiceHstry->save();

            $invoiceEntityId = $invoiceHstry->getId();

            foreach ($this->invoiceItemEntity as $invoiceItem) {
                $data[] = [
                    'invoice_entity_id' => $invoiceEntityId,
                    'item_sku' => $invoiceItem['orderItemId'],
                    'item_qty' => $invoiceItem['qty']
                ];
            }

            $invoiceItemsTable = $this->resource->getTableName('i95dev_sales_invoice_item_history');
            $this->connection->insertMultiple($invoiceItemsTable, $data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->create()->createLog(
                __METHOD__,
                $e->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::INFO,
                'info'
            );
        }
    }
}
