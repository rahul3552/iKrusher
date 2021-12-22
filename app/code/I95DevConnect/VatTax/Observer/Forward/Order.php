<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Observer\Forward;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer class to append Tax Product Posting Group and Tax Business Posting Group to the Order information
 */
class Order implements ObserverInterface
{
    public $helper;
    public $productInfo;
    public $magentoProductModel;

    /**
     *
     * @param \I95DevConnect\VatTax\Helper\Data $helper
     * @param \Magento\Catalog\Model\ProductFactory $magentoProductModel
     * @param \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepo
     */
    public function __construct(
        \I95DevConnect\VatTax\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $magentoProductModel,
        \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepo
    ) {
        $this->helper = $helper;
        $this->magentoProductModel = $magentoProductModel;
        $this->customerRepo = $customerRepo;
    }

    /**
     * Append Tax Product Posting Group and Tax Business Posting Group
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $currentObject = $observer->getEvent()->getData("order");
        if ($this->helper->isVatTaxEnabled() && isset($currentObject->InfoData)) {
            $orderItems = $currentObject->InfoData['orderItems'];
            foreach ($orderItems as $key => $orderItem) {
                $product = $this->magentoProductModel->create()->loadByAttribute("sku", $orderItem['sku']);
                if (is_object($product) && !empty($product->getData())) {
                    $currentObject->InfoData['orderItems'][$key]['taxProductPostingGroupCode'] =
                        $product->getData('tax_product_posting_group');
                }
            }

            $currentObject->InfoData['customer']['taxBusPostingGroupCode'] = null;
            $isGuestOrder = $currentObject->order['customer_is_guest'];
            if (!$isGuestOrder) {
                $customer = $this->customerRepo->create()->getById(
                    $currentObject->order->getCustomerId()
                );
                $currentObject->InfoData['customer']['taxBusPostingGroupCode'] =
                    ($customer->getCustomAttribute('tax_bus_posting_group')!== null) ?
                    $customer->getCustomAttribute('tax_bus_posting_group')->getValue() : null;
            }
        }
    }
}
