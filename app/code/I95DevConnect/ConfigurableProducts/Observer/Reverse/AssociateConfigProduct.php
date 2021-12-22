<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_MessageQueue
 * @createdBy vinayakrao.shetkar
 */

namespace I95DevConnect\ConfigurableProducts\Observer\Reverse;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class for sales order invoice before save
 *
 * @author Arushi Bansal
 */
class AssociateConfigProduct implements ObserverInterface
{

    public $linkManager;
    public $productRepo;
    public $configurableType;
    public $optionRepo;
    const PARENTSKU = "parentSku";

    /**
     * @param  \Magento\ConfigurableProduct\Api\LinkManagementInterface                   $linkManager
     * @param  \Magento\Catalog\Api\ProductRepositoryInterface                            $productRepo
     * @param  \Magento\ConfigurableProduct\Api\OptionRepositoryInterface                 $optionRepo
     * @param  \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableType
     * @author Arushi Bansal
     */
    public function __construct(
        \Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepo,
        \Magento\ConfigurableProduct\Api\OptionRepositoryInterface $optionRepo,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableType
    ) {
        $this->linkManager = $linkManager;
        $this->productRepo = $productRepo;
        $this->optionRepo = $optionRepo;
        $this->configurableType = $configurableType;
    }

    /**
     * Save custom invoice
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return bool|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Arushi Bansal
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $currentObject = $observer->getEvent()->getData("currentObject");
            $stringData = $currentObject->stringData;
            $result = $currentObject->result;
            if (isset($stringData[self::PARENTSKU]) && !empty($stringData[self::PARENTSKU])) {
                $product = $this->productRepo->get($stringData[self::PARENTSKU]);
                //@Hrusikesh Compaire two string by PHP strcmp()
                $isEqual = strcmp($product->getTypeId(), "configurable");
                if ($isEqual === 0) {
                    $childrenIds = array_values($this->configurableType->getChildrenIds($product->getId())[0]);

                    if (!in_array($result->getId(), $childrenIds)) {
                        $this->linkManager->addChild($stringData[self::PARENTSKU], $stringData["sku"]);
                    }
                }
            }

        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {

            return true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {

            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
