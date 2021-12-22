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
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class NetTermsReverseSyncTest extends TestCase
{
    const NTR_TEST_1 = 'ntr-test1';
    const STATUS = 'status';

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
    }

    /**
     * Test case for net terms reverse sync
     * @magentoConfigFixture current_website i95devconnect_netterms/netterms_enabled_settings/enable_netterms 1
     * @magentoDbIsolation enabled
     * @author Hrusikesh Manna
     */
    public function testNettermReverseSync()
    {
        $jsonData = $this->helper->readJsonData('netTerms.json');
        $this->processTestCase($jsonData, 'createNetTermList', self::NTR_TEST_1);
        $collection = $this->getInboundMqData(self::NTR_TEST_1);
        $message = $this->dummyData->readErrorMsg($collection[0]['error_id']);
        $this->assertEquals(
            Data::SUCCESS,
            $collection[0][self::STATUS],
            $message
        );
    }

    /**
     * Assign Net Term To Customer
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_netterms/netterms_enabled_settings/enable_netterms 1
     * @author Hrusikesh Manna
     */
    public function testAssignNetTermToCustomer()
    {
        $jsonData = $this->helper->readJsonData('netTerms.json');
        $this->processTestCase($jsonData, 'createNetTermList', self::NTR_TEST_1);
        $netTermCollection = $this->getInboundMqData(self::NTR_TEST_1);
        $this->assertEquals(
            Data::SUCCESS,
            $netTermCollection[0][self::STATUS],
            'Net Term Not Created In Magento'
        );

        $this->helper->createEavAttribute();
        $jsonData = $this->helper->readJsonData('Customer.json');
        $this->processTestCase($jsonData, 'createCustomersList', '009806');
        $collection = $this->getInboundMqData('009806');
        $this->assertEquals(
            Data::SUCCESS,
            $collection[0][self::STATUS],
            "Issue comes creating customer in Magento"
        );
    }

     /**
      * Assign Net Term To Order Reverse Sync
      * @magentoDbIsolation enabled
      * @magentoConfigFixture current_website i95devconnect_netterms/netterms_enabled_settings/enable_netterms 1
      * @author Hrusikesh Manna
      */
    public function testOrderReversesyncWithNetTerm()
    {
        $jsonData = $this->helper->readJsonData('OrderReverse.json');
        $this->dummyData->createCustomer();
        $this->dummyData->createSingleSimpleProduct('FLEXDECK');
        $this->dummyData->addInventory();
        $this->processTestCase($jsonData, 'createOrdersList', 'ORDST3500');
        $collection = $this->getInboundMqData('ORDST3500');
        $message = $this->dummyData->readErrorMsg($collection[0]['error_id']);
         $this->assertEquals(
             Data::SUCCESS,
             $collection[0][self::STATUS],
             $message
         );
    }

    /**
     * Process Test Case
     * @param type $data
     * @param type $serviceMethod
     * @param type $targetId
     * @author Hrusikesh Manna
     */
    public function processTestCase($data, $serviceMethod, $targetId)
    {
        $mqData = $this->pushDataToInboundMq($data, $serviceMethod, $targetId);
        $this->assertEquals(
            Data::PENDING,
            $mqData[0][self::STATUS],
            "Issue occured in saving record to messagequeue"
        );
        $this->i95devServerRepo->syncMQtoMagento();
    }

    /**
     * push Data to IBMQ
     * @param type $jsnData
     * @param type $methods
     * @return array
     */
    public function pushDataToInboundMq($jsnData, $methods, $targetId)
    {
        $this->i95devServerRepo->serviceMethod($methods, $jsnData);
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter('target_id', $targetId)
                ->getData();
    }

    /**
     * get inbound message queue collection by target id
     * @return Object
     * @author Hrusikesh Manna
     */
    public function getInboundMqData($targetId)
    {
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter('target_id', $targetId)
                ->getData();
    }
}
