<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_Cancelorder
 */

namespace I95DevConnect\CancelOrder\Test\Integration\Cancelorder;

/**
 * Class for cancel order integration testcase
 */
class CancelOrderReverseTest extends \PHPUnit\Framework\TestCase
{

    const ERROR_ID='error_id';
    const STATUS='status';
    const CCOL='createCancelOrderList';
    const REF_NAME='ref_name';
    const ISSUE_MSG="Issue came in saving cancel order to messagequeue";
    const DATAPATH="/Json/CancelOrderPullData.json";

    public $customer;
    public $customerAddress;
    public $productFactory;
    public $quote;
    public $quoteManagement;
    public $quoteItemModel;
    public $quoteAddressInterface;
    public $orderModel;
    public $shipmentModel;
    public $invoiceModel;
    public $transactionFactory;
    public $stockRegistry;
    public $customSalesOrder;
    public $i95devServerRepo;
    public $erpMessageQueue;
    public $errorUpdateData;

    /**
     * @author Hrusikesh Manna
     */
    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->customer = $objectManager->create(
            \Magento\Customer\Model\Customer::class
        );
        $this->customerAddress = $objectManager->create(
            \Magento\Customer\Model\Address::class
        );
        $this->productFactory = $objectManager->create(
            \Magento\Catalog\Model\ProductFactory::class
        );
        $this->quote = $objectManager->create(
            \Magento\Quote\Model\QuoteFactory::class
        );
        $this->quoteManagement = $objectManager->create(
            \Magento\Quote\Api\CartManagementInterface::class
        );
        $this->quoteItemModel = $objectManager->create(
            \Magento\Quote\Model\Quote\ItemFactory::class
        );
        $this->quoteAddressInterface = $objectManager->create(
            \Magento\Quote\Api\Data\AddressInterface::class
        );
        $this->orderModel = $objectManager->create(
            \Magento\Sales\Model\Order::class
        );
        $this->shipmentModel = $objectManager->create(
            \Magento\Sales\Model\Convert\Order::class
        );
        $this->invoiceModel = $objectManager->create(
            \Magento\Sales\Model\Service\InvoiceService::class
        );
        $this->transactionFactory = $objectManager->create(
            \Magento\Framework\DB\TransactionFactory::class
        );
        $this->stockRegistry = $objectManager->create(
            \Magento\CatalogInventory\Api\StockRegistryInterface::class
        );
        $this->customSalesOrder = $objectManager->create(
            \I95DevConnect\MessageQueue\Model\SalesOrderFactory::class
        );
        $this->i95devServerRepo = $objectManager->create(
            \I95DevConnect\I95DevServer\Model\I95DevServerRepository::class
        );
        $this->erpMessageQueue = $objectManager->create(
            \I95DevConnect\MessageQueue\Model\I95DevErpMQRepository::class
        );
        $this->errorUpdateData = $objectManager->create(
            \I95DevConnect\MessageQueue\Model\ErrorUpdateData::class
        );
        $this->dummyData = $objectManager->create(
            \I95DevConnect\I95DevServer\Test\Integration\DummyData::class
        );
    }

    /**
     * Cancel Order Pre requisite Data
     *
     * @return bool
     */
    public function cancelOrderPrerequistiesData()
    {
        $this->dummyData->createCustomer();
        $this->dummyData->createSingleSimpleProduct('1001333');
        $order = $this->dummyData->createSingleOrder('2225111');
        $this->orderId = $order->getId();
        return true;
    }

    /**
     * Create record in message queue
     *
     * @param  $JsonData
     * @return object
     */
    public function createRecord($JsonData)
    {
        $this->i95devServerRepo->serviceMethod(self::CCOL, $JsonData);
        return $this->erpMessageQueue->getCollection()
            ->addFieldToFilter(self::REF_NAME, 'C00011')
            ->getData();
    }

    /**
     * get inbound message queue collection by ref name
     *
     * @return Object
     * @author Hrusikesh Manna
     */
    public function getInboundMqData()
    {
        return $this->erpMessageQueue->getCollection()
            ->addFieldToFilter(self::REF_NAME, 'C00011')
            ->getData();
    }

    /**
     * Process test case
     *
     * @param  $fileName
     * @return Object
     */
    public function processTestCase($fileName)
    {
        $JsonData = $this->readJsonData($fileName);
        $this->cancelOrderPrerequistiesData();
        $response = $this->createRecord($JsonData);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            self::ISSUE_MSG
        );
        $this->i95devServerRepo->syncMQtoMagento();
        return $this->getInboundMqData();
    }

    /**
     * @magentoDbIsolation   enabled
     * Test case if module disabled
     * @magentoConfigFixture current_website i95devconnect_CancelOrder/cancelorder_enabled_settings/enable_cancelorder 0
     * @author               Hrusikesh Manna
     */
    public function testIfModuleDisabled()
    {
        $file = self::DATAPATH;
        $collection = $this->processTestCase($file);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::ERROR,
            $collection[0][self::STATUS],
            'Test case fail to check if Extension is disabled'
        );
    }

    /**
     * @magentoDbIsolation   enabled
     * @magentoConfigFixture current_website i95devconnect_CancelOrder/cancelorder_enabled_settings/enable_cancelorder 1
     * @author               Hrusieksh Manna
     */
    public function testReverseCancelorder()
    {
        $file = self::DATAPATH;
        $collection = $this->processTestCase($file);
        $errorMsg = $this->getErrorData($collection[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $collection[0][self::STATUS],
            $errorMsg
        );
    }

    /**
     * @magentoDbIsolation   enabled
     * @magentoConfigFixture current_website i95devconnect_CancelOrder/cancelorder_enabled_settings/enable_cancelorder 1
     * Test cancel order with invalid target order id
     * @author               Hrusieksh Manna
     */
    public function testCancelOrderWithInvalidTargetOrderId()
    {
        $file = "/Json/CancelOrderWithInvalidTargetOrderId.json";
        $collection = $this->processTestCase($file);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::ERROR,
            $collection[0][self::STATUS]
        );
        $errorMsg = $this->getErrorData($collection[0][self::ERROR_ID]);
        $this->assertSame("order not found", $errorMsg);
    }

    /**
     * Test case if order is closed
     *
     * @magentoDbIsolation   enabled
     * @magentoConfigFixture current_website i95devconnect_CancelOrder/cancelorder_enabled_settings/enable_cancelorder 1
     * @author               Hrusikesh Manna
     */
    public function testCancelorderForClosedOrder()
    {
        $this->cancelOrderGenericCode();
        $JsonData = $this->readJsonData(self::DATAPATH);

        $this->i95devServerRepo->serviceMethod(self::CCOL, $JsonData);
        $this->i95devServerRepo->syncMQtoMagento();
        $mqData = $this->getInboundMqData();
        $errorMsg = $this->getErrorData($mqData[1][self::ERROR_ID]);
        $this->assertSame("Order already closed.", $errorMsg);
    }

    /**
     * @magentoDbIsolation   enabled
     * @magentoConfigFixture current_website i95devconnect_CancelOrder/cancelorder_enabled_settings/enable_cancelorder 1
     * Test Partial cancel order
     * @author               Hrusikesh Manna
     */
    public function testPartailCancelOrder()
    {
        $this->cancelOrderGenericCode();
    }

    /**
     * function for common code for test case
     */
    public function cancelOrderGenericCode()
    {

        $JsonData = $this->readJsonData(self::DATAPATH);
        $this->cancelOrderPrerequistiesData();
        $shipment = $this->doPartialShipment();
        $errorMsg = $this->getErrorData($shipment[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $shipment[0][self::STATUS],
            $errorMsg
        );
        $invoice = $this->doPartialInvoice();
        $invErrorMsg = $this->getErrorData($invoice[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $invoice[0][self::STATUS],
            $invErrorMsg
        );

        $response = $this->createRecord($JsonData);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            self::ISSUE_MSG
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData();
        $this->getErrorData($collection[0][self::ERROR_ID]);
        $order = $this->orderModel->load($this->orderId);
        $this->assertEquals(
            'closed',
            $order->getStatus(),
            "Order status should closed"
        );
        $orderItems = $order->getAllItems();
        foreach ($orderItems as $item) {
            $itemQty = $item->getQtyCanceled();
        }
        $this->assertEquals(
            1,
            $itemQty,
            "Canceled item quantity shows wrong"
        );
    }
    /**
     * Cancel order test case for complete order
     *
     * @magentoDbIsolation   enabled
     * @magentoConfigFixture current_website i95devconnect_CancelOrder/cancelorder_enabled_settings/enable_cancelorder 1
     * @author               Hrusikesh Manna
     */
    public function testCancelOrderForCompleteOrder()
    {
        $this->cancelOrderPrerequistiesData();
        $this->createShipment();
        $this->createInvoice();
        $collection = $this->createCancelOrderList(self::DATAPATH);
        $errorMsg = $this->getErrorData($collection[0][self::ERROR_ID]);
        $this->assertSame("Completed order cannot be canceled.", $errorMsg);
    }

    /**
     * Create partial shipment
     *
     * @author Hrusikesh Manna
     */
    public function doPartialShipment()
    {
        $path = realpath(dirname(__FILE__)) . "/Json/Partialshipment.json";
        $shipmentJsonData = file_get_contents($path);
        $this->i95devServerRepo->serviceMethod("createShipmentList", $shipmentJsonData);
        $this->i95devServerRepo->syncMQtoMagento();
        return $this->erpMessageQueue->getCollection()
            ->addFieldToFilter(self::REF_NAME, '1022336')
            ->getData();
    }

    /**
     * Partial invoice for order
     *
     * @author Hrusieksh Manna
     */
    public function doPartialInvoice()
    {
        $path = realpath(dirname(__FILE__)) . "/Json/Partialinvoice.json";
        $invoiceJsonData = file_get_contents($path);
        $this->i95devServerRepo->serviceMethod("createInvoiceList", $invoiceJsonData);
        $this->i95devServerRepo->syncMQtoMagento();
        return $this->erpMessageQueue->getCollection()
            ->addFieldToFilter(self::REF_NAME, '1031104')
            ->getData();
    }

    /**
     * Create shipment
     *
     * @author Hrusikesh Manna
     */
    public function createShipment()
    {
        $order = $this->orderModel->load($this->orderId);
        $shipment = $this->shipmentModel->toShipment($order);
        foreach ($order->getAllItems() as $orderItem) {
            $qtyShipped = $orderItem->getQtyToShip();
            $shipmentItem = $this->shipmentModel->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            $shipment->addItem($shipmentItem);
        }
        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        $shipment->save();
        $shipment->getOrder()->save();
    }

    /**
     * Create Invoice
     *
     * @author Hrusikesh Manna
     */
    public function createInvoice()
    {
        $order = $this->orderModel->load($this->orderId);
        $invoice = $this->invoiceModel->prepareInvoice($order);
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(false);
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
        $transactionSave->save();
    }

    /**
     * get error message
     *
     * @param  int $error_id
     * @return string
     * @author Hrusikesh Manna
     */
    public function getErrorData($error_id)
    {
        if ($error_id > 0) {
            $errorMsg = $this->errorUpdateData->getCollection()
                ->addFieldToFilter('id', $error_id)
                ->getData();
            $message = $errorMsg[0]['msg'];
        } else {
            $message =  null;
        }
        return $message;
    }

    /**
     * @param  $fileName
     * @return Object
     */
    public function createCancelOrderList($fileName)
    {
        $JsonData = $this->readJsonData($fileName);
        $this->i95devServerRepo->serviceMethod(self::CCOL, $JsonData);
        $this->i95devServerRepo->syncMQtoMagento();
        return $this->getInboundMqData();
    }

    /**
     * read content from file
     *
     * @param  type $fileName
     * @return text
     * @author Hrusikesh Manna
     */
    public function readJsonData($fileName)
    {
        $path = realpath(dirname(__FILE__)) . $fileName;
        return file_get_contents($path);
    }
}
