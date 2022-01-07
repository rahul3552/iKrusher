<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Model;

use Magento\Framework\Model\AbstractModel;
use I95DevConnect\PriceLevel\Helper\Data;

/**
 * Model Class for Item Price
 */
class ItemPrice extends AbstractModel
{

    /**
     * @var array
     */
    private $pricesBySku;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $data;

    /**
     * Customer object
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\ItemPriceListDataFactory
     */
    public $priceListFactory;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\CustomerGroup
     */
    public $customerGroup;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory
     */
    public $priceLevelFactory;

    /**
     *
     * @var string
     */
    public $sku;
    
    /**
     *
     * @var int
     */
    public $qty;
    
    /**
     * @var ResourceModel\ItemPriceListData
     */
    public $itemPriceResource;

    /**
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param Data $data
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \I95DevConnect\PriceLevel\Model\ItemPriceListDataFactory $priceListFactory
     * @param \I95DevConnect\MessageQueue\Model\CustomerGroupFactory $customerGroup
     * @param \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $priceLevelFactory
     * @param \I95DevConnect\PriceLevel\Model\ResourceModel\ItemPriceListData $itemPriceResource
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Data $data,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \I95DevConnect\PriceLevel\Model\ItemPriceListDataFactory $priceListFactory,
        \I95DevConnect\MessageQueue\Model\CustomerGroupFactory $customerGroup,
        \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $priceLevelFactory,
        \I95DevConnect\PriceLevel\Model\ResourceModel\ItemPriceListData $itemPriceResource,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->logger = $logger;
        $this->data = $data;
        $this->customerFactory = $customerFactory;
        $this->priceListFactory = $priceListFactory;
        $this->customerGroup = $customerGroup;
        $this->priceLevelFactory =$priceLevelFactory;
        $this->itemPriceResource = $itemPriceResource;
        parent::__construct($context, $registry);
    }

    /**
     * Get product price level price
     * @updatedBy Debashis S. Gopal. Method definition changed.
     * Directly getting $product instead or loading it using repository.
     * If we load it here it is going to recursive loop for
     * products with special prices(Addressed inMagento version 2.2.8)
     *
     * @param  obj $product
     * @param  int    $customerId
     * @param  int    $qty
     * @return float $finalPrice
     */
    public function getItemFinalPrice($product, $customerId, $qty)
    {
        //@Hrusieksh Removed Product Original Price
        $finalPrice = 0;
        $this->sku = $product->getSku();
        $this->qty = $qty;
        $customer = $this->getCustomer($customerId);
        $customerPriceLevel = isset($customer['pricelevel']) ? $customer['pricelevel'] : '';
        $this->logger->debug($customerPriceLevel);

        if ($customerPriceLevel != '') {
            $finalPrice = $this->getPriceFromPriceList($customerPriceLevel, $qty);
        }

        if ($finalPrice == 0) {
            $customerGroupPriceLevel = $this->getCustomerGroupPriceLevel($customerId);
            if ($customerGroupPriceLevel != '') {
                $finalPrice = $this->getPriceFromPriceList($customerGroupPriceLevel, $qty);
            }
        }

        return $finalPrice;
    }

    /**
     * Get required customer attributes from customer id
     *
     * @param int $customerId
     * @return array
     */
    public function getCustomer($customerId)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        return ['target_customer_id' => $customer->getTargetCustomerId(), 'pricelevel' => $customer->getPricelevel()];
    }

    /**
     * Get customer group assigned price level
     *
     * @param  int $customerId
     * @return string $priceLevel
     */
    public function getCustomerGroupPriceLevel($customerId)
    {
        $groupId = $this->customerFactory->create()->load($customerId)->getGroupId();
        $this->logger->debug($groupId);
        $groupData = $this->customerGroup->create()->getCollection()
                ->addFieldToFilter('customer_group_id', $groupId)->getData();
        $priceLevelId = isset($groupData[0]['pricelevel_id']) ? $groupData[0]['pricelevel_id'] : '';
        $this->logger->debug($priceLevelId);
        $priceLevel = $this->priceLevelFactory->create()->load($priceLevelId)->getPricelevelCode();
        $this->logger->debug($priceLevel);
        return $priceLevel;
    }
    
    
    /**
     * Get customer assigned price level
     *
     * @param  int $customerId
     * @return string $priceLevel
     */
    public function getCustomerPriceLevel($customerId)
    {
	return $this->customerFactory->create()->load($customerId)->getPricelevel();
    }

    /**
     * Get product's price levels list
     *
     * @param  string $customerPriceLevel
     * @param  string $SKU
     * @return array
     */
    public function getItemPriceList($customerPriceLevel, $SKU)
    {
        $now = new \DateTime();
        $todayDate = $now->format('Y-m-d');
        return $this->priceListFactory->create()->getCollection()
            ->addFieldToFilter('sku', $SKU)
            ->addFieldToFilter('pricelevel', $customerPriceLevel)
            ->addFieldToFilter(
                'from_date',
                [
                    ['lteq' => $todayDate],
                    ['from_date', 'null' => '']
                ]
            );
    }

    /**
     * Get product price from price levels list
     *
     * @param string $customerPriceLevel
     * @param int $qty
     *
     * @return float $prevPrice
     */
    public function getPriceFromPriceList($customerPriceLevel, $qty)
    {
        $tierPricesCollection = $this->getItemPriceList($customerPriceLevel, $this->sku);
        $tierPrices = $tierPricesCollection->addFieldToFilter('qty', ['lteq' => $this->qty])
                ->setOrder('qty', 'ASC')->getData();
        $prevPrice = [];
        foreach ($tierPrices as $tier) {
            $tierQty = isset($tier['qty']) ? $tier['qty'] : '';
            if ($tierQty <= $qty) {
                $prevPrice[] = isset($tier['price']) ? $tier['price'] : '';
            }
        }
        if (!empty($prevPrice)) {
            return min($prevPrice);
        } else {
            return 0;
        }
    }

    /**
     * Get product's price levels list
     *
     * @param  string $customerId
     * @param  string $SKU
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemPriceListDisplay($customerId, $SKU)
    {
        $customer = $this->getCustomer($customerId);
        $customerPriceLevel = isset($customer['pricelevel']) ? $customer['pricelevel'] : '';

        $result = [];
        foreach ($this->_getPriceBySku($SKU) as $price) {
            if (strcasecmp(trim($customerPriceLevel), $price['pricelevel']) === 0 && (int) $price['qty'] != 1) {
                $result[] = $price;
            }
        }

        return $result;
    }

    /**
     * Get Prices by SKU
     *
     * @param string $sku
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _getPriceBySku($sku)
    {
        $sku = strtolower($sku);

        if (null === $this->pricesBySku) {
            $this->pricesBySku = $this->itemPriceResource->getRowsBySku();
        }

        return $this->pricesBySku[$sku] ?? [];
    }
}
