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

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Backend\Block\Template\Context;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as SessionQuote;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Updater as QuoteUpdater;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Provider as TotalProvider;
use Aheadworks\Ctq\Model\Quote\Admin\Quote\Total\Renderer as TotalRenderer;
use Magento\Framework\Phrase;

/**
 * Class OrderTotals
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class OrderTotals extends AbstractEdit
{
    /**
     * @var TotalProvider
     */
    private $totalProvider;

    /**
     * @var TotalRenderer
     */
    private $totalRenderer;

    /**
     * @param Context $context
     * @param SessionQuote $sessionQuote
     * @param QuoteUpdater $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param TotalProvider $totalProvider
     * @param TotalRenderer $totalRenderer
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionQuote $sessionQuote,
        QuoteUpdater $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        TotalProvider $totalProvider,
        TotalRenderer $totalRenderer,
        array $data = []
    ) {
        $this->totalProvider = $totalProvider;
        $this->totalRenderer = $totalRenderer;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Order Totals');
    }

    /**
     * Render quote totals
     *
     * @param string|null $area
     * @param int $colspan
     * @return string
     */
    public function renderTotals($area = null, $colspan = 1)
    {
        $totals = $this->totalProvider->getOrderTotals($this->getQuote());
        return $this->totalRenderer->render($totals, $area, $colspan);
    }
}
