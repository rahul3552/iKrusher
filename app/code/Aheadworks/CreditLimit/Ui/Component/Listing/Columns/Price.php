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
namespace Aheadworks\CreditLimit\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Website;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Price
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Columns
 */
class Price extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceFormatter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->priceFormatter = $priceFormatter;
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                /** @var Website $website */
                $website = $this->storeManager->getWebsite($item['website_id']);
                $currencyCode = $website->getBaseCurrencyCode();
                $price = $item[$this->getData('name')];
                $showPlus = $this->getData('config/showPlus') && $price >= 0;
                $item['row_сlass_' . $this->getData('name')] = $price >= 0
                    ? 'aw_cl__price-green'
                    : 'aw_cl__price-red';
                $item[$this->getData('name')] = ($showPlus ? '+' : '') . $this->priceFormatter->format(
                    $price,
                    false,
                    PriceCurrencyInterface::DEFAULT_PRECISION,
                    null,
                    $currencyCode
                );
            }
        }

        return $dataSource;
    }
}
