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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;
use Magento\Framework\Pricing\Adjustment\Calculator as AdjustmentCalculator;

/**
 * Class Price
 *
 * @package Aheadworks\QuickOrder\Ui\Component\Listing\Item\Column
 */
class Price extends Column
{
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var RendererPool
     */
    private $rendererPool;

    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @var AdjustmentCalculator
     */
    private $adjustmentCalculator;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductDetailPool $productDetailPool
     * @param LayoutFactory $layoutFactory
     * @param AdjustmentCalculator $adjustmentCalculator
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductDetailPool $productDetailPool,
        LayoutFactory $layoutFactory,
        AdjustmentCalculator $adjustmentCalculator,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->layoutFactory = $layoutFactory;
        $this->productDetailPool = $productDetailPool;
        $this->adjustmentCalculator = $adjustmentCalculator;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getName()] = $this->getPriceHtml($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get render pool
     *
     * @return bool|BlockInterface|RendererPool
     * @throws LocalizedException
     */
    private function getRenderPool()
    {
        if ($this->rendererPool === null) {
            $layout = $this->layoutFactory->create();
            $layout->getUpdate()->load('catalog_product_prices');
            $layout->generateXml();
            $layout->generateElements();
            $this->rendererPool = $layout->getBlock('render.product.prices');
        }

        return $this->rendererPool;
    }

    /**
     * Get price html
     *
     * @param array $item
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getPriceHtml($item)
    {
        $provider = $this->productDetailPool->get($item);
        $rendererPool = $this->getRenderPool();
        $price = $provider->getFinalPriceForBuyRequest();
        if ($price) {
            $amount = $this->adjustmentCalculator->getAmount(
                $price,
                $provider->getProduct()
            );
            $priceRender = $rendererPool->createAmountRender($amount, $provider->getProduct());
        } else {
            $priceRender = $rendererPool->createPriceRender(
                FinalPrice::PRICE_CODE,
                $provider->getProduct(),
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );

        }

        return $priceRender->toHtml();
    }
}
