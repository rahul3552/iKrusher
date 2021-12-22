<?php

/**
 * @package I95DevConnect_VatTax
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 */

namespace I95DevConnect\VatTax\Test\Integration\VatTax;

/**
 * Tax Business Posting Groups test case for reverse flows
 */
class TaxBusPostingGroupsReverseTest extends \PHPUnit\Framework\TestCase
{

    const VATTAX = 'VATTAX';
    const STATUS = 'status';
    const ERROR_ID = 'error_id';
    const TARGET_ID = 'target_id';

    public $i95devServerRepo;
    public $erpMessageQueue;

    /**
     * @author Ranjith Kumar Rasakatla
     */
    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->i95devServerRepo = $objectManager->create(
            \I95DevConnect\I95DevServer\Model\I95DevServerRepository::class
        );
        $this->erpMessageQueue = $objectManager->create(
            \I95DevConnect\MessageQueue\Model\I95DevErpMQRepository::class
        );
        $this->storeManager = $objectManager->create(
            \Magento\Store\Model\StoreManagerInterface::class
        );
        $this->taxBusinessPosting = $objectManager->create(
            \I95DevConnect\VatTax\Model\TaxBusPostingGroups::class
        );
        $this->customer = $objectManager->create(
            \Magento\Customer\Api\CustomerRepositoryInterface::class
        );
        $this->customerFactory = $objectManager->create(
            \Magento\Customer\Model\CustomerFactory::class
        );
        $this->dummyData = $objectManager->create(
            \I95DevConnect\I95DevServer\Test\Integration\DummyData::class
        );
    }

    /**
     * Test basic happy path for a Tax business posting group
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @author Ranjith Kumar Rasakatla
     */
    public function testTaxBusinessPosting()
    {
        $this->inititeDataSync("TaxBusinessPosting.json", self::VATTAX);
    }

    /**
     * Test case for assign tax class to customer
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @author Hrusikesh Manna
     */
    public function testTaxAssignTocustomer()
    {
        $this->createCustomer("Customer.json");
    }

    /**
     * Test case for update Tax bus posting group
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @author Hrusieksh Manna
     */
    public function testUpdateTaxBusinessPosting()
    {
        $this->inititeDataSync("TaxBusinessPosting.json", self::VATTAX);
        $taxBusPostingData = $this->readFile('UpdateBusinessPostingGroup.json');
        $response = $this->pushDataToInboundMQ("createTaxBusPostingGroupList", $taxBusPostingData, "VATTAXNew");
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            "Issue occured in saving business posting group to messagequeue"
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData('VATTAXNew');

        if ($collection[0][self::ERROR_ID]>0) {
            $errorMsg = $this->dummyData->readErrorMsg($collection[0][self::ERROR_ID]);
            $message = $errorMsg[0]['msg'];
        } else {
            $message =  null;
        }
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $collection[0][self::STATUS],
            $message
        );
    }

    /**
     * Sync Data
     * @param type $fileName
     * @param type $code
     * @return string
     * @author Ranjith Kumar Rasakatla
     */
    public function inititeDataSync($fileName, $code)
    {
        $productPostingJsonData = $this->readFile($fileName);
        $response = $this->pushDataToInboundMQ("createTaxBusPostingGroupList", $productPostingJsonData, $code);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            "Issue occured in saving business posting group to messagequeue"
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData(self::VATTAX);
        $errorMsg = $this->dummyData->readErrorMsg($collection[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $collection[0][self::STATUS],
            $errorMsg
        );
        $taxBusinessPosting = $this->taxBusinessPosting->getCollection()
            ->addFieldToFilter("code", $code)
            ->getLastItem();
        if (!empty($taxBusinessPosting) && isset($taxBusinessPosting["code"])) {
            $this->assertEquals(
                $taxBusinessPosting["code"],
                $collection[0][self::TARGET_ID],
                "Issue occured in saving business posting group from mq to magento"
            );
        }

        return $taxBusinessPosting;
    }

    /**
     * push data to inbound message queue
     * @param $serviceMethod
     * @param $productPostingJsonData
     * @param $code
     * @return mixed
     */
    public function pushDataToInboundMQ($serviceMethod, $productPostingJsonData, $code)
    {
        $this->i95devServerRepo->serviceMethod($serviceMethod, $productPostingJsonData);
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::TARGET_ID, $code)
                ->getData();
    }

    /**
     * get inbound message queue collection by target id
     * @param $targetId
     * @return array
     */
    public function getInboundMqData($targetId)
    {
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::TARGET_ID, $targetId)
                ->getData();
    }

    /**
     * Function to create customer for assign Tex group
     * @param type $fileName
     * @author Hrusikesh Manna
     */
    public function createCustomer($fileName)
    {
        $customerData = $this->readFile($fileName);
        $response = $this->pushDataToInboundMQ("createCustomersList", $customerData, "ERPCUST001");
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            "Issue came in saving customer to messagequeue"
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData('ERPCUST001');
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $collection[0][self::STATUS],
            "Issue occured in saving business posting group from messagequeue to magento"
        );
        $getCustomer = $this->customer->get("christopherjose.yien@bullbeer.org", 1);
        $customerId = $getCustomer->getId();
        $customerDetails = $this->customerFactory->create()->load($customerId)->getData();
        $taxBusPostingGroup = $customerDetails['tax_bus_posting_group'];
        $this->assertEquals(self::VATTAX, $taxBusPostingGroup, "Tax Business Posting Group Not Set To Customr");
    }

    /**
     * Read json data from file
     * @param type $fileName
     * @return json
     * @author Hrusikesh Manna
     */
    public function readFile($fileName)
    {
        $path = realpath(dirname(__FILE__)) . "/Json/" . $fileName;
        return file_get_contents($path);
    }
}
