<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Reverse;

use \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\AbstractOrder;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class Add item information while creating an order
 */
class Item extends AbstractOrder
{

    const I95EXC = 'i95devApiException';
    const PRICE = "price";

    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $magentoProductModel;

    /**
     *
     * @var orderItems[]
     */
    public $orderItems;

    /**
     *
     * @var itemEntity[]
     */
    public $itemEntity = [];

    /**
     *
     * @var subTotals
     */
    public $subTotals = 0;

    /**
     *
     * @var totalQty
     */
    public $totalQty = 0;
    public $genericHelper;
    public $orderItemsCount = 0;
    public $orderQuantity = 0;
    protected $quoteModel;
    protected $productFactory;

    /**
     * Entity attribute factory
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory
     */
    protected $entityAttributeFactory;
    protected $productRepository;
    public $itemsCount;
    public $itemQuantity;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Catalog\Model\ProductFactory $magentoProductModel
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param QuoteFactory $quote
     * @param CartRepositoryInterface $cartRep
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Catalog\Model\ProductFactory $magentoProductModel,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        QuoteFactory $quote,
        CartRepositoryInterface $cartRep,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
    ) {
        $this->dataHelper = $dataHelper;
        $this->magentoProductModel = $magentoProductModel;
        $this->date = $date;
        $this->quote = $quote;
        $this->cartRep = $cartRep;
        $this->eventManager = $eventManager;
        $this->productRepository = $productRepository;
        parent::__construct(
            $logger,
            $genericHelper,
            $validate
        );
    }

    /**
     * Add Item Informations To quote.
     *
     * @param \Magento\Quote\Model\Quote $quoteModel
     * @return \Magento\Quote\Model\Quote $this->quoteModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addItemsToQuote($quoteModel)
    {
        try {
            $this->quoteModel = $quoteModel;
            foreach ($this->quoteItems as $itemData) {
                $this->addSimpleProduct($itemData);
            }
            $this->quoteModel->setItemsCount($this->itemsCount);
            $this->quoteModel->setItemsQty($this->itemQuantity);
            $this->quoteModel->setIsVirtual(0);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        return $this->quoteModel;
    }

    /**
     * Adding Item to quote
     * @param array $itemData
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona. Removed unused code for discount amount.
     */
    public function addSimpleProduct($itemData)
    {
        $this->itemData = $itemData;
        $sku = $itemData['sku'];
        $product = $this->getProductBySku($sku);
        if (!empty($product)) {
            $buyOptions = [];
            $this->simpleproduct = $this->magentoProductModel->create()->loadByAttribute("sku", $sku);
            $productData = $this->simpleproduct->getData();
            $this->parentProduct = $this->magentoProductModel->create()->load($this->simpleproduct->getId());
            $buyOptions = [
                'qty' => $itemData['qty'],
                '_processing_params' => []
            ];
            $markDownPrice = (isset($itemData['markdownPrice']) ? $itemData['markdownPrice'] : '');
            if ($markDownPrice != '') {
                $price = $itemData[self::PRICE] - $markDownPrice;
            } else {
                $price = $itemData[self::PRICE];
            }
            /* @updatedBy Ranjith Rasakatla. Fix for 22920201(Order level item price mapping
             * issue in Magento for the NAV created custom price/Tier price order)
             */
            if ($productData[self::PRICE] != $price) {
                $buyOptions['custom_price'] = $price;
            }

            $this->buyOptions = new \Magento\Framework\DataObject($buyOptions);

            $beforeQuoteSave = 'erpconnect_messagequeuetomagento_beforesave_quoteitem';
            $this->eventManager->dispatch($beforeQuoteSave, ['quoteObject' => $this]);

            $this->parentProduct->setPrice($price);
            $response = $this->quoteModel->addProduct($this->parentProduct, $this->buyOptions);
            if (is_string($response)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("Error Occurred while adding %1 :- %2", $sku, $response)
                );
            }
            $this->itemsCount++;
            $this->itemQuantity += $itemData['qty'];
        } else {
            $message = __('i95dev_quote_product_valid') . "(" . $sku . ")";
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }

    /**
     * Validate request Order item data
     *
     * @param array $stringData
     * @author Divya Koona.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData($stringData)
    {
        $this->stringData = $stringData;
        $orderItemsArray = $this->dataHelper->getValueFromArray("orderItems", $this->stringData);
        $finalItems = [];
        foreach ($orderItemsArray as $itemData) {
            if ($this->dataHelper->getValueFromArray('sku', $itemData) == '') {
                throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_032"));
            }
            $sku = $itemData['sku'];
            $item = $this->getProductBySku($sku);
            if (empty($item)) {
                throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_028"));
            } else {
                $erpItemType = $this->dataHelper->getValueFromArray("typeId", $itemData);
                $mageItemType = $this->dataHelper->getValueFromArray("type_id", $item);
                $this->validateQuoteItem($erpItemType, $mageItemType, $item);
            }

            $itemInformation = $itemData;
            if (key_exists($sku, $finalItems)) {
                $existingItem = $finalItems[$sku];
                if ($existingItem[self::PRICE] == $itemInformation[self::PRICE]) {
                    $itemInformation['qty'] += $existingItem['qty'];
                } else {
                    $message = 'SKU ::' . $sku . " Having Different Price (" . $existingItem[self::PRICE] .
                            "," . $itemInformation[self::PRICE] . ")";
                    throw new \Magento\Framework\Exception\LocalizedException(__($message));
                }
            }
            $finalItems[$sku] = $itemInformation;
        }
        $this->quoteItems = $finalItems;
    }

    /**
     * @param $erpItemType
     * @param $mageItemType
     * @param $item
     */
    public function validateQuoteItem($erpItemType, $mageItemType, $item)
    {
        if ($erpItemType != '' && strtolower($erpItemType) != strtolower($mageItemType)) {
            throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_029"));
        }
        if (isset($item['status']) && $item['status'] !== '1') {
            throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_027"));
        } else {
            $beforeQuoteItemValidate = 'erpconnect_messagequeuetomagento_validatequoteitem';
            $this->eventManager->dispatch($beforeQuoteItemValidate, ['quoteObject' => $this, 'item' => $item]);
        }
    }

    /**
     * Gets product by product sku
     * @param string $sku
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona. Removed API call and converted into interfaces
     */
    public function getProductBySku($sku)
    {
        try {
            $result = $this->productRepository->create()->get($sku);
            return $result->__toArray();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            return [];
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
