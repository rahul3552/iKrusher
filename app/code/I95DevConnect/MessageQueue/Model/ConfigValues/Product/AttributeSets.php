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
class AttributeSets
{

    /**
     *
     * @var ClassModel $attributeSetCollection
     */
    public $attributeSetCollection;
    private $eavConfig;

    /**
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->attributeSetCollection = $attributeSetCollection;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Returns attribute sets option array
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $entityTypeId = $this->eavConfig
                ->getEntityType(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
                ->getEntityTypeId();
        $attributeSetdata = $this->attributeSetCollection->create()
            ->addFieldToFilter(
                \Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID,
                $entityTypeId
            )->setOrder('attribute_set_id', 'ASC')->getData();
        $attributeSets = [];
        foreach ($attributeSetdata as $val) {
            $attributeSets[] = ['value' => $val['attribute_set_id'], 'label' => __($val['attribute_set_name'])];
        }

        return $attributeSets;
    }
}
