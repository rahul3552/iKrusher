<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_netTerms
 */
namespace I95DevConnect\NetTerms\Test\Integration\NetTerms;

use I95DevConnect\I95DevServer\Model\I95DevServerRepository;
use I95DevConnect\I95DevServer\Test\Integration\DummyData;
use I95DevConnect\MessageQueue\Helper\Data;
use I95DevConnect\MessageQueue\Model\I95DevErpMQRepository;
use I95DevConnect\MessageQueue\Model\I95DevMagentoMQRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class NetTermsForwardSyncTest extends TestCase
{
    /**
     * Initialize class object
     * @author Hrusieksh Manna
     */
    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->i95devServerRepo = $objectManager->create(
            I95DevServerRepository::class
        );
        $this->erpMessageQueue = $objectManager->create(
            I95DevErpMQRepository::class
        );
        $this->dummyData = $objectManager->create(
            DummyData::class
        );
        $this->helper = $objectManager->create(
            Helper::class
        );
        $this->customerRepository = $objectManager->create(
            CustomerRepositoryInterface::class
        );
        $this->magentoMessageQueue = $objectManager->create(
            I95DevMagentoMQRepository::class
        );
    }

     /**
      * Test case for NetTerm with customer response
      * @magentoDbIsolation enabled
      * @magentoConfigFixture current_website i95devconnect_netterms/netterms_enabled_settings/enable_netterms 1
      * @author Hrusikesh Manna
      */
    public function testNetTermWithCustomerResponse()
    {
        $this->createNetTerm();
        $this->helper->createEavAttribute();
        $this->helper->createCustomer();
        // @codingStandardsIgnoreStart
        $this->i95devServerRepo->serviceMethod("getCustomersInfo", '{"requestData":[],"packetSize":1,"erp_name":"ERP"}');
        // @codingStandardsIgnoreEnd
    }

    /**
     * Net Term Id with order forward sync
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_netterms/netterms_enabled_settings/enable_netterms 1
     * @author Hrusikesh Manna
     */
    public function testNetTermIdWithOrderForwardSync()
    {
        $jsonData = $this->helper->readJsonData('OrderReverse.json');
        $this->dummyData->createSingleSimpleProduct('FLEXDECK');
        $this->dummyData->addInventory();
        $this->createNetTerm();
        $this->helper->createEavAttribute();
        $this->helper->createCustomer();
        $this->createOrder($jsonData);
        $this->i95devServerRepo->serviceMethod("getOrdersInfo", '{"requestData":[],"packetSize":1,"erp_name":"ERP"}');
    }

    /**
     * Public function test customer response with Net Term Id
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_netterms/netterms_enabled_settings/enable_netterms 1
     * @author Hrusikesh Manna
     */
    public function testSetCustomerResponse()
    {
        $this->createNetTerm();
        $this->helper->createEavAttribute();
        $this->helper->createCustomer();
        // @codingStandardsIgnoreStart
        $response = $this->i95devServerRepo->serviceMethod("getCustomersInfo", '{"requestData":[],"packetSize":50,"erp_name":"ERP"}');
        // @codingStandardsIgnoreEnd
        $this->assertEquals(true, $response->result, "Unable to fetch customer from outbound MQ");
        $responseAfterSync = $this->callSetCustomerResponseService();
        $this->assertNotNull($responseAfterSync);
    }

    public function createNetTerm()
    {
        $jsonData = $this->helper->readJsonData('netTerms.json');
        $this->i95devServerRepo->serviceMethod('createNetTermList', $jsonData);
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->erpMessageQueue->getCollection()
                ->addFieldToFilter('target_id', 'ntr-test1')
                ->getData();
        $this->assertEquals(
            Data::SUCCESS,
            $collection[0]['status'],
            'Issuecame with creating Net Term In Magento'
        );
    }

    /**
     * Function for set customer Response
     * @author Hrusikesh Manna
     */
    public function callSetCustomerResponseService()
    {
        $data = $this->magentoMessageQueue->getCollection()
                    ->addFieldToFilter('entity_code', 'Customer')
                    ->addFieldToFilter('magento_id', $this->helper->customerId)
                    ->getData();
        $msgId = $data[0]['msg_id'];
        // @codingStandardsIgnoreStart
        $request['requestData'][] = ['reference' => 'hrusikesh.manna11122@jiva.com', 'messageId' => $msgId, 'message' => '', 'result' => true, 'targetId' => 'C00011', 'sourceId' => $this->helper->customerId];
        // @codingStandardsIgnoreEnd
        return $this->i95devServerRepo->serviceMethod("setCustomersResponse", json_encode($request));
    }

    /**
     * Function for create customer
     * @author Hrusikesh Manna
     */
    public function createCustomer()
    {

        $jsonData = $this->helper->readJsonData('Customer.json');
        $this->i95devServerRepo->serviceMethod('createCustomersList', $jsonData);
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->erpMessageQueue->getCollection()
                ->addFieldToFilter('target_id', '009806')
                ->getData();

        $this->assertEquals(
            Data::SUCCESS,
            $collection[0]['status'],
            "Issue comes creating customer in Magento"
        );
    }

    /**
     * Function for create order
     * @author Hrusikesh Manna
     * @param type $data
     */
    public function createOrder($data)
    {
        $this->i95devServerRepo->serviceMethod('createOrdersList', $data);
        $this->i95devServerRepo->syncMQtoMagento();
    }
}
