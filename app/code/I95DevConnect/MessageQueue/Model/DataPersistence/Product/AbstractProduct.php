<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev (https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Product;

use \I95DevConnect\MessageQueue\Model\AbstractDataPersistence;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class AbstractProduct parent class of product persistance.
 * @updatedBy Arushi Bansal
 */
class AbstractProduct extends AbstractDataPersistence
{

    public $qty = 0;
    public $storeId = 0;

    /**
     *
     * @var array
     */
    public $validateFields = [
        'sku' => 'i95dev_prod_005',
        'price' => 'i95dev_prod_014',
    ];

    public $dataHelper;
    public $storeManager;
    public $attribute;
    public $productInterface;
    public $productRepo;
    public $productStock;
    public $productExtensionInterface;
    public $isNewItem;
    public $erpAttributes = [];
    public $component = '';

    public $productExtension;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\AttributeFactory $attribute
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterface
     * @param \Magento\Catalog\Model\ProductRepositoryFactory $productRepo
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\Stock $productStock
     * @param \Magento\Catalog\Api\Data\ProductExtensionInterfaceFactory $productExtensionInterface
     * @param \Magento\Framework\Json\Decoder $jsonDecoder
     * @param \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQ
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\AttributeFactory $attribute,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterface,
        \Magento\Catalog\Model\ProductRepositoryFactory $productRepo,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse\Stock $productStock,
        \Magento\Catalog\Api\Data\ProductExtensionInterfaceFactory $productExtensionInterface,
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQ,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
    ) {
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->attribute = $attribute->create();
        $this->productInterface = $productInterface;
        $this->productRepo = $productRepo;
        $this->productStock = $productStock;
        $this->productExtensionInterface = $productExtensionInterface;

        parent::__construct(
            $jsonDecoder,
            $i95DevResponse,
            $messageErrorModel,
            $i95DevErpMQ,
            $logger,
            $i95DevErpMQRepository,
            $date,
            $eventManager,
            $validate,
            $i95DevERPDataRepository
        );
    }

    /**
     * set basic details of a product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setBasicDetails()
    {
        /** @updatedBy kavya.k. added setCurrentStore**/
        $this->storeManager->setCurrentStore(\Magento\Store\Model\Store::ADMIN_CODE);
        $this->productInterface = $this->productInterface->create();
        $this->validate->validateFields = $this->validateFields;
        $this->validate->validateData($this->stringData);
        $this->prepareDataForApi();
    }

    /**
     * assign product attribute set and create attribute
     *
     * @param type $attributeCode
     *
     * @return void
     */
    public function assignAttrSet($attributeCode)
    {
        return $this->attribute->assignAttributeSet($attributeCode);
    }

    /**
     * Prepare post data array
     */
    public function prepareDataForApi()
    {
        $sku = $this->dataHelper->getValueFromArray("sku", $this->stringData);
        $this->component = $this->dataHelper->getComponent();
        /**
         * @updatedBy Debashis S. Gopal
         * $this->processAttributeWithKey() and directly calling $this->attribute->processAttributeWithKey()
         * if $attributeWithKeyList is set. Because facing issue if new attribute creation and assign to product,
         * at same time.Option values are not showing as selected
         */
        $attributeWithKeyList = $this->dataHelper->getValueFromArray("attributeWithKey", $this->stringData);
        if (!empty($attributeWithKeyList) && is_array($attributeWithKeyList)) {
            $this->erpAttributes = $this->attribute->processAttributeWithKey($attributeWithKeyList);
        }
        $this->productInterface->setSku($sku);

        $productId = $this->getProductPrimaryId($sku);
        $this->productExtension = $this->productExtensionInterface->create();

        if ($productId > 0) {
            $status = $this->dataHelper->getValueFromArray("status", $this->stringData);
            if ($this->component == "GP" && isset($status) && $status != '') {
                $this->productInterface->setStatus($status);
            }
        } else {
            $this->isNewItem = true;
            $name = $this->dataHelper->getValueFromArray("name", $this->stringData);
            if ($name == '') {
                throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_prod_001'));
            }
            $attributeSetId = $this->dataHelper->getscopeConfig(
                'i95dev_messagequeue/I95DevConnect_settings/attribute_set',
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $this->productInterface->setAttributeSetId($attributeSetId);
            $this->productInterface->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
            $this->productInterface->setStatus(Status::STATUS_DISABLED);
            $this->productInterface->setName($this->dataHelper->getValueFromArray("name", $this->stringData));
        // @updatedBy Arushi Bansal added getDefaultStoreView() to support multiwebsite default selection
            $this->productExtension->setWebsiteIds([$this->storeManager->getDefaultStoreView()->getWebsiteId()]);
        }
        $price = $this->dataHelper->getValueFromArray("price", $this->stringData);
        $this->productInterface->setPrice(round($price, 2));

        $this->productInterface->setTypeId("simple");

        $weight = $this->dataHelper->getValueFromArray("weight", $this->stringData);
        if ($weight) {
            $this->productInterface->setWeight($weight);
        }
        $this->addCustomAttributesToProduct();
    }

    /**
     * get id of product by sku
     * @param string $sku
     * @return int
     */
    public function getProductPrimaryId($sku)
    {
        try {
            $result = $this->productRepo->create()->get($sku);

            if (!empty($result->getId())) {
                return $result->getId();
            } else {
                return 0;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return 0;
        }
    }

    /**
     * set product stock information
     *
     * @param bool $manageStock
     */
    public function setStockInformation($manageStock = true)
    {
        $stockItem = $this->productStock->setStockInformation($this->stringData, $manageStock);

        $this->productExtension->setStockItem($stockItem);
    }

    /**
     * get id of product by sku
     * @param string $sku
     * @return int
     */
    public function getProductBySku($sku)
    {
        try {
            $result = $this->productRepo->create()->get($sku);

            if (!empty($result->getId())) {
                return $result;
            } else {
                return 0;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return 0;
        }
    }

    /**
     * Add custom attibutes to product
     * Attributes [description,short_description,cost,tax_class_id,update_by,targetproductstatus,
     * and other variant attribute from ERP]
     *
     * @createdBy Debashis S. Gopal
     */
    public function addCustomAttributesToProduct()
    {
        // updatedBy R Ranjith, Sync description and short description only in product creation
        if ($this->isNewItem) {
            $description = $this->dataHelper->getValueFromArray("description", $this->stringData);
            if ($description) {
                $this->productInterface->setCustomAttribute("description", $description);
            }
            $shortDescription = $this->dataHelper->getValueFromArray("shortDescription", $this->stringData);
            if ($shortDescription) {
                $this->productInterface->setCustomAttribute("short_description", $shortDescription);
            }
        }

        $cost = $this->dataHelper->getValueFromArray("cost", $this->stringData);
        if ($cost) {
            $this->productInterface->setCustomAttribute("cost", $cost);
        }
         $taxClassId = $this->dataHelper->getValueFromArray("taxClassId", $this->stringData);
        if ($taxClassId !== null) {
            $this->productInterface->setCustomAttribute("tax_class_id", $taxClassId);
        }

        if ($this->component) {
            $this->productInterface->setCustomAttribute("update_by", $this->component);
        }

        $this->productInterface->setCustomAttribute(
            "targetproductstatus",
            \I95DevConnect\MessageQueue\Helper\Data::SYNCED
        );

        if (!empty($this->erpAttributes)) {
            foreach ($this->erpAttributes as $value) {
                $this->productInterface->setCustomAttribute($value["attributeCode"], $value["value"]);
            }
        }
    }
}
