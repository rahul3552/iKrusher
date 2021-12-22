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
namespace Aheadworks\Ctq\Block\Customer\Quote\Edit;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Edit\Item as QuoteItemViewModel;
use Magento\Quote\Model\Quote\Item as CartItem;

/**
 * Class Item
 * @package Aheadworks\Ctq\Block\Customer\Quote\Edit
 * @method Item setItem(CartItem $item)
 * @method CartItem getItem()
 * @method Item setIsEdit(bool $item)
 * @method bool getIsEdit()
 * @method bool getIsAllowSorting()
 * @method Item setIsAllowSorting(bool $isAllowSorting)
 * @method bool getIsExport()
 * @method Item setIsExport(bool $isExport)
 * @method QuoteItemViewModel getViewModel()
 */
class Item extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Ctq::customer/quote/edit/item.phtml';

    /**
     * @var Image
     */
    private $image;

    /**
     * @param Template\Context $context
     * @param Image $image
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Image $image,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->image = $image;
    }

    /**
     * Get quote item qty
     *
     * @return float|int
     */
    public function getQty()
    {
        if ((string)$this->getItem()->getQty() == '') {
            return '';
        }
        return $this->getItem()->getQty() * 1;
    }

    /**
     * Return the unit price html
     *
     * @param CartItem $item
     * @return string
     */
    public function getUnitPriceHtml(CartItem $item)
    {
        return $this->getPriceBlockHtml($item, 'checkout.item.price.unit');
    }

    /**
     * Return row total html
     *
     * @param CartItem $item
     * @return string
     */
    public function getRowTotalHtml(CartItem $item)
    {
        return $this->getPriceBlockHtml($item, 'checkout.item.price.row');
    }

    /**
     * Retrieve price block html by name
     *
     * @param CartItem $item
     * @param string $blockName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getPriceBlockHtml(CartItem $item, $blockName)
    {
        $block = $this->getLayout()->getBlock($blockName);
        $block->setItem($item);
        $storeData = $item->getStore()->getData();
        $html = $block->toHtml();
        $item->getStore()->setData($storeData);

        return $html;
    }

    /**
     * Get image url
     *
     * @param Product $product
     * @return string
     */
    public function getImageUrl($product)
    {
        $this->image->init($product, 'product_base_image');
        return $this->image->getUrl();
    }
}
