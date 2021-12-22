<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev (https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */


namespace I95DevConnect\MessageQueue\Model\DataPersistence\Product\Product\Reverse;

use \Magento\Store\Model\ScopeInterface;
use \Magento\Catalog\Model\Product\Attribute\Repository;

/**
 * Class Attribute for adding new attribute for products
 */
class Attribute
{

    const DEFAULT_GROUP = 'General';
    const I95DEV_DEFAULT_ATTRIBUTE_TYPE = 'select';
    const ATTRIBUTECODE = 'attributeCode';

    public $dataHelper;
    public $attributeManagement;
    public $productAttributeOption;
    public $productAttributeRepo;
    public $productAttributeFactory;
    public $attributeOptionFactory;
    public $attributeFrontendFactory;
    public $attributeOptionLabel;
    public $eavAttribute;
    public $attributeSetCollection;
    public $storeManager;
    public $productAttributeRepository;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Eav\Model\AttributeManagementFactory $attributeManagement
     * @param \Magento\Catalog\Api\ProductAttributeOptionManagementInterfaceFactory $productAttributeOption
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterfaceFactory $productAttributeRepo
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory $productAttributeFactory
     * @param \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $attributeOptionFactory
     * @param \Magento\Eav\Api\Data\AttributeFrontendLabelInterfaceFactory $attributeFrontendFactory
     * @param \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $attributeOptionLabel
     * @param \Magento\Eav\Model\Entity\AttributeFactory $eavAttribute
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Repository $productAttributeRepository
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Eav\Model\AttributeManagementFactory $attributeManagement,
        \Magento\Catalog\Api\ProductAttributeOptionManagementInterfaceFactory $productAttributeOption,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterfaceFactory $productAttributeRepo,
        \Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory $productAttributeFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $attributeOptionFactory,
        \Magento\Eav\Api\Data\AttributeFrontendLabelInterfaceFactory $attributeFrontendFactory,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $attributeOptionLabel,
        \Magento\Eav\Model\Entity\AttributeFactory $eavAttribute,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Repository $productAttributeRepository
    ) {
        $this->dataHelper = $dataHelper;
        $this->attributeManagement = $attributeManagement;
        $this->productAttributeOption = $productAttributeOption;
        $this->productAttributeRepo = $productAttributeRepo;
        $this->productAttributeFactory = $productAttributeFactory;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->attributeFrontendFactory = $attributeFrontendFactory;
        $this->attributeOptionLabel = $attributeOptionLabel;
        $this->eavAttribute = $eavAttribute;
        $this->attributeSetCollection = $attributeSetCollection;
        $this->storeManager = $storeManager;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * Add attribute and attribute option to attribute set
     *
     * @param string $attributeWithKeyList
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processAttributeWithKey($attributeWithKeyList)
    {
        try {
            $postData = [];
            foreach ($attributeWithKeyList as $attributeWithKey) {
                $getAttributeCode = $this->dataHelper->getValueFromArray(self::ATTRIBUTECODE, $attributeWithKey);
                $attributeValue = $this->dataHelper->getValueFromArray("attributeValue", $attributeWithKey);
                $attributeCode = strtolower($getAttributeCode);
                $options = $this->prepareOptions($attributeCode, $attributeValue, $attributeWithKey);
                $this->assignAttributeSet($attributeCode);

                $count = 0;
                foreach ($options as $option) {
                    if (strtolower($option->getLabel()) == strtolower($attributeValue)) {
                        $attributeValue = $option->getValue();
                        $count = 1;
                        break;
                    }
                }

                if ($count === 0) {
                    $this->addAttrOptionToExistingAttr($attributeCode, $attributeValue);

                    $attributeValue = $this->getAttributeValue($attributeCode, $attributeValue);

                }

                $postData[] = [
                    self::ATTRIBUTECODE => $attributeCode,
                    "value" => $attributeValue
                ];
            }
            return $postData;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param $attributeCode
     * @param $attributeValue
     * @return mixed
     */
    public function getAttributeValue($attributeCode, $attributeValue)
    {
        $options = $this->productAttributeRepository->get($attributeCode)->getOptions();
        foreach ($options as $option) {
            if ($option->getLabel() == $attributeValue) {
                $attributeValue = $option->getValue();
            }
        }

        return $attributeValue;
    }

    /**
     * @param $attributeCode
     * @param $attributeValue
     * @param $attributeWithKey
     * @return mixed
     */
    public function prepareOptions($attributeCode, $attributeValue, $attributeWithKey)
    {
        try {
            $options = $this->productAttributeRepository->get($attributeCode)->getOptions();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // if attribute does not exist then magento throws exception, below code is to create attribute
            $result = $this->createAttribute($attributeCode, $attributeValue, $attributeWithKey);
            if (empty($result->getAttributeCode())) {
                $message = "There is some issue in creation of attribute " . $attributeCode;
                throw new \Magento\Framework\Exception\LocalizedException(__($message));
            }
            $options = $this->productAttributeRepository->get($attributeCode)->getOptions();
        }

        return $options;
    }

    /**
     * add attribute options to existing attribute
     *
     * @param string $attributeCode
     * @param string $attributeValue
     *
     * @return string
     */
    public function addAttrOptionToExistingAttr($attributeCode, $attributeValue)
    {
        try {
            //@author Divya Koona. $this->attributeOptionFactory changed to $attributeOption as
            //I am getting fatal error Invalid method create() while syncing multiple records at a time
            $attributeOption = $this->attributeOptionFactory->create();
            $attributeOption->setLabel($attributeValue);
            //@author Divya Koona. $this->attributeOptionFactory->setValue($attributeValue) removed
            //I am getting SQL state issue if we pass int value to attribute option like style: 10
            $storeLabels = $this->getStoreLabels($attributeValue);
            if (!empty($storeLabels)) {
                $attributeOption->setStoreLabels($storeLabels);
            }

            return $this->productAttributeOption->create()->add($attributeCode, $attributeOption);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * get store labels
     *
     * @param $attributeValue
     *
     * @return array
     * @author Arushi Bansal
     */
    public function getStoreLabels($attributeValue)
    {
        $storeManagerDataList = $this->storeManager->getStores();
        $storeLabels = [];

        foreach ($storeManagerDataList as $store) {
            $attributeOptionLabelData = $this->attributeOptionLabel->create();
            $attributeOptionLabelData->setStoreId($store->getStoreId());
            $attributeOptionLabelData->setLabel($attributeValue);

            $storeLabels[] = $attributeOptionLabelData;
        }

        return $storeLabels;
    }

    /**
     * create product Attribute in magento
     * @param string $attributeCode
     * @param string $attributeValue
     * @param array $attributeWithKey
     * @return Object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAttribute($attributeCode, $attributeValue, $attributeWithKey)
    {
        $attributeType = $this->dataHelper->getValueFromArray("attributeType", $attributeWithKey);
        $attributeType =  $frontendInput = isset($attributeType) ? $attributeType : self::I95DEV_DEFAULT_ATTRIBUTE_TYPE;
        //@author Divya Koona. Getting attribute name from string code modified
        $attributeName = $this->dataHelper->getValueFromArray(self::ATTRIBUTECODE, $attributeWithKey);
        $dataType = $this->eavAttribute->create()->getBackendTypeByInput($attributeType);
        //@author Divya Koona. $this->productAttributeFactory changed to $productAttributeInterface as
        //I am getting fatal error Invalid method create() while syncing multiple records at a time
        $productAttributeInterface = $this->productAttributeFactory->create();
        $productAttributeInterface->setAttributeCode($attributeCode);
        $productAttributeInterface->setFrontendInput($frontendInput);
        $productAttributeInterface->setBackendType($dataType);
        $productAttributeInterface->setDefaultFrontendLabel($attributeName);
        $productAttributeInterface->setFrontendLabels([$this->prepareAttributeFrontend($attributeName)]);
        $productAttributeInterface->setIsUserDefined(true);
        $productAttributeInterface->setIsFilterable(true);
        $productAttributeInterface->setIsVisible(true);
        $productAttributeInterface->setIsSearchable(true);
        $productAttributeInterface->setOptions([$this->prepareAttributeOption($attributeValue)]);
        try {
            $attribute = $this->productAttributeRepo->create()->save($productAttributeInterface);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        return $attribute;
    }

    /**
     * prepare attribute frontend object
     * @param string $attributeName
     * @return object
     * @author Arushi Bansal
     */
    public function prepareAttributeFrontend($attributeName)
    {
        //@author Divya Koona. $this->attributeFrontend changed to $attributeFrontendFactory as
        //I am getting fatal error Invalid method create() while syncing multiple records at a time
        $attributeFrontend = $this->attributeFrontendFactory->create();
        $attributeFrontend->setStoreId(0);
        $attributeFrontend->setLabel($attributeName);

        return $attributeFrontend;
    }

    /**
     * prepare object of attribute options
     * @param string $attributeValue
     * @return Object
     * @author Arushi Bansal
     */
    public function prepareAttributeOption($attributeValue)
    {
        //@author Divya Koona. $this->attributeOptionFactory changed to $attributeOption as
        //I am getting fatal error Invalid method create() while syncing multiple records at a time
        $attributeOption = $this->attributeOptionFactory->create();
        $attributeOption->setLabel($attributeValue);

        // @author Arushi Bansal adding setValue to fix - 22550019
        $attributeOption->setValue($attributeValue);
        $storeLabels = $this->getStoreLabels($attributeValue);
        if (!empty($storeLabels)) {
            $attributeOption->setStoreLabels($storeLabels);
        }

        return $attributeOption;
    }

    /**
     *
     * @param string $attributeCode
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Kavya Koona
     */
    public function assignAttributeSet($attributeCode)
    {
        try {
            $attrSetId = $this->dataHelper->getscopeConfig(
                'i95dev_messagequeue/I95DevConnect_settings/attribute_set',
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $defaultGroupId = $this->dataHelper->getscopeConfig(
                'i95dev_messagequeue/I95DevConnect_settings/attribute_group',
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $this->attributeManagement->create()->assign(
                'catalog_product',
                $attrSetId,
                $defaultGroupId,
                $attributeCode,
                $this->attributeSetCollection->create()->count() * 10
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
