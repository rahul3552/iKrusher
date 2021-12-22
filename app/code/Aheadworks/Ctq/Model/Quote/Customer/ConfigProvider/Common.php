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
namespace Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Aheadworks\Ctq\ViewModel\Customer\Quote\DataProvider as QuoteDataProvider;

/**
 * Class Common
 * @package Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider
 */
class Common implements ConfigProviderInterface
{
    /**
     * @var QuoteDataProvider
     */
    private $quoteDataProvider;

    /**
     * @var LocaleFormat
     */
    private $localeFormat;

    /**
     * @param QuoteDataProvider $quoteDataProvider
     * @param LocaleFormat $localeFormat
     */
    public function __construct(
        QuoteDataProvider $quoteDataProvider,
        LocaleFormat $localeFormat
    ) {
        $this->quoteDataProvider = $quoteDataProvider;
        $this->localeFormat = $localeFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $cart = $this->quoteDataProvider->getCart();

        $output['quoteData'] = $cart->toArray();
        $output['basePriceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $cart->getBaseCurrencyCode()
        );
        $output['priceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $cart->getQuoteCurrencyCode()
        );
        $output['storeCode'] = $cart->getStore()->getCode();

        return $output;
    }
}
