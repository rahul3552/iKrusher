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
namespace Aheadworks\Ctq\Block\Customer\QuoteList;

use Magento\Framework\View\Element\Template;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Container
 *
 * @package Aheadworks\Ctq\Block\Customer\QuoteList
 */
class Container extends Template
{
    /**
     * @var QuoteInterface
     */
    private $quote;

    /**
     * Set quote
     *
     * @param QuoteInterface $quote
     * @return $this
     */
    public function setQuote(QuoteInterface $quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Get quote
     *
     * @return QuoteInterface
     */
    private function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set quote for children during retrieving their HTML
     *
     * @param string $alias
     * @param bool $useCache
     * @return string
     * @throws LocalizedException
     */
    public function getChildHtml($alias = '', $useCache = false)
    {
        $layout = $this->getLayout();
        if ($layout) {
            $name = $this->getNameInLayout();
            foreach ($layout->getChildBlocks($name) as $child) {
                $child->setQuote($this->getQuote());
            }
        }
        return parent::getChildHtml($alias, $useCache);
    }
}
