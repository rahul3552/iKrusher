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
namespace Aheadworks\Ctq\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Quote\Model\Quote\Item;
use Aheadworks\Ctq\Block\Customer\Quote\Edit\Item as QuoteItemBlock;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Edit\ItemFactory as QuoteItemViewModelFactory;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Quote
 * @package Aheadworks\Ctq\Block\Customer
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote getQuoteViewModel()
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote\DataProvider getDataProviderViewModel()
 */
class Quote extends Template
{
    /**
     * @var QuoteItemViewModelFactory
     */
    protected $quoteItemViewModelFactory;

    /**
     * @param Context $context
     * @param QuoteItemViewModelFactory $quoteItemViewModelFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        QuoteItemViewModelFactory $quoteItemViewModelFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quoteItemViewModelFactory = $quoteItemViewModelFactory;
    }

    /**
     * Retrieve item html
     *
     * @param Item $item
     * @return string
     */
    public function getItemHtml($item)
    {
        /** @var QuoteItemBlock $block */
        $block = $this->getLayout()->createBlock(
            QuoteItemBlock::class,
            '',
            ['data' => ['view_model' => $this->quoteItemViewModelFactory->create()]]
        );
        if (!$block) {
            return '';
        }
        $quote = $this->getDataProviderViewModel()->getQuote();
        $block
            ->setItem($item)
            ->setIsEdit($this->getQuoteViewModel()->isEditQuote($quote))
            ->setIsAllowSorting($this->getQuoteViewModel()->isAllowSorting($quote));

        return $block->toHtml();
    }
}
