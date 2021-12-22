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
namespace Aheadworks\Ctq\Block\Checkout;

use Magento\Framework\View\Element\Template;

/**
 * Class RequestQuoteLink
 * @package Aheadworks\Ctq\Block\Checkout
 * @method \Aheadworks\Ctq\ViewModel\Checkout\RequestQuoteLink getViewModel()
 * @method \Aheadworks\Ctq\ViewModel\Customer\FileUploader getFileUploaderViewModel()
 */
class RequestQuoteLink extends Template
{
    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        $this->jsLayout = $this->getFileUploaderViewModel()->prepareJsLayout($this->jsLayout);

        return parent::getJsLayout();
    }
}
