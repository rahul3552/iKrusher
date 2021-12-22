<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */


namespace I95DevConnect\VatTax\Test\Integration\VatTax;

/**
 * Test case for tax with order forward sync
 */
class TaxOrderForwardSync extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->i95devServerRepo = $objectManager->create(
            \I95DevConnect\I95DevServer\Model\I95DevServerRepository::class
        );
        $this->erpMessageQueue = $objectManager->create(
            \I95DevConnect\MessageQueue\Model\I95DevErpMQRepository::class
        );
        $this->dummyData = $objectManager->create(
            \I95DevConnect\I95DevServer\Test\Integration\DummyData::class
        );
        $this->stockRegistryInterface = $objectManager->create(
            \Magento\CatalogInventory\Api\StockRegistryInterface::class
        );
        $this->productFactory = $objectManager->create(
            \Magento\Catalog\Model\ProductFactory::class
        );
    }

    /**
     * Test case for check assigned Tax class Id in Order Forward Sync
     * @magentoDbIsolation enabled
     * @author Hrusikesh Manna
     */
    public function testTaxWithOrderForwardSync()
    {
        $customer = $this->createCustomer();
        $this->assertEquals(
            \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
            $customer[0]['status'],
            "Issue occured in saving customer in Magento"
        );
        $productId = $this->createProduct();
        $sku = $this->productFactory->create()->load($productId)->getSku();
        $this->order = $this->dummyData->createTestOrderWithTax($sku, $customerId);
        $orderInfo = $this->i95devServerRepo->serviceMethod("getOrdersInfo");
        $this->assertEquals(
            true,
            $orderInfo->getResultdata()[0]['result'],
            "Issue came while sending order data to ERP from Magento."
        );
    }

    /**
     * Create customer for order
     * @author Hrusieksh Manna
     * @return text
     */
    public function createCustomer()
    {
        $customerData = $this->readDataFromFile("Customer.json");
        $this->i95devServerRepo->serviceMethod('createCustomersList', $customerData);
        $this->i95devServerRepo->syncMQtoMagento();
        return $this->erpMessageQueue->getCollection()
                ->addFieldToFilter('ref_name', 'ERPCUST001')
                ->getData();
    }

    /**
     * Create product with Tax Id
     * @author Hrusikesh Manna
     * @return test
     */
    public function createProduct()
    {
        return $this->dummyData->createSingleSimpleProduct('JABRA');
    }

    /**
     * Add inventory to product
     * @param type $productId
     * @return int
     * @author Hrusikesh
     */
    public function addInventory($productId)
    {
        $stockItem = $this->stockRegistryInterface->getStockItem($productId);
        $stockItem->setData('is_in_stock', 1);
        $stockItem->setData('qty', 100);
        $stockItem->setData('manage_stock', 1);
        $stockItem->setData('use_config_notify_stock_qty', 1);
        $stockItem->setData('backorders', 1);
        $stockItem->save();
        return $stockItem->getQty();
    }

    /**
     * Read json data from file
     * @param type $filePath
     * @return text
     * @author Hrusieksh Manna
     */
    public function readDataFromFile($filePath)
    {
        $path = realpath(dirname(__FILE__)) . "/Json/" . $filePath;
        return(file_get_contents($path));
    }
}
