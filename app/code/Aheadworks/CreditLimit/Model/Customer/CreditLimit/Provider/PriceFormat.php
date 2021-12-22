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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider;

use Magento\Framework\Locale\FormatInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Model\Currency;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Magento\Framework\Locale\CurrencyInterface;

/**
 * Class PriceFormat
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider
 */
class PriceFormat implements ProviderInterface
{
    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param FormatInterface $localeFormat
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param SummaryRepositoryInterface $summaryRepository
     * @param CurrencyInterface $currency
     */
    public function __construct(
        FormatInterface $localeFormat,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        SummaryRepositoryInterface $summaryRepository,
        CurrencyInterface $currency
    ) {
        $this->localeFormat = $localeFormat;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->summaryRepository = $summaryRepository;
        $this->currency = $currency;
    }

    /**
     * @inheritdoc
     */
    public function getData($customerId, $websiteId)
    {
        try {
            $summary = $this->summaryRepository->getByCustomerId($customerId);
            $currency = $this->currency->getCurrency($summary->getCurrency());
            $currencyCode = $currency->getSymbol();
            $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
            if ($currencyCode != $currentCurrency) {
                $data['priceFormat'] = $this->localeFormat->getPriceFormat(null, $currentCurrency);
            }
        } catch (LocalizedException $exception) {
            $currencyCode = $this->scopeConfig->getValue(
                Currency::XML_PATH_CURRENCY_DEFAULT,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            );
        }
        $data['basePriceFormat'] = $this->localeFormat->getPriceFormat(null, $currencyCode);

        return $data;
    }
}
