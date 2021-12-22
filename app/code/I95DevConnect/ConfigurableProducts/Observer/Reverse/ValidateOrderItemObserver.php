<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_ConfigurableProducts
 */

namespace I95DevConnect\ConfigurableProducts\Observer\Reverse;

use \Magento\Framework\Event\ObserverInterface;

/**
 * Variant order item observer class
 */
class ValidateOrderItemObserver implements ObserverInterface
{

    public $magentoStoreManager;
    public $cartRepository;
    public $productRepository;
    public $requestHelper;
    public $dataObject;
    protected $_eavAttribute;
    public $logger;
    public $magentoProductModel;
    public $typeConfigurableFactory;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory                            $logger
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory
     * @param \Magento\Catalog\Model\ProductFactory                                             $magentoProductModel
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory $typeConfigurableFactory,
        \Magento\Catalog\Model\ProductFactory $magentoProductModel
    ) {
        $this->logger = $logger;
        $this->typeConfigurableFactory = $typeConfigurableFactory;
        $this->magentoProductModel = $magentoProductModel;
    }

    /**
     * Get parent product load by id
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $item = $observer->getData('item');
        //@author Divya Koona. $item['id'] changed to $item['entity_id'] as
        //I am getting undefined index issue while order sync from Inbound MQ to Magento
        $parentproduct = $this->typeConfigurableFactory->create()->getParentIdsByChild($item['entity_id']);
        $parentId = isset($parentproduct[0]) ? $parentproduct[0] : null;
        if ($parentId !== null) {
            $parentProduct = $this->magentoProductModel->create()->load($parentId);
            // @Hrusikesh converted integer to string
            $status = $parentProduct->getStatus();
            if ((string)$status !== '1') {
                throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_027"));
            }
        }
    }
}
