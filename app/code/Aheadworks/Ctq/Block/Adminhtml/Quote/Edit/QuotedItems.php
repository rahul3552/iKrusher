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
namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Quote\Model\Quote\Item;
use Magento\Framework\Phrase;
use Magento\Backend\Block\Widget\Button as WidgetButton;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class QuotedItems
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class QuotedItems extends AbstractEdit
{
    /**
     * Contains button descriptions to be shown at the top of accordion
     *
     * @var array
     */
    protected $_buttons = [];

    /**
     * Define block ID
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_quote_items');
    }

    /**
     * Accordion header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Products');
    }

    /**
     * Returns all visible items
     *
     * @return Item[]
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Add button to the items header
     *
     * @param array $args
     * @return void
     */
    public function addButton($args)
    {
        $this->_buttons[] = $args;
    }

    /**
     * Render buttons and return HTML code
     *
     * @return string
     * @throws LocalizedException
     */
    public function getButtonsHtml()
    {
        $html = '';
        $this->_buttons = array_reverse($this->_buttons);
        foreach ($this->_buttons as $buttonData) {
            $html .= $this
                ->getLayout()
                ->createBlock(WidgetButton::class)
                ->setData($buttonData)
                ->toHtml();
        }

        return $html;
    }

    /**
     * Return HTML code of the block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getStoreId()) {
            return parent::_toHtml();
        }
        return '';
    }
}
