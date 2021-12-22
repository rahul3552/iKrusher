<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\ConfigValues\Product;

/**
 * Class for Tax Class
 * @createdBy kavya koona
 */
class AttributeGroup
{

    /**
     *
     * @var ClassModel $attributeSetCollection
     */
    public $attributeSetCollection;
    private $eavConfig;
    public $groupCollectionFactory;
    public $dataHelper;
    protected $scopeConfig;
    public $storeManager;

    /**
     * AttributeGroup constructor.
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {

        $this->attributeSetCollection = $attributeSetCollection;
        $this->eavConfig = $eavConfig;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        $attributeSetId = $this->scopeConfig->getValue(
            "i95dev_messagequeue/I95DevConnect_settings/attribute_set",
            $storeScope,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );

        $groupCollection = $this->groupCollectionFactory->create()
                ->addFieldToFilter('attribute_set_id', $attributeSetId)
                ->setOrder('attribute_group_id', 'ASC')
                ->getData(); // product attribute group collection
        $attributeGroups = [];
        foreach ($groupCollection as $group) {
            $attributeGroups[] = [
                'value' => $group['attribute_group_id'],
                'label' => __($group['attribute_group_name'])
            ];
        }
        return $attributeGroups;
    }
}
