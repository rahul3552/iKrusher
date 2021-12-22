<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */

namespace I95DevConnect\I95DevServer\Test\Integration\Order;

/**
 * Class responsible for forward order work flow
 */
class OrderForwardTest extends \PHPUnit\Framework\TestCase
{
    const MAGENTO_ID = "magento_id";
    const REFERENCE = "reference";
    const WRONG_RESPONSE = "Wrong reference in response";
    const ODA = "orderDocumentAmount";
    const ODA_RESPONSE = "Wrong orderDocumentAmount in response";
    const COMMENTS = "comments";
    const EMAIL_ID = "hrusikesh.manna@jiva.com";

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->orderModel = $objectManager->create(
            \Magento\Sales\Model\Order::class
        );
        $this->stockRegistry = $objectManager->create(
            \Magento\CatalogInventory\Api\StockRegistryInterface::class
        );
        $this->customSalesOrder = $objectManager->create(
            \I95DevConnect\MessageQueue\Model\SalesOrderFactory::class
        );
        $this->magentoMQ = $objectManager->create(
            \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory::class
        );
        $this->magentoMQRepo = $objectManager->create(
            \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory::class
        );
        $this->i95devServerRepo = $objectManager->create(
            \I95DevConnect\I95DevServer\Model\I95DevServerRepository::class
        );
        $this->magentoMessageQueue = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \I95DevConnect\MessageQueue\Model\I95DevMagentoMQRepository::class
        );
        $this->dummyData = $objectManager->create(
            \I95DevConnect\I95DevServer\Test\Integration\DummyData::class
        );
    }

    /**
     * Create order in magento
     *
     * @param array $requestData
     *
     * @author Sravani Polu
     */
    public function orderPrerequistiesData($requestData = [])
    {
        $this->dummyData->createCustomer();
        $this->dummyData->createSingleSimpleProduct(1000);
        $this->order = $this->dummyData->createSingleOrder(null, null, $requestData);
    }

    /**
     * Create Guest order in magento
     *
     * @return void
     * @author Debashis S. Gopal
     */
    public function guestOrderData()
    {
        $this->dummyData->createSingleSimpleProduct(1000);
        $this->order = $this->dummyData->createGuestOrder('');
    }

    /**
     * Get outbound message queue collection data by magento_id
     *
     * @author Debashis S. Gopal
     * @param $magentoId
     * @return array
     */
    public function getOutbountMqData($magentoId)
    {
        $collections = $this->magentoMessageQueue->getCollection()
            ->addFieldToFilter('entity_code', 'Order')
            ->addFieldToFilter(self::MAGENTO_ID, $magentoId);
        return $collections->getData();
    }

    /**
     * Call getOrdersInfo service and checks required assertion.
     *
     * @author Debashis S. Gopal
     * @param string $orderId
     * @return array
     */
    public function getOrdersInfo($orderId)
    {
        $responseData = $this->getEntityInfoData($orderId, "order", "getOrdersInfo");

        $this->validateMagentoResponseData($responseData);
        return $responseData;
    }

    /**
     * Validate fields in magento response data
     * Fields: sourceId, shippingMethod, billingAddress, orderItems, payment, origin
     *
     * @param array $responseData
     * @return void
     */
    public function validateMagentoResponseData($responseData)
    {
        $orderId = $this->order->getIncrementId();
        $this->assertEquals($responseData['sourceId'], $orderId, 'Wrong sourceId in response');
        $this->assertEquals($responseData['shippingMethod'], 'flatrate_flatrate', 'Wrong shippingMethod in response');
        $this->assertNotEmpty($responseData['shippingAddress']);
        $this->assertNotEmpty($responseData['billingAddress']);
        $this->assertNotEmpty($responseData['orderItems']);
        $this->assertNotEmpty($responseData['payment']);
        $this->assertEquals($responseData['origin'], 'website', "Wrong value set for origin");
    }

    /**
     * Testcase for order creation from magento to ERP, With equal billing and shipping address
     *
     * @magentoDbIsolation enabled
     * @author Sravani Polu
     */
    public function testOrderWithSameBillingAndShippingAddress()
    {
        $this->orderPrerequistiesData();
        $responseData = $this->getOrdersInfo($this->order->getIncrementId());
        $this->assertEquals($responseData[self::REFERENCE], self::EMAIL_ID, self::WRONG_RESPONSE);
        $this->assertEquals($responseData[self::ODA], 40, self::ODA_RESPONSE);
    }

    /**
     * Testcase for order creation from magento to ERP, With different billing and shipping address
     *
     * @magentoDbIsolation enabled
     * @author Sravani Polu
     */
    public function testOrderWithDifferentBillingAndShippingAddress()
    {
        $this->orderPrerequistiesData(['isDifferentAddress' => 1]);
        $responseData = $this->getOrdersInfo($this->order->getIncrementId());
        $this->assertNotEquals($responseData['shippingAddress'], $responseData['billingAddress']);
        $this->assertEquals($responseData[self::REFERENCE], self::EMAIL_ID, self::WRONG_RESPONSE);
        $this->assertEquals($responseData[self::ODA], 40, self::ODA_RESPONSE);
    }
    
    /**
     * Testcase for order creation from magento to ERP With Custom Price.
     * Validated Fields: orderDocumentAmount, specialPrice(in order line item level), price(in order line item level).
     *
     * @magentoDbIsolation enabled
     * @author Debashis S. Gopal
     */
    public function testGetOrderWithCustomPrice()
    {
        $this->orderPrerequistiesData(['custom_price' => 8]);
        $responseData = $this->getOrdersInfo($this->order->getIncrementId());
        $this->assertEquals(
            $responseData[self::REFERENCE],
            self::EMAIL_ID,
            self::WRONG_RESPONSE
        );
        $this->assertEquals(
            $responseData[self::ODA],
            36,
            self::ODA_RESPONSE
        );
        $orderItem = $responseData['orderItems'][0];
        $this->assertNotEquals(
            $orderItem['specialPrice'],
            $orderItem['price'],
            "price and Specialprice must be different"
        );
        $this->assertEquals($orderItem['specialPrice'], 8, 'Wrong value for specialPrice');
    }

    /**
     * Testcase for order creation from magento to ERP With Comment.
     * Validated Field: comments
     *
     * @magentoDbIsolation enabled
     * @author Debashis S. Gopal
     */
    public function testGetOrderWithComment()
    {
        $this->orderPrerequistiesData(['comment' => 'Test case comment']);
        $responseData = $this->getOrdersInfo($this->order->getIncrementId());
        $this->assertEquals(
            $responseData[self::REFERENCE],
            self::EMAIL_ID,
            self::WRONG_RESPONSE
        );
        $this->assertNotEmpty($responseData[self::COMMENTS]);
        $this->assertEquals(
            $responseData[self::COMMENTS][0]['comment'],
            'Test case comment',
            'Wrong comment'
        );
        $this->assertEquals($responseData[self::COMMENTS][0]['source'], 'admin', 'Wrong source');
    }

    /**
     * Testcase for guest order creation from magento to ERP
     * Validated Fields: isGuest(in customer array)
     *
     * @magentoDbIsolation enabled
     * @author Sravani Polu
     */
    public function testGetGuestOrder()
    {
        $this->guestOrderData();
        $responseData = $this->getOrdersInfo($this->order->getIncrementId());
        $this->assertEquals(
            $responseData[self::REFERENCE],
            'jbutt@gmail.com',
            self::WRONG_RESPONSE
        );
        $this->assertEmpty($responseData['targetCustomerId']);
        $this->assertNotEmpty($responseData['customer']);
        $this->assertEquals(
            $responseData['customer']['isGuest'],
            true,
            "isGuest field should be true"
        );
    }
}
