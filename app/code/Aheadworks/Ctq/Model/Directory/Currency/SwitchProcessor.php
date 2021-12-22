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
namespace Aheadworks\Ctq\Model\Directory\Currency;

use Aheadworks\Ctq\Model\QuoteList\Provider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class SwitchProcessor
 * @package Aheadworks\Ctq\Model\Directory\Currency
 */
class SwitchProcessor
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param Provider $provider
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Provider $provider
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->provider = $provider;
    }

    /**
     * Switch quote list currency
     */
    public function switchQuoteListCurrency()
    {
        try {
            $quote = $this->provider->getQuote();
            if ($quote) {
                $this->quoteRepository->save($quote->collectTotals());
            }
        } catch (LocalizedException $e) {
        }
    }
}
