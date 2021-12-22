<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Observer;

use \I95DevConnect\PriceLevel\Helper\Data;
use \I95DevConnect\PriceLevel\Model\ItemPrice;
use \Magento\Framework\Event\ObserverInterface;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;
use \I95DevConnect\MessageQueue\Helper\Data as MQDataHelper;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Directory\Model\CurrencyFactory;
use \Magento\Store\Model\StoreManagerInterface;

/**
 * Observer to set product price level price before quote collect totals.
 */
class QuoteCollectTotalsBefore implements ObserverInterface
{

    /**
     * @var Data
     */
    public $data;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CustomerModelSession
     */
    public $customerSession;

    /**
     * @var ItemPrice
     */
    public $itemPrice;

    /**
     * @var MQDataHelper
     */
    public $dataHelper;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var CurrencyFactory
     */
    public $priceCurrencyFactory;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * QuoteCollectTotalsBefore constructor.
     *
     * @param LoggerInterface $logger
     * @param Data $data
     * @param CustomerSession $customerSession
     * @param MQDataHelper $dataHelper
     * @param ItemPrice $itemPrice
     * @param ProductRepositoryInterface $productRepository
     * @param CurrencyFactory $priceCurrencyFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        LoggerInterface $logger,
        Data $data,
        CustomerSession $customerSession,
        MQDataHelper $dataHelper,
        ItemPrice $itemPrice,
        ProductRepositoryInterface $productRepository,
        CurrencyFactory $priceCurrencyFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->data = $data;
        $this->customerSession = $customerSession;
        $this->itemPrice = $itemPrice;
        $this->dataHelper = $dataHelper;
        $this->productRepository = $productRepository;
        $this->priceCurrencyFactory = $priceCurrencyFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * set Erp tierprice to product
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if (!$this->data->isEnabled()) {
            return $this;
        }

        try {
            $customerId = $this->customerSession->getCustomerId();
            $quote = $observer->getData('quote');
            $items = $quote->getAllVisibleItems();
            if ($customerId) {
                foreach ($items as $item) {
                    $qty = $item->getQty();
                    $sku = $item->getSku();
                    $actualFinalPrice = $item->getProduct()->getPrice();
                    if ($qty == '') {
                        $qty = 1;
                    }
                    $product = $this->productRepository->get($sku);
                    $productFinalPrice = $product->getFinalPrice();
                    $getTirePrice = $this->itemPrice->getItemFinalPrice($product, $customerId, $qty);
                    $finalPrice = min($productFinalPrice, $getTirePrice);
                    $currencyCodeTo = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
                    $currencyCodeFrom = $this->storeManager->getStore()->getBaseCurrency()->getCode();
                    $rate = $this->priceCurrencyFactory->create()->load($currencyCodeFrom)
                        ->getAnyRate($currencyCodeTo);
                    if (!empty($finalPrice) && $actualFinalPrice >= $finalPrice) {
                        $finalPrice = $finalPrice * $rate;
                        $item->setCustomPrice($finalPrice);
                        $item->setOriginalCustomPrice($finalPrice);
                        $item->getProduct()->setIsSuperMode(true);
                        $item->setRowTotal($finalPrice * $qty);
                        $item->setBaseRowTotal($finalPrice * $qty);
                    } else {
                        $finalPrice = $productFinalPrice * $rate;
                        $item->setCustomPrice($finalPrice);
                        $item->setOriginalCustomPrice($finalPrice);
                        $item->getProduct()->setIsSuperMode(true);
                        $item->setRowTotal($finalPrice * $qty);
                        $item->setBaseRowTotal($finalPrice * $qty);
                    }
                }
                $quote->save();
            }
        } catch (LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), LoggerInterface::I95EXC, 'critical');
            return $this;
        }
        return $this;
    }
}
