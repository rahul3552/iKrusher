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
namespace Aheadworks\Ctq\Block\Customer\Quote;

use Magento\Framework\View\Element\Html\Link\Current;

/**
 * Class Link
 * @package Aheadworks\Ctq\Block\Customer\Quote
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote\DataProvider getDataProviderViewModel()
 */
class Link extends Current
{
    /**
     * {@inheritdoc}
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath(), ['quote_id' => $this->getDataProviderViewModel()->getQuote()->getId()]);
    }
}
