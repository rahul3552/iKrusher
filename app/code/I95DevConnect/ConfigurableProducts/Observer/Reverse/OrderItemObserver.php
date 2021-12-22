<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_ConfigurableProducts
 */

namespace I95DevConnect\ConfigurableProducts\Observer\Reverse;

use \Magento\Framework\Event\ObserverInterface;

/**
 * class for order item observer
 */
class OrderItemObserver implements ObserverInterface
{

    public $magentoStoreManager;
    public $cartRepository;
    public $productRepository;
    public $requestHelper;
    public $dataObject;
    protected $_eavAttribute;
    public $logger;
    public $typeConfigurableFactory;
    public $magentoProductModel;
    public $entityAttributeFactory;
    public $productFactory;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory                            $logger
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory                          $entityAttributeFactory
     * @param \Magento\Catalog\Model\ResourceModel\ProductFactory                               $productFactory
     * @param \Magento\Catalog\Model\ProductFactory                                             $magentoProductModel
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory,
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $productFactory,
        \Magento\Catalog\Model\ProductFactory $magentoProductModel
    ) {
        $this->logger = $logger;
        $this->typeConfigurableFactory = $typeConfigurableFactory;
        $this->entityAttributeFactory = $entityAttributeFactory;
        $this->productFactory = $productFactory;
        $this->magentoProductModel = $magentoProductModel;
    }

    /**
     * Set variant attributes options
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $item = $observer->getData('quoteObject');
        $parentProduct = $this->typeConfigurableFactory->create()->getParentIdsByChild($item->simpleproduct->getId());
        $parentId = isset($parentProduct[0]) ? $parentProduct[0] : null;

        // In future if more type of product supported we need to verify product type as well
        if ($parentId !== null) {
            $item->parentProduct = $item->buyOptions = null;

            $item->parentProduct = $this->magentoProductModel->create()->load($parentId);
            $itemVariants = current($item->itemData['itemVariants']);
            $attributeWithKey = $itemVariants['attributeWithKey'];

            $options = null;
            $options = $this->getOptions($attributeWithKey);

            $buyOptions = [
                'qty' => $item->itemData['qty'],
                'super_attribute' => $options,
                '_processing_params' => []
            ];

            $markdownPrice = (isset($item->itemData['markdownPrice']) ? $item->itemData['markdownPrice'] : '');

            $price = ($markdownPrice != '') ? ($item->itemData['price'] - $markdownPrice) : $item->itemData['price'];

            if ($item->simpleproduct->getPrice() != $price) {
                $buyOptions['custom_price'] = $price;
            }

            $item->buyOptions = new \Magento\Framework\DataObject($buyOptions);
        }
    }

    /**
     * @param $attributeWithKey
     * @return mixed
     */
    public function getOptions($attributeWithKey)
    {
        $attributeOption = [];
        foreach ($attributeWithKey as $attr) {
            $attributeId = $this->entityAttributeFactory->create()->getIdByCode(
                'catalog_product',
                $attr['attributeCode']
            );
            $poductReource = $this->productFactory->create();

            //@Hrusieksh Changed the code  for issue #24894993
            $attribute = $poductReource->getAttribute($attr['attributeCode']);
            if ($attribute->usesSource()) {
                foreach ($attribute->getSource()->getAllOptions() as $option) {
                    $attributeOption[strtolower($option['label'])] = $option['value'];
                }
            }

            $optionId = (array_key_exists(strtolower($attr['attributeValue']), $attributeOption)) ?
                $attributeOption[strtolower($attr['attributeValue'])] : null;

            $options[$attributeId] = $optionId;
        }

        return $options;
    }
}
