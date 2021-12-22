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
namespace Aheadworks\Ctq\Block\RequestQuote;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Success
 * @package Aheadworks\Ctq\Block\RequestQuote
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote getQuoteViewModel()
 */
class Success extends Template
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Retrieve continue url
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if (!$this->getLastQuoteId()) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * Retrieve last quote id
     *
     * @return int|null
     */
    public function getLastQuoteId()
    {
        return $this->checkoutSession->getAwCtqLastRealQuoteId();
    }
}
