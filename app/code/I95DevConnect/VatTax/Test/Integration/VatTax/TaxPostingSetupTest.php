<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Test\Integration\VatTax;

/**
 * Tax Posting Setup test case for reverse flows
 */
class TaxPostingSetupTest extends \PHPUnit\Framework\TestCase
{

    const VATTAXP = 'VATTAXP';
    const VATTAX = 'VATTAX';
    const STATUS = 'status';
    const ERROR_ID = 'error_id';
    const TARGET_ID = 'target_id';
    const REF_NAME = 'ref_name';
    const TAXGCODE ='tax_busposting_group_code';

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
        $this->taxPostingSetup = $objectManager->create(
            \I95DevConnect\VatTax\Model\TaxPostingSetup::class
        );
        $this->taxBusinessPosting = $objectManager->create(
            \I95DevConnect\VatTax\Model\TaxPostingSetup::class
        );
        $this->dummyData = $objectManager->create(
            \I95DevConnect\I95DevServer\Test\Integration\DummyData::class
        );
        $this->taxCalculation = $objectManager->create(
            \I95DevConnect\VatTax\Model\TaxCalculation::class
        );
    }

    /**
     * Test basic happy path for a Tax posting setup
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @author Ranjith Kumar Rasakatla
     */
    public function testTaxBusinessPosting()
    {
        $this->createTaxBusPostingGroup();
        $this->createTaxProductPostingGroup();
        $this->inititeDataSync("TaxPostingSetup.json", self::VATTAX, self::VATTAXP);
    }

    /**
     * Test case for check tax percentage
     * @author Hrusikesh Manna
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @magentoConfigFixture current_website i95dev_messagequeue/I95DevConnect_settings/attribute_set 4
     * @magentoConfigFixture current_website i95dev_messagequeue/I95DevConnect_settings/attribute_group 7
     * @magentoDbIsolation enabled
     */
    public function testCheckTaxPercentageOnOrder()
    {
        $this->createTaxBusPostingGroup();
        $this->createTaxProductPostingGroup();
        $this->inititeDataSync("TaxPostingSetup.json", self::VATTAX, self::VATTAXP);
        $customerResponse = $this->createCustomer();
        $message = $this->dummyData->readErrorMsg($customerResponse[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $customerResponse[0][self::STATUS],
            $message
        );
        $productResponse = $this->createProduct();
        $message1 = $this->dummyData->readErrorMsg($productResponse[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $productResponse[0][self::STATUS],
            $message1
        );
        $customerId = $customerResponse[0]['magento_id'];
        $taxPercentage = $this->taxCalculation->getTax('JABRA', $customerId);
        $this->assertEquals('3', $taxPercentage, 'the applied tax percentage does not match');
    }

    /**
     * Test case for Data Validation
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @magentoDbIsolation enabled
     * @author Hrusikesh
     */
    public function testDataValidation()
    {
        $jsonData = $this->readDataFromFile("TaxPostingSetupInvalidData.json");
        $arrayData = [];
        $errorId = [];
        foreach (json_decode($jsonData, 1) as $data) {
            $i=0;
            foreach ($data as $datas) {
                $arrayData["RequestData"][] = $data[$i];
                $targetId = $datas['TargetId'];
                $reference = $datas['Reference'];
                $this->pushPostingSetupToInboundMQ(json_encode($arrayData), $targetId, $reference);
                $this->i95devServerRepo->syncMQtoMagento();
                $collection = $this->getInboundMqData($targetId);
                $errorId[]= $collection[0][self::ERROR_ID];
                 $i++;
            }
        }
        $this->assertEquals(2, count($errorId), "Issue Came In Data Validation");
    }

    /**
     * Initiate Data Sync
     * @param type $path
     * @param type $busCode
     * @param type $prodCode
     * @return array
     * @author Ranjith Kumar Rasakatla
     */
    public function inititeDataSync($path, $busCode, $prodCode)
    {
        $jsonData = $this->readDataFromFile($path);
        $response = $this->pushPostingSetupToInboundMQ($jsonData, $busCode, $prodCode);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            "Issue occured in saving business posting group to messagequeue"
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData($busCode);
        $message = $this->dummyData->readErrorMsg($collection[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $collection[0][self::STATUS],
            $message
        );
        $taxBusinessPosting = $this->taxBusinessPosting->getCollection()
            ->addFieldToFilter(self::TAXGCODE, $busCode)
            ->addFieldToFilter("tax_productposting_group_code", $prodCode)
            ->getLastItem();
        if (!empty($taxBusinessPosting) && isset($taxBusinessPosting[self::TAXGCODE])
            && isset($taxBusinessPosting["tax_productposting_group_code"])) {

            $this->assertEquals(
                $taxBusinessPosting[self::TAXGCODE],
                $collection[0][self::TARGET_ID],
                "Issue occured in saving tax posting setup from mq to magento"
            );
        }

        return $taxBusinessPosting;
    }

    /**
     * Push posting setup to inbound Message Queue
     * @param type $productPostingJsonData
     * @param type $busCode
     * @param type $prodCode
     * @return array
     * @author Ranjith Kumar Rasakatla
     */
    public function pushPostingSetupToInboundMQ($productPostingJsonData, $busCode, $prodCode)
    {
        $this->i95devServerRepo->serviceMethod("createTaxPostingSetupList", $productPostingJsonData);
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::TARGET_ID, $busCode)
                ->addFieldToFilter(self::REF_NAME, $prodCode)
                ->getData();
    }

    /**
     * push tax business posting to inbound message queue
     * @param $classObj
     * @param $data
     * @param $busCode
     * @return array
     */
    public function pushDataToInboundMQ($classObj, $data, $busCode)
    {
        $this->i95devServerRepo->serviceMethod($classObj, $data);
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::TARGET_ID, $busCode)
                ->getData();
    }

    /**
     * get inbound message queue collection by target id
     * @param $busCode
     * @return array
     */
    public function getInboundMqData($busCode)
    {
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::TARGET_ID, $busCode)
                ->getData();
    }

    /**
     * Create Tax Business Posting Group
     * @author Hrusieksh Manna
     */
    public function createTaxBusPostingGroup()
    {
        $this->createGroup(
            "createTaxBusPostingGroupList.json",
            self::VATTAX,
            "createTaxBusPostingGroupList",
            "Issue occured in saving tax business posting group to messagequeue"
        );
    }

    /**
     * Create Tax Product Posting Group
     * @author Hrusikesh Manna
     */
    public function createTaxProductPostingGroup()
    {
        $this->createGroup(
            "TaxBusinessPosting.json",
            self::VATTAXP,
            "createTaxProductPostingGroupList",
            "Issue occured in saving tax product posting group to messagequeue"
        );
    }

    /**
     * common function for test
     * @param $filePath
     * @param $code
     * @param $processName
     * @param $msg
     */
    public function createGroup($filePath, $code, $processName, $msg)
    {
        $jsonData = $this->readDataFromFile($filePath);
        $response = $this->pushDataToInboundMQ($processName, $jsonData, $code);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            $msg
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData($code);
        $message = $this->dummyData->readErrorMsg($collection[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $collection[0][self::STATUS],
            $message
        );
    }

    /**
     * Read data from json file
     * @param type $filePath
     * @return string
     * @author Hrusikesh Manna
     */
    public function readDataFromFile($filePath)
    {
        $path = realpath(dirname(__FILE__)) . "/Json/" . $filePath;
        return(file_get_contents($path));
    }

    /**
     * Create customer with Tax Id
     * @return array
     * @author Hrusikesh Manna
     */
    public function createCustomer()
    {
        $customerData = $this->readDataFromFile("Customer.json");
        $this->i95devServerRepo->serviceMethod('createCustomersList', $customerData);
        $this->i95devServerRepo->syncMQtoMagento();
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::REF_NAME, 'ERPCUST001')
                ->getData();
    }

    /**
     * Create product with Tax Id
     * @author Hrusikesh Manna
     * @return type
     */
    public function createProduct()
    {
        $productData = $this->readDataFromFile("Product.json");
        $this->i95devServerRepo->serviceMethod('createProductList', $productData);
        $this->i95devServerRepo->syncMQtoMagento();
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::REF_NAME, 'JABRA')
                ->getData();
    }
}
