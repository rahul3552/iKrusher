<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Test\Integration\VatTax;

/**
 * Tax Product Posting Groups test case for reverse flows
 */
class TaxProductPostingGroupsReverseTest extends \PHPUnit\Framework\TestCase
{
    const VATTAXP = 'VATTAXP';
    const STATUS = 'status';
    const ERROR_ID = 'error_id';
    const TARGET_ID = 'target_id';
    const JSON_FILE = 'TaxProductPosting.json';
    const POSTINGGROUPLIST ='createTaxProductPostingGroupList';
    const P_TARGET_ID = 'JABRA';

    public $i95devServerRepo;
    public $erpMessageQueue;
    public $errorUpdateData;

    /**
     * @author Ranjith Kumar Rasakatla
     */
    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->product = $objectManager->create(
            \Magento\Catalog\Model\ProductRepository::class
        );
        $this->i95devServerRepo = $objectManager->create(
            \I95DevConnect\I95DevServer\Model\I95DevServerRepository::class
        );
        $this->erpMessageQueue = $objectManager->create(
            \I95DevConnect\MessageQueue\Model\I95DevErpMQRepository::class
        );
        $this->dummyData = $objectManager->create(
            \I95DevConnect\I95DevServer\Test\Integration\DummyData::class
        );
        $this->storeManager = $objectManager->create(
            \Magento\Store\Model\StoreManagerInterface::class
        );
        $this->taxProductPosting = $objectManager->create(
            \I95DevConnect\VatTax\Model\TaxProductPostingGroups::class
        );
        $this->taxBusPostingGroupsReverseTest = $objectManager->create(
            \I95DevConnect\VatTax\Test\Integration\VatTax\TaxBusPostingGroupsReverseTest::class
        );
    }

     /**
      * Test if module is disabled
      * @magentoDbIsolation enabled
      * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 0
      * @author Ranjith Kumar Rasakatla
      */
    public function testIfModuleDisabled()
    {
        $productPostingJsonData = $this->taxBusPostingGroupsReverseTest->readFile(self::JSON_FILE);
        $response = $this->pushDataToInboundMQ(self::POSTINGGROUPLIST, $productPostingJsonData, self::VATTAXP);
        $this->commonCodeTaxTest($response);
    }

    /**
     * Test basic happy path for a Tax product posting group
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @author Ranjith Kumar Rasakatla
     */
    public function testTaxProductPosting()
    {
        $this->inititeDataSync(self::JSON_FILE, self::VATTAXP);
    }

    /**
     * Test tax group assign to product or not
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @magentoConfigFixture current_website i95dev_messagequeue/I95DevConnect_settings/attribute_set 4
     * @magentoConfigFixture current_website i95dev_messagequeue/I95DevConnect_settings/attribute_group 7
     * @author Hrusikesh Manna
     */
    public function testTaxAssignToProduct()
    {
        $this->createProduct('Product.json');
    }

    /**
     * Test case for update product posting group
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_website i95devconnect_vattax/vattax_enabled_settings/enable_vattax 1
     * @author Hrusieksh Manna
     */
    public function testUpdateProductPostingGroup()
    {
        $this->inititeDataSync(self::JSON_FILE, self::VATTAXP);
        $productPostingGroupData = $this->taxBusPostingGroupsReverseTest->readFile('UpdateProductPostingGroup.json');
        $response = $this->pushDataToInboundMQ(
            self::POSTINGGROUPLIST,
            $productPostingGroupData,
            "VATTAXPNew"
        );

        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            "Issue occured in saving business posting group to messagequeue"
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData('VATTAXPNew');
        $errorMsg = $this->dummyData->readErrorMsg($collection[0][self::ERROR_ID]);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $collection[0][self::STATUS],
            $errorMsg
        );
    }

    /**
     * Sync Data
     * @param $fileName
     * @param $code
     * @return json
     * @author Hrusikesh Manna
     */
    public function inititeDataSync($fileName, $code)
    {
        $productPostingJsonData = $this->taxBusPostingGroupsReverseTest->readFile($fileName);
        $response = $this->pushDataToInboundMQ(self::POSTINGGROUPLIST, $productPostingJsonData, $code);
        $collection = $this->getInboundMqData(self::VATTAXP);

        $this->commonCodeTaxTest($response);
        $taxProductPosting = $this->taxProductPosting->getCollection()->addFieldToFilter("code", $code)->getLastItem();
        if (!empty($taxProductPosting) && isset($taxProductPosting["code"])) {
            $this->assertEquals(
                $taxProductPosting["code"],
                $collection[0][self::TARGET_ID],
                "Issue occured in saving product posting group from mq to magento"
            );
        }

        return $taxProductPosting;
    }

    /**
     *
     */
    public function commonCodeTaxTest($response)
    {
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            "Issue occured in saving product posting group to messagequeue"
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData(self::VATTAXP);
        $errorMsg = $this->dummyData->readErrorMsg($collection[0][self::ERROR_ID]);

        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::ERROR,
            $collection[0][self::STATUS],
            $errorMsg
        );
    }
    /**
     * push tax product posting to inbound message queue
     * @param $servicemethod
     * @param $productPostingJsonData
     * @param $code
     * @return array
     * @author Ranjith Kumar Rasakatla
     */
    public function pushDataToInboundMQ($servicemethod, $productPostingJsonData, $code)
    {
        $this->i95devServerRepo->serviceMethod($servicemethod, $productPostingJsonData);
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::TARGET_ID, $code)
                ->getData();
    }

    /**
     * get inbound message queue collection by target id
     * @param $targetId
     * @return Object
     * @author Ranjith Kumar Rasakatla
     */
    public function getInboundMqData($targetId)
    {
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter(self::TARGET_ID, $targetId)
                ->getData();
    }

    /**
     * Create product and assign tax group
     * @param $fileName
     * @author Hrusiekesh Manna
     */
    public function createProduct($fileName)
    {
        $productData = $this->taxBusPostingGroupsReverseTest->readFile($fileName);
        $response = $this->pushDataToInboundMQ("createProductList", $productData, self::P_TARGET_ID);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::PENDING,
            $response[0][self::STATUS],
            "Issue occured in saving product to messagequeue"
        );
        $this->i95devServerRepo->syncMQtoMagento();
        $collection = $this->getInboundMqData(self::P_TARGET_ID);
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $collection[0][self::STATUS],
            "Issue occured in saving product from messagequeue to magento"
        );
        $productDetails = $this->product->get(self::P_TARGET_ID);
        $this->assertEquals(
            self::VATTAXP,
            $productDetails->getTaxProductPostingGroup(),
            "Tax Posting Group Not Set To Product"
        );
    }
}
