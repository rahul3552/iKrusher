<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Quote\Admin\Quote\Item;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\ItemFactory as QuoteItemFactory;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItemResource;
use Magento\Quote\Model\ResourceModel\Quote\Item\Option\CollectionFactory;

/**
 * Class Loader
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote\Item
 */
class Loader
{
    /**
     * @var QuoteItemFactory
     */
    private $quoteItemFactory;

    /**
     * @var QuoteItemResource
     */
    private $quoteItemResource;

    /**
     * @var CollectionFactory
     */
    private $optionCollectionFactory;

    /**
     * @param QuoteItemFactory $quoteItemFactory
     * @param QuoteItemResource $quoteItemResource
     * @param CollectionFactory $optionCollectionFactory
     */
    public function __construct(
        QuoteItemFactory $quoteItemFactory,
        QuoteItemResource $quoteItemResource,
        CollectionFactory $optionCollectionFactory
    ) {
        $this->quoteItemFactory = $quoteItemFactory;
        $this->quoteItemResource = $quoteItemResource;
        $this->optionCollectionFactory = $optionCollectionFactory;
    }

    /**
     * Load quote item with options
     *
     * @param int $quoteItemId
     * @return QuoteItem
     * @throws LocalizedException
     */
    public function load($quoteItemId)
    {
        $quoteItem = $this->quoteItemFactory->create();
        $this->quoteItemResource->load($quoteItem, $quoteItemId);
        if (!$quoteItem->getId()) {
            throw new LocalizedException(
                __('The quote item cannot be loaded.')
            );
        }

        $optionCollection = $this->optionCollectionFactory->create();
        $optionCollection->addItemFilter([$quoteItemId]);
        $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

        return $quoteItem;
    }
}
