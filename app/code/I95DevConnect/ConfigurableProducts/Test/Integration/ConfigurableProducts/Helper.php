<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_ConfigurableProducts
 */


namespace I95DevConnect\ConfigurableProducts\Test\Integration\ConfigurableProducts;

/**
 * Helper class for Configurable product test cases
 */
class Helper
{
    public $productCreate;
    public $i95devServerRepo;
    public $erpMessageQueue;
    public $stockRegistry;
    public $dummyData;
    public $product;
    public $attributeCreate;
    public $productFactory;
    public $productId;
    public $order;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Create            $productCreate
     * @param \I95DevConnect\I95DevServer\Model\I95DevServerRepository                            $i95devServerRepo
     * @param \I95DevConnect\MessageQueue\Model\I95DevErpMQRepository                             $erpMessageQueue
     * @param \I95DevConnect\I95DevServer\Test\Integration\DummyData                              $dummyData
     * @param \Magento\Catalog\Model\ProductRepository                                            $product
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\Attribute $attributeCreate
     * @param \Magento\Catalog\Model\ProductFactory                                               $productFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface                                $stockRegistry
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Create $productCreate,
        \I95DevConnect\I95DevServer\Model\I95DevServerRepository $i95devServerRepo,
        \I95DevConnect\MessageQueue\Model\I95DevErpMQRepository $erpMessageQueue,
        \I95DevConnect\I95DevServer\Test\Integration\DummyData $dummyData,
        \Magento\Catalog\Model\ProductRepository $product,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\Attribute $attributeCreate,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->productCreate = $productCreate;
        $this->i95devServerRepo = $i95devServerRepo;
        $this->erpMessageQueue = $erpMessageQueue;
        $this->dummyData = $dummyData;
        $this->product = $product;
        $this->attributeCreate = $attributeCreate;
        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * create product in inbound messagequeue
     *
     * @param $productJsonData
     * @return array
     */
    public function createConfigurableProductInInboundMQ($productJsonData)
    {
        $this->i95devServerRepo->serviceMethod("createConfigurableProductList", $productJsonData);
        return $this->getInboundMqData();
    }

    /**
     * Get inbound message queue collection by ref name
     *
     * @return array
     * @author Debashis S. Gopal
     */
    public function getInboundMqData()
    {
        return $this->erpMessageQueue->getCollection()
            ->getData();
    }

    /**
     * Create an order in Magento
     *
     * @param  $requestData
     * @return string|null
     * @throws \Exception
     * @author Debashis S. Gopal
     */
    public function createOrderInMagento($requestData)
    {
        $this->dummyData->createCustomer();
        $parentProduct = $this->product->get($requestData['parent_sku'], true, 0, true);
        $parentProduct->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $parentProduct->setVisibility(4);
        $this->product->save($parentProduct);
        $this->dummyData->productSKU = $requestData['child_sku'];
        $this->order = $this->dummyData->createSingleOrder(1025, 1);
        return $this->order->getIncrementId();
    }

    /**
     * Process order reverse sync flow.
     *
     * @param  $requestData
     * @param  $productId
     * @return array
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @author Debashis S. Gopal
     */
    public function orderSyncWithConfigurablePro($requestData, $productId)
    {
        $this->productId = $productId;
        $this->dummyData->createCustomer();
        $this->addInventory($requestData);
        $path = realpath(dirname(__FILE__)) . "/Json/OrderReverse.json";
        $data = file_get_contents($path);
        return $this->syncOrder($data);
    }

    /**
     * * Add inventory to child product.
     *
     * @param  $requestData
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @author Debashis S. Gopal
     */
    public function addInventory($requestData)
    {
        $this->dummyData->productId = $this->productId;
        $this->dummyData->addInventory();
        $this->enableProduct($requestData);
    }

    /**
     * Enable the child product and parent product
     *
     * @param  $requestData
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @author Debashis S. Gopal
     */
    public function enableProduct($requestData)
    {
        $_product = $this->product->get($requestData['child_sku'], true, 0, true);
        $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $_product->setVisibility(4);
        $this->product->save($_product);

        $parentProduct = $this->product->get($requestData['parent_sku'], true, 0, true);
        $parentProduct->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $parentProduct->setVisibility(4);
        $this->product->save($parentProduct);
        $this->stockRegistry->getStockItem($parentProduct->getId());
    }

    /**
     * Create data in message queue and sync to Magento
     *
     * @param  $orderData
     * @return array
     */
    public function syncOrder($orderData)
    {
        $this->createOrderInInboundMQ($orderData);
        $this->i95devServerRepo->syncMQtoMagento();
        return $this->getInboundMqData();
    }

    /**
     * Create order in inbound messagequeue
     *
     * @param  $orderJsonData
     * @return array
     * @author Debashis S. Gopal
     */
    public function createOrderInInboundMQ($orderJsonData)
    {
        $this->i95devServerRepo->serviceMethod("createOrderList", $orderJsonData);
        return $this->getInboundMqData();
    }
}
